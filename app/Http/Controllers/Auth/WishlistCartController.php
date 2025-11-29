<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
class WishlistCartController extends Controller
{
    public function index_wishlist()
    {
        $user_id = Auth::id();
        $wishlistItems = Wishlist::where('user_id', $user_id)->get();

        return view('wishlist', compact('wishlistItems'));
    }
    public function index_cart()
    {
        $user_id = Auth::id();
        $cartItems = Cart::where('user_id', $user_id)->get();
        
        $grandTotal = $cartItems->sum(function($item) {
            return $item->price * $item->quantity;
        });

        $cartItemsWP = $cartItems->map(function($item) {
            $product = Product::find($item->pid);
            $item->discount = $product ? $product->discount : 0; // Lấy giảm giá của sản phẩm, nếu không có thì mặc định là 0
            return $item;
        });
    
        // Tính tổng tiền
        $grandTotal = $cartItemsWP->sum(function($item) {
            $discountedPrice = $item->price - ($item->price * ($item->discount/100));
            return $discountedPrice * $item->quantity;
        });

        return view('cart', compact('cartItemsWP', 'grandTotal'));
    }
    public function quickView($pid)
{
    $product = Product::findOrFail($pid); // Lấy sản phẩm từ bảng products dựa trên ID (pid)
    return view('quick_view', compact('product'));
}

    public function addToWishlistOrCart(Request $request)
    {
        $user_id = Auth::id(); 
        if (!$user_id) {
            return redirect('login');
        }
        $pid = filter_var($request->input('pid'), FILTER_SANITIZE_STRING);
        $name = filter_var($request->input('name'), FILTER_SANITIZE_STRING);
        $price = filter_var($request->input('price'), FILTER_SANITIZE_STRING);
        $image = filter_var($request->input('image'), FILTER_SANITIZE_STRING);
        $qty = filter_var($request->input('qty', 1), FILTER_SANITIZE_STRING); // Giả sử qty có thể không có khi thêm vào wishlist

        if ($request->has('add_to_wishlist')) {
            $this->addToWishlist($user_id, $pid, $name, $price, $image);
        } elseif ($request->has('add_to_cart')) {
            $this->addToCart($user_id, $pid, $name, $price, $image, $qty);
        } elseif ($request->has('delete_item')) {
            $this->deleteItem($request->input('wishlist_id'));
        } elseif ($request->has('clear_wishlist')) {
            $this->clearWishlist();
        } elseif ($request->has('remove_cart')) {
            $this->removeFromCart($request->input('cart_id'));
        } elseif ($request->has('clear_cart')) {
            $this->clearCart();
        }elseif ($request->has('update_cart')) {
            $this->updateCart($request);
        }

        return back()->with('message', 'Action completed successfully!');
    }

    private function addToWishlist($user_id, $pid, $name, $price, $image)
{
    $check_wishlist = Wishlist::where('name', $name)->where('user_id', $user_id)->exists();

    if ($check_wishlist) {
        return back()->with('message', 'Already added to wishlist!');
    }

    Wishlist::create([
        'user_id' => $user_id,
        'pid' => $pid,
        'name' => $name,
        'price' => $price,
        'image' => $image,
    ]);

    return back()->with('message', 'Added to wishlist!');
}


    private function addToCart($user_id, $pid, $name, $price, $image, $qty)
{
    $check_cart = Cart::where('name', $name)->where('user_id', $user_id)->exists();
    if ($check_cart) {
        
        $cartItem = Cart::where('name', $name)->where('user_id', $user_id)->first();
        $cartItem->quantity += $qty; 
        $cartItem->save();
        return back()->with('message', 'Product quantity updated in cart!');
    } else {
        Cart::create([
            'user_id' => $user_id,
            'pid' => $pid,
            'name' => $name,
            'price' => $price,
            'quantity' => $qty,
            'image' => $image,
        ]);
        return back()->with('message', 'Added to cart!');
    }
}


    private function deleteItem($wishlistId)
    {
        Wishlist::destroy($wishlistId);
        return back()->with('message', 'Item removed from wishlist!');
    }

    private function clearWishlist()
    {
        $user_id = Auth::id();
        Wishlist::where('user_id', $user_id)->delete();
        return redirect()->route('wishlist.index')->with('message', 'All items removed from wishlist!');
    }

    private function removeFromCart($cartId)
    {
        Cart::findOrFail($cartId)->delete();
        return back()->with('message', 'Item removed from cart!');
    }

    private function clearCart()
    {
        Cart::where('user_id', Auth::id())->delete();
        return back()->with('message', 'All items removed from cart!');
    }
    private function updateCart(Request $request) 
    {
        $cartItem = Cart::findOrFail($request->cart_id);
        $cartItem->quantity = $request->qty;
        $cartItem->save();
        return back()->with('message', 'Cart updated successfully!');
    }

    // public function checkoutSelected(Request $request)
    // {
    //     $selectedItems = $request->input('selected_items', []);
        
    //     if (empty($selectedItems)) {
    //         return back()->with('message', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán.');
    //     }

    //     $user_id = Auth::id();
    //     $cartItems = Cart::whereIn('id', $selectedItems)->where('user_id', $user_id)->get();

    //     // Lấy thông tin tổng cộng và chuyển sang trang thanh toán
    //     $grandTotal = $cartItems->sum(function ($item) {
    //         return $item->price * $item->quantity;
    //     });

    //     return view('checkout', compact('cartItems', 'grandTotal'));
    // }


    public function removeSelectedItems(Request $request)
    {
        $user = Auth::user();
        
        // Lấy các ID sản phẩm đã chọn từ request
        $selectedItems = $request->input('selected_items');
        
        // Xóa các sản phẩm trong giỏ hàng của người dùng
        Cart::where('user_id', $user->id)
            ->whereIn('id', $selectedItems)
            ->delete();

        return redirect()->route('cart.index')->with('message', 'Đã xóa các sản phẩm đã chọn!');
    }

}
