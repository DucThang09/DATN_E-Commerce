<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
class SearchController extends Controller
{
    public function search()
    {
        return view('search_page'); // view tìm kiếm
    }

    public function searchResults(Request $request)
    {
        $search_box = $request->input('search_box');

    // Kiểm tra nếu có từ khóa tìm kiếm
    if ($search_box) {
        $products = Product::where('name', 'LIKE', "%{$search_box}%")->get();
    } else {
        $products = collect(); // Trả về một tập hợp rỗng nếu không có từ khóa
    }

    return view('search_page', compact('products', 'search_box'));
    }
}
