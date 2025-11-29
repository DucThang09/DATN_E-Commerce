<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $districts = District::where('province_id', $province_id)->get();

        return response()->json($districts);
    }

    public function getWards(Request $request)
    {
        $district_id = $request->district_id;
        $wards = Ward::where('district_id', $district_id)->get();

        return response()->json($wards);
    }

    public function checkoutSelected(Request $request)
    {
        $province = Province::all();
        $district = District::all();
        $wards = Ward::all();

        $user = Auth::user();
        $selectedItems = $request->input('selected_items', []);

        if (empty($selectedItems)) {
            return back()->with('message', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán.');
        }

        $cartItems = Cart::where('user_id', $user->id)
            ->whereIn('id', $selectedItems)
            ->get();

        $cartItemsWP = $cartItems->map(function ($item) {
            $product = Product::find($item->pid);
            $item->discount = $product ? $product->discount : 0; // Lấy giảm giá của sản phẩm, nếu không có thì mặc định là 0
            return $item;
        });

        // Tính tổng tiền
        $grandTotal = $cartItemsWP->sum(function ($item) {
            $discountedPrice = $item->price;
            return $discountedPrice * $item->quantity;
        });

        foreach ($cartItemsWP as $item) {
            $product = Product::find($item->pid); // Kiểm tra `pid` trong bảng product

            if (!$product || $product->inventory <= 0) {
                // Nếu sản phẩm không tồn tại hoặc không còn hàng trong kho
                $outOfStockItems[] = $item->name;
            }
        }

        if (!empty($outOfStockItems)) {
            return back()->withErrors([
                'message' => 'Sản phẩm đã hết hàng: ' . implode(', ', $outOfStockItems),
            ]);
        }

        $totalProducts = $cartItemsWP->map(function ($item) {
            $discountedPrice = $item->price;
            return $item->name . ' (' . $discountedPrice . ' x ' . $item->quantity . ')';
        })->implode(' - ');

        return view('checkout', compact('cartItemsWP', 'grandTotal', 'totalProducts', 'province', 'district', 'wards'));
    }


    public function placeOrder(Request $request)
    {
        $user = Auth::user();

        $provinceName = Province::where('province_id', $request->input('province'))->value('name');
        $address = $request->input('flat') . ', ' . $request->input('ward') . ', ' . $request->input('district') . ', ' . $provinceName;

        if ($request->input('method') === 'cash on delivery') {

            // 1. Tạo đơn hàng tổng
            $order = Order::create([
                'user_id'        => $user->id,
                'name'           => $request->input('name'),
                'number'         => $request->input('number'),
                'email'          => $request->input('email'),
                'method'         => $request->input('method'),
                'address'        => $address,
                'total_products' => $request->input('total_products'),
                'total_price'    => $request->input('total_price'),
                'placed_on'      => now(),
                'payment_status' => 'pending',
            ]);

            // 2. Lưu các sản phẩm vào order_items
            $cartIds   = $request->input('idCart', []);     // id của bảng carts
            $cartItems = Cart::whereIn('id', $cartIds)->get();

            foreach ($cartItems as $cart) {
                // Lấy product tương ứng trong bảng products
                // Cart đang dùng trường pid nên mình lấy theo pid
                $product = Product::find($cart->pid);

                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $cart->pid,                      // 👈 dùng pid
                    'product_name'  => $cart->name,
                    'product_image' => $cart->image ?? null,
                    'quantity'      => $cart->quantity,
                    'unit_price'    => $cart->price,
                    'cost_price'    => $product ? $product->purchase_price : 0, // 👈 GIÁ VỐN
                    'total_price'   => $cart->price * $cart->quantity,
                ]);
                
            }

            // 3. Xóa sản phẩm khỏi giỏ
            Cart::whereIn('id', $cartIds)->delete();

            // 4. Thông báo cho admin (giữ nguyên)
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
                ->route('checkout.success')
                ->with('message', 'Đơn hàng của bạn đã được đặt thành công!');
        }

        return back()->with('message', 'Vui lòng hoàn tất thanh toán theo phương thức đã chọn.');
    }
}
