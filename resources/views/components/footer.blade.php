<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/guest/footer.css">
    <title>Document</title>
</head>
<body>
    <footer class="footer-modern">
    <div class="footer-container">

        {{-- TOP: 4 cột giống ảnh --}}
        <div class="footer-top">
            {{-- Cột 1: Logo + mô tả + social --}}
            <div class="footer-col footer-brand">
                <div class="footer-logo">
                    <span class="footer-logo-badge">
                        <i class="fa-solid fa-mobile-screen-button"></i>
                    </span>
                    <span class="footer-logo-text">Banana<span>.</span>Com</span>
                </div>

                <p class="footer-desc">
                    Cửa hàng công nghệ uy tín hàng đầu Việt Nam với đa dạng sản phẩm điện tử chính hãng.
                </p>

                <div class="footer-social">
                    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" aria-label="Youtube"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>

            {{-- Cột 2 --}}
            <div class="footer-col">
                <h4 class="footer-title">Danh mục</h4>
                <a class="footer-link" href="{{ route('shop') }}">Điện thoại</a>
                <a class="footer-link" href="{{ route('shop') }}">Laptop</a>
                <a class="footer-link" href="{{ route('shop') }}">Tablet</a>
                <a class="footer-link" href="{{ route('shop') }}">Tai nghe</a>
                <a class="footer-link" href="{{ route('shop') }}">Đồng hồ thông minh</a>
                <a class="footer-link" href="{{ route('shop') }}">Phụ kiện</a>
            </div>

            {{-- Cột 3 --}}
            <div class="footer-col">
                <h4 class="footer-title">Hỗ trợ</h4>
                <a class="footer-link" href="#">Trung tâm trợ giúp</a>
                <a class="footer-link" href="#">Chính sách giao hàng</a>
                <a class="footer-link" href="#">Bảo hành &amp; Đổi trả</a>
                <a class="footer-link" href="#">Trả góp 0%</a>
                <a class="footer-link" href="{{ route('contact.form') }}">Liên hệ</a>
                <a class="footer-link" href="#">Câu hỏi thường gặp</a>
            </div>

            {{-- Cột 4 --}}
            <div class="footer-col">
                <h4 class="footer-title">Thông tin</h4>
                <a class="footer-link" href="#">Về chúng tôi</a>
                <a class="footer-link" href="#">Tuyển dụng</a>
                <a class="footer-link" href="#">Chính sách bảo mật</a>
                <a class="footer-link" href="#">Điều khoản sử dụng</a>
                <a class="footer-link" href="#">Tin công nghệ</a>
            </div>
        </div>

        <div class="footer-divider"></div>

        {{-- MID: 3 cụm giống ảnh --}}
        <div class="footer-mid">
            <div class="footer-mid-block">
                <div class="footer-mid-title">Phương thức thanh toán</div>
                <div class="footer-badges">
                    <span class="footer-badge">VISA</span>
                    <span class="footer-badge">Mastercard</span>
                    <span class="footer-badge">MoMo</span>
                    <span class="footer-badge">ZaloPay</span>
                </div>
            </div>

            <div class="footer-mid-block">
                <div class="footer-mid-title">Vận chuyển</div>
                <div class="footer-badges">
                    <span class="footer-badge">Giao hàng nhanh</span>
                    <span class="footer-badge">Giao hàng tiết kiệm</span>
                </div>
            </div>

            <div class="footer-mid-block">
                <div class="footer-mid-title">Chứng nhận</div>
                <div class="footer-badges">
                    <span class="footer-badge">Bộ Công Thương</span>
                    <span class="footer-badge">TMĐT</span>
                </div>
            </div>
        </div>

        <div class="footer-divider"></div>

        {{-- BOTTOM --}}
        <div class="footer-bottom">
            <div class="footer-copy">
                © {{ date('Y') }} Banana. Tất cả quyền được bảo lưu.
            </div>

            <div class="footer-bottom-links">
                <a href="#" class="footer-mini-link">Chính sách bảo mật</a>
                <a href="#" class="footer-mini-link">Điều khoản sử dụng</a>
            </div>
        </div>

    </div>
</footer>
</body>
</html>

