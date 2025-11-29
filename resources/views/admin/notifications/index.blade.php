@extends('components.admin_header')

@section('title', 'Tất cả thông báo')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/admin_notifications.css') }}">
@endpush

@section('content')
    <div class="content-header">
        <div>
            <h1>Tất cả thông báo</h1>
            <p>Bạn có <strong>{{ $unreadCount }}</strong> thông báo chưa đọc.</p>
        </div>

        <div class="header-actions">
            <form id="markAllReadForm" action="{{ route('admin.notifications.mark_all_read') }}" method="POST">
                @csrf
                <button type="submit" class="primary-btn">
                    <i class="fa-solid fa-check-double"></i> Đánh dấu tất cả đã đọc
                </button>
            </form>
        </div>

    </div>

    {{-- TAB FILTER – dùng button, không dùng <a> --}}
    <div class="notify-tabs" id="notifyTabs" data-url="{{ route('admin.notifications.index') }}">
        <button type="button" class="notify-tab {{ $tab === 'all' ? 'active' : '' }}" data-tab="all">
            Tất cả
        </button>

        <button type="button" class="notify-tab {{ $tab === 'unread' ? 'active' : '' }}" data-tab="unread">
            Chưa đọc
            @if ($unreadCount > 0)
                <span class="pill-count">{{ $unreadCount }}</span>
            @endif
        </button>

        <button type="button" class="notify-tab {{ $tab === 'read' ? 'active' : '' }}" data-tab="read">
            Đã đọc
        </button>

        <button type="button" class="notify-tab {{ $tab === 'order' ? 'active' : '' }}" data-tab="order">
            Đơn hàng
        </button>

        <button type="button" class="notify-tab {{ $tab === 'stock' ? 'active' : '' }}" data-tab="stock">
            Kho hàng
        </button>

        <button type="button" class="notify-tab {{ $tab === 'user' ? 'active' : '' }}" data-tab="user">
            Khách hàng
        </button>
    </div>

    {{-- Vùng danh sách sẽ bị thay bằng AJAX --}}
    <div id="notifyListWrapper">
        @include('admin.notifications._list')
    </div>
    @push('scripts')
        <script src="{{ asset('assets') }}/js/admin_notifications.js"></script>
    @endpush
@endsection
