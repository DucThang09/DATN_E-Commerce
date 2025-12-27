<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;  // Nếu bạn có model Product
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $user_id = Auth::id();

        // subquery: chọn variant_id nhỏ nhất (hoặc lớn nhất) theo mỗi product để làm "variant mặc định"
        $pvPick = DB::table('product_variants')
            ->selectRaw('MIN(id) as variant_id, product_id')
            ->groupBy('product_id');

        $topProducts = Product::query()
            // join để lấy variant mặc định
            ->leftJoinSub($pvPick, 'pvpick', function ($join) {
                $join->on('pvpick.product_id', '=', 'products.id');
            })
            ->leftJoin('product_variants as pv', 'pv.id', '=', 'pvpick.variant_id')
            ->leftJoin('color as c', 'c.colorProduct_id', '=', 'pv.colorProduct_id')

            // join thêm pv2 để tính tổng đã bán (không ảnh hưởng variant mặc định)
            ->leftJoin('product_variants as pv2', 'pv2.product_id', '=', 'products.id')

            ->select([
                'products.*',
                'pv.id as variant_id',
                'pv.colorProduct_id as colorProduct_id',
                'pv.inventory as v_inventory',
                'pv.qty_sold as v_qty_sold',
                'pv.image_01 as v_image_01',
                'pv.image_02 as v_image_02',
                'pv.image_03 as v_image_03',
                'c.colorProduct as v_color',
                DB::raw('COALESCE(SUM(pv2.qty_sold),0) as total_sold'),
            ])
            ->groupBy(
                'products.id',
                'pv.id',
                'pv.colorProduct_id',
                'pv.inventory',
                'pv.qty_sold',
                'pv.image_01',
                'pv.image_02',
                'pv.image_03',
                'c.colorProduct'
            )
            ->orderByDesc('total_sold')
            ->limit(11)
            ->get();


        $pvPick2 = DB::table('product_variants')
            ->selectRaw('MIN(id) as variant_id, product_id')
            ->groupBy('product_id');

        $homeCategories = Category::whereHas('products')
            ->with(['products' => function ($q) use ($pvPick2) {
                $q->leftJoinSub($pvPick2, 'pvpick', function ($join) {
                    $join->on('pvpick.product_id', '=', 'products.id');
                })
                    ->leftJoin('product_variants as pv', 'pv.id', '=', 'pvpick.variant_id')
                    ->leftJoin('color as c', 'c.colorProduct_id', '=', 'pv.colorProduct_id')
                    ->select([
                        'products.*',
                        'pv.id as variant_id',
                        'pv.colorProduct_id as colorProduct_id',
                        'pv.inventory as v_inventory',
                        'pv.qty_sold as v_qty_sold',
                        'pv.image_01 as v_image_01',
                        'pv.image_02 as v_image_02',
                        'pv.image_03 as v_image_03',
                        'c.colorProduct as v_color',
                    ])
                    ->orderByDesc('products.id')
                    ->take(8);
            }])
            ->get();

        return view('home', [
            'user_id'        => $user_id,
            'topProducts'    => $topProducts,
            'homeCategories' => $homeCategories,
        ]);
    }


    public function showCompanyProducts($company)
    {
        // Truy xuất sản phẩm dựa trên cột 'company' trong cơ sở dữ liệu
        $products = Product::where('company', $company)->get();

        // Truyền danh sách sản phẩm và tên công ty vào view
        return view('company', compact('products', 'company'));
    }

    public function showByCategory($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        // lấy variant mặc định cho mỗi product
        $pvPick = DB::table('product_variants')
            ->selectRaw('MIN(id) as variant_id, product_id')
            ->groupBy('product_id');

        $productsQuery = Product::query()
            ->where('products.category_id', $category->category_id)
            ->leftJoinSub($pvPick, 'pvpick', function ($join) {
                $join->on('pvpick.product_id', '=', 'products.id');
            })
            ->leftJoin('product_variants as pv', 'pv.id', '=', 'pvpick.variant_id')
            ->leftJoin('color as c', 'c.colorProduct_id', '=', 'pv.colorProduct_id')
            ->select([
                'products.*',

                // ✅ alias về đúng tên field cũ để view không phải sửa hàng loạt
                'pv.image_01 as image_01',
                'pv.image_02 as image_02',
                'pv.image_03 as image_03',
                'pv.inventory as inventory',
                'pv.qty_sold as qty_sold',
                'c.colorProduct as color',
                'pv.id as variant_id',
                'pv.colorProduct_id as colorProduct_id',
            ]);

        // lọc theo brand (company) nếu có
        $brand = request('brand');
        if (!empty($brand)) {
            $productsQuery->where('products.company', $brand);
        }

        $products = $productsQuery->paginate(12)->withQueryString();

        $brands = Product::where('category_id', $category->category_id)
            ->whereNotNull('company')
            ->where('company', '!=', '')
            ->select('company')
            ->distinct()
            ->orderBy('company')
            ->pluck('company');

        return view('category', compact('category', 'products', 'brands'));
    }
}
