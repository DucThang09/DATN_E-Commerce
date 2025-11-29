<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Color;
use App\Models\Brand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // Tìm kiếm theo tên
        if ($search = $request->input('search')) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Lọc theo danh mục
        if ($category = $request->input('category_filter')) {
            $query->where('category', $category);
        }

        // Lọc theo trạng thái tồn kho
        if ($status = $request->input('status_filter')) {
            if ($status === 'in_stock') {
                $query->where('inventory', '>', 0);
            } elseif ($status === 'low_stock') {
                // ví dụ: sắp hết = 1–5 sp
                $query->whereBetween('inventory', [1, 5]);
            } elseif ($status === 'out_stock') {
                $query->where('inventory', '=', 0);
            }
        }

        // Lọc theo khoảng giá
        if ($priceFilter = $request->input('price_filter')) {
            switch ($priceFilter) {
                case 'lt_5m':
                    $query->where('price', '<', 5_000_000);
                    break;
                case '5_15m':
                    $query->whereBetween('price', [5_000_000, 15_000_000]);
                    break;
                case 'gt_15m':
                    $query->where('price', '>', 15_000_000);
                    break;
            }
        }

        // Lọc theo mức tồn kho
        if ($invFilter = $request->input('inventory_filter')) {
            switch ($invFilter) {
                case 'lt_5':
                    $query->where('inventory', '<', 5);
                    break;
                case '5_20':
                    $query->whereBetween('inventory', [5, 20]);
                    break;
                case 'gt_20':
                    $query->where('inventory', '>', 20);
                    break;
            }
        }
        $sort = $request->input('sort');
        if ($sort === 'name_asc') {
            $query->orderBy('name', 'asc');      // tên A → Z
        } elseif ($sort === 'name_desc') {
            $query->orderBy('name', 'desc');     // tên Z → A
        } else {
            $query->orderBy('id', 'desc');       // mặc định: mới nhất
        }

        // Phân trang + giữ query string khi chuyển trang
        $products = $query->paginate(10)->appends($request->query());
        $categories = Category::all();
        $color      = Color::all();
        $brand      = Brand::all();

        return view('admin.products', compact('products', 'categories', 'color', 'brand'));
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:100|unique:products,name',
            'price'          => 'required|numeric|min:0|max:9999999999',
            'purchase_price' => 'required|numeric|min:0|max:9999999999',
            // 'qty_sold'    => 'nullable|numeric|min:0',  // ❌ bỏ, không cho nhập
            'details'        => 'required|string|max:500',
            'category'       => 'required|string|max:100',
            'company'        => 'required|string|max:100',
            'color'          => 'required|string|max:100',
            'inventory'      => 'required|integer|min:0',
            'discount'       => 'nullable|integer|min:0|max:100',
            'image_01'       => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_02'       => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_03'       => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $images = [];
        for ($i = 1; $i <= 3; $i++) {
            $image = $request->file("image_0{$i}");
            $images[] = $image->store('uploaded_img', 'public');
        }

        Product::create([
            'name'           => $request->name,
            'details'        => $request->details,
            'price'          => $request->price,
            'purchase_price' => $request->purchase_price,
            'qty_sold'       => 0,                      // ✅ luôn khởi tạo = 0
            'category'       => $request->category,
            'company'        => $request->company,
            'color'          => $request->color,
            'inventory'      => $request->inventory,
            'discount'       => $request->discount ?? 0,
            'revenue'        => 0,
            'image_01'       => $images[0],
            'image_02'       => $images[1],
            'image_03'       => $images[2],
        ]);

        return redirect()->route('admin.products')
            ->with('message', 'Sản phẩm mới đã được thêm!');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        Storage::disk('public')->delete($product->image_01);
        Storage::disk('public')->delete($product->image_02);
        Storage::disk('public')->delete($product->image_03);

        $product->delete();

        return redirect()->route('admin.products')->with('success', 'Xóa sản phẩm thành công!');
    }
    public function deleteSelected(Request $request)
    {
        $ids = $request->input('selected_products', []);

        if (empty($ids)) {
            return redirect()
                ->route('admin.products')
                ->with('error', 'Bạn chưa chọn sản phẩm nào.');
        }

        // Lấy các sản phẩm cần xóa
        $products = Product::whereIn('id', $ids)->get();

        // Xóa ảnh trong storage (nếu đang lưu theo kiểu storage/public)
        foreach ($products as $product) {
            foreach (['image_01', 'image_02', 'image_03'] as $field) {
                if (!empty($product->$field) && Storage::disk('public')->exists($product->$field)) {
                    Storage::disk('public')->delete($product->$field);
                }
            }
        }

        // Xóa record trong DB
        Product::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', 'Đã xóa các sản phẩm được chọn.');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name'           => 'required|string|max:100',
            'price'          => 'required|numeric|min:0|max:9999999999',
            'purchase_price' => 'required|numeric|min:0|max:9999999999',
            // 'qty_sold'    => 'nullable|integer|min:0',   // ❌ bỏ, không cho sửa
            'details'        => 'required|string|max:500',
            'category'       => 'required|string|max:100',
            'company'        => 'required|string|max:100',
            'color'          => 'required|string|max:100',
            'inventory'      => 'required|integer|min:0',
            'discount'       => 'nullable|integer|min:0|max:100',
            'image_01'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_02'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_03'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Các field text / number (KHÔNG include qty_sold)
        $data = [
            'name'           => $request->name,
            'price'          => $request->price,
            'purchase_price' => $request->purchase_price,
            // 'qty_sold'    => $request->qty_sold ?? 0,   // ❌ bỏ
            'details'        => $request->details,
            'category'       => $request->category,
            'company'        => $request->company,
            'color'          => $request->color,
            'inventory'      => $request->inventory,
            'discount'       => $request->discount ?? 0,
        ];

        // Ảnh
        for ($i = 1; $i <= 3; $i++) {
            $field = "image_0{$i}";

            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('uploaded_img', 'public');
                $data[$field] = $path;

                if (!empty($product->$field) && Storage::disk('public')->exists($product->$field)) {
                    Storage::disk('public')->delete($product->$field);
                }
            }
        }

        $product->update($data);

        return redirect()->back()->with('success', 'Sản phẩm đã được cập nhật!');
    }


    public function product_search(Request $request)
    {
        $query = Product::query();

        // Tìm kiếm theo tên sản phẩm
        if ($request->has('search') && $request->input('search') != '') {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        // Lấy kết quả
        $products = $query->get();

        // Truyền thêm dữ liệu danh mục, màu sắc, thương hiệu
        $categories = Category::all();
        $color = Color::all();
        $brand = Brand::all();

        // Trả về view với dữ liệu
        return view('admin.products', compact('products', 'categories', 'color', 'brand'));
    }
}
