<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/guest/checkout.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/guest/payment.css">
</head>

<body>
    @include('components.user_header')

    <section class="checkout-orders">
        <form action="{{ route('checkout.placeOrder') }}" method="POST" class="checkout-card">
            @csrf
            @foreach ($payload['idCart'] ?? [] as $cid)
                <input type="hidden" name="idCart[]" value="{{ $cid }}">
            @endforeach
            <input type="hidden" name="name" value="{{ $payload['name'] ?? '' }}">
            <input type="hidden" name="number" value="{{ $payload['number'] ?? '' }}">
            <input type="hidden" name="email" value="{{ $payload['email'] ?? '' }}">
            <input type="hidden" name="flat" value="{{ $payload['flat'] ?? '' }}">
            <input type="hidden" name="province" value="{{ $payload['province'] ?? '' }}">
            <input type="hidden" name="district" value="{{ $payload['district'] ?? '' }}">
            <input type="hidden" name="ward" value="{{ $payload['ward'] ?? '' }}">
            <input type="hidden" name="note" value="{{ $payload['note'] ?? '' }}">

            <div class="cartcard-head">
                <div class="cartcard-head__title">Phương thức thanh toán</div>
            </div>

            <div class="checkout-form">

                <label class="pay-item">
                    <input type="radio" name="method" value="cash on delivery" checked>
                    <div class="pay-box">
                        <div class="pay-top">
                            <i class="fa-solid fa-coins"></i>
                            <strong>Thanh toán khi nhận hàng (COD)</strong>
                        </div>
                        <div class="pay-sub">Thanh toán bằng tiền mặt khi nhận hàng</div>
                    </div>
                </label>

                <label class="pay-item">
                    <input type="radio" name="method" value="bank transfer">
                    <div class="pay-box">
                        <div class="pay-top">
                            <i class="fa-solid fa-building-columns"></i>
                            <strong>Chuyển khoản ngân hàng</strong>
                        </div>
                        <div class="pay-sub">Chuyển khoản qua tài khoản ngân hàng</div>
                    </div>
                </label>

                <label class="pay-item">
                    <input type="radio" name="method" value="momo">
                    <div class="pay-box">
                        <div class="pay-top">
                            <i class="fa-solid fa-wallet"></i>
                            <strong>Ví điện tử MoMo</strong>
                        </div>
                        <div class="pay-sub">Thanh toán qua ví MoMo</div>
                    </div>
                </label>

                <label class="pay-item">
                    <input type="radio" name="method" value="card">
                    <div class="pay-box">
                        <div class="pay-top">
                            <i class="fa-regular fa-credit-card"></i>
                            <strong>Thẻ tín dụng/Ghi nợ</strong>
                        </div>
                        <div class="pay-sub">Visa, Mastercard, JCB</div>
                    </div>
                </label>

                <label class="pay-item">
                    <input type="radio" name="method" value="paypal">
                    <div class="pay-box">
                        <div class="pay-top">
                            <i class="fa-brands fa-paypal"></i>
                            <strong>PayPal</strong>
                        </div>
                        <div class="pay-sub">Thanh toán an toàn qua PayPal</div>
                    </div>
                </label>

                <div class="ck-actions">
                    <a href="{{ route('checkout.info') }}" class="ck-btn ghost">Quay lại</a>
                    <button type="submit" class="ck-btn primary">Xác nhận</button>
                </div>
            </div>
        </form>
        @include('components.cart_summary', [
            'grand_total' => $subTotal,
            'vatRate' => 0,
            'shipping' => $shipping,
        ])
    </section>

    @include('components.footer')
</body>

</html>
