<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $category->category_name }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/responsive.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/guest/category.css">
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
    <div class="catpage-banners">
        <a class="cat-banner" href="#">
            <img src="{{ asset('assets/images/cat-banner-1.jpg') }}" alt="Banner 1">
        </a>
        <a class="cat-banner" href="#">
            <img src="{{ asset('assets/images/cat-banner-2.jpg') }}" alt="Banner 2">
        </a>
    </div>

    {{-- title --}}
    <h1 class="catpage-title">{{ $category->category_name }}</h1>

    {{-- brand grid --}}
    <div class="catpage-brands">
        @foreach($brands as $b)
            <a
                class="brand-pill {{ request('brand') === $b ? 'active' : '' }}"
                href="{{ route('products.category', $category->slug) }}?brand={{ urlencode($b) }}"
                title="{{ $b }}"
            >
                {{-- Nếu bạn có logo theo brand thì thay <span> bằng <img> --}}
                <span class="brand-text">{{ $b }}</span>
            </a>
        @endforeach
    </div>

    {{-- products grid --}}
    <div class="catpage-products">
        @forelse($products as $product)
            <form action="{{ route('add.to.wishlist.or.cart') }}" method="post" class="cat-card">
                @csrf

                @php
                    $sale = $product->price * (1 - $product->discount / 100);
                @endphp

                <input type="hidden" name="pid" value="{{ $product->id }}">
                <input type="hidden" name="name" value="{{ $product->name }}">
                <input type="hidden" name="price" value="{{ $sale }}">
                <input type="hidden" name="image" value="{{ $product->image_01 }}">

                <button class="cat-heart" type="submit" name="add_to_wishlist" aria-label="Yêu thích">
                    <i class="fa-regular fa-heart"></i>
                </button>

                <div class="cat-img"
                     onclick="window.location.href='{{ route('quick.view', ['pid' => $product->id]) }}'">
                    <img src="{{ asset('storage/' . $product->image_01) }}" alt="{{ $product->name }}">
                </div>

                <div class="cat-name" title="{{ $product->name }}"
                     onclick="window.location.href='{{ route('quick.view', ['pid' => $product->id]) }}'">
                    {{ $product->name }}
                </div>

                <div class="cat-price">
                    <span class="sale">{{ number_format($sale, 0, ',', '.') }}đ</span>
                    <span class="old">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                </div>

                <div class="cat-meta">
                    <span class="stock {{ $product->inventory > 0 ? 'in' : 'out' }}">
                        {{ $product->inventory > 0 ? 'Còn hàng' : 'Hết hàng' }}
                    </span>

                    <span class="discount">-{{ $product->discount }}%</span>
                </div>

                <button type="submit" class="cat-add" name="add_to_cart">Thêm vào giỏ</button>
            </form>
        @empty
            <p class="empty">Không tìm thấy sản phẩm</p>
        @endforelse
    </div>

    {{-- pagination --}}
    <div class="catpage-pagination">
        {{ $products->links() }}
    </div>
</section>

@include('components.footer')

<script src="{{ asset('assets') }}/js/script.js"></script>
</body>
</html>
