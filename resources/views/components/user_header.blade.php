<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banana.Com</title>

    {{-- CSS chính của bạn --}}
    <link rel="stylesheet" href="{{ asset('assets') }}/css/guest/user_header.css">

    {{-- Font Awesome để dùng các icon fas fa-... --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkTtDVIltj0bI5xZVxYl9FkRuqVIxZC1b8x7V0d2ZJ4xGqk5xI5Dqg3g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

    {{-- ========== HEADER ========== --}}
    @if (isset($messages))
        @foreach ($messages as $message)
            <div class="message">
                <span>{{ $message }}</span>
                <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
            </div>
        @endforeach
    @endif

    @php
        $total_wishlist_counts = Auth::check() ? \App\Models\Wishlist::where('user_id', Auth::id())->count() : 0;
        $total_cart_counts = Auth::check() ? \App\Models\Cart::where('user_id', Auth::id())->count() : 0;
    @endphp

    <header class="main-header">
        <div class="main-header__inner">

            {{-- LOGO --}}
            <a href="{{ route('home') }}" class="main-header__logo">
                <span class="logo-icon">
                    <i class="fas fa-mobile-alt"></i>
                </span>
                <span class="logo-text">Banana<span>.Com</span></span>
            </a>

            {{-- MENU TRÁI: Danh mục / Khuyến mãi / Thương hiệu --}}
            <nav class="main-header__nav">
                {{-- Danh mục (có dropdown) --}}
                <div class="nav-category">
                    <button type="button" class="nav-category__btn">
                        Danh mục
                        <i class="fas fa-chevron-down"></i>
                    </button>

                    <div class="nav-category__dropdown">
                        @foreach ($categories as $cat)
                            <a href="{{ route('products.category', $cat->slug) }}">
                                {{ $cat->category_name }}
                            </a>
                        @endforeach
                    </div>

                </div>

                {{-- Các mục bên cạnh --}}
                <a href="{{ route('shop') }}" class="nav-link">Khuyến mãi</a>
                <a href="#" class="nav-link">Thương hiệu</a>
            </nav>

            {{-- Ô TÌM KIẾM Ở GIỮA --}}
            <form action="{{ route('search') }}" method="GET" class="main-header__search">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Tìm kiếm điện thoại, laptop, phụ kiện...">
            </form>

            {{-- ICON BÊN PHẢI: Giỏ hàng / Yêu thích / Tài khoản --}}
            <div class="main-header__right">

                {{-- Giỏ hàng --}}
                <a href="{{ route('cart.index') }}" class="icon-btn">
                    <i class="fas fa-shopping-cart"></i>
                    @if ($total_cart_counts > 0)
                        <span class="icon-badge">{{ $total_cart_counts }}</span>
                    @endif
                </a>

                {{-- Yêu thích --}}
                <a href="{{ route('wishlist.index') }}" class="icon-btn">
                    <i class="fas fa-heart"></i>
                    @if ($total_wishlist_counts > 0)
                        <span class="icon-badge">{{ $total_wishlist_counts }}</span>
                    @endif
                </a>

                {{-- Tài khoản --}}
                <div class="account-dropdown">
                    <button type="button" class="account-btn">
                        <i class="fas fa-user"></i>
                        <span>Tài khoản</span>
                        <i class="fas fa-chevron-down chevron"></i>
                    </button>

                    <div class="account-menu">
                        @if (Auth::check())
                            @php $user = Auth::user(); @endphp

                            <div class="account-menu__info">
                                <span class="account-name">{{ $user->name }}</span>
                            </div>

                            <a href="{{ route('profile.update.form') }}" class="account-menu__link">
                                Cập nhật hồ sơ
                            </a>

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="account-menu__link account-menu__logout"
                                    onclick="return confirm('Logout from the website?');">
                                    Đăng xuất
                                </button>
                            </form>
                        @else
                            <p class="account-menu__text">Vui lòng đăng nhập để tiếp tục</p>
                            <a href="{{ route('login') }}" class="account-menu__link">Đăng nhập</a>
                            <a href="{{ route('login', ['mode' => 'register']) }}" class="account-menu__link">
                                Đăng ký
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </header>
    <script src="{{ asset('assets') }}/js/guest/user_header.js"></script>
</body>

</html>
