<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class RevenueStatistics extends Controller
{
    public function index(Request $request)
    {
        /**
         * 1) Subquery: gom tồn kho + đã bán theo product_id từ bảng product_variants
         */
        $pvAgg = DB::table('product_variants')
            ->selectRaw('product_id, SUM(qty_sold) as qty_sold, SUM(inventory) as inventory')
            ->groupBy('product_id');

        /**
         * 2) Base query: join products với pvAgg
         * - qty_sold / inventory giờ lấy từ pv (đã SUM)
         */
        $baseQuery = Product::query()
            ->leftJoinSub($pvAgg, 'pv', function ($join) {
                $join->on('pv.product_id', '=', 'products.id');
            });

        // Search theo tên
        if ($search = $request->input('search')) {
            $baseQuery->where('products.name', 'like', '%' . $search . '%');
        }

        // Filter trạng thái: đã bán > 0 / sắp hết hàng
        if ($status = $request->input('status_filter')) {
            if ($status === 'sold_gt_0') {
                $baseQuery->whereRaw('COALESCE(pv.qty_sold,0) > 0');
            } elseif ($status === 'low_stock') {
                $baseQuery->whereRaw('COALESCE(pv.inventory,0) < 10'); // ngưỡng sắp hết
            }
        }

        /**
         * 3) SUMMARY: tổng lợi nhuận + top sản phẩm
         * profit = revenue - qty_sold * purchase_price
         * (revenue trong bảng products của bạn)
         */
        $salePriceExpr = '(COALESCE(products.price,0) * (1 - COALESCE(products.discount,0)/100.0))';
        $revenueExpr   = '(COALESCE(pv.qty_sold,0) * ' . $salePriceExpr . ')';
        $profitExpr    = '(COALESCE(pv.qty_sold,0) * (' . $salePriceExpr . ' - COALESCE(products.purchase_price,0)))';
        $totalProfit = (clone $baseQuery)
            ->selectRaw("SUM($profitExpr) as total_profit")
            ->value('total_profit') ?? 0;

        $topProduct = (clone $baseQuery)
            ->selectRaw("products.*, COALESCE(pv.qty_sold,0) as qty_sold, COALESCE(pv.inventory,0) as inventory, $salePriceExpr as sale_price, $revenueExpr as revenue_calc, $profitExpr as profit")
            ->orderByRaw("$profitExpr DESC")
            ->first();


        /**
         * 4) LIST + SORT + PAGINATE
         */
        $sortBy = $request->input('sort_by', 'id');
        $order  = $request->input('order', 'asc');
        $order  = strtolower($order) === 'desc' ? 'desc' : 'asc';

        $productsQuery = (clone $baseQuery)
            ->selectRaw("
            products.id,
            products.name,
            products.purchase_price,
            products.price,
            COALESCE(products.discount,0) as discount,             
            COALESCE(products.revenue,0) as revenue,
            COALESCE(pv.qty_sold,0) as qty_sold,
            COALESCE(pv.inventory,0) as inventory,
             $salePriceExpr as sale_price,
            $revenueExpr as revenue_calc,
            $profitExpr as profit
        ");

        // chỉ cho sort các cột hợp lệ
        $allowSort = ['id', 'price', 'purchase_price', 'qty_sold', 'inventory', 'revenue', 'profit'];

        if (!in_array($sortBy, $allowSort, true)) {
            $sortBy = 'id';
        }

        // sort theo alias (profit/qty_sold/inventory đều là alias ở selectRaw)
        $productsQuery->orderBy($sortBy, $order);

        $products = $productsQuery
            ->paginate(10)
            ->appends($request->query());

        return view('admin.revenue_statistics', compact(
            'products',
            'totalProfit',
            'topProduct'
        ));
    }

    public function search(Request $request)
    {
        return $this->index($request);
    }

    public function sort(Request $request)
    {
        return $this->index($request);
    }
}
