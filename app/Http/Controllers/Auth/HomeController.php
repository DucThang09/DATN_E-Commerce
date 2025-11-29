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
        // $products = Product::latest()->limit(6)->get();
        $products = Product::orderBy('qty_sold', 'desc')->limit(6)->get();
        $phones = Product::where('category', 'Điện Thoại')->get();
        $headphones = Product::where('category', 'Tai nghe')->get();
        $powerbank = Product::where('category', 'Sạc dự phòng')->get();
        $chargers = Product::where('category', 'Sạc')->get();
        $category = Category::all();

        return view('home', compact('user_id', 'products', 'phones', 'headphones','powerbank', 'chargers','category'));
    }
    

    public function showCompanyProducts($company)
    {
        // Truy xuất sản phẩm dựa trên cột 'company' trong cơ sở dữ liệu
        $products = Product::where('company', $company)->get();

        // Truyền danh sách sản phẩm và tên công ty vào view
        return view('company', compact('products', 'company'));
    }

    public function showByCategory($category)
    {
        $products = Product::where('category', $category)->get();  // Giả sử bạn có trường 'category' trong bảng sản phẩm
        return view('category', compact('products', 'category'));
    }
       
    public function showCategory($categories)
    {
        $categories = Category::all();
        return view('home', compact('categories'));
    }

}
