@extends('components.admin_header')

@section('title', 'Quản lý đơn hàng')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets') }}/css/admin/admin_orders.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/admin/flash_toast.css">
@endpush

@section('content')
    @if (session('success'))
        <div class="toast toast-success js-auto-hide-toast">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="toast toast-error js-auto-hide-toast">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    {{-- HEADER --}}
    <div class="content-header">
        <div>
            <h1>Quản lý đơn hàng</h1>
            <p>Theo dõi, xử lý và cập nhật trạng thái thanh toán.</p>
        </div>

        <div class="header-actions">
            <button type="button" class="secondary-btn">
                <i class="fa-solid fa-file-arrow-down"></i> Xuất báo cáo
            </button>
        </div>
    </div>

    {{-- THANH TÌM KIẾM + BỘ LỌC NHANH --}}
    <div class="order-filters">
        <form method="GET" action="{{ route('admin.placed_orders') }}" class="search-bar">
            <div class="search-input-wrapper">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search_value" id="search-input"
                    placeholder="Tìm đơn hàng theo tên khách / mã đơn..." value="{{ request()->get('search_value') }}">
            </div>

            <div class="filter-inline">
                <select name="search_type" id="search-type" class="filter-select">
                    <option value="name" {{ request('search_type') === 'name' ? 'selected' : '' }}>Theo tên khách hàng
                    </option>
                    <option value="id" {{ request('search_type') === 'id' ? 'selected' : '' }}>Theo mã đơn hàng
                    </option>
                </select>

                <select name="payment_status" id="search-payment-status" class="filter-select">
                    <option value="" {{ request('payment_status') == '' ? 'selected' : '' }}>Tất cả thanh
                        toán</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Chờ xử lý
                    </option>
                    <option value="completed" {{ request('payment_status') == 'completed' ? 'selected' : '' }}>Đã hoàn
                        thành</option>
                    <option value="canceled" {{ request('payment_status') == 'canceled' ? 'selected' : '' }}>Đã hủy
                    </option>
                </select>

                <button type="submit" class="filter-btn">
                    <i class="fa-solid fa-filter"></i> Lọc
                </button>
            </div>
        </form>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h2>Danh sách đơn hàng</h2>
        </div>

        <div class="order-table-wrapper">
            @if ($orders->count() > 0)
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Sản phẩm</th>
                            <th>Số tiền</th>
                            <th>Thanh toán</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($orders as $order)
                            @php
                                $payStatus = $order->payment_status; 
                                switch ($payStatus) {
                                    case 'completed':
                                        $payText = 'Đã thanh toán';
                                        $payClass = 'badge-paid';
                                        break;
                                    case 'canceled':
                                        $payText = 'Đã hoàn tiền / Hủy';
                                        $payClass = 'badge-refund';
                                        break;
                                    default:
                                        $payText = 'Chờ thanh toán';
                                        $payClass = 'badge-pending';
                                        break;
                                }                     
                                switch ($payStatus) {
                                    case 'completed':
                                        $statusText = 'Hoàn thành';
                                        $statusClass = 'chip-success';
                                        break;
                                    case 'canceled':
                                        $statusText = 'Đã hủy';
                                        $statusClass = 'chip-danger';
                                        break;
                                    default:
                                        $statusText = 'Đang xử lý';
                                        $statusClass = 'chip-warning';
                                        break;
                                }
                            @endphp
                            <tr>
                                <td class="col-code">#{{ $order->order_code }}</td>
                                <td class="col-customer">{{ $order->name }}</td>

                                <td class="col-products">
                                    {{ $order->total_quantity ?? $order->items->sum('quantity') }} sản phẩm
                                </td>
                                <td class="col-price">
                                    {{ number_format($order->total_price, 0, ',', '.') }}đ
                                </td>
                                <td class="col-payment">
                                    <span class="status-badge {{ $payClass }}">
                                        {{ $payText }}
                                    </span>
                                </td>
                                <td class="col-status">
                                    <form action="{{ route('admin.update_payment') }}" method="post" class="status-form">
                                        @csrf
                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                        <input type="hidden" name="total_products" value="{{ $order->total_products }}">

                                        <select name="payment_status" class="status-select"
                                            {{ in_array($payStatus, ['completed', 'canceled']) ? 'disabled' : '' }}>
                                            <option value="">Chọn trạng thái</option>
                                            @foreach ($status as $st)
                                                <option value="{{ $st->status }}"
                                                    {{ $payStatus == $st->status ? 'selected' : '' }}>
                                                    {{ $st->status }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <button type="submit" class="icon-btn confirm"
                                            {{ in_array($payStatus, ['completed', 'canceled']) ? 'disabled' : '' }}>
                                            <i class="fa-regular fa-circle-check"></i>
                                        </button>
                                    </form>
                                </td>

                                <td class="col-date">
                                    {{ \Carbon\Carbon::parse($order->placed_on)->format('d/m/Y') }}
                                </td>
                                @php
                                    if ($order->relationLoaded('items') && $order->items->count()) {
                                        $modalProducts = $order->items
                                            ->map(function ($item) {
                                                return $item->product_name . ' (x' . $item->quantity . ')';
                                            })
                                            ->implode(' • ');
                                    } else {
                                        $modalProducts = $order->total_products;
                                    }
                                @endphp

                                <td class="col-actions">
                                    <div class="table-actions">                              
                                        <a href="#" class="icon-btn view btn-order-detail" title="Xem chi tiết"
                                            data-detail-url="{{ route('admin.orders.detail_json', $order->id) }}">
                                            <i class="fa-regular fa-eye"></i>
                                        </a>                           
                                        <a href="{{ route('admin.delete_order', $order->id) }}" class="icon-btn delete"
                                            onclick="return confirm('Xóa đơn hàng này?');" title="Xóa đơn">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if ($orders->hasPages())
                    <div class="pagination-wrapper">
                        {{ $orders->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            @else
                <p class="empty">Chưa có đơn hàng nào được đặt!</p>
            @endif
        </div>
    </div>
    <div id="orderDetailOverlay" class="order-modal-overlay order-modal-hidden">
        <div class="order-modal" id="orderDetailModal">
            <div class="order-modal-header">
                <div class="order-modal-header-left">
                    <div class="order-modal-icon">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div class="order-modal-header-text">
                        <h2 id="modalOrderTitle">Đơn hàng #12345</h2>
                        <p id="modalOrderSubtitle">
                        </p>
                    </div>
                </div>
                <div class="order-modal-header-right">
                    <span id="modalOrderStatusChip" class="order-status-chip">
                        Chờ xác nhận
                    </span>
                    <button type="button" class="order-modal-close" id="orderDetailClose">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
            <div class="order-modal-body">           
                <section class="order-section">
                    <div class="order-section-title">
                        <i class="fa-regular fa-user"></i>
                        <span>Thông tin khách hàng</span>
                    </div>
                    <div class="order-section-grid">
                        <div class="order-field">
                            <span class="order-field-label">Họ tên</span>
                            <span id="modalCustomerName" class="order-field-value">—</span>
                        </div>
                        <div class="order-field">
                            <span class="order-field-label">Email</span>
                            <span id="modalCustomerEmail" class="order-field-value">—</span>
                        </div>
                        <div class="order-field">
                            <span class="order-field-label">Số điện thoại</span>
                            <span id="modalCustomerPhone" class="order-field-value">—</span>
                        </div>
                        <div class="order-field">
                            <span class="order-field-label">Địa chỉ</span>
                            <span id="modalCustomerAddress" class="order-field-value">—</span>
                        </div>
                    </div>
                </section>              
                <section class="order-section">
                    <div class="order-section-title">
                        <i class="fa-regular fa-file-lines"></i>
                        <span>Chi tiết đơn hàng</span>
                    </div>

                    <div class="order-summary-row">
                        <div class="order-summary-item">
                            <span class="order-field-label">Mã đơn hàng</span>
                            <span id="modalOrderCode" class="order-field-value">#12345</span>
                        </div>
                        <div class="order-summary-item">
                            <span class="order-field-label">Tổng tiền</span>
                            <span id="modalOrderTotal" class="order-field-value order-total-text">—</span>
                        </div>
                        <div class="order-summary-item">
                            <span class="order-field-label">Thanh toán</span>
                            <span id="modalOrderMethod" class="order-field-value">—</span>
                        </div>
                        <div class="order-summary-item">
                            <span class="order-field-label">Thời gian đặt</span>
                            <span id="modalOrderPlacedOn" class="order-field-value">—</span>
                        </div>
                    </div>
                    <div class="order-items-wrapper">
                        <div class="order-items-title">Sản phẩm</div>
                        <div id="modalOrderItems" class="order-items-list">
                        </div>
                    </div>
                </section>
            </div>
            <div class="order-modal-footer">
                <div class="order-footer-left">
                    <button type="button" class="order-footer-btn order-footer-secondary">
                        <i class="fa-regular fa-envelope"></i>
                        Liên hệ khách
                    </button>
                </div>
                <button type="button" class="order-footer-btn order-footer-ghost" id="orderDetailCloseBottom">
                    Đóng
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/flash_toast.js') }}"></script>
@endpush
