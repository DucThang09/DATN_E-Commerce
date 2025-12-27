<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class Category_ManageController extends Controller
{
    public function index()
    {
        $categories = Category::query()
            ->withCount(['products as product_count']) // ✅ số sản phẩm theo từng danh mục
            ->orderByDesc('category_id')
            ->get();

        $brands = Brand::all();
        $color  = Color::all();

        $totalProducts = Product::count();

        return view('admin.category_manage', compact(
            'categories',
            'brands',
            'color',
            'totalProducts'
        ));
    }

    // ================== CATEGORY ==================
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        Category::create([
            'category_name' => $request->name,
        ]);

        return redirect()->route('admin.category_manage')
            ->with('success', 'Danh mục được thêm thành công!');
    }

    public function edit($category_id)
    {
        $category = Category::where('category_id', $category_id)->firstOrFail();
        return view('admin.category_edit', compact('category'));
    }

    public function update(Request $request, $category_id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $category = Category::where('category_id', $category_id)->firstOrFail();
        $category->category_name = $request->name;
        $category->save();

        $tab = $request->input('tab', 'category');
        return redirect()->route('admin.category_manage', ['tab' => $tab])
            ->with('success', 'Cập nhật danh mục thành công!');
    }

    public function delete($category_id)
    {
        $category = Category::where('category_id', $category_id)->firstOrFail();
        $category->delete();

        return redirect()->route('admin.category_manage')
            ->with('success', 'Danh mục đã được xóa!');
    }

    // ================== BRAND ==================
    public function store_brand(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        Brand::create([
            'brand_name' => $request->name,
        ]);

        return redirect()->route('admin.category_manage')
            ->with('success', 'Thương hiệu được thêm thành công!');
    }

    public function edit_brand($brand_id)
    {
        $brand = Brand::where('brand_id', $brand_id)->firstOrFail();
        return view('admin.brand_edit', compact('brand'));
    }

    public function update_brand(Request $request, $brand_id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $brand = Brand::where('brand_id', $brand_id)->firstOrFail();
        $brand->brand_name = $request->name;
        $brand->save();

        $tab = $request->input('tab', 'brand');
        return redirect()->route('admin.category_manage', ['tab' => $tab])
            ->with('success', 'Cập nhật thương hiệu thành công!');
    }

    public function delete_brand($brand_id)
    {
        $brandName = Brand::where('brand_id', $brand_id)->value('brand_name');

        $used = Product::where('company', $brandName)->exists();
        if ($used) {
            return redirect()->route('admin.category_manage')
                ->with('error', 'Thương hiệu này đang được dùng trong sản phẩm, không thể xóa.');
        }

        $brand = Brand::where('brand_id', $brand_id)->firstOrFail();
        $brand->delete();

        return redirect()->route('admin.category_manage')
            ->with('success', 'Thương hiệu đã được xóa!');
    }

    // ================== COLOR ==================
    public function store_color(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        Color::create([
            'colorProduct' => $request->name,
        ]);

        return redirect()->route('admin.category_manage')
            ->with('success', 'Màu sắc được thêm thành công!');
    }

    public function edit_color($color_id)
    {
        $color = Color::where('colorProduct_id', $color_id)->firstOrFail();
        return view('admin.color_edit', compact('color'));
    }

    public function update_color(Request $request, $color_id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $color = Color::where('colorProduct_id', $color_id)->firstOrFail();
        $color->colorProduct = $request->name;
        $color->save();

        $tab = $request->input('tab', 'color');
        return redirect()->route('admin.category_manage', ['tab' => $tab])
            ->with('success', 'Cập nhật màu sắc thành công!');
    }

    public function delete_color($color_id)
    {
        $used = DB::table('product_variants')
            ->where('colorProduct_id', $color_id)
            ->exists();

        if ($used) {
            return redirect()->route('admin.category_manage')
                ->with('error', 'Màu này đang được dùng trong sản phẩm, không thể xóa.');
        }

        $color = Color::where('colorProduct_id', $color_id)->firstOrFail();
        $color->delete();

        return redirect()->route('admin.category_manage')
            ->with('success', 'Màu sắc đã được xóa!');
    }
}
