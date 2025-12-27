<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search page</title>

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- CSS chung của bạn -->
    <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">

    <!-- CSS riêng cho trang search -->
    <link rel="stylesheet" href="{{ asset('assets') }}/css/guest/search_page.css">
</head>

<body>

    @include('components.user_header')

    <main class="sp-wrap">

        {{-- Breadcrumb --}}
        <div class="sp-breadcrumb">
            <a href="{{ route('home') ?? url('/') }}">
                <i class="fa-solid fa-house"></i>
                <span>Trang chủ</span>
            </a>
            <span>/</span>
            <span class="current">
                Kết quả tìm kiếm cho: '<strong>{{ $search ?? '' }}</strong>'
            </span>
        </div>

        {{-- Tiêu đề --}}
        <h2 class="sp-title">
            @php $count = isset($products) ? $products->count() : 0; @endphp
            Tìm thấy <strong>{{ $count }}</strong> sản phẩm cho từ khoá
            '<strong>{{ $search ?? '' }}</strong>'
        </h2>

        {{-- Chips (UI thôi, chưa filter backend) --}}
        <section class="sp-toolbar">
            <div class="sp-chips">
                <button type="button" class="sp-chip is-active">Tất cả</button>
                <button type="button" class="sp-chip">iPhone 17 Series</button>
                <button type="button" class="sp-chip">Macbook</button>
                <button type="button" class="sp-chip">iPad</button>
                <button type="button" class="sp-chip">iPad Pro 2025</button>
                <button type="button" class="sp-chip">Đồng hồ Apple Watch</button>
                <button type="button" class="sp-chip">Apple Watch Series 11</button>
            </div>

            <div class="sp-sort">
                <div class="sp-sort-label">Sắp xếp theo</div>
                <div class="sp-sort-btns">
                    <button type="button" class="sp-sortbtn is-active">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Liên quan
                    </button>
                    <button type="button" class="sp-sortbtn">
                        <i class="fa-solid fa-arrow-up-wide-short"></i> Giá cao
                    </button>
                    <button type="button" class="sp-sortbtn">
                        <i class="fa-solid fa-arrow-down-wide-short"></i> Giá thấp
                    </button>
                </div>
            </div>
        </section>

        {{-- Products --}}
        <section class="sp-products">
            @if (isset($products) && $products->isNotEmpty())
                {{-- products grid --}}
                <div class="catpage-products v2" id="productsGrid">
                    @include('partials.search_products', ['products' => $products])
                </div>
                @if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator && $products->hasMorePages())
                    @php
                        $remaining = max(0, $products->total() - $products->currentPage() * $products->perPage());
                    @endphp

                    <div class="loadmore-wrap">
                        <button type="button" class="loadmore-btn" id="loadMoreBtn"
                            data-next-page="{{ $products->currentPage() + 1 }}" data-base-url="{{ route('search') }}">
                            Xem thêm {{ $remaining }} sản phẩm <i class="fa-solid fa-chevron-down"></i>
                        </button>
                    </div>
                @endif


                @if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator && $products->hasMorePages())
                    @php
                        $remaining = max(0, $products->total() - $products->currentPage() * $products->perPage());
                    @endphp
                    <div class="loadmore-wrap">
                        <button type="button" class="loadmore-btn" id="loadMoreBtn"
                            data-next-page="{{ $products->currentPage() + 1 }}" data-base-url="{{ route('search') }}">
                            Xem thêm {{ $remaining }} sản phẩm <i class="fa-solid fa-chevron-down"></i>
                        </button>
                    </div>
                @endif
            @else
                <div class="sp-empty-center">
                    <img src="{{ asset('assets') }}/images/Search-Empty.webp" alt="">
                </div>
                <p class="sp-empty">Không có kết quả bạn cần tìm!</p>
            @endif
        </section>

    </main>

    @include('components.footer')

    <script src="{{ asset('assets') }}/js/guest/search_loadmore.js"></script>

</body>

</html>
