<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Lịch sử đơn hàng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
<link rel="stylesheet" href="{{ asset('assets') }}/css/guest/orders_history.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">
    
</head>

<body>
    @include('components.user_header')

    <main class="oh-wrap">
        <div class="oh-head">
            <h1 class="oh-title">Lịch sử đơn hàng</h1>
            <p class="oh-sub">Quản lý và theo dõi tất cả đơn hàng của bạn</p>
        </div>

        {{-- Tabs --}}
        <div class="oh-tabs">
            <a class="oh-tab {{ $active === 'all' ? 'is-active' : '' }}"
                href="{{ route('orders.history', ['status' => 'all']) }}">
                Tất cả ({{ $totalCount }})
            </a>

            @foreach ($statuses as $st)
                @php
                    $label = match ($st->status) {
                        'pending' => 'Chờ xác nhận',
                        'completed' => 'Đã giao',
                        'canceled' => 'Đã hủy',
                        default => $st->status,
                    };
                    $count = $counts[$st->status_id] ?? 0;
                    $isActive = (string) $active === (string) $st->status_id;
                @endphp

                <a class="oh-tab {{ $isActive ? 'is-active' : '' }}"
                    href="{{ route('orders.history', ['status' => $st->status_id]) }}">
                    {{ $label }} ({{ $count }})
                </a>
            @endforeach
        </div>


        {{-- Toast --}}
        @if (session('success'))
            <div class="oh-toast oh-toast--success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="oh-toast oh-toast--error">{{ session('error') }}</div>
        @endif

        {{-- Orders list --}}
        <div class="oh-list">
            @forelse($orders as $order)
                @php
                    // ✅ lấy status string từ bảng statuses
                    $raw = $order->orderStatus?->status; // pending/completed/canceled

                    $statusText = match ($raw) {
                        'pending' => 'Chờ xác nhận',
                        'completed' => 'Đã giao',
                        'canceled' => 'Đã hủy',
                        default => $raw ?? '-',
                    };

                    $statusClass = match ($raw) {
                        'pending' => 'st st--pending',
                        'completed' => 'st st--delivered',
                        'canceled' => 'st st--cancelled',
                        default => 'st',
                    };

                    $placedAt = $order->placed_on ?? $order->created_at;
                    $items = $order->items ?? collect();
                    $itemsCount = $items->sum('quantity') ?: $items->count();

                    // ✅ TOTAL: ưu tiên orders.total_price, nếu không có thì tự tính từ items
                    $total = (int) ($order->total_price ?? 0);

                    if ($total <= 0) {
                        $total = $items->sum(function ($it) {
                            $qty = (int) ($it->quantity ?? 1);

                            // nếu bạn có cột giá đơn vị thì dùng, không thì sẽ rơi về total_price
                            $unit = (int) ($it->price ?? ($it->product_price ?? ($it->unit_price ?? 0)));
                            $line = (int) ($it->total_price ?? 0);

                            return $line > 0 ? $line : $unit * $qty;
                        });
                    }
                @endphp



                <section class="oh-card" data-order-card>
                    {{-- Header --}}
                    <div class="oh-card-head">
                        <div class="oh-meta">
                            <div class="oh-meta-col">
                                <div class="oh-meta-label">Mã đơn hàng</div>
                                <div class="oh-meta-value">#{{ $order->id }}</div>
                            </div>

                            <div class="oh-meta-col">
                                <div class="oh-meta-label">Ngày đặt</div>
                                <div class="oh-meta-value">
                                    {{ \Carbon\Carbon::parse($placedAt)->format('H:i d/m/Y') }}
                                </div>
                            </div>

                            <div class="oh-meta-col">
                                <span class="{{ $statusClass }}">{{ $statusText }}</span>
                            </div>
                        </div>

                        <button type="button" class="oh-toggle" data-order-toggle>
                            Xem chi tiết <span class="chev">▼</span>
                        </button>

                    </div>

                    {{-- Body --}}
                    <div class="oh-body" data-order-body>
                        <div class="oh-items">
                            @foreach ($items as $it)
                                @php
                                    $name = $it->product_name ?? ($it->product?->name ?? 'Sản phẩm');
                                    $qty = (int) ($it->quantity ?? 1);

                                    // ✅ giá dòng: ưu tiên total_price, nếu không có thì unit * qty
                                    $unit = (int) ($it->price ?? ($it->product_price ?? ($it->unit_price ?? 0)));
                                    $line = (int) ($it->total_price ?? 0);
                                    $priceShow = $line > 0 ? $line : $unit * $qty;

                                    $img =
                                        $it->product_image ?? ($it->product?->image ?? ($it->product?->thumb ?? null));
                                    $src = $img
                                        ? asset('storage/' . ltrim($img, '/'))
                                        : asset('assets/images/no-image.png');
                                @endphp


                                <div class="oh-item">
                                    <div class="oh-item-left">
                                        <div class="oh-thumb">
                                            <img src="{{ $src }}" alt="{{ $name }}">
                                        </div>
                                        <div class="oh-info">
                                            <div class="oh-name">{{ $name }}</div>
                                            <div class="oh-qty">Số lượng: {{ $qty }}</div>
                                            @if ($it->color?->colorProduct)
                                                <div class="oh-variant">
                                                    Màu: {{ $it->color->colorProduct }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="oh-price">
                                        {{ number_format($priceShow, 0, ',', '.') }}đ
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="oh-detail" data-order-detail>
                            <div class="oh-divider"></div>

                            <div class="oh-two">
                                <div class="oh-box">
                                    <div class="oh-box-title">Thông tin người nhận</div>
                                    <div class="oh-box-content">
                                        <div><b>Họ tên:</b> {{ $order->name ?? '-' }}</div>
                                        <div><b>Số điện thoại:</b> {{ $order->number ?? '-' }}</div>
                                        <div><b>Địa chỉ:</b> {{ $order->address ?? '-' }}</div>
                                    </div>
                                </div>

                                <div class="oh-box">
                                    <div class="oh-box-title">Phương thức thanh toán</div>
                                    <div class="oh-box-content">
                                        {{ $order->method ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="oh-foot">
                            <div class="oh-leftnote">
                                {{ $itemsCount }} sản phẩm
                            </div>

                            <div class="oh-right">
                                <div class="oh-total">
                                    <span class="oh-total-label">Tổng tiền</span>
                                    <span class="oh-total-value">{{ number_format($total, 0, ',', '.') }}đ</span>
                                </div>
                                @if ($raw === 'pending')
                                    <form action="{{ route('orders.cancel', $order) }}" method="POST"
                                        onsubmit="return confirm('Bạn chắc chắn muốn huỷ đơn này?');">
                                        @csrf
                                        <button type="submit" class="oh-cancel">Huỷ đơn</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>
            @empty
                <div class="oh-empty">Chưa có đơn hàng nào.</div>
            @endforelse
        </div>
    </main>

    <script src="{{ asset('assets') }}/js/guest/orders_history.js"></script>
</body>

</html>
