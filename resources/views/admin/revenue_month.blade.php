@extends('components.admin_header')

@section('title', 'Thống kê doanh thu theo tháng')

@push('styles')
    {{-- CSS riêng cho trang thống kê --}}
    <link rel="stylesheet" href="{{ asset('assets') }}/css/admin/revenue_stat.css">

    {{-- CSS cho flatpickr month select --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
@endpush

@section('content')

    {{-- HEADER --}}
    <div class="content-header">
        <div>
            <h1>Thống kê doanh thu theo tháng</h1>
            <p>Lọc theo tháng / năm và xem tổng doanh thu đã bán.</p>
        </div>
    </div>

    {{-- THANH CHỌN THÁNG / NĂM --}}
    <div class="revenue-filters">
        <form class="search-bar mY" action="{{ route('admin.revenue_month') }}" method="GET">

            <label for="monthYearPicker" class="filter-label">
                Chọn tháng / năm:
            </label>

            <div class="search-input-wrapper month-input-wrapper">
                <i class="fa-regular fa-calendar"></i>
                <input type="text" id="monthYearPicker" name="month_year" placeholder="MM/YYYY"
                    value="{{ request('month_year', $monthYear ?? '') }}" required>
            </div>

            {{-- filter trạng thái --}}
            <select name="payment_status" class="mini-select">
                <option value="">Tất cả trạng thái</option>
                <option value="completed" {{ request('payment_status') == 'completed' ? 'selected' : '' }}>Hoàn tất</option>
                <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Đang xử lý</option>
                <option value="canceled" {{ request('payment_status') == 'canceled' ? 'selected' : '' }}>Đã hủy</option>
            </select>

            {{-- filter phương thức --}}
            <select name="method" class="mini-select">
                <option value="">Tất cả phương thức</option>
                <option value="cash on delivery" {{ request('method') == 'cash on delivery' ? 'selected' : '' }}>COD
                </option>
                {{-- sau này thêm các loại khác --}}
            </select>

            <button type="submit" class="filter-btn">
                <i class="fas fa-search"></i> Tìm kiếm
            </button>
        </form>

        {{-- nút reset + export --}}
        <div class="revenue-actions">

            <form action="{{ route('admin.revenue_month_export') }}" method="GET">
                {{-- giữ lại filter hiện tại --}}
                <input type="hidden" name="month_year" value="{{ request('month_year', $monthYear ?? '') }}">
                <input type="hidden" name="payment_status" value="{{ request('payment_status') }}">
                <input type="hidden" name="method" value="{{ request('method') }}">
                <button type="submit" class="secondary-btn export-btn">
                    <i class="fa-regular fa-file-excel"></i> Xuất Excel
                </button>
            </form>
        </div>
    </div>

    {{-- TỔNG DOANH THU --}}
    <div class="panel statistics-panel">
        <div class="panel-header">
            <h2>Tổng tiền đã bán</h2>
        </div>
        <div class="panel-body">
            <h2 class="total-revenue">
                {{ number_format($totalRevenue, 0, ',', '.') }} đ
            </h2>
        </div>
    </div>

    {{-- MINI SUMMARY --}}
    <div class="mini-stats-row">
        <div class="mini-stat-card">
            <span class="mini-label">Số đơn trong tháng</span>
            <span class="mini-value">{{ $totalOrders }}</span>
        </div>

        <div class="mini-stat-card">
            <span class="mini-label">Trạng thái đơn</span>
            <span class="mini-value">
                Hoàn tất: {{ $statusCounts['completed'] ?? 0 }}
            </span>
            <span class="mini-sub">
                Đang xử lý: {{ $statusCounts['pending'] ?? 0 }},
                Đã hủy: {{ $statusCounts['canceled'] ?? 0 }}
            </span>
        </div>

        <div class="mini-stat-card">
            <span class="mini-label">Giá trị đơn trung bình</span>
            <span class="mini-value">
                {{ number_format($avgOrderValue, 0, ',', '.') }} đ
            </span>
        </div>
    </div>

@if ($topProducts->count())
        <div class="panel">
            <div class="panel-header">
                <h2>Top 5 sản phẩm doanh thu cao trong tháng</h2>
            </div>

            <div class="revenue-table-wrapper">
                <table class="revenue-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Sản phẩm</th>
                            <th>SL bán ra</th>
                            <th>Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topProducts as $idx => $p)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>{{ $p->product_name }}</td>
                                <td>{{ $p->qty_sold }}</td>
                                <td>{{ number_format($p->revenue, 0, ',', '.') }} đ</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
    {{-- BẢNG ĐƠN HÀNG THEO THÁNG --}}
    <div class="panel">
        <div class="panel-header">
            <h2>Danh sách đơn hàng trong tháng</h2>
        </div>

        <div class="revenue-table-wrapper">
            @if ($orders->count() > 0)
                <table class="revenue-table">
                    <thead>
                        <tr>
                            <th>
                                <a
                                    href="{{ route(
                                        'admin.revenue_month',
                                        array_merge(request()->query(), [
                                            'sort_by' => 'id',
                                            'order' => request('order') == 'asc' ? 'desc' : 'asc',
                                        ]),
                                    ) }}">
                                    ID
                                </a>
                            </th>
                            <th>
                                <a
                                    href="{{ route(
                                        'admin.revenue_month',
                                        array_merge(request()->query(), [
                                            'sort_by' => 'method',
                                            'order' => request('order') == 'asc' ? 'desc' : 'asc',
                                        ]),
                                    ) }}">
                                    Phương thức
                                </a>
                            </th>
                            <th>
                                <a
                                    href="{{ route(
                                        'admin.revenue_month',
                                        array_merge(request()->query(), [
                                            'sort_by' => 'total_products',
                                            'order' => request('order') == 'asc' ? 'desc' : 'asc',
                                        ]),
                                    ) }}">
                                    Sản phẩm
                                </a>
                            </th>
                            <th>
                                <a
                                    href="{{ route(
                                        'admin.revenue_month',
                                        array_merge(request()->query(), [
                                            'sort_by' => 'total_price',
                                            'order' => request('order') == 'asc' ? 'desc' : 'asc',
                                        ]),
                                    ) }}">
                                    Tổng tiền
                                </a>
                            </th>
                            <th>
                                <a
                                    href="{{ route(
                                        'admin.revenue_month',
                                        array_merge(request()->query(), [
                                            'sort_by' => 'placed_on',
                                            'order' => request('order') == 'asc' ? 'desc' : 'asc',
                                        ]),
                                    ) }}">
                                    Ngày bán
                                </a>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->method }}</td>
                                <td>{{ $order->total_products }}</td>
                                <td>{{ number_format($order->total_price, 0, ',', '.') }} đ</td>
                                <td>{{ \Carbon\Carbon::parse($order->placed_on)->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="empty">Chưa bán được đơn hàng nào!</p>
            @endif
        </div>

        {{-- PHÂN TRANG --}}
        @if ($orders->hasPages())
            <div class="pagination-wrapper">
                {{ $orders->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
    


@endsection

@push('scripts')
    {{-- JS cho flatpickr chọn tháng --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#monthYearPicker", {
                plugins: [
                    new monthSelectPlugin({
                        shorthand: true,
                        dateFormat: "m/Y", // gửi lên server
                        altFormat: "F Y", // hiển thị cho người dùng
                    })
                ]
            });
        });
    </script>
@endpush
