<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;  // Nếu bạn có model Product
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        $user_id = Auth::id();

        // Sản phẩm bán chạy (cho block "Sản phẩm nổi bật" / "Gợi ý cho bạn")
        $topProducts = Product::orderBy('qty_sold', 'desc')
            ->limit(6)
            ->get();

        // Lấy tất cả danh mục có sản phẩm để show trên home
        $homeCategories = Category::whereHas('products')
            ->with(['products' => function ($q) {
                $q->orderByDesc('id')->take(10);   // mỗi danh mục lấy 10 sản phẩm mới nhất
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

        $productsQuery = Product::where('category_id', $category->category_id);

        // lọc theo brand (company) nếu có
        $brand = request('brand');
        if (!empty($brand)) {
            $productsQuery->where('company', $brand);
        }

        $products = $productsQuery->paginate(12)->withQueryString();

        // danh sách brand theo category (để render dãy logo)
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
