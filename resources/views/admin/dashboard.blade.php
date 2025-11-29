@extends('components.admin_header')

@section('title', 'Bảng điều khiển - Admin')

@section('content')

    <div class="page-header">
        <div class="page-header-left">

            <h1 class="page-title">Tổng quan hệ thống</h1>
            <p class="page-subtitle">Theo dõi hiệu suất và trạng thái hệ thống</p>
        </div>
        <div class="filter-actions"> <select class="select-range">
                <option>30 ngày qua</option>
                <option>7 ngày qua</option>
                <option>Hôm nay</option>
            </select> <button class="primary-btn"> <i class="fa-solid fa-download"></i> Xuất báo cáo </button> </div>
        <div class="page-header-right">
            <button type="button" id="btnDashboardRefresh" class="link-refresh">
                <i class="fa-solid fa-rotate-right"></i>
                Làm mới
            </button>
            <span class="last-updated">
                Cập nhật lần cuối:
                <span id="dashboardLastUpdated">
                    {{ now()->format('H:i:s d/m/Y') }}
                </span>
            </span>
        </div>
    </div>

    <!-- HÀNG 1: TỔNG QUAN -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-title">Tổng doanh thu</div>
            <div class="stat-value">
                {{ isset($totalRevenue) ? number_format($totalRevenue, 0, ',', '.') . 'đ' : '0đ' }}
            </div>

            <div class="stat-footer">
                <span class="stat-change up">+0%</span>
                <span>So với kỳ trước</span>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-sack-dollar"></i></div>
        </div>

        <div class="stat-card">
            <div class="stat-title">Đơn hàng</div>
            <div class="stat-value">{{ $numberOfOrders }}</div>
            <div class="stat-footer">
                <span class="stat-change up">+0%</span>
                <span>Tổng đơn hàng</span>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-cart-shopping"></i></div>
        </div>

        <div class="stat-card">
            <div class="stat-title">Sản phẩm</div>
            <div class="stat-value">{{ $numberOfProducts }}</div>
            <div class="stat-footer">
                <span class="stat-change up">+0%</span>
                <span>Sản phẩm đã thêm</span>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-bag-shopping"></i></div>
        </div>

        <div class="stat-card">
            <div class="stat-title">Người dùng</div>
            <div class="stat-value">{{ $numberOfUsers }}</div>
            <div class="stat-footer">
                <span class="stat-change up">+0%</span>
                <span>Người dùng bình thường</span>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-user"></i></div>
        </div>
    </div>

    <!-- HÀNG 2: CÁC CHỨC NĂNG CHÍNH -->
    <div class="stats-row secondary">
        <div class="stat-card">
            <div class="stat-title">Tổng số lượng tồn kho</div>
            <div class="stat-value">{{ number_format($totalInventory, 0, ',', '.') }}</div>
            <a href="{{ route('admin.products') }}" class="link-inline">
                Xem danh sách sản phẩm <i class="fa-solid fa-arrow-right-long"></i>
            </a>
        </div>


        <div class="stat-card">
            <div class="stat-title">Tin nhắn mới</div>
            <div class="stat-value">{{ $newMessages ?? 0 }}</div>
            <a href="{{ route('admin.messages') }}" class="link-inline">
                Xem tin nhắn <i class="fa-solid fa-arrow-right-long"></i>
            </a>
        </div>

        <div class="stat-card">
            <div class="stat-title">Lợi nhuận</div>
            <div class="stat-value">
                {{ number_format($totalProfit, 0, ',', '.') }}đ
            </div>
            <a href="{{ route('admin.revenue_statistics') }}" class="link-inline">
                Xem thống kê <i class="fa-solid fa-arrow-right-long"></i>
            </a>
        </div>

    </div>

    <!-- DOANH THU & THAO TÁC NHANH -->
    <div class="bottom-row">
        <div class="panel" id="revenue-panel">
            <div class="panel-header panel-header-with-actions">
                <div>
                    <h2>Doanh thu</h2>
                    <span class="muted">Theo dõi doanh thu theo thời gian</span>
                </div>

                <div class="panel-actions">
                    <select id="revenueRange" class="filter-select">
                        <option value="7d">7 ngày gần nhất</option>
                        <option value="30d">30 ngày gần nhất</option>
                        <option value="6m">6 tháng gần nhất</option>
                        <option value="12m">12 tháng gần nhất</option>
                    </select>
                </div>
            </div>

            <div class="chart-wrapper">
                <canvas id="revenueChart" height="90"></canvas>
            </div>
        </div>


        {{-- PANEL TỔNG NGƯỜI DÙNG --}}
        <div class="panel">
            <div class="panel-header panel-header-with-select">
                <div>
                    <h2>Tổng người dùng (theo thời gian)</h2>
                    <p class="muted">Theo dõi tổng số tài khoản tích lũy</p>
                </div>

                <select id="userRangeSelect" class="chart-range-select">
                    <option value="7d">7 ngày gần nhất</option>
                    <option value="30d">30 ngày gần nhất</option>
                    <option value="6m">6 tháng gần nhất</option>
                    <option value="12m" selected>12 tháng gần nhất</option>
                </select>
            </div>

            <div class="chart-wrapper">
                <canvas id="userChart"></canvas>
            </div>

            <p class="user-summary" id="userSummary">
                <!-- JS sẽ fill: VD: “Tổng 7 ngày gần nhất: 5 người dùng mới” -->
            </p>
        </div>

    </div>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // 1) Lấy canvas
                const canvas = document.getElementById('revenueChart');
                const rangeSelect = document.getElementById('revenueRange');

                // Nếu không có thì dừng luôn, tránh lỗi JS
                if (!canvas) {
                    console.warn('Không tìm thấy #revenueChart trong DOM');
                    return;
                }

                const ctx = canvas.getContext('2d');
                let revenueChart = null;

                // 2) Hàm gọi API & vẽ chart
                async function loadRevenue(range) {
                    try {
                        const url = "{{ route('admin.revenue_data') }}" + '?range=' + encodeURIComponent(range);
                        console.log('Gọi API doanh thu:', url);

                        const res = await fetch(url);
                        if (!res.ok) {
                            console.error('API doanh thu lỗi HTTP:', res.status);
                            return;
                        }

                        const json = await res.json();
                        console.log('Dữ liệu nhận được:', json);

                        const labels = json.labels || [];
                        const data = json.data || [];

                        if (!labels.length) {
                            console.warn('Không có dữ liệu doanh thu để vẽ');
                        }

                        if (revenueChart) {
                            // update chart cũ
                            revenueChart.data.labels = labels;
                            revenueChart.data.datasets[0].data = data;
                            revenueChart.update();
                        } else {
                            // tạo chart mới
                            revenueChart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: 'Doanh thu (đ)',
                                        data: data,
                                        tension: 0.35,
                                        borderWidth: 2,
                                        fill: true,
                                        borderColor: '#fb7185',
                                        backgroundColor: 'rgba(251, 113, 133, 0.12)',
                                        pointRadius: 3,
                                        pointBackgroundColor: '#fb7185'
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
                                        },
                                        tooltip: {
                                            callbacks: {
                                                label: function(ctx) {
                                                    const v = ctx.parsed.y || 0;
                                                    return ' ' + v.toLocaleString('vi-VN') + ' đ';
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            grid: {
                                                display: false
                                            }
                                        },
                                        y: {
                                            grid: {
                                                color: 'rgba(148, 163, 184, 0.15)'
                                            },
                                            ticks: {
                                                callback: function(v) {
                                                    if (v >= 1_000_000) return v / 1_000_000 + 'm';
                                                    if (v >= 1_000) return v / 1_000 + 'k';
                                                    return v;
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    } catch (e) {
                        console.error('Lỗi JS loadRevenue:', e);
                    }
                }

                // 3) Gọi lần đầu và bắt sự kiện đổi range
                if (rangeSelect) {
                    loadRevenue(rangeSelect.value);
                    rangeSelect.addEventListener('change', function() {
                        loadRevenue(this.value);
                    });
                } else {
                    // fallback: vẫn vẽ với range mặc định nếu không có select
                    loadRevenue('7d');
                }
            });
        </script>
        <script src="{{ asset('assets/js/admin_dashboard.js') }}"></script>
    @endpush
@endsection
