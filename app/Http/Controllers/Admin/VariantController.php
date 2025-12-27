<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VariantController extends Controller
{
    /**
     * Trả danh sách màu (variants) theo sản phẩm (phục vụ list bên phải).
     * Route gợi ý: GET admin/products/{product}/variants
     */
    public function index(Request $request, int $productId)
    {
        $rows = DB::table('product_variants as pv')
            ->join('color as c', 'c.colorProduct_id', '=', 'pv.colorProduct_id')
            ->where('pv.product_id', $productId)
            ->select([
                'pv.id',
                'pv.product_id',
                'pv.colorProduct_id',
                'pv.inventory',
                'pv.qty_sold',
                'pv.image_01',
                'pv.image_02',
                'pv.image_03',
                'c.colorProduct as color_name',
                // nếu bảng color có cột mã màu thì thêm vào đây (ví dụ: c.color_hex)
                // 'c.color_hex',
            ])
            ->orderByDesc('pv.id')
            ->get();

        // map thêm URL ảnh để JS render
        $variants = $rows->map(function ($r) {
            return [
                'id' => $r->id,
                'product_id' => $r->product_id,
                'colorProduct_id' => $r->colorProduct_id,
                'color_name' => $r->color_name,
                'inventory' => (int)$r->inventory,
                'qty_sold' => (int)$r->qty_sold,
                'image_01' => $r->image_01,
                'image_02' => $r->image_02,
                'image_03' => $r->image_03,
                'image_01_url' => $r->image_01 ? asset('storage/' . ltrim($r->image_01, '/')) : null,
                'image_02_url' => $r->image_02 ? asset('storage/' . ltrim($r->image_02, '/')) : null,
                'image_03_url' => $r->image_03 ? asset('storage/' . ltrim($r->image_03, '/')) : null,
            ];
        });

        // Nếu gọi AJAX thì trả JSON, còn không bạn có thể return view tuỳ nhu cầu
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['variants' => $variants]);
        }

        // Nếu bạn không dùng view ở đây thì có thể bỏ phần dưới
        return view('admin.variants.index', compact('variants', 'productId'));
    }

    /**
     * Thêm màu (variant) cho sản phẩm (form bên trái).
     * Route bạn đang dùng: POST admin.variants.store
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id'      => ['required', 'integer', 'exists:products,id'],
            'colorProduct_id' => [
                'required',
                'integer',
                'exists:color,colorProduct_id',
                // ✅ chặn trùng màu trong cùng product bằng rule chuẩn
                Rule::unique('product_variants')->where(function ($q) use ($request) {
                    return $q->where('product_id', (int)$request->product_id)
                        ->where('colorProduct_id', (int)$request->colorProduct_id);
                }),
            ],
            'inventory'       => ['required', 'integer', 'min:0'],
            'image_01'        => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'image_02'        => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'image_03'        => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'colorProduct_id.unique' => 'Màu này đã tồn tại cho sản phẩm.',
        ]);

        $paths = [];

        try {
            $variantId = DB::transaction(function () use ($request, &$paths) {
                // 1) Upload ảnh
                $paths['image_01'] = $request->file('image_01')->store('uploaded_img', 'public');
                $paths['image_02'] = $request->file('image_02')->store('uploaded_img', 'public');
                $paths['image_03'] = $request->file('image_03')->store('uploaded_img', 'public');

                // 2) Insert variant
                return DB::table('product_variants')->insertGetId([
                    'product_id'      => (int)$request->product_id,
                    'colorProduct_id' => (int)$request->colorProduct_id,
                    'inventory'       => (int)$request->inventory,
                    'qty_sold'        => 0,
                    'image_01'        => $paths['image_01'],
                    'image_02'        => $paths['image_02'],
                    'image_03'        => $paths['image_03'],
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            });

            // ✅ Nếu bạn submit bằng AJAX thì trả JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Đã thêm màu mới cho sản phẩm!',
                    'variant_id' => $variantId,
                ], 200);
            }

            return redirect()->back()->with('success', 'Đã thêm màu mới cho sản phẩm!');
        } catch (\Throwable $e) {
            // rollback DB xong thì dọn ảnh rác
            foreach ($paths as $p) {
                if ($p) Storage::disk('public')->delete($p);
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Có lỗi khi thêm màu.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Có lỗi khi thêm màu.');
        }
    }

    /**
     * Xoá 1 variant + xoá ảnh.
     * Route gợi ý: DELETE admin/variants/{variant}
     */
    public function destroy(Request $request, int $variantId)
    {
        $row = DB::table('product_variants')->where('id', $variantId)->first();

        if (!$row) {
            return $request->expectsJson() || $request->ajax()
                ? response()->json(['message' => 'Variant không tồn tại.'], 404)
                : redirect()->back()->with('error', 'Variant không tồn tại.');
        }

        DB::transaction(function () use ($row, $variantId) {
            DB::table('product_variants')->where('id', $variantId)->delete();

            // xoá ảnh
            foreach (['image_01', 'image_02', 'image_03'] as $k) {
                if (!empty($row->$k)) {
                    Storage::disk('public')->delete($row->$k);
                }
            }
        });

        return $request->expectsJson() || $request->ajax()
            ? response()->json(['message' => 'Đã xoá màu/variant.'], 200)
            : redirect()->back()->with('success', 'Đã xoá màu/variant.');
    }
    public function index_by_product(int $productId)
    {
        $rows = DB::table('product_variants as pv')
            ->join('color as c', 'c.colorProduct_id', '=', 'pv.colorProduct_id')
            ->where('pv.product_id', $productId)
            ->select([
                'pv.id',
                'pv.product_id',
                'pv.colorProduct_id',
                'pv.inventory',
                'pv.qty_sold',
                'pv.image_01',
                'pv.image_02',
                'pv.image_03',
                'c.colorProduct as color_name',
            ])
            ->orderByDesc('pv.id')
            ->get();

        $variants = $rows->map(function ($r) {
            $img1 = $r->image_01 ? asset('storage/' . ltrim($r->image_01, '/')) : null;
            $img2 = $r->image_02 ? asset('storage/' . ltrim($r->image_02, '/')) : null;
            $img3 = $r->image_03 ? asset('storage/' . ltrim($r->image_03, '/')) : null;

            $thumb = $img1 ?: ($img2 ?: $img3); // ✅ fallback

            return [
                'id' => $r->id,
                'product_id' => $r->product_id,
                'colorProduct_id' => $r->colorProduct_id,
                'color_name' => $r->color_name,
                'inventory' => (int)$r->inventory,
                'qty_sold' => (int)$r->qty_sold,

                'image_01_url' => $img1,
                'image_02_url' => $img2,
                'image_03_url' => $img3,
                'thumb_url'    => $thumb,
            ];
        });

        return response()->json(['variants' => $variants]);
    }

    public function update(Request $request, int $variant)
    {
        $row = DB::table('product_variants')->where('id', $variant)->first();
        if (!$row) {
            return response()->json(['message' => 'Variant không tồn tại.'], 404);
        }

        $request->validate([
            'colorProduct_id' => ['required', 'integer', 'exists:color,colorProduct_id'],
            'inventory' => ['required', 'integer', 'min:0'],
            'image_01'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'image_02'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'image_03'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image_01' => ['nullable'],
            'remove_image_02' => ['nullable'],
            'remove_image_03' => ['nullable'],
        ]);
        // chống trùng màu trong cùng product
        $exists = DB::table('product_variants')
            ->where('product_id', $row->product_id)
            ->where('colorProduct_id', (int)$request->colorProduct_id)
            ->where('id', '!=', $row->id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Màu này đã tồn tại cho sản phẩm.'], 422);
        }

        $data = [
            'colorProduct_id' => (int)$request->colorProduct_id,
            'inventory'  => (int)$request->inventory,
            'updated_at' => now(),
        ];

        DB::transaction(function () use ($request, $row, &$data) {
            for ($i = 1; $i <= 3; $i++) {
                $f  = "image_0{$i}";
                $rm = "remove_image_0{$i}";

                if ($request->input($rm) === '1') {
                    if (!empty($row->$f) && Storage::disk('public')->exists($row->$f)) {
                        Storage::disk('public')->delete($row->$f);
                    }
                    $data[$f] = null; // nếu cột không cho null thì đổi thành ảnh mặc định
                }

                if ($request->hasFile($f)) {
                    $path = $request->file($f)->store('uploaded_img', 'public');
                    $data[$f] = $path;

                    if (!empty($row->$f) && Storage::disk('public')->exists($row->$f)) {
                        Storage::disk('public')->delete($row->$f);
                    }
                }
            }

            DB::table('product_variants')->where('id', $row->id)->update($data);
        });

        return response()->json(['message' => 'Đã cập nhật!'], 200);
    }
}
