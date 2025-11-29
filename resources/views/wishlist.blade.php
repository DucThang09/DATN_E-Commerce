<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Wishlist</title>
   
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">

   <style>
        .products .box .fa-eye {
            position: absolute;
            top: 2rem;
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

        .products .box .fa-eye {
            right: -6rem;
        }

        .products .box .fa-eye:hover {
            background-color: var(--black);
            color: var(--white);
        }

        .products .box:hover .fa-eye {
            right: 1rem;
        }
   </style>

</head>
<body>
   
   @include('components.user_header')

   <section class="products">
       <h3 class="heading">Danh sách yêu thích.</h3>
       <div class="box-container">  
           @php
               $grand_total = 0;
           @endphp
           @if($wishlistItems->isNotEmpty())
               @foreach($wishlistItems as $item)
                   @php
                       $grand_total += $item->price;
                   @endphp
                   <form action="{{ route('add.to.wishlist.or.cart') }}" method="post" class="box">
                    @csrf
                    <input type="hidden" name="wishlist_id" value="{{ $item->id }}">
                    <input type="hidden" name="pid" value="{{ $item->pid }}">
                    <input type="hidden" name="name" value="{{ $item->name }}">
                    <input type="hidden" name="price" value="{{ $item->price * (1 - $item->discount / 100)}}">
                    <input type="hidden" name="image" value="{{ $item->image }}">
                    
                    <a href="{{ route('quick.view', ['pid' => $item->pid]) }}" class="fas fa-eye"></a>
                    <img src="{{ asset('storage/' . $item->image) }}" alt="">
                    <div class="name">{{ $item->name }}</div>
                    <div class="flex">
                        <div class="price">{{ number_format($item->price  * (1 - $item->discount / 100), 0, ',','.') }}đ <span> x </span></div>
                        <input type="number" name="qty" class="qty" min="1" max="99" value="1" onkeypress="if(this.value.length == 2) return false;">
                    </div>
                    <input type="submit" value="Thêm vào giỏ hàng" class="btn" name="add_to_cart">
                    <button type="submit" class="delete-btn" name="delete_item" onclick="return confirm('Delete this from wishlist?');">Xóa sản phẩm</button>
                </form>
                
               @endforeach
           @else
               <p class="empty">Trống</p>
           @endif
       </div>
       
       <div class="wishlist-total">
           <p>Tổng tiền : <span>{{ number_format($grand_total, 0, ',','.') }}đ</span></p>
           <a href="{{ route('home') }}" class="option-btn">Tiếp tục mua sắm.</a>
           <form action="{{ route('add.to.wishlist.or.cart') }}" method="post" style="display:inline;">
            @csrf
            <input type="hidden" name="clear_wishlist" value="1"> 
            <button type="submit" class="delete-btn {{ $grand_total > 1 ? '' : 'disabled' }}" onclick="return confirm('Delete all from wishlist?');">Xóa tất cả </button>
        </form>
       </div>
   </section>

   @include('components.footer')

<script src="{{ asset('assets') }}/js/script.js"></script>

</body>
</html>