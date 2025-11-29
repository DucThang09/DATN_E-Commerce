<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class Category_ManageController extends Controller
{

    public function index()
    {
        $categories = Category::all();
        $brands     = Brand::all();
        $color      = Color::all();

        // 👉 Tổng số sản phẩm của TẤT CẢ danh mục
        $totalProducts = Product::count();

        return view('admin.category_manage', compact(
            'categories',
            'brands',
            'color',
            'totalProducts'
        ));
    }

    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        // Tạo mới danh mục
        Category::create([
            'category_name' => $request->name,
        ]);

        // Chuyển hướng về trang quản lý với thông báo thành công
        return redirect()->route('admin.category_manage')->with('success', 'Danh mục được thêm thành công!');
    }

    public function edit($id)
    {
        $categories = Category::findOrFail($id);
        return view('admin.category_edit', compact('category'));
    }

    public function delete($category_id)
    {
        $categories = Category::where('category_id', $category_id)->firstOrFail();
        $categories->delete();

        return redirect()->route('admin.category_manage')->with('success', 'Danh mục đã được xóa!');
    }


    public function store_brand(Request $request)
    {
        // Validate input
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        // Tạo mới danh mục
        Brand::create([
            'brand_name' => $request->name,
        ]);

        // Chuyển hướng về trang quản lý với thông báo thành công
        return redirect()->route('admin.category_manage')->with('success', 'Danh mục được thêm thành công!');
    }

    public function delete_brand($brand_id)
    {
        $brand = Brand::where('brand_id', $brand_id)->firstOrFail();
        $brand->delete();

        return redirect()->route('admin.category_manage')->with('success', 'Thương hiệu đã được xóa!');
    }


    public function store_color(Request $request)
    {
        // Validate input
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        // Tạo mới danh mục
        Color::create([
            'colorProduct' => $request->name,
        ]);

        // Chuyển hướng về trang quản lý với thông báo thành công
        return redirect()->route('admin.category_manage')->with('success', 'Danh mục được thêm thành công!');
    }

    public function delete_color($color_id)
    {
        $color = Color::where('colorProduct_id', $color_id)->firstOrFail();
        $color->delete();

        return redirect()->route('admin.category_manage')->with('success', 'Thương hiệu đã được xóa!');
    }
}
