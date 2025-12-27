<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $category->category_name }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/guest/category.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/guest/footer.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/responsive.css">
</head>

<body>

    @include('components.user_header')

    <section class="catpage">
        {{-- breadcrumb --}}
        <div class="catpage-breadcrumb">
            <a href="{{ route('home') }}"><i class="fa-solid fa-house"></i> Trang chủ</a>
            <span>/</span>
            <span class="current">{{ $category->category_name }}</span>
        </div>

        {{-- 2 banner ngang --}}
        @if (isset($category) && $category->slug === 'dien-thoai')
            <div class="catpage-banners-grid">
                <!-- KHỐI TRÁI -->
                <div class="banner-block">
                    <div class="banner-mask swiper cat-swiper-left">
                        <div class="swiper-wrapper">
                            <a class="swiper-slide cat-banner" href="#">
                                <img src="{{ asset('assets/images/iphone-17-1225-cate.webp') }}" alt="Left 1">
                            </a>
                            <a class="swiper-slide cat-banner" href="#">
                                <img src="{{ asset('assets/images/banner-left-2.jpg') }}" alt="Left 2">
                            </a>
                        </div>
                    </div>

                    <button class="banner-nav banner-prev" type="button">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button class="banner-nav banner-next" type="button">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                    <div class="swiper-pagination banner-pagination"></div>
                </div>

                <!-- KHỐI PHẢI -->
                <div class="banner-block">
                    <div class="banner-mask swiper cat-swiper-right">
                        <div class="swiper-wrapper">
                            <a class="swiper-slide cat-banner" href="#">
                                <img src="{{ asset('assets/images/poco-F8-Cate-1225.webp') }}" alt="Right 1">
                            </a>
                            <a class="swiper-slide cat-banner" href="#">
                                <img src="{{ asset('assets/images/banner-right-2.jpg') }}" alt="Right 2">
                            </a>
                        </div>
                    </div>

                    <button class="banner-nav banner-prev" type="button">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button class="banner-nav banner-next" type="button">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        @endif

        {{-- title --}}
        <h1 class="catpage-title">{{ $category->category_name }}</h1>

        {{-- brand grid --}}
        <div class="catpage-brands">
            @foreach ($brands as $b)
                <a class="brand-pill {{ request('brand') === $b ? 'active' : '' }}"
                    href="{{ route('products.category', $category->slug) }}?brand={{ urlencode($b) }}"
                    title="{{ $b }}">
                    {{-- Nếu bạn có logo theo brand thì thay <span> bằng <img> --}}
                    <img src="{{ asset('assets/images/Brand/Apple.webp') }}" alt="">

                </a>
            @endforeach
        </div>


        <section class="criteria">
            <h2 class="criteria__title">Chọn theo tiêu chí</h2>

            <div class="criteria__chips" role="toolbar" aria-label="Bộ tiêu chí lọc">
                <!-- Bộ lọc (viền đỏ) -->
                <button class="chip chip--primary" type="button">
                    <svg class="chip__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M3 5h18l-7 8v5l-4 2v-7L3 5z" stroke="currentColor" stroke-width="2"
                            stroke-linejoin="round" />
                    </svg>
                    Bộ lọc
                </button>

                <!-- Có icon -->
                <button class="chip" type="button">
                    <svg class="chip__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M3 7h11v10H3z" stroke="currentColor" stroke-width="2" />
                        <path d="M14 10h4l3 3v4h-7z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                        <path d="M7 19a1.5 1.5 0 1 0 0 .01M18 19a1.5 1.5 0 1 0 0 .01" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" />
                    </svg>
                    Sẵn hàng
                </button>

                <button class="chip" type="button">
                    <svg class="chip__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 7h16l-2 13H6L4 7z" stroke="currentColor" stroke-width="2"
                            stroke-linejoin="round" />
                        <path d="M9 7a3 3 0 0 1 6 0" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    Hàng mới về
                </button>

                <button class="chip" type="button">
                    <svg class="chip__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                        <path d="M10 9h3a2.5 2.5 0 1 1 0 5h-3V9z" stroke="currentColor" stroke-width="2"
                            stroke-linejoin="round" />
                        <path d="M10 14h4" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    Xem theo giá
                </button>

                <!-- Dropdown -->
                <div class="chipWrap">
                    <button class="chip chip--dropdown" type="button" aria-expanded="false" data-dd="use">
                        Nhu cầu sử dụng <span class="caret" aria-hidden="true"></span>
                    </button>
                    <div class="dropdown" data-menu="use" hidden>
                        <button type="button">Gaming</button>
                        <button type="button">Chụp ảnh</button>
                        <button type="button">Pin trâu</button>
                    </div>
                </div>

                <div class="chipWrap">
                    <button class="chip chip--dropdown" type="button" aria-expanded="false" data-dd="cpu">
                        Chip xử lí <span class="caret" aria-hidden="true"></span>
                    </button>
                    <div class="dropdown" data-menu="cpu" hidden>
                        <button type="button">Snapdragon</button>
                        <button type="button">Apple</button>
                        <button type="button">MediaTek</button>
                    </div>
                </div>

                <div class="chipWrap">
                    <button class="chip chip--dropdown" type="button" aria-expanded="false" data-dd="type">
                        Loại điện thoại <span class="caret" aria-hidden="true"></span>
                    </button>
                    <div class="dropdown" data-menu="type" hidden>
                        <button type="button">Android</button>
                        <button type="button">iOS</button>
                    </div>
                </div>

                <div class="chipWrap">
                    <button class="chip chip--dropdown" type="button" aria-expanded="false" data-dd="ram">
                        Dung lượng RAM <span class="caret" aria-hidden="true"></span>
                    </button>
                    <div class="dropdown" data-menu="ram" hidden>
                        <button type="button">4 GB</button>
                        <button type="button">8 GB</button>
                        <button type="button">12 GB+</button>
                    </div>
                </div>

                <div class="chipWrap">
                    <button class="chip chip--dropdown" type="button" aria-expanded="false" data-dd="rom">
                        Bộ nhớ trong <span class="caret" aria-hidden="true"></span>
                    </button>
                    <div class="dropdown" data-menu="rom" hidden>
                        <button type="button">128 GB</button>
                        <button type="button">256 GB</button>
                        <button type="button">512 GB+</button>
                    </div>
                </div>

                <div class="chipWrap">
                    <button class="chip chip--dropdown" type="button" aria-expanded="false" data-dd="special">
                        Tính năng đặc biệt <span class="caret" aria-hidden="true"></span>
                    </button>
                    <div class="dropdown" data-menu="special" hidden>
                        <button type="button">Chống nước</button>
                        <button type="button">Sạc nhanh</button>
                        <button type="button">5G</button>
                    </div>
                </div>

                <div class="chipWrap">
                    <button class="chip chip--dropdown" type="button" aria-expanded="false" data-dd="camera">
                        Tính năng camera <span class="caret" aria-hidden="true"></span>
                    </button>
                    <div class="dropdown" data-menu="camera" hidden>
                        <button type="button">OIS</button>
                        <button type="button">Tele</button>
                        <button type="button">Góc rộng</button>
                    </div>
                </div>

                <div class="chipWrap">
                    <button class="chip chip--dropdown" type="button" aria-expanded="false" data-dd="hz">
                        Tần số quét <span class="caret" aria-hidden="true"></span>
                    </button>
                    <div class="dropdown" data-menu="hz" hidden>
                        <button type="button">60 Hz</button>
                        <button type="button">90 Hz</button>
                        <button type="button">120 Hz+</button>
                    </div>
                </div>

                <div class="chipWrap">
                    <button class="chip chip--dropdown" type="button" aria-expanded="false" data-dd="size">
                        Kích thước màn hình <span class="caret" aria-hidden="true"></span>
                    </button>
                    <div class="dropdown" data-menu="size" hidden>
                        <button type="button">&lt; 6.1"</button>
                        <button type="button">6.1" – 6.6"</button>
                        <button type="button">&gt; 6.6"</button>
                    </div>
                </div>

                <div class="chipWrap">
                    <button class="chip chip--dropdown" type="button" aria-expanded="false" data-dd="screenType">
                        Kiểu màn hình <span class="caret" aria-hidden="true"></span>
                    </button>
                    <div class="dropdown" data-menu="screenType" hidden>
                        <button type="button">AMOLED</button>
                        <button type="button">OLED</button>
                        <button type="button">LCD</button>
                    </div>
                </div>

                <div class="chipWrap">
                    <button class="chip chip--dropdown" type="button" aria-expanded="false" data-dd="nfc">
                        Công nghệ NFC <span class="caret" aria-hidden="true"></span>
                    </button>
                    <div class="dropdown" data-menu="nfc" hidden>
                        <button type="button">Có NFC</button>
                        <button type="button">Không NFC</button>
                    </div>
                </div>
            </div>
        </section>
        <div class="sortbar">
            <div class="sortbar__left">Sắp xếp theo</div>

            <div class="sortbar__right" role="tablist" aria-label="Sắp xếp">
                <button class="sort-pill is-active" type="button" data-sort="popular">
                    <span class="ico" aria-hidden="true">☆</span>
                    Phổ biến
                </button>

                <button class="sort-pill" type="button" data-sort="hot">
                    <span class="ico" aria-hidden="true">✹</span>
                    Khuyến mãi HOT
                </button>

                <button class="sort-pill" type="button" data-sort="price_asc">
                    <span class="ico" aria-hidden="true">≡↑</span>
                    Giá Thấp - Cao
                </button>

                <button class="sort-pill" type="button" data-sort="price_desc">
                    <span class="ico" aria-hidden="true">≡↓</span>
                    Giá Cao - Thấp
                </button>
            </div>
        </div>

        {{-- products grid --}}
        <div class="catpage-products v2">
            @forelse($products as $product)
                @php
                    $discount = (float) ($product->discount ?? 0);
                    $hasDiscount = $discount > 0;

                    $sale = $product->price * (1 - $discount / 100);
                    $finalPrice = $hasDiscount ? $sale : $product->price;

                    $href = route('quick.view', ['pid' => $product->id]);
                    $rating = (float) ($product->rating ?? 5);
                @endphp

                <form action="{{ route('cart.add') }}" method="post" class="p-card">
                    @csrf
                    <input type="hidden" name="pid" value="{{ $product->id }}">
                    <input type="hidden" name="name" value="{{ $product->name }}">
                    <input type="hidden" name="price" value="{{ $finalPrice }}">
                    <input type="hidden" name="image" value="{{ $product->image_01 }}">

                    <div class="p-top">
                        <a class="p-media" href="{{ $href }}">
                            @if ($hasDiscount)
                                <span class="p-badge">
                                    Giảm {{ rtrim(rtrim(number_format($discount, 2, '.', ''), '0'), '.') }}%
                                </span>
                            @endif

                            <img src="{{ asset('storage/' . $product->image_01) }}" alt="{{ $product->name }}">
                        </a>
                        {{-- Trả góp luôn hiển thị, nằm góc phải --}}
                        <span class="p-install">Trả góp 0%</span>
                    </div>

                    <div class="p-body">
                        <a class="p-title" href="{{ $href }}" title="{{ $product->name }}">
                            {{ $product->name }}
                        </a>

                        <div class="p-prices">
                            <span class="p-sale">{{ number_format($finalPrice, 0, ',', '.') }}đ</span>

                            @if ($hasDiscount)
                                <span class="p-old">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                            @endif
                        </div>

                    </div>
                </form>
            @empty
                <p class="empty">Không tìm thấy sản phẩm</p>
            @endforelse
        </div>

    </section>

    @include('components.footer')

    <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
    <script src="{{ asset('assets') }}/js/guest/category.js"></script>

</body>

</html>
