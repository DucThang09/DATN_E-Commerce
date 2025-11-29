<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class OrderUserController extends Controller
{
    public function showOrders()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('message', 'Please login to see your orders');
        }

        $user_id = Auth::id();
        $orders = Order::where('user_id', $user_id)->get();

        if ($orders->isEmpty()) {
            return view('orders', compact('orders'))->with('message', 'No orders placed yet!');
        }

        return view('orders', compact('orders'));
    }
}
