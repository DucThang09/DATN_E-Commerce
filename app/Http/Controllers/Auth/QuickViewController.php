<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

class QuickViewController extends Controller
{
    public function quickView($pid)
    {
        // 1) Lấy variants + tên màu
        $variants = DB::table('product_variants as pv')
            ->leftJoin('color as c', 'c.colorProduct_id', '=', 'pv.colorProduct_id')
            ->where('pv.product_id', $pid)
            ->select([
                'pv.id as variant_id',
                'pv.product_id',
                'pv.colorProduct_id',
                'c.colorProduct as color',
                'pv.inventory',
                'pv.qty_sold',
                'pv.image_01',
                'pv.image_02',
                'pv.image_03',
            ])
            ->orderBy('pv.id', 'asc')
            ->get();

        // 2) Lấy product
        $product = Product::findOrFail($pid);

        // 3) Chuẩn hoá color (trim + bỏ null/empty)
        $variants = $variants->map(function ($v) {
            $v->color = is_string($v->color) ? trim($v->color) : '';
            return $v;
        });

        // 4) List colors
        $colors = $variants->pluck('color')->filter(fn($c) => $c !== '')->values()->all();

        // 5) Variant mặc định (đang chọn)
        $selected =
            $variants->first(fn($v) => !empty($v->color) && (int)$v->inventory > 0)
            ?? $variants->first(fn($v) => !empty($v->color))
            ?? $variants->first();
        $selectedColor = $selected?->color ?? '';

        // 6) Map màu -> dữ liệu variant cho JS (lọc color rỗng trước khi keyBy)
        $variantMap = $variants
            ->filter(fn($v) => !empty($v->color))
            ->keyBy('color')
            ->map(function ($v) {
                return [
                    'variant_id'       => (int) $v->variant_id,
                    'colorProduct_id'  => (int) $v->colorProduct_id,
                    'inventory'        => (int) $v->inventory,
                    'qty_sold'         => (int) $v->qty_sold,
                    'image_01'         => $v->image_01 ?? '',
                    'image_02'         => $v->image_02 ?? '',
                    'image_03'         => $v->image_03 ?? '',
                ];
            })
            ->toArray();

        // 7) Dữ liệu hỗ trợ render ban đầu cho view
        $vDefault = $variantMap[$selectedColor] ?? [];
        $mainPath = $vDefault['image_01']
            ?? ($product->image_01 ?? '');

        return view('quick_view', compact(
            'product',
            'variants',
            'colors',
            'selectedColor',
            'variantMap',
            'vDefault',
            'mainPath'
        ));
    }
}
