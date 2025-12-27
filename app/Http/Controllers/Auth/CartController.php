<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Province;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        $user_id = Auth::id();
        if (!$user_id) return redirect('login');
        $preselected = request()->input('selected', session('preselected', []));
        $preselected = array_filter(array_map('intval', (array) $preselected));

        $cartItems = DB::table('cart as ca')
            ->leftJoin('product_variants as pv', 'pv.id', '=', 'ca.variant_id')
            ->leftJoin('color as c', 'c.colorProduct_id', '=', 'pv.colorProduct_id')
            ->where('ca.user_id', $user_id)
            ->select([
                'ca.*',
                DB::raw('pv.image_01 as v_image_01'),
                DB::raw('pv.image_02 as v_image_02'),
                DB::raw('pv.image_03 as v_image_03'),
                DB::raw('pv.inventory as v_inventory'),
                DB::raw('c.colorProduct as color_name'),
            ])
            ->get();

        $pids = $cartItems->pluck('pid')->unique()->values();
        $products = Product::whereIn('id', $pids)->get()->keyBy('id');

        $cartItemsWP = $cartItems->map(function ($item) use ($products) {
            $product = $products->get($item->pid);

            $item->discount = $product ? (float)($product->discount ?? 0) : 0;
            $basePrice = $product ? (float)($product->price ?? $item->price) : (float)$item->price;

            $item->base_price = $basePrice;
            $item->final_price = (int) round($basePrice * (1 - $item->discount / 100));

            $item->image_path =
                ($item->v_image_01 ?: null)
                ?: ($item->v_image_02 ?: null)
                ?: ($item->v_image_03 ?: null)
                ?: ($item->image ?? 'uploaded_img/default.png');

            $inv = (int) ($item->v_inventory ?? 0);
            $item->stock_message = $inv <= 0 ? 'Sản phẩm đã hết hàng' : null;
            return $item;
        });
        $grandTotal = $cartItemsWP->sum(fn($i) => $i->final_price * (int)$i->quantity);
        $province = Province::all();

        $totalProducts = $cartItemsWP->map(function ($i) {
            return $i->name . ' (' . $i->final_price . ' x ' . $i->quantity . ')';
        })->implode(' - ');

        return view('cart', compact('cartItemsWP', 'grandTotal', 'province', 'totalProducts', 'preselected'));
    }

    public function checkoutSelected(Request $request)
    {
        $user_id = Auth::id();
        if (!$user_id) return redirect('login');

        $selected = $request->input('selected_items', []);
        $selected = array_filter(array_map('intval', (array) $selected));

        if (empty($selected)) {
            return redirect()->route('cart.index')
                ->with('message', 'Bạn hãy chọn ít nhất 1 sản phẩm để tiếp tục thanh toán.');
        }

        $cartItems = Cart::where('user_id', $user_id)
            ->whereIn('id', $selected)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('message', 'Danh sách sản phẩm đã chọn không hợp lệ hoặc đã bị xóa.');
        }

        $variantIds = $cartItems->pluck('variant_id')->filter()->unique()->values();
        $variantsById = DB::table('product_variants')
            ->whereIn('id', $variantIds)
            ->select(['id', 'product_id', 'image_01', 'image_02', 'image_03', 'inventory'])
            ->get()
            ->keyBy('id');
        // ✅ Soát tồn trước khi cho vào checkout
        foreach ($cartItems as $it) {
            if ($it->variant_id) {
                $v = $variantsById->get($it->variant_id);
                $inv = (int)($v->inventory ?? 0);

                if ($inv <= 0) {
                    return redirect()->route('cart.index')
                        ->with('message', "Sản phẩm '{$it->name}' đã hết hàng, vui lòng chọn sản phẩm khác.");
                }

                if ((int)$it->quantity > $inv) {
                    return redirect()->route('cart.index')
                        ->with('message', "Sản phẩm '{$it->name}' chỉ còn {$inv}, vui lòng giảm số lượng.");
                }
            }
        }
        $cartItemsWP = $this->attachDiscounts($cartItems)->map(function ($item) use ($variantsById) {
            $v = $item->variant_id ? ($variantsById->get($item->variant_id) ?? null) : null;
            $item->image_path = ($v->image_01 ?? null)
                ?: ($v->image_02 ?? null)
                ?: ($v->image_03 ?? null)
                ?: ($item->image ?? 'uploaded_img/default.png');

            return $item;
        });

        $grandTotal  = $this->calcGrandTotal($cartItemsWP);

        $province = Province::all();

        $totalProducts = $cartItemsWP->map(function ($item) {
            $discountedPrice = $item->price - ($item->price * (($item->discount ?? 0) / 100));
            return $item->name . ' (' . $discountedPrice . ' x ' . $item->quantity . ')';
        })->implode(' - ');

        return view('checkout', compact('cartItemsWP', 'grandTotal', 'province', 'totalProducts', 'selected'));
    }

    public function add(Request $request)
    {
        $user_id = Auth::id();

        $isAjax = $request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';

        if (!$user_id) {
            if ($isAjax) {
                return response()->json([
                    'ok' => false,
                    'need_login' => true,
                    'redirect' => route('login'),
                    'message' => 'Bạn cần đăng nhập để thêm vào giỏ.',
                ], 401);
            }
            return redirect('login');
        }

        $request->validate([
            'pid' => 'required|integer',
            'variant_id' => 'nullable|integer',
            'qty' => 'nullable|integer|min:1|max:99',
        ]);

        $pid = (int) $request->pid;
        $variantId = $request->filled('variant_id') ? (int) $request->variant_id : null;
        $qty = (int) ($request->qty ?? 1);

        $product = Product::find($pid);
        if (!$product) {
            if ($isAjax) return response()->json(['ok' => false, 'message' => 'Sản phẩm không tồn tại!'], 404);
            return back()->with('message', 'Sản phẩm không tồn tại!');
        }

        // ✅ Lấy variant nếu có truyền lên
        $variant = null;
        if ($variantId) {
            $variant = DB::table('product_variants')
                ->where('id', $variantId)
                ->where('product_id', $pid)
                ->select(['id', 'product_id', 'colorProduct_id', 'inventory', 'image_01', 'image_02', 'image_03'])
                ->first();

            if (!$variant) {
                $variantId = null;
                $variant = null;
            }
        }

        // ✅ Nếu chưa chọn variant => tự lấy variant đầu tiên
        if (!$variantId) {
            $variant = DB::table('product_variants')
                ->where('product_id', $pid)
                ->orderByDesc(DB::raw('inventory > 0')) // ✅ ưu tiên còn hàng
                ->orderBy('id')
                ->select(['id', 'product_id', 'colorProduct_id', 'inventory', 'image_01', 'image_02', 'image_03'])
                ->first();


            if ($variant) {
                $variantId = (int) $variant->id;
            }
        }

        $variantInventory = $variant ? (int)($variant->inventory ?? 0) : null;
        $isOutOfStock = ($variant && $variantInventory <= 0);
        $variantColorId = $variant ? (int)($variant->colorProduct_id ?? 0) : null;

        // ✅ Ảnh ưu tiên theo variant -> product -> default
        $image = ($variant?->image_01 ?? null)
            ?: ($variant?->image_02 ?? null)
            ?: ($variant?->image_03 ?? null)
            ?: ($product->image ?? null)
            ?: ($product->product_image ?? null)
            ?: 'uploaded_img/default.png';

        // ✅ tìm dòng cart theo pid + variant_id
        $cartItem = Cart::where('user_id', $user_id)
            ->where('pid', $pid)
            ->where(function ($q) use ($variantId) {
                if ($variantId === null) $q->whereNull('variant_id');
                else $q->where('variant_id', $variantId);
            })
            ->first();

        if ($cartItem) {
            // ✅ KIỂU 1: Hết hàng -> không tăng qty, chỉ giữ lại trong giỏ
            if ($isOutOfStock) {
                $cartItem->variant_id = $variantId;
                $cartItem->colorProduct_id = $variantColorId;
                $cartItem->image = $image;
                $cartItem->quantity = max(1, (int)$cartItem->quantity);
                $cartItem->save();

                $msg = 'Sản phẩm đang hết hàng, đã giữ lại trong giỏ (không tăng số lượng).';

                if ($request->has('buy_now')) {
    if ($isAjax) {
        return response()->json([
            'ok' => true,
            'action' => 'buy_now',
            'message' => $msg,
            'redirect' => route('cart.index'),
            'preselected' => [(int) $cartItem->id],
        ]);
    }

    return redirect()->route('cart.index')
        ->with('preselected', [(int) $cartItem->id])
        ->with('message', $msg);
}


                if ($isAjax) {
                    $cartCount = Cart::where('user_id', $user_id)->count();
                    return response()->json([
                        'ok' => true,
                        'action' => 'out_of_stock',
                        'message' => $msg,
                        'cart_item_id' => (int) $cartItem->id,
                        'cart_count' => (int) $cartCount,
                    ]);
                }

                return back()->with('message', $msg);
            }

            // ✅ Còn hàng -> tăng qty như bình thường
            $newQty = (int)$cartItem->quantity + $qty;

            // (Tuỳ chọn) chặn vượt tồn khi còn hàng
            if ($variant && $newQty > (int)$variantInventory) {
                $msg = "Chỉ còn {$variantInventory} sản phẩm trong kho.";
                if ($isAjax) {
                    return response()->json([
                        'ok' => false,
                        'message' => $msg,
                        'max' => (int)$variantInventory,
                    ], 422);
                }
                return back()->with('message', $msg);
            }

            $cartItem->quantity = $newQty;
            $cartItem->variant_id = $variantId;
            $cartItem->colorProduct_id = $variantColorId;
            $cartItem->image = $image;
            $cartItem->save();

            if ($request->has('buy_now')) {
    $msg2 = 'Đã cập nhật số lượng trong giỏ và chọn sẵn sản phẩm!';
    if ($isAjax) {
        return response()->json([
            'ok' => true,
            'action' => 'buy_now',
            'message' => $msg2,
            'redirect' => route('cart.index'),
            'preselected' => [(int) $cartItem->id],
        ]);
    }

    return redirect()->route('cart.index')
        ->with('preselected', [(int) $cartItem->id])
        ->with('message', $msg2);
}


            if ($isAjax) {
                $cartCount = Cart::where('user_id', $user_id)->count();
                return response()->json([
                    'ok' => true,
                    'action' => 'updated',
                    'message' => 'Đã cập nhật số lượng trong giỏ!',
                    'cart_item_id' => (int) $cartItem->id,
                    'cart_count' => (int) $cartCount,
                ]);
            }

            return back()->with('message', 'Đã cập nhật số lượng trong giỏ!');
        }

        // ✅ create mới: hết hàng vẫn thêm, nhưng qty = 1 (kiểu 1)
        $qtyToAdd = $isOutOfStock ? 1 : $qty;

        $newItem = Cart::create([
            'user_id' => $user_id,
            'pid' => $pid,
            'variant_id' => $variantId,
            'colorProduct_id' => $variantColorId,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $qtyToAdd,
            'image' => $image,
        ]);

        $msg = $isOutOfStock
            ? 'Sản phẩm đang hết hàng, đã thêm vào giỏ để theo dõi.'
            : 'Đã thêm vào giỏ!';

       if ($request->has('buy_now')) {
    if ($isAjax) {
        return response()->json([
            'ok' => true,
            'action' => 'buy_now',
            'message' => $msg,
            'redirect' => route('cart.index'),
            'preselected' => [(int) $newItem->id],
        ]);
    }

    return redirect()->route('cart.index')
        ->with('preselected', [(int) $newItem->id])
        ->with('message', $msg);
}


        if ($isAjax) {
            $cartCount = Cart::where('user_id', $user_id)->count();
            return response()->json([
                'ok' => true,
                'action' => 'created',
                'message' => $msg,
                'cart_item_id' => (int) $newItem->id,
                'cart_count' => (int) $cartCount,
            ]);
        }

        return back()->with('message', $msg);
    }

    public function updateQty(Request $request)
    {
        $user_id = Auth::id();
        if (!$user_id) return response()->json(['ok' => false], 401);

        $request->validate([
            'cart_id' => 'required|integer',
            'qty' => 'required|integer|min:1|max:99',
        ]);

        $item = Cart::where('user_id', $user_id)->where('id', $request->cart_id)->first();
        if (!$item) return response()->json(['ok' => false], 404);

        $reqQty = (int)$request->qty;

        $inv = null;
        if (!empty($item->variant_id)) {
            $inv = (int) DB::table('product_variants')->where('id', $item->variant_id)->value('inventory');

            // ✅ hết hàng -> không cho tăng > 1
            if ($inv <= 0 && $reqQty > 1) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Sản phẩm đang hết hàng, không thể tăng số lượng.',
                    'max' => 1
                ], 422);
            }

            // ✅ còn hàng -> không cho vượt tồn
            if ($inv > 0 && $reqQty > $inv) {
                return response()->json([
                    'ok' => false,
                    'message' => "Chỉ còn {$inv} sản phẩm.",
                    'max' => $inv
                ], 422);
            }
        }

        $item->quantity = $reqQty;
        $item->save();

        return response()->json(['ok' => true, 'qty' => $item->quantity]);
    }

    public function removeSelected(Request $request)
    {
        $user_id = Auth::id();
        if (!$user_id) return redirect('login');

        $selected = $request->input('selected_items', []);
        $selected = array_filter(array_map('intval', (array) $selected));

        if (empty($selected)) {
            return back()->with('message', 'Vui lòng chọn ít nhất một sản phẩm để xóa.');
        }

        Cart::where('user_id', $user_id)->whereIn('id', $selected)->delete();
        return back()->with('message', 'Đã xóa các sản phẩm đã chọn!');
    }

    private function attachDiscounts($cartItems)
    {
        $pids = $cartItems->pluck('pid')->unique()->values();
        $productsById = Product::whereIn('id', $pids)->get()->keyBy('id');

        return $cartItems->map(function ($item) use ($productsById) {
            $product = $productsById->get($item->pid);
            $item->discount = $product ? ($product->discount ?? 0) : 0;
            return $item;
        });
    }

    private function calcGrandTotal($items)
    {
        return $items->sum(function ($item) {
            $discount = (float) ($item->discount ?? 0);
            $price = (float) $item->price;
            $qty = (int) $item->quantity;

            $discountedPrice = $price - ($price * ($discount / 100));
            return $discountedPrice * $qty;
        });
    }
}
