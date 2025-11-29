<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin')</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- CSS giao diện admin -->
    <link rel="stylesheet" href="{{ asset('assets') }}/css/admin/admin-dashboard.css">

    @stack('styles')
</head>

<body>

    <div class="admin-layout">
        <!-- ========== SIDEBAR ========== -->
        <aside class="sidebar">
            <div class="sidebar-logo">
                <div class="logo-circle">Q</div>
                <div class="sidebar-logo-text">
                    Quản trị viên<br>
                    <span>ShopVN Management</span>
                </div>
            </div>

            <ul class="sidebar-menu">
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                        class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span class="menu-text">Bảng điều khiển</span>
                    </a>
                </li>

                {{-- NHÓM SẢN PHẨM + DANH MỤC --}}
                <li
                    class="sidebar-group
    {{ request()->routeIs('admin.products*') || request()->routeIs('admin.category_manage*') ? 'open' : '' }}">

                    {{-- menu cha: Sản phẩm --}}
                    <div class="sidebar-parent">
                        <i class="fa-solid fa-box"></i>
                        <span class="menu-text">Sản phẩm</span>
                        <span class="sidebar-arrow">
                            <i class="fa-solid fa-chevron-down"></i>
                        </span>
                    </div>

                    {{-- menu con: Tất cả SP + Danh mục --}}
                    <ul class="sidebar-submenu">
                        <li>
                            <a href="{{ route('admin.products') }}"
                                class="sidebar-sub-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>Tất cả sản phẩm</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.category_manage') }}"
                                class="sidebar-sub-link {{ request()->routeIs('admin.category_manage*') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>Danh mục</span>
                            </a>
                        </li>
                    </ul>
                </li>


                <li>
                    <a href="{{ route('admin.placed_orders') }}"
                        class="{{ request()->routeIs('admin.placed_orders*') ? 'active' : '' }}">
                        <i class="fa-solid fa-receipt"></i>
                        <span class="menu-text">Đơn hàng</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.users_accounts') }}"
                        class="{{ request()->routeIs('admin.users_accounts*') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-group"></i>
                        <span class="menu-text">Người dùng</span>
                    </a>
                </li>

                {{-- QUẢN LÝ TÀI KHOẢN QUẢN TRỊ VIÊN --}}
                @if (auth('admin')->check() && auth('admin')->user()->role === 'super_admin')
                    <li>
                        <a href="{{ route('admin.accounts') }}"
                            class="{{ request()->routeIs('admin.accounts*') ? 'active' : '' }}">
                            <i class="fa-solid fa-user-shield"></i>
                            <span class="menu-text">Quản trị viên</span>
                        </a>
                    </li>
                @endif

                <li>
                    <a href="{{ route('admin.revenue_statistics') }}"
                        class="{{ request()->routeIs('admin.revenue_statistics*') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-line"></i>
                        <span class="menu-text">Thống kê</span>
                    </a>
                </li>

            </ul>
        </aside>

        <!-- ========== MAIN ========== -->
        <div class="main">
            <!-- TOPBAR -->
            <header class="topbar">
                <div class="topbar-left">
                    <span class="hamburger" id="sidebarToggle">
                        <i class="fa-solid fa-bars"></i>
                    </span>

                    {{-- ===== BREADCRUMB NGAY TRONG TOPBAR ===== --}}
                    @php
                        use Illuminate\Support\Facades\Route;

                        $routeName = Route::currentRouteName();

                        $pageNames = [
                            'admin.dashboard' => 'Bảng điều khiển',
                            'admin.products' => 'Sản phẩm',
                            'admin.placed_orders' => 'Đơn hàng',
                            'admin.users_accounts' => 'Người dùng',
                            'admin.accounts' => 'Quản trị viên',
                            'admin.revenue_statistics' => 'Thống kê',
                            'admin.category_manage' => 'Danh mục',
                        ];

                        $currentTitle = $pageNames[$routeName] ?? 'Trang hiện tại';
                    @endphp

                    <div class="page-breadcrumb">
                        <a href="{{ route('admin.dashboard') }}" class="crumb-home">
                            <i class="fa-solid fa-house"></i>
                            <span>Admin</span>
                        </a>
                        <span class="crumb-sep">
                            <i class="fa-solid fa-angle-right"></i>
                        </span>
                        <span class="crumb-current">
                            {{ $currentTitle }}
                        </span>
                    </div>
                </div>

                <div class="topbar-right">
                    <div class="topbar-metrics">
                        <span>Trực tuyến: <span class="online">0</span></span>
                    </div>

                    {{-- Chuông thông báo --}}
                    <div class="notify-wrapper">
                        <button class="notify-btn" type="button" id="notifyToggle"
                            data-latest-id="{{ isset($notifications) && count($notifications) ? $notifications->max('id') : 0 }}"
                            data-noti-url="{{ route('admin.notifications.latest') }}">
                            <i class="fa-regular fa-bell"></i>

                            <div class="notify-badge">
                                {{ ($unreadCount ?? 0) > 9 ? '9+' : $unreadCount ?? 0 }}
                            </div>
                        </button>

                        <div class="notify-dropdown" id="notifyDropdown">
                            <div class="notify-header">
                                <h4>Thông báo</h4>
                                <button type="button" class="notify-settings">
                                    <i class="fa-solid fa-gear"></i>
                                </button>
                            </div>

                            <div class="notify-list" id="notifyList">
                                @forelse($notifications as $noti)
                                    @php
                                        // Chọn icon / màu theo loại
                                        $iconClass = '';
                                        $iconName = 'fa-bell';
                                        $tagText = 'Hệ thống';

                                        switch ($noti->type) {
                                            case 'user_registered':
                                                $iconClass = 'user';
                                                $iconName = 'fa-user-plus';
                                                $tagText = 'Khách hàng';
                                                break;
                                            case 'order_created':
                                                $iconClass = 'order';
                                                $iconName = 'fa-receipt';
                                                $tagText = 'Đơn hàng';
                                                break;
                                        }

                                        $timeText = \Carbon\Carbon::parse($noti->created_at)->diffForHumans();
                                    @endphp

                                    <div class="notify-item {{ $noti->is_read ? '' : 'is-unread' }}">
                                        <div class="notify-icon {{ $iconClass }}">
                                            <i class="fa-solid {{ $iconName }}"></i>
                                        </div>
                                        <div class="notify-content">
                                            <div class="notify-title">{{ $noti->title }}</div>
                                            @if ($noti->message)
                                                <div class="notify-text">{{ $noti->message }}</div>
                                            @endif
                                            <div class="notify-meta">
                                                <span>{{ $timeText }}</span>
                                                <span class="notify-tag">{{ $tagText }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="notify-empty">
                                        Không có thông báo nào.
                                    </div>
                                @endforelse
                            </div>

                            <div class="notify-footer">
                                <a href="{{ route('admin.notifications.index') }}" class="notify-view-all">
                                    Xem tất cả thông báo
                                </a>
                            </div>
                        </div>
                    </div>


                    {{-- Avatar + Tên + Mũi tên --}}
                    @php
                        $admin = auth('admin')->user();
                        $adminName = $admin?->name ?? 'Admin';
                        $initial = mb_strtoupper(mb_substr($adminName, 0, 1, 'UTF-8'));
                    @endphp

                    <div class="topbar-user" id="adminUserToggle">
                        <div class="user-avatar-img">
                            <span>{{ $initial }}</span>
                        </div>
                        <span class="user-name-single">{{ $adminName }}</span>
                        <i class="fa-solid fa-chevron-down user-caret"></i>

                        {{-- Dropdown menu --}}
                        <div class="user-menu" id="adminUserMenu">
                            <div class="user-menu-header">
                                <div class="user-avatar-img small">
                                    <span>{{ $initial }}</span>
                                </div>
                                <div class="user-menu-info">
                                    <div class="user-menu-name">{{ $adminName }}</div>
                                    <div class="user-menu-role">
                                        {{ $admin?->role === 'super_admin' ? 'Super Admin' : 'Quản trị viên' }}
                                    </div>
                                </div>
                            </div>

                            <div class="user-menu-divider"></div>

                            {{-- Cài đặt tài khoản --}}
                            <a href="{{ route('admin.profile_edit') }}" class="user-menu-item">
                                <button type="button">
                                    <i class="fa-regular fa-user"></i>
                                    <span>Cài đặt tài khoản</span>
                                </button>
                            </a>

                            <div class="user-menu-divider"></div>

                            {{-- Đăng xuất --}}
                            <form action="{{ route('admin.logout') }}" method="POST"
                                class="user-menu-item logout-item">
                                @csrf
                                <button type="submit">
                                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                    <span>Đăng xuất</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- NỘI DUNG TRANG CON -->
            <div class="content">
                @yield('content')
            </div>

        </div>
    </div>

    <script src="{{ asset('assets') }}/js/admin_script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const onlineSpan = document.querySelector('.topbar-metrics .online');
            if (!onlineSpan) return;

            async function fetchOnlineCount() {
                try {
                    const res = await fetch('{{ route('admin.online_count') }}', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!res.ok) return;

                    const data = await res.json();
                    const count = data.online_users ?? 0;

                    onlineSpan.textContent = new Intl.NumberFormat('vi-VN').format(count);
                } catch (e) {
                    console.error('Fetch online count error', e);
                }
            }

            // Gọi ngay khi load
            fetchOnlineCount();

            // Cập nhật lại mỗi 5 giây
            setInterval(fetchOnlineCount, 5000);
        });
    </script>

    @stack('scripts')
</body>

</html>
