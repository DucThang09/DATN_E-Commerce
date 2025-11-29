<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>{{ ucfirst($company) }} - Products</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">

</head>
<body>
   
   @include('components.user_header')


<section class="products">

<h1 class="heading">Sản phẩm mới nhất.</h1>

<div class="box-container">
@if($products->count() > 0)
      @foreach($products as $product)
      <form action="{{ route('add.to.wishlist.or.cart') }}" method="post" class="box" >
               @csrf
               <input type="hidden" name="pid" value="{{ $product->id }}">
               <input type="hidden" name="name" value="{{ $product->name }}">
               <input type="hidden" name="price" value="{{ $product->price }}">
               <input type="hidden" name="image" value="{{ $product->image_01 }}">
               
               <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
               <img src="{{ asset('storage/' . $product->image_01) }}" alt="" onclick="window.location.href='{{ route('quick.view', ['pid' => $product->id]) }}'; return false;"
               style="cursor: pointer;">
               <div class="name">{{ $product->name }}</div>
               <div class="original-price"><span></span>{{ $product->price }}<span>đ</span></div>      
               <div class="flex">
                  <div class="discount-price"><span></span>{{ $product->price  * (1 - $product->discount / 100) }}<span>đ</span></div>    
                  <div class="inventory">
                     {{ $product->inventory > 0 ? 'Còn hàng' : 'Hết hàng' }}
                  </div>                                     
               </div>
               <div class="discount-label">Giảm {{ $product->discount }}%</div>

               <input type="submit" value="Thêm vào giỏ hàng" class="btn" name="add_to_cart">
            </form>
      @endforeach
   @else
      <p class="empty">Chưa có sản phẩm nào cho {{ ucfirst($company) }}!</p>
   @endif
</div>

</section>



@include('components.footer')

<script src="{{ asset('assets') }}/js/script.js"></script>

</body>
</html>
