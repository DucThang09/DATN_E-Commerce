<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Orders</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">
</head>
<body>

@include('components.user_header')

<section class="orders">
   <h1 class="heading">Đơn hàng đã đặt</h1>

   <div class="box-container">
      @if(session('message'))
         <p class="empty">{{ session('message') }}</p>
      @else
         @forelse($orders as $order)
            <div class="box">
               <p>Ngày đặt hàng : <span>{{ $order->placed_on }}</span></p>
               <p>Họ và tên : <span>{{ $order->name }}</span></p>
               <p>Email : <span>{{ $order->email }}</span></p>
               <p>Số điện thoại : <span>{{ $order->number }}</span></p>
               <p>Địa chỉ : <span>{{ $order->address }}</span></p>
               <p>Phương thức thanh toán: <span>{{ $order->method }}</span></p>
               <p>Sản phẩm : <span>{{ $order->total_products }}</span></p>
               <p>Thành tiền : <span>{{ $order->total_price }}</span></p>
               <p>Trạng thái : 
                  <span style="color:{{ $order->payment_status == 'pending' ? 'red' : 'green' }}">
                     {{ $order->payment_status }}
                  </span>
               </p>
            </div>
         @empty
            <p class="empty">Chưa có đơn hàng nào được đặt!</p>
         @endforelse
      @endif
   </div>
</section>

@include('components.footer')

<script src="{{ asset('assets') }}/js/script.js"></script>

</body>
</html>
