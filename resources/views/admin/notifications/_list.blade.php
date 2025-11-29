{{-- resources/views/admin/notifications/_list.blade.php --}}
<div class="notify-page-list" data-latest-id="{{ $notifications->max('id') ?? 0 }}"
    data-latest-url="{{ route('admin.notifications.latest') }}" data-current-tab="{{ $tab }}">

    @forelse($notifications as $noti)
        @php
            $timeText = \Carbon\Carbon::parse($noti->created_at)->diffForHumans();

            $iconClass = '';
            $iconName = 'fa-bell';
            $tagText = 'Hệ thống';

            switch ($noti->type) {
                case 'order_created':
                    $iconClass = 'icon-order';
                    $iconName = 'fa-receipt';
                    $tagText = 'Đơn hàng';
                    break;
                case 'stock_warning':
                    $iconClass = 'icon-stock';
                    $iconName = 'fa-triangle-exclamation';
                    $tagText = 'Kho hàng';
                    break;
                case 'user_registered':
                    $iconClass = 'icon-user';
                    $iconName = 'fa-user-plus';
                    $tagText = 'Khách hàng';
                    break;
            }
        @endphp

        {{-- nhớ thêm data-mark-url và data-link-url để JS dùng --}}
        <div class="notify-card {{ $noti->is_read ? '' : 'is-unread' }}"
            data-mark-url="{{ route('admin.notifications.mark_read', $noti->id) }}"
            data-link-url="{{ $noti->link_url ?? '' }}">

            {{-- NÚT "ĐÁNH DẤU ĐÃ ĐỌC" GÓC PHẢI TRÊN --}}


            <div class="notify-card-main">
                <div class="notify-card-icon {{ $iconClass }}">
                    <i class="fa-solid {{ $iconName }}"></i>
                </div>

                <div class="notify-card-body">
                    <div class="notify-card-title-row">
                        <h3 class="notify-card-title">{{ $noti->title }}</h3>

                        @unless ($noti->is_read)
                            <span class="notify-dot"></span>
                            <span class="notify-priority">Mới</span>
                        @endunless
                    </div>

                    {{-- dòng mô tả ngắn --}}
                    @if ($noti->message)
                        <p class="notify-card-text">{{ $noti->message }}</p>
                    @endif

                    {{-- BLOCK CHI TIẾT ĐƠN HÀNG --}}
                    @if (!empty($noti->details))
                        <div class="notify-card-details">
                            {{ $noti->details }}
                        </div>
                    @endif

                    <div class="notify-card-meta">
                        <span>
                            <i class="fa-regular fa-clock"></i> {{ $timeText }}
                        </span>
                        <span class="notify-card-tag">{{ $tagText }}</span>
                    </div>
                </div>
            </div>

            <div class="notify-card-actions">
                {{-- chỉ còn nút Xóa --}}
                <form action="{{ route('admin.notifications.destroy', $noti->id) }}" method="POST"
                    class="notify-delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="icon-btn delete">
                        <i class="fa-regular fa-trash-can"></i>
                    </button>
                </form>


            </div>
        </div>
    @empty
        <p class="empty">Không có thông báo nào.</p>
    @endforelse
</div>

<div class="pagination-wrapper">
    {{ $notifications->links('pagination::bootstrap-4') }}
</div>
