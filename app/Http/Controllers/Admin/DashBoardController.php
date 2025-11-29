<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Admin;
use App\Models\Message;
use Carbon\Carbon;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class DashBoardController extends Controller
{
    public function dashboard()
    {
        // ===== 1. Doanh thu theo trạng thái =====
        $totalPendings  = Order::where('payment_status', 'pending')
            ->sum('total_price');

        $totalCompletes = Order::where('payment_status', 'completed')
            ->sum('total_price');

        // Tổng doanh thu (mình lấy theo đơn đã completed cho chuẩn)
        $totalRevenue = $totalCompletes;
        // Nếu bạn muốn tính cả pending:
        // $totalRevenue = $totalPendings + $totalCompletes;


        // ===== 2. Giá vốn & LỢI NHUẬN =====
        // Lấy tổng giá vốn của các item thuộc đơn completed
        $totalCost = OrderItem::whereHas('order', function ($q) {
            $q->where('payment_status', 'completed');
        })
            ->sum(DB::raw('cost_price * quantity'));

        // Lợi nhuận = Doanh thu - Giá vốn
        $totalProfit = $totalRevenue - $totalCost;


        // ===== 3. Các thống kê khác =====
        $numberOfOrders   = Order::count();
        $numberOfProducts = Product::count();
        $numberOfUsers    = User::count();
        $numberOfAdmins   = Admin::count();
        $numberOfMessages = Message::count();
        $totalInventory = Product::sum('inventory');

        // Trả về view
        return view('admin.dashboard', compact(
            'totalPendings',
            'totalCompletes',
            'totalRevenue',
            'totalCost',
            'totalProfit',   // ✅ truyền thêm lợi nhuận
            'numberOfOrders',
            'numberOfProducts',
            'numberOfUsers',
            'numberOfAdmins',
            'numberOfMessages',
            'totalInventory', 
        ));
    }



    public function userStats(Request $request)
    {
        $range = $request->query('range', '7d'); // 7d, 30d, 6m, 12m

        try {
            $labels       = [];
            $data         = []; // tổng user tích lũy
            $totalNew     = 0;  // tổng user mới trong khoảng
            $rangeText    = ''; // text hiển thị dưới chart

            if (in_array($range, ['7d', '30d'])) {
                // ===== THEO NGÀY =====
                $days  = $range === '7d' ? 7 : 30;
                $end   = Carbon::today();
                $start = $end->copy()->subDays($days - 1);

                $rangeText = $days . ' ngày gần nhất';

                // Tổng user đã tồn tại trước khoảng này
                $beforeStartCount = User::where('created_at', '<', $start->copy()->startOfDay())->count();

                // User tạo trong khoảng
                $users = User::whereBetween(
                    'created_at',
                    [$start->copy()->startOfDay(), $end->copy()->endOfDay()]
                )->get();

                $totalNew = $users->count();

                // group theo ngày
                $perDay = [];
                foreach ($users as $u) {
                    $d = Carbon::parse($u->created_at)->format('Y-m-d');
                    if (!isset($perDay[$d])) $perDay[$d] = 0;
                    $perDay[$d]++;
                }

                $runningTotal = $beforeStartCount;

                for ($i = 0; $i < $days; $i++) {
                    $d   = $start->copy()->addDays($i);
                    $key = $d->format('Y-m-d');

                    $runningTotal += $perDay[$key] ?? 0;

                    $labels[] = $d->format('d/m');
                    $data[]   = $runningTotal;
                }
            } else {
                // ===== THEO THÁNG ===== (6m, 12m)
                $months = $range === '6m' ? 6 : 12;
                $end    = Carbon::now()->startOfMonth();
                $start  = $end->copy()->subMonths($months - 1);

                $rangeText = $months . ' tháng gần nhất';

                $beforeStartCount = User::where('created_at', '<', $start->copy()->startOfMonth())->count();

                $users = User::whereBetween(
                    'created_at',
                    [$start->copy()->startOfMonth(), $end->copy()->endOfMonth()]
                )->get();

                $totalNew = $users->count();

                // group theo tháng
                $perMonth = [];
                foreach ($users as $u) {
                    $m = Carbon::parse($u->created_at)->format('Y-m');
                    if (!isset($perMonth[$m])) $perMonth[$m] = 0;
                    $perMonth[$m]++;
                }

                $runningTotal = $beforeStartCount;

                for ($i = 0; $i < $months; $i++) {
                    $m   = $start->copy()->addMonths($i);
                    $key = $m->format('Y-m');

                    $runningTotal += $perMonth[$key] ?? 0;

                    $labels[] = $m->format('m/Y');
                    $data[]   = $runningTotal;
                }
            }

            return response()->json([
                'labels'      => $labels,
                'data'        => $data,
                'total_new'   => $totalNew,
                'range_label' => $rangeText,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'labels' => [],
                'data'   => [],
                'error'  => $e->getMessage(),
            ], 500);
        }
    }

    public function revenueData(Request $request)
    {
        $range = $request->get('range', '7d'); // 7d, 30d, 6m, 12m

        $query = Order::where('payment_status', 'completed'); // chỉ lấy đơn đã hoàn thành

        $labels = [];
        $values = [];

        if (in_array($range, ['7d', '30d'])) {
            // theo NGÀY
            $days = $range === '7d' ? 7 : 30;
            $startDate = Carbon::today()->subDays($days - 1);

            $rows = $query
                ->whereDate('placed_on', '>=', $startDate)
                ->selectRaw('DATE(placed_on) as d, SUM(total_price) as total')
                ->groupBy('d')
                ->orderBy('d')
                ->get();

            // build map để fill ngày trống = 0
            $map = $rows->pluck('total', 'd');

            for ($i = 0; $i < $days; $i++) {
                $date = $startDate->copy()->addDays($i)->format('Y-m-d');
                $labels[] = Carbon::parse($date)->format('d/m');
                $values[] = (float)($map[$date] ?? 0);
            }
        } else {
            // theo THÁNG (6m, 12m)
            $months = $range === '6m' ? 6 : 12;
            $startMonth = Carbon::now()->startOfMonth()->subMonths($months - 1);

            $rows = $query
                ->whereDate('placed_on', '>=', $startMonth)
                ->selectRaw("DATE_FORMAT(placed_on, '%Y-%m') as ym, SUM(total_price) as total")
                ->groupBy('ym')
                ->orderBy('ym')
                ->get();

            $map = $rows->pluck('total', 'ym');

            for ($i = 0; $i < $months; $i++) {
                $m = $startMonth->copy()->addMonths($i);
                $key = $m->format('Y-m');
                $labels[] = $m->format('m/Y');
                $values[] = (float)($map[$key] ?? 0);
            }
        }

        return response()->json([
            'labels' => $labels,
            'data'   => $values,
        ]);
    }
}
