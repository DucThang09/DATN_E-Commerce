<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Giỏ hàng</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">
   <link rel="stylesheet" href="{{ asset('assets') }}/css/noti.css">

</head>
<body>
   
   @include('components.user_header')

   @if(session('message'))
        <div class="alert alert-success">
            <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
            {{ session('message') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="products shopping-cart">
        <h3 class="heading">Giỏ hàng</h3>

        

        <div class="box-container">
            @php
                $grand_total = 0;
            @endphp

            @if($cartItemsWP->isNotEmpty())
                @foreach($cartItemsWP as $item)
                    @php
                        $sub_total = $item->price * $item->quantity;
                        $grand_total += $sub_total;
                    @endphp
                    <div class="box" style="display: inline;">
                        <input type="checkbox" name="selected_items[]" value="{{ $item->id }}" class="select-item">
                        <input type="hidden" name="cart_id" value="{{ $item->id }}">
                        <input type="hidden" name="pid" value="{{ $item->pid }}"> 
                        <input type="hidden" name="name" value="{{ $item->name }}">
                        <input type="hidden" name="price" value="{{ $item->price }}">
                        <input type="hidden" name="image" value="{{ $item->image }}">
                        <!-- Các thông tin sản phẩm -->
                        <img src="{{ asset('storage/' . $item->image) }}" alt="">
                        <div class="name">{{ $item->name }}</div>
                        <div class="flex">
                            <div class="price">{{ number_format($item->price, 0, ',', '.') }}đ  <span>x</span></div>
                            <input type="number" name="qty" class="qty" min="1" max="99" value="{{ $item->quantity }}" onkeypress="if(this.value.length == 2) return false;">
                            
                        </div>
                        <div class="sub-total">Tổng : <span>{{ number_format($sub_total, 0, ',', '.') }}đ</span></div>
                    </div>
                @endforeach
            @else
                <p class="empty">Giỏ hàng của bạn đang trống</p>
            @endif
        </div>

        <div class="cart-total">
           <p>Tổng tiền : <span>{{ number_format($grand_total, 0, ',', '.') }}đ</span></p>
           <a href="" class="option-btn">Tiếp tục mua sắm</a>
           
            <button type="button" class="delete-btn" onclick="removeSelectedItems()">Xóa các sản phẩm đã chọn</button>
            <button type="button" class="btn" onclick="checkoutSelectedItems()">Tiến hành thanh toán các sản phẩm đã chọn</button>
            
       </div>

    </section>

    


   @include('components.footer')

<script src="{{ asset('assets') }}/js/script.js"></script>

<script>
    function checkoutSelectedItems() {
        // Lấy tất cả checkbox của sản phẩm đã chọn
        const selectedItems = Array.from(document.querySelectorAll('.select-item:checked')).map(item => item.value);

        // Kiểm tra nếu không có sản phẩm nào được chọn
        if (selectedItems.length === 0) {
            alert("Vui lòng chọn ít nhất một sản phẩm để thanh toán.");
            return;
        }

        // Tạo form để gửi dữ liệu
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('checkout.selected') }}";

        // Thêm CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = "{{ csrf_token() }}";
        form.appendChild(csrfInput);

        // Thêm ID của các sản phẩm đã chọn vào form
        selectedItems.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_items[]';
            input.value = id;
            form.appendChild(input);
        });

        // Thêm form vào body và submit
        document.body.appendChild(form);
        form.submit();
    }
</script>
<script>
    function removeSelectedItems() {
        // Lấy tất cả checkbox của sản phẩm đã chọn
        const selectedItems = Array.from(document.querySelectorAll('.select-item:checked')).map(item => item.value);

        // Kiểm tra nếu không có sản phẩm nào được chọn
        if (selectedItems.length === 0) {
            alert("Vui lòng chọn ít nhất một sản phẩm để xóa.");
            return;
        }

        // Tạo form để gửi dữ liệu
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('remove.selected') }}";

        // Thêm CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = "{{ csrf_token() }}";
        form.appendChild(csrfInput);

        // Thêm ID của các sản phẩm đã chọn vào form
        selectedItems.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_items[]';
            input.value = id;
            form.appendChild(input);
        });

        // Thêm form vào body và submit
        document.body.appendChild(form);
        form.submit();
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = `${message}<span class="close-btn" onclick="closeToast(this)">×</span>`;
        document.body.appendChild(toast);
        
        // Sau khi thông báo biến mất, xóa nó
        setTimeout(() => {
            toast.remove();
        }, 5000); // Thời gian thông báo xuất hiện và biến mất (5s)
    }

    function closeToast(element) {
        const toast = element.parentElement;
        toast.remove();
    }


</script>



</body>
</html>