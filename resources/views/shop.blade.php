<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Shop</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">

</head>
<body>
   
   @include('components.user_header')


<section class="home-products">
   <h1 class="heading">Điện thoại</h1>
   <div class="swiper products-slider">
      <div class="swiper-wrapper">
         @if($phones->count() > 0)
            @foreach($phones as $product)
               <form action="{{ route('add.to.wishlist.or.cart') }}" method="post" class="swiper-slide slide" >
                  @csrf
                  <input type="hidden" name="pid" value="{{ $product->id }}">
                  <input type="hidden" name="name" value="{{ $product->name }}">
                  <input type="hidden" name="price" value="{{ $product->price }}">
                  <input type="hidden" name="image" value="{{ $product->image_01 }}">

                  <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
                  <img src="{{ asset('storage/' . $product->image_01) }}" alt="{{ $product->name }}" onclick="window.location.href='{{ route('quick.view', ['pid' => $product->id]) }}'; return false;"
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
            <p class="empty">Chưa có sản phẩm nào được thêm vào!</p>
         @endif
      </div>
      <div class="swiper-pagination"></div>
   </div>
</section>



<section class="home-products">
   <h1 class="heading">Tai nghe</h1>
   <div class="swiper products-slider">
      <div class="swiper-wrapper">
         @if($headphones->count() > 0)
            @foreach($headphones as $product)
               <form action="{{ route('add.to.wishlist.or.cart') }}" method="post" class="swiper-slide slide" onclick="window.location.href='{{ route('quick.view', ['pid' => $product->id]) }}'; return false;"
               style="cursor: pointer;">
                  @csrf
                  <input type="hidden" name="pid" value="{{ $product->id }}">
                  <input type="hidden" name="name" value="{{ $product->name }}">
                  <input type="hidden" name="price" value="{{ $product->price }}">
                  <input type="hidden" name="image" value="{{ $product->image_01 }}">

                  <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
                  <img src="{{ asset('storage/' . $product->image_01) }}" alt="{{ $product->name }}" onclick="window.location.href='{{ route('quick.view', ['pid' => $product->id]) }}'; return false;"
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
            <p class="empty">Chưa có sản phẩm nào được thêm vào!</p>
         @endif
      </div>
      <div class="swiper-pagination"></div>
   </div>
</section>

<section class="home-products">
   <h1 class="heading">Sạc dự phòng điện thoại</h1>
   <div class="swiper products-slider">
      <div class="swiper-wrapper">
         @if($powerbank->count() > 0)
            @foreach($powerbank as $product)
               <form action="{{ route('add.to.wishlist.or.cart') }}" method="post" class="swiper-slide slide" onclick="window.location.href='{{ route('quick.view', ['pid' => $product->id]) }}'; return false;"
               style="cursor: pointer;">
                  @csrf
                  <input type="hidden" name="pid" value="{{ $product->id }}">
                  <input type="hidden" name="name" value="{{ $product->name }}">
                  <input type="hidden" name="price" value="{{ $product->price }}">
                  <input type="hidden" name="image" value="{{ $product->image_01 }}">

                  <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
                  <img src="{{ asset('storage/' . $product->image_01) }}" alt="{{ $product->name }}"  onclick="window.location.href='{{ route('quick.view', ['pid' => $product->id]) }}'; return false;"
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
            <p class="empty">Chưa có sản phẩm nào được thêm vào!</p>
         @endif
      </div>
      <div class="swiper-pagination"></div>
   </div>
</section>

<section class="home-products">
   <h1 class="heading">Dây sạc điện thoại</h1>
   <div class="swiper products-slider">
      <div class="swiper-wrapper">
         @if($chargers->count() > 0)
            @foreach($chargers as $product)
               <form action="{{ route('add.to.wishlist.or.cart') }}" method="post" class="swiper-slide slide" onclick="window.location.href='{{ route('quick.view', ['pid' => $product->id]) }}'; return false;"
               style="cursor: pointer;">
                  @csrf
                  <input type="hidden" name="pid" value="{{ $product->id }}">
                  <input type="hidden" name="name" value="{{ $product->name }}">
                  <input type="hidden" name="price" value="{{ $product->price }}">
                  <input type="hidden" name="image" value="{{ $product->image_01 }}">

                  <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
                  <img src="{{ asset('storage/' . $product->image_01) }}" alt="{{ $product->name }}">
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
            <p class="empty">Chưa có sản phẩm nào được thêm vào!</p>
         @endif
      </div>
      <div class="swiper-pagination"></div>
   </div>
</section>



   @include('components.footer')

<script src="{{ asset('assets') }}/js/script.js"></script>
<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>


<script>

var swiper = new Swiper(".products-slider", {
   loop:false,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
   breakpoints: {
      550: {
        slidesPerView: 2,
      },
      768: {
        slidesPerView: 2,
      },
      1024: {
        slidesPerView: 5
      },
   },
});

</script>
</body>
</html>