<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/guest/checkout.css">
    <script src="https://code.jquery.com/jquery-3.6.4.js"></script>
</head>

<body>

    @include('components.user_header')
    <section class="checkout-orders">
        @if (session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif
        <form action="{{ route('checkout.payment.post') }}" method="POST" id="formPlaceOrder">
            @csrf
            @foreach ($cartItemsWP as $item)
                <input type="hidden" name="idCart[]" value="{{ $item->id }}">
            @endforeach

            <input type="hidden" name="total_products" value="{{ $totalProducts ?? '' }}">
            <input type="hidden" name="total_price" value="{{ $grandTotal ?? 0 }}">
            <input type="hidden" name="method" value="cash on delivery">

            <div class="cartcard checkout-card">
                <div class="cartcard-head">
                    <div class="cartcard-head__title checkout-title">Thông tin đặt hàng</div>
                </div>
                <div class="checkout-form">
                    <div class="ck-row ck-1">
                        <label class="ck-field">
                            <span>Họ và tên <b class="req">*</b></span>
                            <input type="text" name="name" class="ck-input" placeholder="Nhập họ và tên"
                                value="{{ old('name', $form['name'] ?? '') }}" required>
                        </label>
                    </div>
                    <div class="ck-row ck-2">
                        <label class="ck-field">
                            <span>Số điện thoại <b class="req">*</b></span>
                            <input type="text" name="number" class="ck-input" placeholder="Nhập số điện thoại"
                                value="{{ old('number', $form['number'] ?? '') }}" required>
                        </label>
                        <label class="ck-field">
                            <span>Email</span>
                            <input type="email" name="email" class="ck-input" placeholder="Nhập email"
                                value="{{ old('email', $form['email'] ?? '') }}">
                        </label>
                    </div>
                    <div class="ck-row ck-1">
                        <label class="ck-field">
                            <span>Địa chỉ <b class="req">*</b></span>
                            <input type="text" name="flat" class="ck-input" placeholder="Số nhà, tên đường"
                                value="{{ old('flat', $form['flat'] ?? '') }}" required>
                        </label>
                    </div>
                    <div class="ck-row ck-3">
                        <label class="ck-field">
                            <span>Tỉnh/Thành phố <b class="req">*</b></span>
                            <select id="province" name="province" class="ck-input"
                                data-district-url="{{ url('/get-districts') }}" required>
                                <option value="">Chọn tỉnh/thành</option>
                                @foreach ($province as $provinces)
                                    <option value="{{ $provinces->province_id }}"
                                        {{ old('province', $form['province'] ?? '') == $provinces->province_id ? 'selected' : '' }}>
                                        {{ $provinces->name }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                        <label class="ck-field">
                            <span>Quận/Huyện <b class="req">*</b></span>
                            <select id="district" name="district" class="ck-input"
                                data-old="{{ old('district', $form['district'] ?? '') }}"
                                data-ward-url="{{ url('/get-wards') }}" disabled required>
                                <option value="">Chọn quận/huyện</option>
                            </select>
                        </label>
                        <label class="ck-field">
                            <span>Phường/Xã <b class="req">*</b></span>
                            <select id="ward" name="ward" class="ck-input"
                                data-old="{{ old('ward', $form['ward'] ?? '') }}" disabled required>
                                <option value="">Chọn phường/xã</option>
                            </select>
                        </label>
                    </div>
                    <div class="ck-row ck-1">
                        <label class="ck-field">
                            <span>Ghi chú đơn hàng</span>
                            <textarea name="note" class="ck-textarea"
                                placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay chỉ dẫn địa điểm giao hàng chi tiết hơn">{{ old('note', $form['note'] ?? '') }}</textarea>
                        </label>
                    </div>

                    <div class="ck-actions">
                        <a href="{{ route('checkout.info') }}" class="ck-btn ghost">Quay lại</a>
                        <button type="submit" class="ck-btn primary">Tiếp tục</button>
                    </div>
                </div>

            </div>
        </form>
        @include('components.cart_summary', [
            'grandTotal' => $grandTotal,
            'vatRate' => 0,
            'shipping' => 0,
        ])
    </section>

    @include('components.footer')
    <script src="{{ asset('assets') }}/js/guest/checkout_wizard.js"></script>
    <script>
        window.CART_ROUTES = {
            checkoutSelected: "{{ route('checkout.selected') }}",
            removeSelected: "{{ route('remove.selected') }}",
            updateQty: "{{ route('cart.update') }}",
        };
    </script>
</body>

</html>
