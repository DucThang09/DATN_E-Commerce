<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Banana</title>
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/guest/home.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/responsive.css">

</head>
<div id="top-toast-root" class="top-toast-root" aria-live="polite" aria-atomic="true"></div>

<body>
    @include('components.user_header')
    <div class="home-bg">
        <section class="hero">
            <div class="hero-layout">
                <aside class="hero-left-menu">
                    <ul class="hero-menu">
                        <li>
                            <a href="">
                                <i class="fa-solid fa-mobile-screen-button"></i>
                                <span>Điện thoại, Tablet</span>
                                <i class="fa-solid fa-chevron-right hero-menu-arrow"></i>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <i class="fa-solid fa-laptop"></i>
                                <span>Laptop</span>
                                <i class="fa-solid fa-chevron-right hero-menu-arrow"></i>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <i class="fa-solid fa-headphones-simple"></i>
                                <span>Âm thanh, Mic thu âm</span>
                                <i class="fa-solid fa-chevron-right hero-menu-arrow"></i>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <i class="fa-regular fa-clock"></i>
                                <span>Đồng hồ, Camera</span>
                                <i class="fa-solid fa-chevron-right hero-menu-arrow"></i>
                            </a>
                        </li>
                        <li>
                            <a href="">
                                <i class="fa-solid fa-plug-circle-bolt"></i>
                                <span>Phụ kiện</span>
                                <i class="fa-solid fa-chevron-right hero-menu-arrow"></i>
                            </a>
                        </li>
                    </ul>
                </aside>

                <div class="hero-banner">
                    <div class="swiper home-slider">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide hero-slide">
                                <img src="{{ asset('assets/images/iphone-17-1225-home.webp') }}" alt="Khuyến mãi 1">
                            </div>

                            <div class="swiper-slide hero-slide">
                                <img src="{{ asset('assets/images/fold7-home-1225-v1.webp') }}" alt="Khuyến mãi 2">
                            </div>

                            <div class="swiper-slide hero-slide">
                                <img src="{{ asset('assets/images/sdvsgdvgsfrg.webp') }}" alt="Khuyến mãi 3">
                            </div>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>

                <aside class="hero-right-panel">
                    <div class="hero-user-card">
                        <div class="hero-user-top">
                            <div class="hero-user-name">
                                <span class="hero-user-label">Xin chào</span>
                                <strong>{{ Auth::check() ? Auth::user()->name : 'Khách Banana' }}</strong>
                            </div>
                            <span class="hero-user-tag">S-Student</span>
                        </div>
                        <ul class="hero-user-benefits">
                            <li>
                                <i class="fa-solid fa-piggy-bank"></i>
                                <span>Tích điểm đổi quà</span>
                            </li>
                            <li>
                                <i class="fa-solid fa-gift"></i>
                                <span>Ưu đãi sinh viên lên đến 300K</span>
                            </li>
                            <li>
                                <i class="fa-solid fa-shield-heart"></i>
                                <span>Bảo hành chính hãng</span>
                            </li>
                        </ul>
                        <a href="" class="hero-user-link">
                            Xem ưu đãi của bạn
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </aside>
            </div>
        </section>
    </div>

    <section class="suggest-products">
        <div class="suggest-wrapper">
            <div class="suggest-header">
                <span class="suggest-icon">✨</span>
                <span class="suggest-title-text">GỢI Ý CHO BẠN</span>
            </div>
            <div class="swiper suggest-slider">
                <div class="swiper-wrapper">
                    @if ($topProducts->count() > 0)
                        @foreach ($topProducts as $index => $item)
                            @if ($index == 10)
                                @break
                            @endif

                            @php
                                $discount = (float) ($item->discount ?? 0);
                                $hasDiscount = $discount > 0;
                                $salePrice = $item->price * (1 - $discount / 100);
                            @endphp

                            <div class="swiper-slide suggest-card">
                                <div class="suggest-tag-top">
                                    @if ($hasDiscount)
                                        <span class="suggest-tag-discount">Giảm
                                            {{ rtrim(rtrim(number_format($discount, 2, '.', ''), '0'), '.') }}%</span>
                                    @endif

                                    <span class="suggest-tag-install">Trả góp 0%</span>
                                </div>

                                <div class="suggest-image">
                                    <img src="{{ asset('storage/' . $item->v_image_01) }}" alt="{{ $item->name }}"
                                        onclick="window.location.href='{{ route('quick.view', ['pid' => $item->id]) }}'; return false;"
                                        style="cursor: pointer;">
                                </div>

                                <div class="suggest-content">
                                    <div class="suggest-name" title="{{ $item->name }}"
                                        onclick="window.location.href='{{ route('quick.view', ['pid' => $item->id]) }}'; return false;"
                                        style="cursor: pointer;">
                                        {{ $item->name }}
                                    </div>

                                    <div class="suggest-prices">
                                        <div class="suggest-price-sale">
                                            {{ number_format($hasDiscount ? $salePrice : $item->price, 0, ',', '.') }}đ
                                        </div>

                                        @if ($hasDiscount)
                                            <div class="suggest-price-old">
                                                {{ number_format($item->price, 0, ',', '.') }}đ
                                            </div>
                                        @endif
                                    </div>

                                    <div class="suggest-bottom">
                                        <form action="{{ route('cart.add') }}" method="post" data-ajax-cart="1">
                                            @csrf
                                            <input type="hidden" name="pid" value="{{ $item->id }}">
                                            <input type="hidden" name="name" value="{{ $item->name }}">
                                            <input type="hidden" name="price"
                                                value="{{ $hasDiscount ? $salePrice : $item->price }}">
                                            <input type="hidden" name="image" value="{{ $item->v_image_01 }}">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="empty">Chưa có sản phẩm nào để gợi ý</p>
                    @endif
                </div>

                <div class="swiper-button-prev suggest-prev">
                    <i class="fa-solid fa-angle-left"></i>
                </div>

                <div class="swiper-button-next suggest-next">
                    <i class="fa-solid fa-angle-right"></i>
                </div>
            </div>
        </div>
    </section>

    <section class="home-products featured-products">
        <div class="featured-header">
            <div class="featured-header-left">
                <h2 class="featured-title">Sản phẩm nổi bật</h2>
                <p class="featured-subtitle">Những sản phẩm được yêu thích nhất hiện tại</p>
            </div>
            <a href="{{ url('category') }}" class="featured-view-all">
                Xem tất cả
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
        <div class="swiper products-slider">
            <div class="swiper-wrapper">
                @if ($topProducts->count() > 0)
                    @foreach ($topProducts as $fetch_product)
                        @php
                            $discount = (float) ($fetch_product->discount ?? 0);
                            $hasDiscount = $discount > 0;
                            $salePrice = $fetch_product->price * (1 - $discount / 100);
                            $finalPrice = $hasDiscount ? $salePrice : $fetch_product->price;
                        @endphp
                        <form action="{{ route('cart.add') }}" method="post"
                            class="swiper-slide slide product-card " data-ajax-cart="1">
                            @csrf
                            <input type="hidden" name="pid" value="{{ $fetch_product->id }}">
                            <input type="hidden" name="name" value="{{ $fetch_product->name }}">
                            <input type="hidden" name="price" value="{{ $finalPrice }}">
                            <input type="hidden" name="image" value="{{ $fetch_product->v_image_01 }}">
                            <input type="hidden" name="discount" value="{{ $discount }}">
                            <div class="product-top-row">
                                @if ($hasDiscount)
                                    <span class="product-discount-badge">
                                        Giảm {{ rtrim(rtrim(number_format($discount, 2, '.', ''), '0'), '.') }}%
                                    </span>
                                @endif

                                <span class="product-install-badge">
                                    Trả góp 0%
                                </span>
                            </div>
                            <div class="product-image-wrapper">
                                <img src="{{ asset('storage/' . $fetch_product->v_image_01) }}"
                                    alt="{{ $fetch_product->name }}"
                                    onclick="window.location.href='{{ route('quick.view', ['pid' => $fetch_product->id]) }}'; return false;"
                                    style="cursor: pointer;">
                            </div>
                            <div class="product-info">
                                <div class="product-name" title="{{ $fetch_product->name }}">
                                    {{ $fetch_product->name }}
                                </div>

                                <div class="product-prices">
                                    <span class="price-sale">
                                        {{ number_format($finalPrice, 0, ',', '.') }}đ
                                    </span>

                                    @if ($hasDiscount)
                                        <span class="price-old">
                                            {{ number_format($fetch_product->price, 0, ',', '.') }}đ
                                        </span>
                                    @endif
                                </div>

                            </div>
                        </form>
                    @endforeach
                @else
                    <p class="empty">Chưa có sản phẩm nào được thêm vào</p>
                @endif
            </div>
        </div>
    </section>
    @foreach ($homeCategories as $category)
        <section class="home-products featured-products">

            <div class="featured-header">
                <div>
                    <h2 class="featured-title">{{ $category->category_name }}</h2>
                    <p class="featured-subtitle">
                        Sản phẩm {{ strtolower($category->category_name) }} được yêu thích
                    </p>
                </div>

                <a href="{{ route('products.category', $category->slug) }}" class="featured-view-all">
                    Xem tất cả
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="swiper products-slider">
                <div class="swiper-wrapper">
                    @forelse ($category->products as $product)
                        @php
                            $discount = (float) ($product->discount ?? 0);
                            $hasDiscount = $discount > 0;
                            $salePrice = $product->price * (1 - $discount / 100);
                            $finalPrice = $hasDiscount ? $salePrice : $product->price;
                        @endphp

                        <form action="{{ route('cart.add') }}" method="post"
                            class="swiper-slide slide product-card" data-ajax-cart="1">
                            @csrf
                            <input type="hidden" name="pid" value="{{ $product->id }}">
                            <input type="hidden" name="name" value="{{ $product->name }}">
                            <input type="hidden" name="price" value="{{ $finalPrice }}">
                            <input type="hidden" name="image" value="{{ $product->v_image_01 }}">
                            <input type="hidden" name="discount" value="{{ $discount }}">

                            <div class="product-top-row">
                                @if ($hasDiscount)
                                    <span class="product-discount-badge">
                                        Giảm {{ rtrim(rtrim(number_format($discount, 2, '.', ''), '0'), '.') }}%
                                    </span>
                                @endif

                                <span class="product-install-badge">
                                    Trả góp 0%
                                </span>
                            </div>

                            <div class="product-image-wrapper">
                                <img src="{{ asset('storage/' . $product->v_image_01) }}" alt="{{ $product->name }}"
                                    onclick="window.location.href='{{ route('quick.view', ['pid' => $product->id]) }}'; return false;"
                                    style="cursor: pointer;">
                            </div>

                            <div class="product-info">
                                <div class="product-name" title="{{ $product->name }}">
                                    {{ $product->name }}
                                </div>

                                <div class="product-prices">
                                    <span class="price-sale">
                                        {{ number_format($finalPrice, 0, ',', '.') }}đ
                                    </span>

                                    @if ($hasDiscount)
                                        <span class="price-old">
                                            {{ number_format($product->price, 0, ',', '.') }}đ
                                        </span>
                                    @endif
                                </div>

                            </div>

                        </form>
                    @empty
                        <p class="empty">Chưa có sản phẩm cho danh mục này</p>
                    @endforelse
                </div>
            </div>
        </section>
    @endforeach

    <section class="partner-brands">
        <div class="partner-header">
            <h2>Thương hiệu đối tác</h2>
            <p>Hợp tác với các thương hiệu hàng đầu thế giới</p>
        </div>

        <div class="swiper category-slider partner-slider">
            <div class="swiper-wrapper">

                <a href="{{ route('products.company', ['company' => 'apple']) }}" class="swiper-slide partner-item">
                    <img src="{{ asset('assets') }}/images/apple-icon-1.png" alt="Apple">
                    <span class="sr-only">Apple</span>
                </a>

                <a href="{{ route('products.company', ['company' => 'samsung']) }}"
                    class="swiper-slide partner-item">
                    <img src="{{ asset('assets') }}/images/samsung-icon-1.png" alt="Samsung">
                    <span class="sr-only">Samsung</span>
                </a>

                <a href="{{ route('products.company', ['company' => 'sony']) }}" class="swiper-slide partner-item">
                    <img src="{{ asset('assets') }}/images/sony-icon-1.png" alt="Sony">
                    <span class="sr-only">Sony</span>
                </a>

                <a href="{{ route('products.company', ['company' => 'xiaomi']) }}" class="swiper-slide partner-item">
                    <img src="{{ asset('assets') }}/images/xiaomi-icon-1.png" alt="Xiaomi">
                    <span class="sr-only">Xiaomi</span>
                </a>

                <a href="{{ route('products.company', ['company' => 'oppo']) }}" class="swiper-slide partner-item">
                    <img src="{{ asset('assets') }}/images/oppo-icon-1.png" alt="Oppo">
                    <span class="sr-only">Oppo</span>
                </a>

                <a href="{{ route('products.company', ['company' => 'vivo']) }}" class="swiper-slide partner-item">
                    <img src="{{ asset('assets') }}/images/vivo-icon-1.png" alt="Vivo">
                    <span class="sr-only">Vivo</span>
                </a>

                <a href="{{ route('products.company', ['company' => 'huawei']) }}" class="swiper-slide partner-item">
                    <img src="{{ asset('assets') }}/images/huawei-icon-1.png" alt="Huawei">
                    <span class="sr-only">Huawei</span>
                </a>

                <a href="{{ route('products.company', ['company' => 'realme']) }}" class="swiper-slide partner-item">
                    <img src="{{ asset('assets') }}/images/realme-icon-1.png" alt="Realme">
                    <span class="sr-only">Realme</span>
                </a>
            </div>
        </div>
    </section>
    <section class="news-guides">
        <div class="news-head">
            <div class="news-head-left">
                <h2 class="news-title">Tin tức &amp; Cẩm nang</h2>
                <p class="news-subtitle">Cập nhật xu hướng và hướng dẫn mua sắm hữu ích</p>
            </div>

            <a href="{{ url('/news') }}" class="news-viewall">
                Xem tất cả <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
        <div class="news-grid">
            <a href="#" class="news-card">
                <div class="news-thumb">
                    <img src="{{ asset('assets/images/news-1.jpg') }}" alt="Top 10 smartphone flagship 2024">
                </div>
                <div class="news-body">
                    <h3 class="news-card-title">Top 10 smartphone flagship đáng mua nhất 2024</h3>
                    <p class="news-excerpt">
                        Khám phá những chiếc điện thoại cao cấp nhất năm 2024 với công nghệ AI, camera tiên tiến và hiệu
                        năng mạnh mẽ...
                    </p>
                    <div class="news-meta">
                        <span class="news-author">Tech Review VN</span>
                        <span class="news-time">15/11/2024 • 5 phút</span>
                    </div>
                </div>
            </a>
            <a href="#" class="news-card">
                <div class="news-thumb">
                    <img src="{{ asset('assets/images/news-2.jpg') }}" alt="So sánh MacBook Air M3 vs Dell XPS 13">
                </div>
                <div class="news-body">
                    <h3 class="news-card-title">So sánh MacBook Air M3 vs Dell XPS 13</h3>
                    <p class="news-excerpt">
                        Phân tích chi tiết hai dòng laptop cao cấp để giúp bạn chọn được sản phẩm phù hợp nhất...
                    </p>
                    <div class="news-meta">
                        <span class="news-author">Laptop Expert</span>
                        <span class="news-time">14/11/2024 • 7 phút</span>
                    </div>
                </div>
            </a>
            <a href="#" class="news-card">
                <div class="news-thumb">
                    <img src="{{ asset('assets/images/news-3.jpg') }}" alt="Tai nghe không dây: Xu hướng 2024">
                </div>
                <div class="news-body">
                    <h3 class="news-card-title">Tai nghe không dây: Xu hướng 2024</h3>
                    <p class="news-excerpt">
                        Cập nhật những công nghệ âm thanh mới nhất trên tai nghe không dây cao cấp...
                    </p>

                    <div class="news-meta">
                        <span class="news-author">Audio Insider</span>
                        <span class="news-time">13/11/2024 • 6 phút</span>
                    </div>
                </div>
            </a>
        </div>
    </section>
    @include('components.footer')

    <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

    <script src="{{ asset('assets') }}/js/guest/script.js"></script>
    <script src="{{ asset('assets') }}/js/guest/home.js"></script>

</body>

</html>
