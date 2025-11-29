<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;   // 👈 THÊM DÒNG NÀY
use App\Models\Product;

class RevenueStatistics extends Controller
{
    public function index(Request $request)
    {
        // ====== LỌC CHUNG (search + trạng thái) ======
        $baseQuery = Product::query();

        // Search theo tên sản phẩm
        if ($search = $request->input('search')) {
            $baseQuery->where('name', 'like', '%' . $search . '%');
        }

        // Filter trạng thái: đã bán > 0 / sắp hết hàng
        if ($status = $request->input('status_filter')) {
            if ($status === 'sold_gt_0') {
                $baseQuery->where('qty_sold', '>', 0);
            } elseif ($status === 'low_stock') {
                $baseQuery->where('inventory', '<', 10); // ngưỡng sắp hết
            }
        }

        // ====== SUMMARY: tổng lợi nhuận + top sản phẩm ======
        $totalProfit = (clone $baseQuery)
            ->select(DB::raw('SUM(revenue - qty_sold * purchase_price) AS total_profit'))
            ->value('total_profit') ?? 0;

        $topProduct = (clone $baseQuery)
            ->select('*', DB::raw('(revenue - qty_sold * purchase_price) AS profit'))
            ->orderByDesc(DB::raw('revenue - qty_sold * purchase_price'))
            ->first();

        // ====== DANH SÁCH CHI TIẾT + SORT ======
        $sortBy = $request->input('sort_by', 'id');
        $order  = $request->input('order', 'asc');

        $productsQuery = (clone $baseQuery)
            ->select(
                'id',
                'name',
                'purchase_price',
                'price',
                'qty_sold',
                'inventory',
                'revenue'
            )
            ->selectRaw('(revenue - qty_sold * purchase_price) AS profit'); // 👈 alias profit

        $allowSort = ['id', 'price', 'purchase_price', 'qty_sold', 'inventory', 'revenue', 'profit'];

        if (in_array($sortBy, $allowSort, true)) {
            if ($sortBy === 'profit') {
                $productsQuery->orderBy('profit', $order);
            } else {
                $productsQuery->orderBy($sortBy, $order);
            }
        } else {
            $productsQuery->orderBy('id', 'asc');
        }

        $products = $productsQuery
            ->paginate(10)
            ->appends($request->query());

        return view('admin.revenue_statistics', compact(
            'products',
            'totalProfit',
            'topProduct'
        ));
    }

    // Cho route search / sort cũ dùng chung logic
    public function search(Request $request)
    {
        return $this->index($request);
    }

    public function sort(Request $request)
    {
        return $this->index($request);
    }
}
