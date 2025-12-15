<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banana</title>

    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/guest/home.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/responsive.css">

</head>

<body>
    @include('components.user_header')
    <div class="home-bg">
        <section class="hero">
            <div class="hero-layout">
                {{-- CỘT TRÁI: MENU DANH MỤC --}}
                <aside class="hero-left-menu">
                    <ul class="hero-menu">
                        <li>
                            <a href="{{ url('category?category=smartphone') }}">
                                <i class="fa-solid fa-mobile-screen-button"></i>
                                <span>Điện thoại, Tablet</span>
                                <i class="fa-solid fa-chevron-right hero-menu-arrow"></i>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('category?category=laptop') }}">
                                <i class="fa-solid fa-laptop"></i>
                                <span>Laptop</span>
                                <i class="fa-solid fa-chevron-right hero-menu-arrow"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fa-solid fa-headphones-simple"></i>
                                <span>Âm thanh, Mic thu âm</span>
                                <i class="fa-solid fa-chevron-right hero-menu-arrow"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fa-regular fa-clock"></i>
                                <span>Đồng hồ, Camera</span>
                                <i class="fa-solid fa-chevron-right hero-menu-arrow"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fa-solid fa-plug-circle-bolt"></i>
                                <span>Phụ kiện</span>
                                <i class="fa-solid fa-chevron-right hero-menu-arrow"></i>
                            </a>
                        </li>
                        {{-- thêm các dòng khác nếu cần --}}
                    </ul>
                </aside>
                {{-- CỘT GIỮA: BANNER SWIPER (ẢNH QUẢNG CÁO) --}}
                <div class="hero-banner">
                    <div class="swiper home-slider">
                        <div class="swiper-wrapper">

                            <div class="swiper-slide hero-slide">
                                <img src="{{ asset('assets/images/banner-1.jpg') }}" alt="Khuyến mãi 1">
                            </div>

                            <div class="swiper-slide hero-slide">
                                <img src="{{ asset('assets/images/banner-2.jpg') }}" alt="Khuyến mãi 2">
                            </div>

                            <div class="swiper-slide hero-slide">
                                <img src="{{ asset('assets/images/banner-3.jpg') }}" alt="Khuyến mãi 3">
                            </div>

                            {{-- thêm bao nhiêu banner cũng được, cứ thêm swiper-slide là xong --}}

                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>

                {{-- CỘT PHẢI: THÔNG TIN NGƯỜI DÙNG / ƯU ĐÃI --}}
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
                        <a href="{{ route('orders') }}" class="hero-user-link">
                            Xem ưu đãi của bạn
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </aside>
            </div>
        </section>
    </div>

    {{-- ========== GỢI Ý CHO BẠN ========== --}}
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

                            <div class="swiper-slide suggest-card">
                                <div class="suggest-tag-top">
                                    <span class="suggest-tag-discount">Giảm {{ $item->discount }}%</span>
                                    <span class="suggest-tag-install">Trả góp 0%</span>
                                </div>

                                <div class="suggest-image">
                                    <img src="{{ asset('storage/' . $item->image_01) }}" alt="{{ $item->name }}"
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
                                            {{ number_format($item->price * (1 - $item->discount / 100), 0, ',', '.') }}đ
                                        </div>
                                        <div class="suggest-price-old">
                                            {{ number_format($item->price, 0, ',', '.') }}đ
                                        </div>
                                    </div>

                                    <div class="suggest-student">
                                        S-Student giảm thêm 300.000đ
                                    </div>

                                    <div class="suggest-bottom">
                                        <form action="{{ route('add.to.wishlist.or.cart') }}" method="post">
                                            @csrf
                                            <input type="hidden" name="pid" value="{{ $item->id }}">
                                            <input type="hidden" name="name" value="{{ $item->name }}">
                                            <input type="hidden" name="price"
                                                value="{{ $item->price * (1 - $item->discount / 100) }}">
                                            <input type="hidden" name="image" value="{{ $item->image_01 }}">
                                            <button class="suggest-like-btn" type="submit" name="add_to_wishlist">
                                                <i class="fa-regular fa-heart"></i>
                                                <span>Yêu thích</span>
                                            </button>
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
    {{-- ====== END GỢI Ý CHO BẠN ====== --}}

    <section class="category-section">

        <div class="category-header">
            <h2>Danh mục sản phẩm</h2>
            <p>Khám phá đa dạng thiết bị công nghệ chính hãng</p>
        </div>

        <div class="category-grid">

            <a href="{{ url('category?category=smartphone') }}" class="category-card cat-phone">
                <div class="category-icon">
                    <i class="fa-solid fa-mobile-screen"></i>
                </div>
                <div class="category-name">Điện thoại</div>
                <div class="category-meta">5,000+ sản phẩm</div>
            </a>

            <a href="{{ url('category?category=laptop') }}" class="category-card cat-laptop">
                <div class="category-icon">
                    <i class="fa-solid fa-laptop"></i>
                </div>
                <div class="category-name">Laptop</div>
                <div class="category-meta">3,000+ sản phẩm</div>
            </a>

            <a href="{{ url('category?category=tablet') }}" class="category-card cat-tablet">
                <div class="category-icon">
                    <i class="fa-solid fa-tablet-screen-button"></i>
                </div>
                <div class="category-name">Tablet</div>
                <div class="category-meta">1,500+ sản phẩm</div>
            </a>

            <a href="{{ url('category?category=headphone') }}" class="category-card cat-headphone">
                <div class="category-icon">
                    <i class="fa-solid fa-headphones"></i>
                </div>
                <div class="category-name">Tai nghe</div>
                <div class="category-meta">2,000+ sản phẩm</div>
            </a>

            <a href="{{ url('category?category=watch') }}" class="category-card cat-watch">
                <div class="category-icon">
                    <i class="fa-regular fa-clock"></i>
                </div>
                <div class="category-name">Đồng hồ thông minh</div>
                <div class="category-meta">1,200+ sản phẩm</div>
            </a>

            <a href="{{ url('category?category=accessories') }}" class="category-card cat-accessory">
                <div class="category-icon">
                    <i class="fa-solid fa-plug"></i>
                </div>
                <div class="category-name">Phụ kiện</div>
                <div class="category-meta">4,000+ sản phẩm</div>
            </a>

        </div>
    </section>

    <section class="home-products featured-products">
        <div class="featured-header">
            <div class="featured-header-left">
                <h2 class="featured-title">Sản phẩm nổi bật</h2>
                <p class="featured-subtitle">Những sản phẩm được yêu thích nhất hiện tại</p>
            </div>

            {{-- TODO: sửa lại đường dẫn xem tất cả cho đúng route của bạn --}}
            <a href="{{ url('category') }}" class="featured-view-all">
                Xem tất cả
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="swiper products-slider">
            <div class="swiper-wrapper">
                @if ($topProducts->count() > 0)
                    @foreach ($topProducts as $fetch_product)
                        <form action="{{ route('add.to.wishlist.or.cart') }}" method="post"
                            class="swiper-slide slide product-card">
                            @csrf
                            <input type="hidden" name="pid" value="{{ $fetch_product->id }}">
                            <input type="hidden" name="name" value="{{ $fetch_product->name }}">
                            <input type="hidden" name="price"
                                value="{{ $fetch_product->price * (1 - $fetch_product->discount / 100) }}">
                            <input type="hidden" name="image" value="{{ $fetch_product->image_01 }}">
                            <input type="hidden" name="discount" value="{{ $fetch_product->discount }}">

                            {{-- Hàng trên cùng: badge giảm giá + tim --}}
                            <div class="product-top-row">
                                <span class="product-discount-badge">
                                    -{{ $fetch_product->discount }}%
                                </span>

                                <button type="submit" name="add_to_wishlist" class="product-wishlist-btn">
                                    <i class="fa-regular fa-heart"></i>
                                </button>
                            </div>

                            {{-- Hình ảnh sản phẩm --}}
                            <div class="product-image-wrapper">
                                <img src="{{ asset('storage/' . $fetch_product->image_01) }}"
                                    alt="{{ $fetch_product->name }}"
                                    onclick="window.location.href='{{ route('quick.view', ['pid' => $fetch_product->id]) }}'; return false;"
                                    style="cursor: pointer;">
                            </div>

                            {{-- Thông tin sản phẩm --}}
                            <div class="product-info">
                                <div class="product-category">Sản phẩm</div>
                                {{-- Nếu trong DB có field category, bạn có thể đổi thành:
                                 {{ $fetch_product->category }} --}}
                                <div class="product-name" title="{{ $fetch_product->name }}">
                                    {{ $fetch_product->name }}
                                </div>

                                {{-- Rating: tạm cho 5 sao cố định, sau này có dữ liệu thì thay --}}
                                <div class="product-rating">
                                    <span class="stars">
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                    </span>
                                    {{-- <span class="rating-count">(0)</span> --}}
                                </div>

                                <div class="product-prices">
                                    <span class="price-sale">
                                        {{ number_format($fetch_product->price * (1 - $fetch_product->discount / 100), 0, ',', '.') }}đ
                                    </span>
                                    <span class="price-old">
                                        {{ number_format($fetch_product->price, 0, ',', '.') }}đ
                                    </span>
                                </div>

                                <div class="product-inventory">
                                    {{ $fetch_product->inventory > 0 ? 'Còn hàng' : 'Hết hàng' }}
                                </div>
                            </div>

                            {{-- Nút thêm vào giỏ --}}
                            <input type="submit" value="Thêm vào giỏ" class="btn product-add-btn"
                                name="add_to_cart">
                        </form>
                    @endforeach
                @else
                    <p class="empty">Chưa có sản phẩm nào được thêm vào</p>
                @endif
            </div>
            <div class="swiper-pagination"></div>
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
                        <form action="{{ route('add.to.wishlist.or.cart') }}" method="post"
                            class="swiper-slide slide product-card">
                            @csrf
                            <input type="hidden" name="pid" value="{{ $product->id }}">
                            <input type="hidden" name="name" value="{{ $product->name }}">
                            <input type="hidden" name="price"
                                value="{{ $product->price * (1 - $product->discount / 100) }}">
                            <input type="hidden" name="image" value="{{ $product->image_01 }}">
                            <input type="hidden" name="discount" value="{{ $product->discount }}">

                            <div class="product-top-row">
                                <span class="product-discount-badge">
                                    -{{ $product->discount }}%
                                </span>
                                <button type="submit" name="add_to_wishlist" class="product-wishlist-btn">
                                    <i class="fa-regular fa-heart"></i>
                                </button>
                            </div>

                            <div class="product-image-wrapper">
                                <img src="{{ asset('storage/' . $product->image_01) }}" alt="{{ $product->name }}"
                                    onclick="window.location.href='{{ route('quick.view', ['pid' => $product->id]) }}'; return false;"
                                    style="cursor: pointer;">
                            </div>

                            <div class="product-info">
                                <div class="product-category">Sản phẩm</div>
                                <div class="product-name" title="{{ $product->name }}">
                                    {{ $product->name }}
                                </div>

                                <div class="product-rating">
                                    <span class="stars">
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                        <i class="fa-solid fa-star"></i>
                                    </span>
                                </div>

                                <div class="product-prices">
                                    <span class="price-sale">
                                        {{ number_format($product->price * (1 - $product->discount / 100), 0, ',', '.') }}đ
                                    </span>
                                    <span class="price-old">
                                        {{ number_format($product->price, 0, ',', '.') }}đ
                                    </span>
                                </div>

                                <div class="product-inventory">
                                    {{ $product->inventory > 0 ? 'Còn hàng' : 'Hết hàng' }}
                                </div>
                            </div>

                            <input type="submit" value="Thêm vào giỏ" class="btn product-add-btn"
                                name="add_to_cart">
                        </form>
                    @empty
                        <p class="empty">Chưa có sản phẩm cho danh mục này</p>
                    @endforelse
                </div>

                <div class="swiper-pagination"></div>
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
            {{-- ảnh mẫu không có pagination nên bạn có thể bỏ dòng này --}}
            {{-- <div class="swiper-pagination"></div> --}}
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
            {{-- Demo tĩnh (sau này bạn thay bằng @foreach ($posts as $post) ...) --}}

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

    <script src="{{ asset('assets') }}/js/script.js"></script>

    <script>
document.addEventListener('DOMContentLoaded', () => {

  /* ========= HERO BANNER (home-slider) ========= */
  const homeSliderEl = document.querySelector('.home-slider');
  if (homeSliderEl) {
    new Swiper(homeSliderEl, {
      loop: true,
      spaceBetween: 16,
      autoplay: {
        delay: 3500,
        disableOnInteraction: false,
        pauseOnMouseEnter: true,
      },
      pagination: {
        el: homeSliderEl.querySelector('.swiper-pagination'),
        clickable: true,
      },
    });
  }

  /* ========= GỢI Ý CHO BẠN (suggest-slider) ========= */
  const suggestEl = document.querySelector('.suggest-slider');
  if (suggestEl) {
    new Swiper(suggestEl, {
      loop: false,
      spaceBetween: 16,
      navigation: {
        nextEl: ".suggest-next",
        prevEl: ".suggest-prev",
      },
      breakpoints: {
        0:    { slidesPerView: 1.15, spaceBetween: 12 },
        480:  { slidesPerView: 1.6,  spaceBetween: 14 },
        768:  { slidesPerView: 2.2,  spaceBetween: 16 },
        992:  { slidesPerView: 3.2,  spaceBetween: 16 },
        1200: { slidesPerView: 5,    spaceBetween: 16 },
      },
    });
  }

  /* ========= SẢN PHẨM (products-slider) - có NHIỀU slider trên home =========
     ✅ Mỗi slider tự dùng pagination của chính nó => không lỗi, không đè nhau
  */
  document.querySelectorAll('.products-slider').forEach((el) => {
    const pagEl = el.querySelector('.swiper-pagination');

    new Swiper(el, {
      loop: false,
      spaceBetween: 20,
      pagination: {
        el: pagEl,
        clickable: true,
      },
      breakpoints: {
        0:    { slidesPerView: 1.15, spaceBetween: 12 },
        480:  { slidesPerView: 2,    spaceBetween: 14 },
        768:  { slidesPerView: 3,    spaceBetween: 16 },
        992:  { slidesPerView: 4,    spaceBetween: 18 },
        1200: { slidesPerView: 5,    spaceBetween: 20 },
      },
    });
  });

  /* ========= THƯƠNG HIỆU ĐỐI TÁC (category-slider) - auto chạy =========
     (slider brand logo của bạn đang dùng class .category-slider)
  */
  document.querySelectorAll('.category-slider').forEach((el) => {
    const pagEl = el.querySelector('.swiper-pagination');

    const brandSwiper = new Swiper(el, {
      loop: true,
      spaceBetween: 18,
      slidesPerView: 6,
      speed: 4000,              // tốc độ chạy mượt
      autoplay: {
        delay: 0,               // chạy liên tục
        disableOnInteraction: false,
        pauseOnMouseEnter: true,
      },
      freeMode: true,
      freeModeMomentum: false,
      pagination: pagEl ? { el: pagEl, clickable: true } : undefined,
      breakpoints: {
        0:    { slidesPerView: 2.4, spaceBetween: 12 },
        480:  { slidesPerView: 3.2, spaceBetween: 14 },
        768:  { slidesPerView: 4.2, spaceBetween: 16 },
        992:  { slidesPerView: 5.2, spaceBetween: 18 },
        1200: { slidesPerView: 6,   spaceBetween: 18 },
      },
    });

    // (tuỳ chọn) dừng khi hover: Swiper đã có pauseOnMouseEnter ở trên
  });

});
</script>


</body>

</html>
