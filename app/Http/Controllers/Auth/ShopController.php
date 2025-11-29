<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
class ShopController extends Controller
{
    public function ShowShop()
    {
         
        $products = Product::latest()->limit(6)->get();
        $phones = Product::where('category', 'Điện thoại')->get();
        $headphones = Product::where('category', 'Tai nghe')->get();
        $powerbank = Product::where('category', 'Sạc dự phòng')->get();
        $chargers = Product::where('category', 'Sạc')->get();

        return view('shop', compact( 'products', 'phones', 'headphones','powerbank', 'chargers'));
    }

}
