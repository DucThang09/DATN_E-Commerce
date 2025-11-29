<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Banana</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">

</head>
<body>
   
   @include('components.user_header')

<div class="home-bg">
   <section class="home">
      <div class="swiper home-slider">
         <div class="swiper-wrapper">
            @if($products -> count() >0)
               @foreach($phones as $fetch_product)
                  <div class="swiper-slide slide" >
                     <div class="image">
                     <img src="{{ asset('storage/' . $fetch_product->image_01) }}" alt="{{ $fetch_product->name }}" onclick="window.location.href='{{ route('quick.view', ['pid' => $fetch_product->id]) }}'; return false;"
                     style="cursor: pointer;">>
                     </div>
                     <div class="content">
                     <h3>Điện thoại thông minh mới nhất</h3>
                        <input type="hidden" name="name"   value="{{ $fetch_product->name }}">
                        <div class="name" title="{{ $fetch_product->name }}" onclick="window.location.href='{{ route('quick.view', ['pid' => $fetch_product->id]) }}'; return false;"
                        style="cursor: pointer;">{{ $fetch_product->name }}</div>
                        <div class="discount-label">Giảm {{ $fetch_product->discount }}%</div>
                        <a href="{{ url('category?category=smartphone') }}" class="btn" onclick="window.location.href='{{ route('quick.view', ['pid' => $fetch_product->id]) }}'; return false;"
                        style="cursor: pointer;">Mua ngay</a>
                     </div>
                  </div>
                  @endforeach
            @else
               <p class="empty">Chưa có sản phẩm nào được thêm vào</p>
            @endif   
         </div>
         <div class="swiper-pagination"></div>
      </div>
   </section>
</div>

<section class="category">

   <h1 class="heading">Mua sắm theo danh mục</h1>

   <div class="swiper category-slider">

   <div class="swiper-wrapper">

      <a href="{{ route('products.company', ['company' => 'apple']) }}" class="swiper-slide slide">
         <img src="{{ asset('assets') }}/images/apple-icon-1.png" alt="Apple">
         <h3>Apple</h3>
      </a>

      <a href="{{ route('products.company', ['company' => 'samsung']) }}" class="swiper-slide slide">
         <img src="{{ asset('assets') }}/images/samsung-icon-1.png" alt="Samsung">
         <h3>Samsung</h3>
      </a>

      <a href="{{ route('products.company', ['company' => 'sony']) }}" class="swiper-slide slide">
         <img src="{{ asset('assets') }}/images/sony-icon-1.png" alt="Sony">
         <h3>Sony</h3>
      </a>

      <a href="{{ route('products.company', ['company' => 'xiaomi']) }}" class="swiper-slide slide">
         <img src="{{ asset('assets') }}/images/xiaomi-icon-1.png" alt="Xiaomi">
         <h3>Xiaomi</h3>
      </a>

      <a href="{{ route('products.company', ['company' => 'oppo']) }}" class="swiper-slide slide">
         <img src="{{ asset('assets') }}/images/oppo-icon-1.png" alt="Oppo">
         <h3>Oppo</h3>
      </a>

      <a href="{{ route('products.company', ['company' => 'vivo']) }}" class="swiper-slide slide">
         <img src="{{ asset('assets') }}/images/vivo-icon-1.png" alt="Vivo">
         <h3>Vivo</h3>
      </a>

      <a href="{{ route('products.company', ['company' => 'huawei']) }}" class="swiper-slide slide">
         <img src="{{ asset('assets') }}/images/huawei-icon-1.png" alt="Huawei">
         <h3>Huawei</h3>
      </a>

      <a href="{{ route('products.company', ['company' => 'realme']) }}" class="swiper-slide slide">
         <img src="{{ asset('assets') }}/images/realme-icon-1.png" alt="Realme">
         <h3>Realme</h3>
      </a>


   </div>

   <div class="swiper-pagination"></div>

   </div>

</section>

<section class="home-products">
   <h1 class="heading">Các sản phẩm bán chạy</h1>
   <div class="swiper products-slider">
      <div class="swiper-wrapper">
         @if($products->count() > 0)
            @foreach($products as $fetch_product)
               <form action="{{ route('add.to.wishlist.or.cart') }}" method="post" class="swiper-slide slide" >
                  @csrf
                  <input type="hidden" name="pid" value="{{ $fetch_product->id }}">
                  <input type="hidden" name="name"   value="{{ $fetch_product->name }}">
                  <input type="hidden" name="price" value="{{ $fetch_product->price * (1 - $fetch_product->discount / 100) }}">
                  <input type="hidden" name="image" value="{{ $fetch_product->image_01 }}">
                  <input type="hidden" name="discount" value="{{ $fetch_product->discount}}">

                  <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
                  <img src="{{ asset('storage/' . $fetch_product->image_01) }}" alt="{{ $fetch_product->name }}" onclick="window.location.href='{{ route('quick.view', ['pid' => $fetch_product->id]) }}'; return false;"
                     style="cursor: pointer;">
                  <div class="name" title="{{ $fetch_product->name }}">{{ $fetch_product->name }}</div>
                  <div class="original-price"><span></span>{{ number_format($fetch_product->price, 0, ',','.') }}<span></span>đ</div>     
                  <div class="flex"> 
                     <div class="discount-price"><span></span>{{ number_format($fetch_product->price  * (1 - $fetch_product->discount / 100), 0,',','.') }}<span>đ</span></div>  
                     <div class="inventory">
                        {{ $fetch_product->inventory > 0 ? 'Còn hàng' : 'Hết hàng' }}
                     </div>                  
                  </div>
                  <div class="discount-label">Giảm {{ $fetch_product->discount }}%</div>

                  <input type="submit" value="Thêm vào giỏ hàng" class="btn" name="add_to_cart">          
               </form>
            @endforeach
         @else
            <p class="empty">Chưa có sản phẩm nào được thêm vào</p>
         @endif
      </div>
      <div class="swiper-pagination"></div>
   </div>
</section>

<section class="home-products">
   <h1 class="heading">Điện Thoại</h1>
   <div class="swiper products-slider">
      <div class="swiper-wrapper">
         @if($phones->count() > 0)
            @foreach($phones as $product)
               <form action="{{ route('add.to.wishlist.or.cart') }}" method="post" class="swiper-slide slide" >
                  @csrf
                  <input type="hidden" name="pid" value="{{ $product->id }}">
                  <input type="hidden" name="name" value="{{ $product->name }}">
                  <input type="hidden" name="price" value="{{ $product->price * (1 - $product->discount / 100)}}">
                  <input type="hidden" name="image" value="{{ $product->image_01 }}">

                  <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
                  <img src="{{ asset('storage/' . $product->image_01) }}" alt="{{ $product->name }}" onclick="window.location.href='{{ route('quick.view', ['pid' => $product->id]) }}'; return false;"
                  style="cursor: pointer;">
                  <div class="name">{{ $product->name }}</div>
                  <div class="original-price"><span></span>{{ number_format($product->price, 0, ',','.') }}<span>đ</span></div>      
                  <div class="flex"> 
                     <div class="discount-price"><span></span>{{ number_format($product->price  * (1 - $product->discount / 100), 0,',','.') }}<span>đ</span></div>                         
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
               <form action="{{ route('add.to.wishlist.or.cart') }}" method="post" class="swiper-slide slide" >
                  @csrf
                  <input type="hidden" name="pid" value="{{ $product->id }}">
                  <input type="hidden" name="name" value="{{ $product->name }}">
                  <input type="hidden" name="price" value="{{ $product->price * (1 - $product->discount / 100)}}">
                  <input type="hidden" name="image" value="{{ $product->image_01 }}">

                  <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
                  <img src="{{ asset('storage/' . $product->image_01) }}" alt="{{ $product->name }}" onclick="window.location.href='{{ route('quick.view', ['pid' => $product->id]) }}'; return false;"
                  style="cursor: pointer;">
                  <div class="name">{{ $product->name }}</div>
                  <div class="original-price"><span></span>{{ number_format($product->price, 0, ',','.') }}<span>đ</span></div>      
                  <div class="flex"> 
                     <div class="discount-price"><span></span>{{ number_format($product->price  * (1 - $product->discount / 100), 0,',','.') }}<span>đ</span></div>                         
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
               <form action="{{ route('add.to.wishlist.or.cart') }}" method="post" class="swiper-slide slide" >
                  @csrf
                  <input type="hidden" name="pid" value="{{ $product->id }}">
                  <input type="hidden" name="name" value="{{ $product->name }}">
                  <input type="hidden" name="price" value="{{ $product->price * (1 - $product->discount / 100)}}">
                  <input type="hidden" name="image" value="{{ $product->image_01 }}">

                  <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
                  <img src="{{ asset('storage/' . $product->image_01) }}" alt="{{ $product->name }}"  onclick="window.location.href='{{ route('quick.view', ['pid' => $product->id]) }}'; return false;"
               style="cursor: pointer;">
                  <div class="name">{{ $product->name }}</div>
                  <div class="original-price"><span></span>{{ number_format($product->price, 0, ',','.') }}<span>đ</span></div>      
                  <div class="flex"> 
                     <div class="discount-price"><span></span>{{ number_format($product->price  * (1 - $product->discount / 100), 0,',','.') }}<span>đ</span></div>                         
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
               <form action="{{ route('add.to.wishlist.or.cart') }}" method="post" class="swiper-slide slide" >
                  @csrf
                  <input type="hidden" name="pid" value="{{ $product->id }}">
                  <input type="hidden" name="name" value="{{ $product->name }}">
                  <input type="hidden" name="price" value="{{ $product->price * (1 - $product->discount / 100)}}">
                  <input type="hidden" name="image" value="{{ $product->image_01 }}">

                  <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
                  <img src="{{ asset('storage/' . $product->image_01) }}" alt="{{ $product->name }}" onclick="window.location.href='{{ route('quick.view', ['pid' => $product->id]) }}'; return false;"
                  style="cursor: pointer;">
                  <div class="name">{{ $product->name }}</div>
                  <div class="original-price"><span></span>{{ number_format($product->price, 0, ',','.') }}<span>đ</span></div>      
                  <div class="flex"> 
                     <div class="discount-price"><span></span>{{ number_format($product->price  * (1 - $product->discount / 100), 0,',','.') }}<span>đ</span></div>                         
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

<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

<script src="{{ asset('assets') }}/js/script.js"></script>

<script>

var swiper = new Swiper(".home-slider", {
   loop:true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
    },
});

 var swiper = new Swiper(".category-slider", {
   loop:true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
   breakpoints: {
      0: {
         slidesPerView: 2,
       },
      650: {
        slidesPerView: 3,
      },
      768: {
        slidesPerView: 4,
      },
      1024: {
        slidesPerView: 5,
      },
   },
});

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
