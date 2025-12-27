<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OrderStatisticsController extends Controller
{
    public function index(Request $request)
    {
        $monthYear = $request->input('month_year', now()->format('m/Y'));
        [$month, $year] = explode('/', $monthYear);

        $baseQuery = Order::whereYear('placed_on', $year)
            ->whereMonth('placed_on', $month);

        if ($status = $request->input('payment_status')) {
            $baseQuery->where('payment_status', $status);
        }

        if ($method = $request->input('method')) {
            $baseQuery->where('method', $method);
        }

        $totalOrders = (clone $baseQuery)->count();

        $revenueQuery = (clone $baseQuery)->where('payment_status', 'completed');
        $totalRevenue = $revenueQuery->sum('total_price');

        // Đếm đơn theo trạng thái
        $statusCounts = (clone $baseQuery)
            ->select('payment_status', DB::raw('COUNT(*) AS total'))
            ->groupBy('payment_status')
            ->pluck('total', 'payment_status');   // ['completed' => 10, 'pending' => 3, ...]

        // Giá trị đơn trung bình
        $avgOrderValue = $totalOrders > 0
            ? (int) round($totalRevenue / $totalOrders)
            : 0;

        // ===== 4. SORT + PHÂN TRANG BẢNG ĐƠN HÀNG =====
        $sortBy  = $request->input('sort_by', 'id');
        $orderBy = $request->input('order', 'asc');

        // Chỉ cho phép sort theo vài cột an toàn
        $allowedSort = ['id', 'total_price', 'placed_on'];
        if (!in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'id';
        }

        $orders = (clone $baseQuery)
            ->orderBy($sortBy, $orderBy)
            ->paginate(10)
            ->appends($request->query());

        // ===== 5. TOP 5 SẢN PHẨM DOANH THU CAO TRONG THÁNG =====
        $topProducts = OrderItem::select(
            'product_name',
            DB::raw('SUM(quantity) AS qty_sold'),
            DB::raw('SUM(total_price) AS revenue')
        )
            ->whereHas('order', function ($q) use ($year, $month) {
                $q->whereYear('placed_on', $year)
                    ->whereMonth('placed_on', $month)
                    ->where('payment_status', 'completed');
            })
            ->groupBy('product_name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        return view('admin.revenue_month', compact(
            'orders',
            'totalRevenue',
            'totalOrders',
            'statusCounts',
            'avgOrderValue',
            'topProducts',
            'monthYear'
        ));
    }

    // Route sort cũ có thể trỏ về đây luôn cho đỡ phải viết lại logic
    public function sort(Request $request)
    {
        return $this->index($request);
    }

    // (tuỳ chọn) Export CSV để mở bằng Excel
    public function exportMonth(Request $request)
    {
        $monthYear = $request->input('month_year', now()->format('m/Y'));
        [$month, $year] = explode('/', $monthYear);

        $query = Order::whereYear('placed_on', $year)
            ->whereMonth('placed_on', $month);

        if ($status = $request->input('payment_status')) {
            $query->where('payment_status', $status);
        }

        if ($method = $request->input('method')) {
            $query->where('method', $method);
        }

        $orders = $query->orderBy('placed_on', 'asc')->get();

        // ===== TẠO FILE EXCEL =====
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        // ==== SỬA CHỖ ĐẶT TÊN SHEET (LOẠI BỎ / : ? * [ ] \) ====
        $rawTitle   = 'Doanh thu ' . $monthYear;              // ví dụ: "Doanh thu 11/2025"
        $sheetTitle = preg_replace('/[\\\\\\/\\?\\*\\[\\]:]/u', '-', $rawTitle); // "Doanh thu 11-2025"
        $sheetTitle = substr($sheetTitle, 0, 31);             // Excel chỉ cho tối đa 31 ký tự

        $sheet->setTitle($sheetTitle);
        // =======================================================

        // --- 1. TIÊU ĐỀ LỚN TRÊN CÙNG ---
        $title = "BÁO CÁO ĐƠN HÀNG THÁNG {$month}/{$year}";
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // --- 2. DÒNG TÓM TẮT NHỎ ---
        $totalRevenue = $orders->sum('total_price');
        $sheet->setCellValue('A2', 'Tổng số đơn: ' . $orders->count());
        $sheet->setCellValue('D2', 'Tổng tiền đã bán:');
        $sheet->setCellValue('E2', $totalRevenue);

        $sheet->getStyle('A2:E2')->getFont()->setSize(11);
        $sheet->getStyle('E2')->getNumberFormat()
            ->setFormatCode('#,##0 "đ"');

        // --- 3. HEADER BẢNG (DÒNG 4) ---
        $headerRow = 4;
        $sheet->fromArray(
            ['ID', 'Phương thức', 'Sản phẩm', 'Tổng tiền', 'Ngày bán', 'Trạng thái'],
            null,
            'A' . $headerRow
        );

        $sheet->getStyle("A{$headerRow}:F{$headerRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'ffffff'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'fb7185'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // --- 4. DATA ROWS ---
        $row = $headerRow + 1;

        foreach ($orders as $order) {
            $sheet->setCellValue("A{$row}", $order->id);
            $sheet->setCellValue("B{$row}", $order->method);
            $sheet->setCellValue("C{$row}", $order->total_products);
            $sheet->setCellValue("D{$row}", $order->total_price);
            $sheet->setCellValue("E{$row}", optional($order->placed_on)->format('d/m/Y'));
            $sheet->setCellValue("F{$row}", $order->payment_status);
            $row++;
        }

        $lastRow = $row - 1;

        // Định dạng tiền cột D
        $sheet->getStyle("D" . ($headerRow + 1) . ":D{$lastRow}")
            ->getNumberFormat()
            ->setFormatCode('#,##0 "đ"');

        // Wrap text cho cột sản phẩm
        $sheet->getStyle("C" . ($headerRow + 1) . ":C{$lastRow}")
            ->getAlignment()->setWrapText(true);

        // --- 5. KẺ VIỀN BẢNG ---
        $sheet->getStyle("A{$headerRow}:F{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'e5e7eb'],
                ],
            ],
        ]);

        // --- 6. AUTO WIDTH + FREEZE HEADER ---
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->freezePane('A' . ($headerRow + 1));       // giữ header
        $sheet->setAutoFilter("A{$headerRow}:F{$headerRow}");

        // ===== TRẢ FILE VỀ TRÌNH DUYỆT =====
        $fileName = "Bao_cao_don_hang_{$month}_{$year}.xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
