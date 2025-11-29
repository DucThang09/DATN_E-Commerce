<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Checkout</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">
   <script src="https://code.jquery.com/jquery-3.6.4.js"></script>
</head>
<body>
   
@include('components.user_header')  
<section class="checkout-orders">
   @if (session('message'))
      <div class="alert alert-success">{{ session('message') }}</div>
   @endif
   <form action="{{ route('checkout.placeOrder') }}" method="POST">
      @csrf

      <h3>Đơn Hàng Của Bạn</h3>

      <div class="display-orders">
         

         @if ($cartItemsWP->isNotEmpty())
            @foreach ($cartItemsWP as $item)
               
               <p>{{ $item->name }} <span>({{ number_format($item->price, 0,',','.') . 'đ x ' . $item->quantity }})</span></p>
               <input type="hidden" name="idCart[]" value="{{ $item->id }}">
            @endforeach
         @else
            <p class="empty">Giỏ hàng của bạn đang trống!</p>
         @endif

         <input type="hidden" name="total_products" value="{{ $totalProducts }}">
         
         <input type="hidden" name="total_price" value="{{ $grandTotal }}">
         <div class="grand-total">Tổng Cộng : <span>{{ number_format($grandTotal, 0,',','.') . 'đ'}}</span></div>
      </div>

      <h3>Đặt hàng của bạn</h3>

      <div class="flex">
         <div class="inputBox">
            <span>Tên của bạn:</span>
            <input type="text" name="name" placeholder="Nhập tên của bạn" class="box" maxlength="20" required>
         </div>
         <div class="inputBox">
            <span>Số điện thoại :</span>
            <input type="number" name="number" placeholder="Nhập số điện thoại" class="box" min="0" max="9999999999" required>
         </div>
         <div class="inputBox">
            <span>Email:</span>
            <input type="email" name="email" placeholder="Nhập email" class="box" maxlength="50">
         </div>
         <div class="inputBox">
            <span>Phương thức thanh toán:</span>
            <select name="method" class="box" required>
               <option value="cash on delivery">Thanh toán khi nhận hàng</option>
               <option value="credit card">Thanh toán bằng thẻ tín dụng</option>
               
            </select>
         </div>
         <div class="inputBox">
            <span>Tỉnh thành:</span>
            <select class="box" id="province" name="province" >
               <option value="">Chọn Tỉnh Thành</option>
               @foreach ($province as $provinces)
                  <option value="{{ $provinces->province_id }}">{{ $provinces->name }}</option>
               @endforeach
               
            </select>
         </div>
         <div class="inputBox">
            <span>Quận Huyện:</span>
            <select name="district" id="district" class="box" >
               <option value="">Chọn Quận Huyện</option>
               
               
            </select>
         </div>
         <div class="inputBox">
            <span>Xã/phường:</span>
            <input type="text" name="ward" placeholder="Nhập xã/phường" class="box" maxlength="50" required>
         </div>
         
         <div class="inputBox">
            <span>Tên đường, số nhà:</span>
            <input type="text" name="flat" placeholder="Nhập tên đường và số nhà" class="box" maxlength="50" required>
         </div>
      </div>

      <input type="submit" name="order" class="btn {{ $grandTotal > 0 ? '' : 'disabled' }}" value="Đặt hàng">
   </form>
</section>


@include('components.footer')

<script src="{{ asset('assets') }}/js/script.js"></script>

<script>
$(document).ready(function() {
// Khi thay đổi tỉnh/thành phố
   $('#province').on('change', function() {
      var province_id = $(this).val();
      if (province_id) {
            $.ajax({
               url: '/get-districts',
               method: 'GET',
               data: { province_id: province_id },
               success: function(data) {
                  $('#district').empty().append('<option value="">Chọn một quận/huyện</option>');
                  $.each(data, function(i, district) {
                        $('#district').append($('<option>', {
                           value: district.id,
                           text: district.name
                        }));
                  });
                  $('#wards').empty().append('<option value="">Chọn một xã</option>');
               },
               error: function(xhr, textStatus, errorThrown) {
                  console.log('Error: ' + errorThrown);
               }
            });
      } else {
            $('#district').empty().append('<option value="">Chọn một quận/huyện</option>');
            $('#wards').empty().append('<option value="">Chọn một xã</option>');
      }
   });

   
   
});

</script>

<script>
$(document).ready(function() {
    $('form').on('submit', function(e) {
        var paymentMethod = $("select[name='method']").val();
        
        if (paymentMethod !== 'cash on delivery') {
            e.preventDefault();
            alert('Chức năng này đang được phát triển. Vui lòng chọn "Thanh toán khi nhận hàng".');
        }
    });
});
</script>

</body>
</html>
