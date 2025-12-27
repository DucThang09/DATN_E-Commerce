<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="storage-base" content="{{ asset('storage') }}/">
    <title>{{ $product?->name ?? 'Xem sản phẩm' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/guest/quick_view.css">
</head>
<div id="top-toast-root" class="top-toast-root" aria-live="polite" aria-atomic="true"></div>
<body>

    @include('components.user_header')

    <main class="pd">
        <div class="pd-wrap">
            <nav class="pd-breadcrumb">
                <a href="{{ \Illuminate\Support\Facades\Route::has('home') ? route('home') : url('/') }}">Trang chủ</a>

                <span>›</span>
                <a href="#">{{ $product?->category ?? 'Danh mục' }}</a>
                <span>›</span>
                <span class="current">{{ $product?->name ?? 'Sản phẩm' }}</span>
            </nav>
            @php
                $variantMap = $variantMap ?? [];
                $vDefault = $vDefault ?? [];
                $colors = $colors ?? [];

                $colors = array_values(array_filter(array_map('trim', $colors)));
                $selectedColor = isset($selectedColor) ? trim($selectedColor) : $colors[0] ?? '';

                $mainPath =
                    $mainPath ??
                    ($variantMap[$selectedColor]['image_01'] ??
                        (null ?? ($vDefault['image_01'] ?? (null ?? ($product->image_01 ?? (null ?? ''))))));
            @endphp


            @if ($product)
                @php
                    $discount = (int) ($product->discount ?? 0);
                    $oldPrice = (float) ($product->price ?? 0);
                    $newPrice = $oldPrice * (1 - $discount / 100);

                    // Giả lập số còn hàng nếu DB chưa có trường
                    $stock = $product->quantity ?? ($product->stock ?? 48);

                    // ✅ Màu lấy từ variants (controller đã truyền $colors, $selectedColor)
                    $selectedColor = $selectedColor ?? ($colors[0] ?? '');

                    // Map tên màu -> mã màu hiển thị
                    $colorHexMap = [
                        'Titan Xanh' => '#2b6cb0',
                        'Titan Đen' => '#111827',
                        'Titan Trắng' => '#e5e7eb',
                        'Titan Tự nhiên' => '#d6c7b7',
                        'Đen' => '#111827',
                        'Trắng' => '#e5e7eb',
                        'Xanh' => '#2b6cb0',
                        'Vàng' => '#facc15',
                        'Hồng' => '#fb7185',
                    ];
                @endphp
                @php
                    $storageBase = asset('storage') . '/';
                @endphp

                <section class="pd-grid">
                    {{-- gallery bên trái --}}
                    <div class="pd-left">
                        <div class="pd-gallery">
                            <div class="pd-main">
                                @php
                                    $storageBase = asset('storage') . '/';

                                    $imgUrl = function ($p) use ($storageBase) {
                                        if (!$p) {
                                            return '';
                                        }
                                        return str_starts_with($p, 'http') ? $p : $storageBase . ltrim($p, '/');
                                    };
                                @endphp

                                <img id="jsMainImage" src="{{ $imgUrl($mainPath) }}" alt="">

                            </div>
                            <div class="pd-thumbs" id="jsThumbs">
                                @php
                                    $paths = [];

                                    // ưu tiên theo selectedColor
                                    $vSel = $variantMap[$selectedColor] ?? null;
                                    if (!empty($vSel['image_01'])) $paths[] = $vSel['image_01'];
                                    if (!empty($vSel['image_02'])) $paths[] = $vSel['image_02'];
                                    if (!empty($vSel['image_03'])) $paths[] = $vSel['image_03'];

                                    // fallback default
                                    if (count($paths) === 0) {
                                        if (!empty($vDefault['image_01'])) $paths[] = $vDefault['image_01'];
                                        if (!empty($vDefault['image_02'])) $paths[] = $vDefault['image_02'];
                                        if (!empty($vDefault['image_03'])) $paths[] = $vDefault['image_03'];
                                    }

                                    // fallback product
                                    if (count($paths) === 0) {
                                        if (!empty($product->image_01)) $paths[] = $product->image_01;
                                        if (!empty($product->image_02)) $paths[] = $product->image_02;
                                        if (!empty($product->image_03)) $paths[] = $product->image_03;
                                    }
                                @endphp


                                @foreach ($paths as $i => $p)
                                    <button type="button" class="pd-thumb {{ $i === 0 ? 'is-active' : '' }}"
                                        data-src="{{ $imgUrl($p) }}">
                                        <img src="{{ $imgUrl($p) }}" alt="">
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- thông tin bên phải --}}
                    <div class="pd-right">
                        <h1 class="pd-title">{{ $product->name }}</h1>

                        {{-- rating + số đánh giá + còn hàng --}}
                        <div class="pd-meta">
                            <div class="pd-stars" aria-label="rating">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                            </div>
                            <span class="pd-score">4.8</span>
                            <span class="pd-dot">|</span>
                            <span class="pd-link">1247 đánh giá</span>
                            <span class="pd-dot">|</span>
                            @php $inv = $variantMap[$selectedColor]['inventory'] ?? ($product->inventory ?? 0); @endphp
                            <span class="pd-stock" id="jsStockText" data-inv="{{ (int)$inv }}">Còn {{ $inv }} sản phẩm</span>
                        </div>

                        <form action="{{ route('cart.add') }}" method="post" class="pd-form" data-ajax-cart="1">
                            @csrf
                            <input type="hidden" name="pid" value="{{ $product->id }}">
                            <input type="hidden" name="name" value="{{ $product->name }}">
                            <input type="hidden" name="price" value="{{ $product->price }}">
                            <input type="hidden" name="category" value="{{ $product->category }}">
                            <input type="hidden" name="company" value="{{ $product->company }}">
                            {{-- ✅ variant_id để add-to-cart đúng màu --}}
                            <input type="hidden" name="color" id="jsColorInput" value="{{ $selectedColor }}">

                            <input type="hidden" name="variant_id" id="jsVariantInput"
                                value="{{ $variantMap[$selectedColor]['variant_id'] ?? '' }}">

                            <input type="hidden" name="image" id="jsImageInput"
                                value="{{ $variantMap[$selectedColor]['image_01'] ?? '' }}">

                            {{-- giá --}}
                            <div class="pd-pricebox">
                                <div class="pd-price-row">
                                    <div class="pd-newprice">
                                        {{ number_format($newPrice, 0, ',', '.') }}đ
                                    </div>
                                    <div class="pd-oldprice">
                                        {{ number_format($oldPrice, 0, ',', '.') }}đ
                                    </div>
                                </div>
                                @if ($discount > 0)
                                    <div class="pd-badge">Giảm {{ $discount }}%</div>
                                @endif
                            </div>

                            {{-- chọn màu --}}
                            {{-- chọn màu --}}
                            <div class="pd-block">
                                <div class="pd-label">Chọn màu sắc:</div>

                                <div class="pd-colors" id="jsColors">
                                    @foreach ($colors as $c)
                                        @php
                                            $cTrim  = trim($c);
                                            $active = ($cTrim === $selectedColor) ? 'is-active' : '';
                                        @endphp

                                        <button type="button"
                                                class="pd-color {{ $active }}"
                                                data-color="{{ $cTrim }}"
                                                aria-label="Màu {{ $cTrim }}">
                                            <span class="pd-color-text">{{ $cTrim }}</span>
                                            <span class="pd-color-check"><i class="fa-solid fa-check"></i></span>
                                        </button>
                                    @endforeach
                                </div>
                                <div class="pd-picked">
                                    Đã chọn: <strong id="jsColorText">{{ $selectedColor }}</strong>
                                </div>
                            </div>

                            {{-- số lượng --}}
                            <div class="pd-block">
                                <div class="pd-qtyrow">
                                    <div class="pd-label">Số lượng:</div>
                                    <div class="pd-qty">
                                        <button type="button" class="pd-qtybtn" id="jsQtyMinus">−</button>
                                        <input type="number" name="qty" id="jsQtyInput"
                                        value="1" min="1" max="{{ max(1, (int)$inv) }}">
                                        <button type="button" class="pd-qtybtn" id="jsQtyPlus">+</button>
                                    </div>
                                    <div class="pd-qtyhint" id="jsQtyHint" data-inv="{{ (int)$inv }}">{{ $inv }} sản phẩm có sẵn</div>
                                </div>
                            </div>

                            {{-- nút --}}
                            <div class="pd-actions">
                                <button type="submit" class="pd-btn pd-btn-outline" name="add_to_cart"
                                    value="1">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                    Thêm vào giỏ
                                </button>
                                <button type="submit" class="pd-btn pd-btn-solid" name="buy_now" value="1">
                                    Mua ngay
                                </button>
                            </div>

                            <hr class="pd-sep">

                            {{-- Đặc điểm nổi bật --}}
                            <div class="pd-block">
                                <div class="pd-label">Đặc điểm nổi bật:</div>
                                <ul class="pd-highlights">
                                    <li><i class="fa-solid fa-circle-check"></i> Chip mạnh, tối ưu hiệu năng</li>
                                    <li><i class="fa-solid fa-circle-check"></i> Camera sắc nét, zoom tốt</li>
                                    <li><i class="fa-solid fa-circle-check"></i> Thiết kế bền bỉ, sang</li>
                                </ul>
                            </div>

                            {{-- thông tin nhỏ --}}
                            <div class="pd-mini">
                                <div><strong>Hãng:</strong> {{ $product->company }}</div>
                                <div><strong>Chi tiết:</strong> {{ $product->details }}</div>
                            </div>

                        </form>
                    </div>
                </section>
                @php
                    // specs thường là array [{label:'...', value:'...'}]
                    $specs = $product->specs ?? [];

                    // fallback nếu DB lưu LONGTEXT mà cast không hoạt động
                    if (is_string($specs)) {
                        $specs = json_decode($specs, true) ?: [];
                    }

                    // chuẩn hóa label
                    $specs = collect($specs)
                        ->map(function ($it) {
                            $label = trim($it['label'] ?? '');
                            $value = trim($it['value'] ?? '');
                            if ($label !== '' && !str_ends_with($label, ':')) {
                                $label .= ':';
                            }
                            return ['label' => $label, 'value' => $value];
                        })
                        ->filter(fn($it) => $it['label'] !== '' && $it['value'] !== '')
                        ->values();
                @endphp

                <div class="pd-specs">
                    <h3 class="pd-specs-title">Thông số kỹ thuật</h3>

                    @if ($specs->count())
                        <div class="pd-specs-grid">
                            @foreach ($specs as $row)
                                <div class="pd-spec-item">
                                    <div class="pd-spec-k">{{ $row['label'] }}</div>
                                    <div class="pd-spec-v">{{ $row['value'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="pd-spec-empty">Chưa có thông số kỹ thuật.</p>
                    @endif
                </div>
            @else
                <p class="pd-empty">Không tìm thấy sản phẩm nào!</p>
            @endif
        </div>

    </main>

    @include('components.footer')
    <script>
        window.STORAGE_BASE = "{{ asset('storage') }}/";
        window.QV_VARIANTS = @json($variantMap);
    </script>
    
    <script src="{{ asset('assets') }}/js/guest/quick_view.js"></script>

</body>

</html>
