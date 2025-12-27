<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Color;
use App\Models\Brand;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // subquery lấy variant_id nhỏ nhất theo mỗi product (coi như màu mặc định để hiển thị list)
        $pvPick = DB::table('product_variants')
            ->selectRaw('MIN(id) as variant_id, product_id')
            ->groupBy('product_id');
            $pvAgg = DB::table('product_variants')
    ->selectRaw('product_id, SUM(inventory) as total_inventory, SUM(qty_sold) as total_qty_sold')
    ->groupBy('product_id');


        $query = Product::query()
    ->leftJoinSub($pvPick, 'pvmin', function ($join) {
        $join->on('pvmin.product_id', '=', 'products.id');
    })
    ->leftJoin('product_variants as pv', 'pv.id', '=', 'pvmin.variant_id')
    ->leftJoin('color as c', 'c.colorProduct_id', '=', 'pv.colorProduct_id')

    // ✅ join tổng tồn kho theo product
    ->leftJoinSub($pvAgg, 'pvt', function ($join) {
        $join->on('pvt.product_id', '=', 'products.id');
    })

    ->select([
        'products.*',
        'pv.id as variant_id',
        'pv.colorProduct_id as colorProduct_id',
        'pv.image_01 as v_image_01',
        'pv.image_02 as v_image_02',
        'pv.image_03 as v_image_03',
        'c.colorProduct as v_color',

        // ✅ tổng tồn kho / tổng đã bán (tất cả màu)
        DB::raw('COALESCE(pvt.total_inventory, 0) as total_inventory'),
        DB::raw('COALESCE(pvt.total_qty_sold, 0) as total_qty_sold'),
    ]);



        // search theo tên
        if ($search = $request->input('search')) {
            $query->where('products.name', 'like', "%{$search}%");
        }

        // filter category
        if ($categoryId = $request->input('category_filter')) {
            $query->where('products.category_id', $categoryId);
        }

        // filter trạng thái tồn kho (đổi inventory -> v_inventory)
        if ($status = $request->input('status_filter')) {
    if ($status === 'in_stock') {
        $query->where('pvt.total_inventory', '>', 0);
    } elseif ($status === 'low_stock') {
        $query->whereBetween('pvt.total_inventory', [1, 5]);
    } elseif ($status === 'out_stock') {
        $query->where('pvt.total_inventory', '=', 0);
    }
}


        // filter khoảng giá vẫn theo products.price
        if ($priceFilter = $request->input('price_filter')) {
            if ($priceFilter === 'lt_5m') $query->where('products.price', '<', 5_000_000);
            if ($priceFilter === '5_15m') $query->whereBetween('products.price', [5_000_000, 15_000_000]);
            if ($priceFilter === 'gt_15m') $query->where('products.price', '>', 15_000_000);
        }

        // filter mức tồn kho (đổi inventory -> pv.inventory)
        if ($invFilter = $request->input('inventory_filter')) {
    if ($invFilter === 'lt_5') $query->where('pvt.total_inventory', '<', 5);
    if ($invFilter === '5_20') $query->whereBetween('pvt.total_inventory', [5, 20]);
    if ($invFilter === 'gt_20') $query->where('pvt.total_inventory', '>', 20);
}


        // sort
        $sort = $request->input('sort');
        if ($sort === 'name_asc') $query->orderBy('products.name', 'asc');
        elseif ($sort === 'name_desc') $query->orderBy('products.name', 'desc');
        else $query->orderBy('products.id', 'desc');

        $products = $query->paginate(10)->appends($request->query());
        $categories = Category::all();
        $color = Color::all();
        $brand = Brand::all();

        return view('admin.products', compact('products', 'categories', 'color', 'brand'));
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:100|unique:products,name',
            'price'          => 'required|numeric|min:0|max:9999999999',
            'purchase_price' => 'required|numeric|min:0|max:9999999999',
            'details'        => 'required|string|max:500',
            'category_id'    => 'required|exists:categories,category_id',
            'company'        => 'required|string|max:100',

            // ✅ đổi sang id
            'colorProduct_id' => 'required|exists:color,colorProduct_id',

            // ✅ tồn kho theo variant
            'inventory'      => 'required|integer|min:0',

            'discount'       => 'nullable|integer|min:0|max:100',
            'image_01'       => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_02'       => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_03'       => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'spec_label'     => 'nullable|array',
            'spec_label.*'   => 'nullable|string|max:100',
            'spec_value'     => 'nullable|array',
            'spec_value.*'   => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // upload ảnh (ảnh này là ảnh theo màu - variant)
        $images = [];
        for ($i = 1; $i <= 3; $i++) {
            $images[] = $request->file("image_0{$i}")->store('uploaded_img', 'public');
        }

        $catName = Category::where('category_id', $request->category_id)->value('category_name');

        // build specs JSON như bạn đang làm
        $labels = $request->input('spec_label', []);
        $values = $request->input('spec_value', []);
        $specs = [];
        $max = max(count($labels), count($values));
        for ($i = 0; $i < $max; $i++) {
            $k = trim((string)($labels[$i] ?? ''));
            $v = trim((string)($values[$i] ?? ''));
            if ($k === '' || $v === '') continue;
            $k = rtrim($k, ':') . ':';
            $specs[] = ['label' => $k, 'value' => $v];
        }

        DB::transaction(function () use ($request, $catName, $specs, $images) {

            // ✅ 1) tạo product (chỉ info chung)
            $product = Product::create([
                'name'           => $request->name,
                'details'        => $request->details,
                'price'          => $request->price,
                'purchase_price' => $request->purchase_price,
                'category'       => $catName,
                'category_id'    => $request->category_id,
                'company'        => $request->company,
                'discount'       => $request->filled('discount') ? (int)$request->discount : 0,
                'revenue'        => 0,
                'specs'          => $specs,
            ]);

            // ✅ 2) tạo variant màu đầu tiên
            DB::table('product_variants')->insert([
                'product_id'      => $product->id,
                'colorProduct_id' => (int)$request->colorProduct_id,
                'inventory'       => (int)$request->inventory,
                'qty_sold'        => 0,
                'image_01'        => $images[0],
                'image_02'        => $images[1],
                'image_03'        => $images[2],
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        });

        return redirect()->route('admin.products')->with('message', 'Sản phẩm mới đã được thêm!');
    }

    public function destroy($id)
    {
        // xóa ảnh của tất cả variants
        $variants = DB::table('product_variants')->where('product_id', $id)->get();
        foreach ($variants as $v) {
            foreach (['image_01', 'image_02', 'image_03'] as $f) {
                if (!empty($v->$f) && Storage::disk('public')->exists($v->$f)) {
                    Storage::disk('public')->delete($v->$f);
                }
            }
        }

        // xóa product (variant sẽ tự xóa nhờ ON DELETE CASCADE)
        Product::where('id', $id)->delete();

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

        // 1) Lấy tất cả variants của các product được chọn
        $variants = DB::table('product_variants')
            ->whereIn('product_id', $ids)
            ->get();

        // 2) Xóa ảnh trong storage theo variants
        foreach ($variants as $v) {
            foreach (['image_01', 'image_02', 'image_03'] as $field) {
                if (!empty($v->$field) && Storage::disk('public')->exists($v->$field)) {
                    Storage::disk('public')->delete($v->$field);
                }
            }
        }

        // 3) Xóa products (variants sẽ tự xóa nhờ FK ON DELETE CASCADE)
        Product::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', 'Đã xóa các sản phẩm được chọn.');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name'            => 'required|string|max:100',
            'price'           => 'required|numeric|min:0|max:9999999999',
            'purchase_price'  => 'required|numeric|min:0|max:9999999999',
            'details'         => 'required|string|max:500',
            'category_id'     => 'required|exists:categories,category_id',
            'company'         => 'required|string|max:100',

            // ✅ variant fields
            'variant_id'      => 'required|integer',
            'colorProduct_id' => 'required|exists:color,colorProduct_id',
            'inventory'       => 'required|integer|min:0',

            'discount'        => 'nullable|integer|min:0|max:100',
            'spec_label'      => 'nullable|array',
            'spec_label.*'    => 'nullable|string|max:100',
            'spec_value'      => 'nullable|array',
            'spec_value.*'    => 'nullable|string|max:255',

            'image_01'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_02'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'image_03'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // build specs json (giữ như bạn)
        $labels = $request->input('spec_label', []);
        $values = $request->input('spec_value', []);

        $specs = [];
        $max = max(count($labels), count($values));
        for ($i = 0; $i < $max; $i++) {
            $k = trim((string)($labels[$i] ?? ''));
            $v = trim((string)($values[$i] ?? ''));
            if ($k === '' || $v === '') continue;

            $k = rtrim($k, ':') . ':';
            $specs[] = ['label' => $k, 'value' => $v];
        }

        DB::transaction(function () use ($request, $product, $specs) {

            // 1) Update bảng products (info chung)
            $product->update([
                'name'           => $request->name,
                'price'          => $request->price,
                'purchase_price' => $request->purchase_price,
                'details'        => $request->details,
                'category_id'    => $request->category_id,
                'category'       => Category::where('category_id', $request->category_id)->value('category_name'),
                'company'        => $request->company,
                'discount'       => $request->filled('discount') ? (int)$request->discount : 0,
                'specs'          => $specs,
            ]);

            // 2) Lấy variant cần update (đúng product)
            $variant = DB::table('product_variants')
                ->where('id', (int)$request->variant_id)
                ->where('product_id', $product->id)
                ->first();

            if (!$variant) {
                // ném exception để rollback transaction
                throw new \Exception('Không tìm thấy biến thể để cập nhật.');
            }

            // 3) Update variant fields
            $vData = [
                'colorProduct_id' => (int)$request->colorProduct_id,
                'inventory'       => (int)$request->inventory,
                'updated_at'      => now(),
            ];

            // 4) Ảnh theo variant
            for ($i = 1; $i <= 3; $i++) {
                $field = "image_0{$i}";

                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store('uploaded_img', 'public');
                    $vData[$field] = $path;

                    // xóa ảnh cũ của variant
                    $old = $variant->$field ?? null;
                    if (!empty($old) && Storage::disk('public')->exists($old)) {
                        Storage::disk('public')->delete($old);
                    }
                }
            }

            DB::table('product_variants')->where('id', $variant->id)->update($vData);
        });

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
        $products = $query->paginate(10)->appends($request->query());

        // Truyền thêm dữ liệu danh mục, màu sắc, thương hiệu
        $categories = Category::all();
        $color = Color::all();
        $brand = Brand::all();

        // Trả về view với dữ liệu
        return view('admin.products', compact('products', 'categories', 'color', 'brand'));
    }
}
