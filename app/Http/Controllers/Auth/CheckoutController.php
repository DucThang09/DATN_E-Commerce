<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Order;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Province;
use App\Models\District;
use App\Models\Ward;
use App\Models\AdminNotification;
use App\Models\OrderItem;

class CheckoutController extends Controller
{
    public function getDistricts(Request $request)
    {
        $province_id = $request->province_id;
        if (!$province_id) return response()->json([]);

        $districts = District::where('province_id', $province_id)->get();

        $data = $districts->map(function ($d) {
            return [
                'id'   => $d->district_id ?? $d->id,
                'name' => $d->name,
            ];
        });

        return response()->json($data);
    }

    public function getWards(Request $request)
    {
        $district_id = $request->district_id;

        $wards = Ward::where('district_id', $district_id)->get();

        $data = $wards->map(function ($w) {
            return [
                'id' => $w->wards_id ?? $w->id,
                'name' => $w->name,
            ];
        });
        return response()->json($data);
    }


    public function checkoutSelected(Request $request)
    {
        $user = Auth::user();
        if (!$user) return redirect('login');
        $request->validate([
            'selected_items' => 'required|array|min:1',
            'selected_items.*' => 'integer',
        ]);
        $province = Province::all();
        $selectedItems = array_values(array_unique(array_filter(array_map(
            'intval',
            (array) $request->input('selected_items', [])
        ))));

        if (empty($selectedItems)) {
            return back()->with('message', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán.');
        }

        $cartItems = Cart::where('user_id', $user->id)
            ->whereIn('id', $selectedItems)
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('message', 'Không tìm thấy sản phẩm hợp lệ.');
        }
        $pids = $cartItems->pluck('pid')->unique()->values();
        $products = Product::whereIn('id', $pids)->get()->keyBy('id');

        $outOfStockItems = [];

        // lấy variants theo variant_id trong cart
        $variantIds = $cartItems->pluck('variant_id')->filter()->unique()->values();

        $variants = DB::table('product_variants')
            ->whereIn('id', $variantIds)
            ->select(['id', 'product_id', 'inventory', 'image_01'])
            ->get()
            ->keyBy('id');

        $outOfStockItems = [];

        $cartItemsWP = $cartItems->map(function ($item) use ($products, $variants, &$outOfStockItems) {
            $product = $products->get($item->pid);

            if (!$product) {
                $outOfStockItems[] = $item->name;
                $item->discount = 0;
                return $item;
            }

            $item->discount = (float) ($product->discount ?? 0);
            $item->price    = (float) ($product->price ?? $item->price);

            // ✅ tồn kho theo variant
            $v = $item->variant_id ? ($variants->get($item->variant_id) ?? null) : null;
            $inv = (int) ($v->inventory ?? 0);

            if ($inv < (int)$item->quantity) {
                $outOfStockItems[] = $item->name;
            }

            // ✅ ảnh theo variant (nếu bạn muốn checkout hiển thị đúng màu)
            $item->image_path = $v && !empty($v->image_01) ? $v->image_01 : ($item->image ?? null);

            return $item;
        });


        if (!empty($outOfStockItems)) {
            return back()->withErrors([
                'message' => 'Sản phẩm không đủ hàng: ' . implode(', ', array_unique($outOfStockItems)),
            ]);
        }

        $grandTotal = $cartItemsWP->sum(function ($item) {
            $price = (float) $item->price;
            $discount = (float) ($item->discount ?? 0);
            $qty = (int) $item->quantity;

            $unit = $price * (1 - $discount / 100);
            return $unit * $qty;
        });

        $totalProducts = $cartItemsWP->map(function ($item) {
            $price = (float) $item->price;
            $discount = (float) ($item->discount ?? 0);
            $qty = (int) $item->quantity;

            $unit = $price * (1 - $discount / 100);
            $unitText = number_format($unit, 0, ',', '.');

            return $item->name . ' (' . $unitText . ' x ' . $qty . ')';
        })->implode(' - ');
        return view('checkout', compact('cartItemsWP', 'grandTotal', 'totalProducts', 'province'));
    }

    public function placeOrder(Request $request)
    {
        $user = Auth::user();
        if (!$user) return redirect('login');

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'number'   => ['required', 'string', 'max:20'],
            'email'    => ['nullable', 'email', 'max:255'],
            'flat'     => ['required', 'string', 'max:255'],

            'province' => ['required'],
            'district' => ['required'],
            'ward'     => ['required'],

            'method'   => ['required', 'string'],
            'idCart'   => ['required', 'array', 'min:1'],
            'idCart.*' => ['integer'],
        ]);

        $cartIds = array_values(array_unique(array_filter(array_map(
            'intval',
            (array) $request->input('idCart', [])
        ))));

        if (empty($cartIds)) {
            return back()->with('message', 'Không tìm thấy sản phẩm hợp lệ để đặt hàng.');
        }

        return DB::transaction(function () use ($request, $user, $cartIds) {

            $cartItems = Cart::where('user_id', $user->id)
                ->whereIn('id', $cartIds)
                ->lockForUpdate()
                ->get();

            if ($cartItems->isEmpty()) {
                return back()->with('message', 'Không tìm thấy sản phẩm hợp lệ để đặt hàng.');
            }

            $pids = $cartItems->pluck('pid')->unique()->values();
            $products = Product::whereIn('id', $pids)->lockForUpdate()->get()->keyBy('id');
            $variantIds = $cartItems->pluck('variant_id')->filter()->unique()->values();
            $variants = DB::table('product_variants')
                ->whereIn('id', $variantIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $outOfStock = [];
            $totalPrice = 0;

            foreach ($cartItems as $c) {
                $v = $c->variant_id ? ($variants->get($c->variant_id) ?? null) : null;
                $inv = (int) ($v->inventory ?? 0);

                if ($inv < (int)$c->quantity) {
                    $outOfStock[] = $c->name;
                }
            }

            if (!empty($outOfStock)) {
                return back()->withErrors([
                    'message' => 'Sản phẩm không đủ hàng: ' . implode(', ', array_unique($outOfStock)),
                ]);
            }

            foreach ($cartItems as $c) {
                $p = $products->get($c->pid);

                $price    = (float) ($p->price ?? $c->price);
                $discount = (float) ($p->discount ?? 0);
                $qty      = (int) $c->quantity;

                $unit = $price * (1 - $discount / 100);

                $totalPrice += (int) round($unit * $qty);
            }

            $totalProducts = $cartItems->map(function ($c) use ($products) {
                $p = $products->get($c->pid);
                $price    = (float) ($p->price ?? $c->price);
                $discount = (float) ($p->discount ?? 0);
                $unit     = $price * (1 - $discount / 100);

                $unitText = number_format($unit, 0, ',', '.');

                return $c->name . ' (' . $unitText . ' x ' . (int)$c->quantity . ')';
            })->implode(' - ');


            $provinceId = $request->input('province');
            $districtId = $request->input('district');
            $wardId     = $request->input('ward');

            $provinceName = Province::where('province_id', $provinceId)->value('name')
                ?? Province::where('id', $provinceId)->value('name');

            $districtName = District::where('district_id', $districtId)->value('name')
                ?? District::where('id', $districtId)->value('name');

            $wardName = Ward::where('wards_id', $wardId)->value('name');

            $address = $request->input('flat')
                . ', ' . ($wardName ?? $wardId)
                . ', ' . ($districtName ?? $districtId)
                . ', ' . ($provinceName ?? $provinceId);


            if ($request->input('method') !== 'cash on delivery') {
                return back()->with('message', 'Vui lòng hoàn tất thanh toán theo phương thức đã chọn.');
            }

            $order = Order::create([
                'user_id'        => $user->id,
                'name'           => $request->input('name'),
                'number'         => $request->input('number'),
                'email'          => $request->input('email'),
                'method'         => $request->input('method'),
                'address'        => $address,
                'total_products' => $totalProducts,
                'total_price'    => $totalPrice,
                'placed_on'      => now(),
                'payment_status' => 'pending',
            ]);

            foreach ($cartItems as $cart) {
                $p = $products->get($cart->pid);

                $price = (float) ($p->price ?? $cart->price);
                $discount = (float) ($p->discount ?? 0);
                $unit = $price * (1 - $discount / 100);

                OrderItem::create([
                    'order_id'        => $order->id,
                    'product_id'      => $cart->pid,
                    'variant_id'      => $cart->variant_id,        // ✅ thêm
                    'colorProduct_id' => $cart->colorProduct_id,   // ✅ thêm
                    'product_name'    => $cart->name,
                    'product_image'   => $cart->image ?? null,
                    'quantity'        => (int) $cart->quantity,
                    'unit_price'      => $unit,
                    'cost_price'      => $p ? (float) ($p->purchase_price ?? 0) : 0,
                    'total_price'     => $unit * (int)$cart->quantity,
                ]);


                if ($cart->variant_id) {
                    $qty = (int)$cart->quantity;

                    $affected = DB::table('product_variants')
                        ->where('id', $cart->variant_id)
                        ->where('inventory', '>=', $qty) // ✅ điều kiện chống âm kho
                        ->update([
                            'inventory' => DB::raw("inventory - {$qty}"),
                            'qty_sold'  => DB::raw("qty_sold + {$qty}"),
                        ]);

                    if ($affected === 0) {
                        // ✅ Người khác mua trước -> rollback toàn bộ đơn
                        throw new \Exception("Sản phẩm '{$cart->name}' không đủ hàng. Vui lòng thử lại.");
                    }
                }
            }

            Cart::where('user_id', $user->id)->whereIn('id', $cartIds)->delete();

            $customerName  = $order->name ?? 'Khách hàng';
            $customerEmail = $order->email ?? 'Không có email';
            $total         = number_format($order->total_price, 0, ',', '.');

            AdminNotification::create([
                'type'    => 'order_created',
                'title'   => 'Đơn hàng mới #' . $order->id,
                'message' => "Khách hàng {$customerName} ({$customerEmail}) vừa đặt đơn hàng trị giá {$total}₫.",
                'details' => $request->input('order_details_text') ?? null,
                'is_read' => false,
            ]);

            return redirect()
                ->route('checkout.success', ['order' => $order->id])
                ->with('message', 'Đơn hàng của bạn đã được đặt thành công!');
        });
    }
    public function paymentPage(Request $request)
    {
        $user = Auth::user();
        if (!$user) return redirect('login');

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'number'   => ['required', 'string', 'max:20'],
            'flat'     => ['required', 'string', 'max:255'],
            'province' => ['required'],
            'district' => ['required'],
            'ward'     => ['required'],
            'idCart'   => ['required', 'array'],
        ]);

        $cartIds = array_values(array_unique(array_map('intval', (array)$request->input('idCart', []))));
        $cartItems = Cart::where('user_id', $user->id)->whereIn('id', $cartIds)->get();
        if ($cartItems->isEmpty()) return redirect()->route('cart.index')->with('message', 'Giỏ hàng không hợp lệ.');

        $pids = $cartItems->pluck('pid')->unique()->values();
        $products = Product::whereIn('id', $pids)->get()->keyBy('id');

        $cartItemsWP = $cartItems->map(function ($item) use ($products) {
            $p = $products->get($item->pid);
            $item->discount = (float)($p->discount ?? 0);
            $item->price = (float)($p->price ?? $item->price);
            return $item;
        });

        $subTotal = $cartItemsWP->sum(function ($i) {
            $unit = (float)$i->price * (1 - (float)$i->discount / 100);
            return $unit * (int)$i->quantity;
        });
        $shipping = 0;
        $vatRate = 0;
        $vat = (int) round($subTotal * $vatRate);
        $grandTotal = (int) round($subTotal + $shipping + $vat);

        $totalProducts = $cartItemsWP->map(function ($i) {
            $unit = (float)$i->price * (1 - (float)$i->discount / 100);
            return $i->name . ' (' . (int)round($unit) . ' x ' . $i->quantity . ')';
        })->implode(' - ');

        $payload = $request->only(['name', 'number', 'email', 'flat', 'province', 'district', 'ward', 'note']);
        $payload['idCart'] = $cartIds;
        session(['checkout_payload' => $payload]);

        return redirect()->route('checkout.payment');
    }
    public function paymentInfo()
    {
        $user = Auth::user();
        if (!$user) return redirect('login');

        $payload = session('checkout_payload');
        if (!$payload || empty($payload['idCart'])) {
            return redirect()->route('cart.index')->with('message', 'Vui lòng chọn sản phẩm để thanh toán.');
        }

        $cartIds = array_values(array_unique(array_map('intval', (array) $payload['idCart'])));

        $cartItems = Cart::where('user_id', $user->id)->whereIn('id', $cartIds)->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('message', 'Giỏ hàng không hợp lệ.');
        }

        $pids = $cartItems->pluck('pid')->unique()->values();
        $products = Product::whereIn('id', $pids)->get()->keyBy('id');

        $cartItemsWP = $cartItems->map(function ($item) use ($products) {
            $p = $products->get($item->pid);
            $item->discount = (float)($p->discount ?? 0);
            $item->price = (float)($p->price ?? $item->price);
            return $item;
        });

        $subTotal = $cartItemsWP->sum(function ($i) {
            $unit = (float)$i->price * (1 - (float)$i->discount / 100);
            return $unit * (int)$i->quantity;
        });

        $shipping = 0;
        $vatRate = 0;
        $vat = (int) round($subTotal * $vatRate);
        $grandTotal = (int) round($subTotal + $shipping + $vat);

        $totalProducts = $cartItemsWP->map(function ($i) {
            $unit = (float)$i->price * (1 - (float)$i->discount / 100);
            return $i->name . ' (' . (int)round($unit) . ' x ' . $i->quantity . ')';
        })->implode(' - ');

        return view('checkout_payment', compact(
            'cartItemsWP',
            'subTotal',
            'shipping',
            'vat',
            'grandTotal',
            'totalProducts',
            'payload'
        ));
    }

    public function checkoutInfo()
    {
        $user = Auth::user();
        if (!$user) return redirect('login');

        $payload = session('checkout_payload');
        if (!$payload || empty($payload['idCart'])) {
            return redirect()->route('cart.index')->with('message', 'Vui lòng chọn sản phẩm để thanh toán.');
        }

        $cartIds = array_values(array_unique(array_map('intval', (array)$payload['idCart'])));

        $province = Province::all();

        $cartItems = Cart::where('user_id', $user->id)->whereIn('id', $cartIds)->get();
        if ($cartItems->isEmpty()) return redirect()->route('cart.index')->with('message', 'Giỏ hàng không hợp lệ.');

        $pids = $cartItems->pluck('pid')->unique()->values();
        $products = Product::whereIn('id', $pids)->get()->keyBy('id');

        $cartItemsWP = $cartItems->map(function ($item) use ($products) {
            $p = $products->get($item->pid);
            $item->discount = (float)($p->discount ?? 0);
            $item->price = (float)($p->price ?? $item->price);
            return $item;
        });

        $grandTotal = $cartItemsWP->sum(function ($i) {
            $unit = (float)$i->price * (1 - (float)$i->discount / 100);
            return $unit * (int)$i->quantity;
        });

        $totalProducts = $cartItemsWP->map(function ($i) {
            $unit = (float)$i->price * (1 - (float)$i->discount / 100);
            return $i->name . ' (' . (int)round($unit) . ' x ' . $i->quantity . ')';
        })->implode(' - ');

        // ✅ truyền payload về view để fill lại input
        $form = $payload;

        return view('checkout', compact('cartItemsWP', 'grandTotal', 'totalProducts', 'province', 'form'));
    }
    public function success($orderId)
    {
        $user = Auth::user();
        if (!$user) return redirect('login');

        $order = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $items = DB::table('order_items as oi')
            ->leftJoin('color as c', 'c.colorProduct_id', '=', 'oi.colorProduct_id')
            ->where('oi.order_id', $order->id)
            ->select([
                'oi.*',
                DB::raw('c.colorProduct as color_name'),
            ])
            ->get();


        $itemsCount = $items->sum('quantity');
        $subtotal = (int) $order->total_price;
        $shipping = 0;
        $total = $subtotal + $shipping;

        $orderCode = 'DH' . str_pad((string)$order->id, 8, '0', STR_PAD_LEFT);

        return view('checkout_success', compact(
            'order',
            'items',
            'itemsCount',
            'subtotal',
            'shipping',
            'total',
            'orderCode'
        ));
    }
}
