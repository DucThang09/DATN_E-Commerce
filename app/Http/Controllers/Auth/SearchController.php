<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $search  = trim((string) $request->query('search', ''));
        $perPage = 25;

        if ($search === '') {
            return view('search_page', [
                'products' => collect(),
                'search'   => '',
            ]);
        }

        // Nếu keyword match hãng -> chỉ lọc theo company
        $companyExists = Product::query()
            ->where('company', 'LIKE', "%{$search}%")
            ->exists();

        $query = Product::query();

        if ($companyExists) {
            $query->where('company', 'LIKE', "%{$search}%");
        } else {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('details', 'LIKE', "%{$search}%")
                    ->orWhere('company', 'LIKE', "%{$search}%")
                    ->orWhereHas('categoryModel', fn($cq) => $cq->where('name', 'LIKE', "%{$search}%"));
            });
        }

        /**
         * ✅ Lấy variant đại diện (id nhỏ nhất) để có ảnh + tồn kho
         * - v_image_01: ảnh đại diện
         * - variant_id: id variant đại diện
         * - v_inventory: tồn kho variant đại diện
         */
        $query->addSelect([
            'variant_id' => DB::table('product_variants as pv')
                ->select('pv.id')
                ->whereColumn('pv.product_id', 'products.id')
                ->orderBy('pv.id', 'asc')
                ->limit(1),

            'v_image_01' => DB::table('product_variants as pv')
                ->select('pv.image_01')
                ->whereColumn('pv.product_id', 'products.id')
                ->orderBy('pv.id', 'asc')
                ->limit(1),

            'v_inventory' => DB::table('product_variants as pv')
                ->select('pv.inventory')
                ->whereColumn('pv.product_id', 'products.id')
                ->orderBy('pv.id', 'asc')
                ->limit(1),
        ]);

        $products = $query
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends(['search' => $search]);

        // AJAX load-more
        if ($request->ajax()) {
            $remaining = max(0, $products->total() - ($products->currentPage() * $products->perPage()));

            return response()->json([
                'html'      => view('partials.search_products', compact('products'))->render(),
                'has_more'  => $products->hasMorePages(),
                'next_page' => $products->currentPage() + 1,
                'remaining' => $remaining,
            ]);
        }

        return view('search_page', [
            'products' => $products,
            'search'   => $search,
        ]);
    }
}
