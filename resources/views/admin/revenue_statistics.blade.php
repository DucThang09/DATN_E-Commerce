@extends('components.admin_header')

@section('title', 'Thống kê doanh thu')

@push('styles')
    {{-- CSS riêng cho trang thống kê --}}
    <link rel="stylesheet" href="{{ asset('assets') }}/css/admin/revenue_stat.css">
@endpush

@section('content')

    {{-- HEADER --}}
    <div class="content-header">
        <div>
            <h1>Thống kê doanh thu</h1>
            <p>Theo dõi doanh thu theo sản phẩm và thời gian.</p>
        </div>

        <div class="header-actions">
            <a href="{{ route('admin.revenue_month') }}" class="primary-btn">
                <i class="fa-solid fa-arrow-right"></i>
                Thống kê theo tháng
            </a>
        </div>
    </div>

    {{-- THANH TÌM KIẾM --}}
    <div class="revenue-filters">
        <form method="GET" action="{{ route('admin.revenue_statistics') }}" class="search-bar">

            <div class="search-input-wrapper">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="search-input" name="search" placeholder="Nhập tên sản phẩm để tìm kiếm…"
                    value="{{ request()->get('search') }}">
            </div>
            {{-- Filter trạng thái nhanh --}}
            <select name="status_filter" class="status-filter-select">
                <option value="">Tất cả sản phẩm</option>
                <option value="sold_gt_0" {{ request('status_filter') == 'sold_gt_0' ? 'selected' : '' }}>
                    Đã bán > 0
                </option>
                <option value="low_stock" {{ request('status_filter') == 'low_stock' ? 'selected' : '' }}>
                    Sắp hết hàng (&lt; 10)
                </option>
            </select>
            <button type="submit" class="filter-btn">
                <i class="fa-solid fa-filter"></i> Lọc
            </button>
        </form>
    </div>
    @if (isset($totalProfit))
        <div class="panel statistics-panel">
            <div class="summary-grid">
                <div class="summary-card">
                    <p class="summary-label">Tổng lợi nhuận (theo bộ lọc hiện tại)</p>
                    <p class="summary-value">
                        {{ number_format($totalProfit, 0, ',', '.') }} đ
                    </p>
                </div>

                @if ($topProduct)
                    <div class="summary-card">
                        <p class="summary-label">Sản phẩm lợi nhuận cao nhất</p>
                        <p class="summary-product">
                            {{ $topProduct->name }}
                        </p>
                        <p class="summary-value highlight">
                            {{ number_format($topProduct->revenue, 0, ',', '.') }} đ
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- BẢNG THỐNG KÊ --}}
    <div class="panel">
        <div class="panel-header">
            <h2>Danh sách sản phẩm & doanh thu</h2>
        </div>

        <div class="revenue-table-wrapper">
            @if ($products->count() > 0)
                <table class="revenue-table">
                    <thead>
                        <tr>
                            <th>
                                <a
                                    href="{{ route(
                                        'admin.revenue_statistics',
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
                                        'admin.revenue_statistics',
                                        array_merge(request()->query(), [
                                            'sort_by' => 'name',
                                            'order' => request('order') == 'asc' ? 'desc' : 'asc',
                                        ]),
                                    ) }}">
                                    Tên sản phẩm
                                </a>
                            </th>
                            <th>
                                <a
                                    href="{{ route(
                                        'admin.revenue_statistics',
                                        array_merge(request()->query(), [
                                            'sort_by' => 'purchase_price',
                                            'order' => request('order') == 'asc' ? 'desc' : 'asc',
                                        ]),
                                    ) }}">
                                    Giá nhập
                                </a>
                            </th>
                            <th>
                                <a
                                    href="{{ route(
                                        'admin.revenue_statistics',
                                        array_merge(request()->query(), [
                                            'sort_by' => 'price',
                                            'order' => request('order') == 'asc' ? 'desc' : 'asc',
                                        ]),
                                    ) }}">
                                    Giá bán
                                </a>
                            </th>
                            <th>
                                <a
                                    href="{{ route(
                                        'admin.revenue_statistics',
                                        array_merge(request()->query(), [
                                            'sort_by' => 'qty_sold',
                                            'order' => request('order') == 'asc' ? 'desc' : 'asc',
                                        ]),
                                    ) }}">
                                    SL bán ra
                                </a>
                            </th>
                            <th>
                                <a
                                    href="{{ route(
                                        'admin.revenue_statistics',
                                        array_merge(request()->query(), [
                                            'sort_by' => 'inventory',
                                            'order' => request('order') == 'asc' ? 'desc' : 'asc',
                                        ]),
                                    ) }}">
                                    SL tồn
                                </a>
                            </th>
                            <th>
                                <a
                                    href="{{ route(
                                        'admin.revenue_statistics',
                                        array_merge(request()->query(), [
                                            'sort_by' => 'revenue',
                                            'order' => request('order') == 'asc' ? 'desc' : 'asc',
                                        ]),
                                    ) }}">
                                    Tổng tiền bán ra
                                </a>
                            </th>
                            <th>
                                <a
                                    href="{{ route(
                                        'admin.revenue_statistics',
                                        array_merge(request()->query(), [
                                            'sort_by' => 'profit',
                                            'order' => request('order') === 'asc' ? 'desc' : 'asc',
                                        ]),
                                    ) }}">
                                    Lợi nhuận
                                    <i class="fa-solid fa-arrow-trend-up profit-icon"></i>
                                </a>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td class="col-name">{{ $product->name }}</td>
                                <td>{{ number_format($product->purchase_price, 0, ',', '.') }}đ</td>
                                <td>{{ number_format($product->price, 0, ',', '.') }}đ</td>
                                <td>{{ $product->qty_sold }}</td>
                                <td>{{ $product->inventory }}</td>
                                <td>{{ number_format($product->revenue, 0, ',', '.') }}đ</td>
                                <td class="text-right text-profit">
                                    {{ number_format($product->profit ?? 0, 0, ',', '.') }}đ
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="empty">Chưa có sản phẩm nào được thêm!</p>
            @endif
        </div>

        {{-- PHÂN TRANG --}}
        @if ($products->hasPages())
            <div class="pagination-wrapper">
                {{ $products->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>

@endsection
