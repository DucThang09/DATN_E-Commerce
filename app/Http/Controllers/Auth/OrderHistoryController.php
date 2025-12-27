<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Status;
use Illuminate\Support\Facades\DB;

class OrderHistoryController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        // chỉ lấy các status bạn đang dùng (4,5,6)
        $statuses = Status::whereIn('status', ['pending', 'completed', 'canceled'])
            ->orderBy('status_id')
            ->get();

        $active = $request->get('status', 'all'); // 'all' hoặc status_id

        $counts = Order::where('user_id', $userId)
            ->select('status_id', DB::raw('COUNT(*) as c'))
            ->groupBy('status_id')
            ->pluck('c', 'status_id')
            ->toArray();

        $totalCount = array_sum($counts);

        $query = Order::with(['orderStatus', 'items.product', 'items.color'])
            ->where('user_id', $userId)
            ->orderByDesc('created_at');

        if ($active !== 'all') {
            $query->where('status_id', (int) $active);
        }

        $orders = $query->get();


        return view('orders_history', compact('orders', 'statuses', 'active', 'counts', 'totalCount'));
    }

    public function cancel(Request $request, Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        // ✅ nếu đã completed (5) hoặc đã canceled (6) thì không cho huỷ nữa
        if (in_array((int)$order->status_id, [5, 6], true)) {
            return back()->with('error', 'Không thể huỷ đơn ở trạng thái hiện tại.');
        }

        // ✅ set status_id = 6 (canceled)
        $order->status_id = 6;

        // (tuỳ bạn) nếu vẫn muốn đồng bộ payment_status để admin/user thống nhất:
        $order->payment_status = 'canceled';
        
        $order->save();

        return back()->with('success', 'Đã huỷ đơn hàng.');
    }
}
