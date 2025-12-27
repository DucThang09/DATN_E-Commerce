{{-- resources/views/components/cart_summary.blade.php --}}

@php
    // ✅ Nhận cả 2 kiểu tên biến: grandTotal (checkout) hoặc grand_total (cart)
    $subtotal = $grandTotal ?? $grand_total ?? 0;

    // Mặc định
    $vatRate  = $vatRate ?? 0;
    $shipping = $shipping ?? 0;

    // Tính luôn VAT + total để render ra UI (tránh bị 0 nếu JS không chạy)
    $vat   = (int) round($subtotal * $vatRate);
    $total = (int) ($subtotal + $vat + $shipping);
@endphp

<div class="cartcard cartsummary"
    data-subtotal="{{ (int) $subtotal }}"
    data-vat-rate="{{ $vatRate }}"
    data-shipping="{{ (int) $shipping }}"
>
    <div class="cartcard-head">
        <div class="cartcard-head__title">Tóm tắt đơn hàng</div>
    </div>

    <div class="sumrows">
        <div class="sumrow">
            <span>Tạm tính:</span>
            <strong class="js-subtotal">{{ number_format($subtotal, 0, ',', '.') }} đ</strong>
        </div>

        <div class="sumrow">
            <span>VAT:</span>
            <strong class="js-vat">{{ number_format($vat, 0, ',', '.') }} đ</strong>
        </div>

        <div class="sumrow">
            <span>Phí vận chuyển:</span>
            <strong class="js-shipping">{{ number_format($shipping, 0, ',', '.') }} đ</strong>
        </div>

        <hr class="sumhr">

        <div class="sumrow sumtotal">
            <span>Tổng cộng:</span>
            <strong class="js-total">{{ number_format($total, 0, ',', '.') }} đ</strong>
        </div>
    </div>

    {{-- MÃ GIẢM GIÁ --}}
    <div class="coupon">
        <input type="text" class="coupon-input" placeholder="Mã giảm giá">
        <button type="button" class="coupon-btn" disabled>Áp dụng mã</button>
    </div>

    <div class="promise-box">
        <div class="promise-title">
            <i class="fa-regular fa-circle-check"></i>
            <strong>Cam kết của chúng tôi</strong>
        </div>
        <ul class="promise-list">
            <li>Giao hàng nhanh chóng</li>
            <li>Đổi trả trong 7 ngày</li>
            <li>Hỗ trợ 24/7</li>
        </ul>
    </div>

    <ul class="trust-list">
        <li><i class="fa-solid fa-truck"></i> Giao hàng toàn quốc</li>
        <li><i class="fa-solid fa-shield-halved"></i> Thanh toán an toàn &amp; bảo mật</li>
        <li><i class="fa-solid fa-headset"></i> Hỗ trợ 24/7</li>
    </ul>
</div>
