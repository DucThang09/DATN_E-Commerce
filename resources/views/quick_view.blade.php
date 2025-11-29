<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Quick view</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">

</head>
<body>
   
   @include('components.user_header')

<section class="quick-view">
   <h1 class="heading">Xem sản phẩm</h1>

   @if($product)
   <form action="{{ route('add.to.wishlist.or.cart') }}" method="post" class="box">
       @csrf
       <input type="hidden" name="pid" value="{{ $product->id }}">
       <input type="hidden" name="name" value="{{ $product->name }}">
       <input type="hidden" name="price" value="{{ $product->price }}">
       <input type="hidden" name="category" value="{{ $product->category }}">
       <input type="hidden" name="company" value="{{ $product->company }}">
       <input type="hidden" name="color" value="{{ $product->color }}">
       <input type="hidden" name="image" value="{{ $product->image_01 }}">

       <div class="row">
           <div class="image-container">
               <div class="main-image">
                   <img src="{{ asset('storage/' . $product->image_01) }}" alt="">
               </div>
               <div class="sub-image">
                   <img src="{{ asset('storage/' . $product->image_01) }}" alt="">
                   <img src="{{ asset('storage/' . $product->image_02) }}" alt="">
                   <img src="{{ asset('storage/' . $product->image_03) }}" alt="">
               </div>
           </div>
           <div class="content">
                <!-- Hiển thị tên sản phẩm -->
                <div class="name" title="{{ $product->name }}">
                    <strong>Tên sản phẩm:</strong> {{ $product->name }}
                </div>

                <!-- Hiển thị giá sản phẩm -->
                 <div class="price">
                    <span class="price-title">Giá gốc: </span>    
                    <div class="original-price"><span></span>{{ number_format($product->price, 0, ',','.') }}<span>đ</span></div>  
                 </div>
                    
                <div class="flex">
                    <div class="discount-price">
                    <!-- {{ number_format($product->purchase_price, 0, ',', '.') }} đ -->
                        <strong>Giá mới:</strong> <span></span>{{ number_format($product->price  * (1 - $product->discount / 100), 0,',',',') }}<span>đ</span>
                    </div>
                </div>
                <div class="qty">
                    <label for="qty">Số Lượng</label>
                    <div class="qty-input">
                        <button type="button" onclick="changeQuantity(-1)">−</button>
                        <input type="number" id="qty" name="qty" value="1" min="1" max="99" readonly>
                        <button type="button" onclick="changeQuantity(1)">+</button>
                    </div>
                </div>

                <!-- Hiển thị chi tiết sản phẩm -->
                <div class="details">
                    <strong>Chi tiết:</strong> {{ $product->details }}
                </div>

                <!-- Hiển thị danh mục sản phẩm -->
                <div class="category">
                    <strong>Danh mục:</strong> {{ $product->category }}
                </div>

                <!-- Hiển thị tên công ty -->
                <div class="company">
                    <strong>Công ty:</strong> {{ $product->company }}
                </div>

                <!-- Hiển thị màu sắc sản phẩm -->
                <div class="color">
                    <strong>Màu sắc:</strong> {{ $product->color }}
                </div>

                <!-- Các nút thêm vào giỏ hàng và yêu thích -->
                <div class="flex-btn">
                    <input type="submit" value="Thêm vào giỏ hàng" class="btn" name="add_to_cart">
                    <input type="submit" class="option-btn" name="add_to_wishlist" value="Thêm vào danh sách yêu thích">
                </div>
            </div>

       </div>
   </form>
   @else
   <p class="empty">Không tìm thấy sản phẩm nào!</p>
   @endif
</section>

@include('components.footer')

<script src="{{ asset('assets') }}/js/script.js"></script>
<script>
    function changeQuantity(delta) {
        const input = document.getElementById('qty');
        let value = parseInt(input.value) || 1;
        value += delta;
        
        if (value >= 1 && value <= 99) {
            input.value = value;
        }
    }
</script>
</body>
</html>