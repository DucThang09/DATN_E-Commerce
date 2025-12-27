<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt hàng thành công</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/guest/checkout_success.css">
</head>

<body>
    @include('components.user_header')

    <section class="os-wrap">
        <div class="os-grid">
            {{-- LEFT --}}
            <div class="os-left card">
                <div class="os-hero">
                    <div class="os-icon">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <h1 class="os-title">Đặt hàng thành công!</h1>
                    <p class="os-desc">Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ liên hệ với bạn sớm nhất.</p>
                    <div class="os-code">Mã đơn hàng: <b>#{{ $order->order_code }}</b></div>
                </div>

                <div class="os-section">
                    <div class="os-section-title">Thông tin người nhận</div>
                    <div class="os-box">
                        <div><b>Họ tên:</b> {{ $order->name }}</div>
                        <div><b>Số điện thoại:</b> {{ $order->number }}</div>
                        <div><b>Email:</b> {{ $order->email ?? '—' }}</div>
                        <div><b>Địa chỉ:</b> {{ $order->address }}</div>
                    </div>
                </div>

                <div class="os-section">
                    <div class="os-section-title">Phương thức thanh toán</div>
                    <div class="os-box">
                        {{ $order->method === 'cash on delivery' ? 'Thanh toán khi nhận hàng (COD)' : $order->method }}
                    </div>
                </div>

                <div class="os-section">
                    <div class="os-section-title">Sản phẩm đã đặt</div>
                    <div class="os-items">
                        @foreach ($items as $it)
                            <div class="os-item">
                                <div class="os-item-thumb">
                                    @php
                                        $img = ltrim($it->product_image ?? '', '/');

                                        if (!$img) {
                                            $src = asset('assets/images/no-image.png');
                                        } elseif (
                                            str_starts_with($img, 'http://') ||
                                            str_starts_with($img, 'https://')
                                        ) {
                                            $src = $img;
                                        } else {
                                            // ✅ ảnh nằm trong storage/app/public => link qua /storage/...
                                            // nếu DB đã lỡ lưu "storage/xxx" thì asset($img), còn lại asset('storage/'.$img)
                                            $src = str_starts_with($img, 'storage/')
                                                ? asset($img)
                                                : asset('storage/' . $img);
                                        }
                                    @endphp
                                    <img src="{{ $src }}" alt="{{ $it->product_name }}">
                                </div>
                                <div class="os-item-info">
                                    <div class="os-item-name">{{ $it->product_name }}</div>
                                    <div class="os-item-qty">Số lượng: {{ $it->quantity }}</div>

                                    @if (!empty($it->color_name))
                                        <div class="os-item-color">Màu: {{ $it->color_name }}</div>
                                    @endif
                                </div>


                                <div class="os-item-price">
                                    {{ number_format($it->total_price, 0, ',', '.') }}đ
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>

                <div class="os-actions">
                    <a href="{{ route('home') ?? url('/') }}" class="os-btn ghost">Về trang chủ</a>
                    <a href="{{ route('orders.history') }}" class="os-btn primary">Xem đơn hàng</a>
                </div>
            </div>

            {{-- RIGHT --}}
            <aside class="os-right card">
                <div class="os-right-title">Tóm tắt đơn hàng</div>

                <div class="os-sum">
                    <div class="os-row">
                        <span>Tạm tính ({{ $itemsCount }} sản phẩm)</span>
                        <b>{{ number_format($subtotal, 0, ',', '.') }}đ</b>
                    </div>
                    <div class="os-row">
                        <span>Phí vận chuyển</span>
                        <b>{{ number_format($shipping, 0, ',', '.') }}đ</b>
                    </div>

                    <div class="os-hr"></div>

                    <div class="os-row total">
                        <span>Tổng cộng</span>
                        <b class="os-total">{{ number_format($total, 0, ',', '.') }}đ</b>
                    </div>
                </div>

                <div class="os-note card-soft">
                    <div class="os-note-title">
                        <i class="fa-regular fa-circle-check"></i>
                        <b>Đơn hàng của bạn</b>
                    </div>
                    <ul>
                        <li>Đang được xử lý</li>
                        <li>Sẽ được giao sớm nhất</li>
                        <li>Theo dõi qua email/SMS</li>
                    </ul>
                </div>
            </aside>
        </div>
    </section>

    @include('components.footer')
</body>

</html>
