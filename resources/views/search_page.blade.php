<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Search page</title>

   <!-- font awesome cdn link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link -->
   <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">
   <style>
      .products .box-container .box .fa-eye {
         position: absolute;
         top: 1rem;
         height: 4.5rem;
         width: 4.5rem;
         line-height: 4.2rem;
         font-size: 2rem;
         background-color: var(--white);
         border: var(--border);
         border-radius: 0.5rem;
         text-align: center;
         color: var(--black);
         cursor: pointer;
         transition: 0.2s linear;
      }

      .products .box-container .box .fa-eye {
         left: -6rem;
      }

      .products .box-container .box .fa-eye:hover {
         background-color: var(--black);
         color: var(--white);
      }

      .products .box-container .box:hover .fa-eye {
         left: 1rem;
      }
   </style>

</head>
<body>
   
@include('components.user_header')

<section class="search-form">
   <form action="{{ route('search.results') }}" method="post">
      @csrf
      <input type="text" name="search_box" placeholder="search here..." maxlength="100" class="box" required>
      <button type="submit" class="fas fa-search"></button>
   </form>
</section>

<section class="products" style="padding-top: 0; min-height:100vh;">
   <div class="box-container">
      @if(isset($products) && $products->isNotEmpty())
         @foreach($products as $product)
            <form action="{{ route('add.to.wishlist.or.cart') }}" method="post" class="box">
               @csrf
               <input type="hidden" name="pid" value="{{ $product->id }}">
               <input type="hidden" name="name" value="{{ $product->name }}">
               <input type="hidden" name="price" value="{{ $product->price }}">
               <input type="hidden" name="image" value="{{ $product->image_01 }}">
               
               <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
               <a href="{{ route('quick.view', ['pid' => $product->id]) }}" class="fas fa-eye"></a>
               <img src="{{ asset('storage/' . $product->image_01) }}" alt="">
               <div class="name">{{ $product->name }}</div>
               <div class="flex">
                  <div class="price"><span></span>{{ number_format($product->price * (1 - $product->discount / 100), 0, ',', '.') }}<span> x</span></div>
                  <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="1">
               </div>
               <input type="submit" value="add to cart" class="btn" name="add_to_cart">
            </form>
         @endforeach
      @else
         <p class="empty">Không tìm thấy sản phẩm nào!</p>
      @endif
   </div>
</section>


@include('components.footer')

<script src="{{ asset('assets') }}/js/script.js"></script>

</body>
</html>
