@extends('components.admin_header')

@section('title', 'Quản lý người dùng')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets') }}/css/admin/admin_users.css">
<link rel="stylesheet" href="{{ asset('assets') }}/css/admin/flash_toast.css">
@endpush

@section('content')
{{-- TOAST MESSAGE --}}
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
        <h1>Quản Lý Người Dùng</h1>
        <p>Theo dõi hoạt động và trạng thái tài khoản.</p>
    </div>

    <div class="header-actions">
        <button type="button" class="primary-btn" id="btnOpenUserModal">
            <i class="fa-solid fa-user-plus"></i> Thêm Người Dùng
        </button>
    </div>
</div>


{{-- THANH TÌM KIẾM --}}
<div class="user-filters">
    <form action="{{ route('admin.users_accounts') }}" method="GET" class="search-bar">
        <div class="search-input-wrapper">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text"
                name="search_query"
                placeholder="Tìm kiếm người dùng..."
                value="{{ request('search_query') }}">
        </div>

        <div class="filter-inline">
            <select name="search_type" class="filter-select">
                <option value="name" {{ request('search_type') === 'name' ? 'selected' : '' }}>Theo tên</option>
                <option value="email" {{ request('search_type') === 'email' ? 'selected' : '' }}>Theo email</option>
                <option value="id" {{ request('search_type') === 'id' ? 'selected' : '' }}>Theo ID</option>
            </select>

            <select name="status" class="filter-select">
                <option value="">Tất cả trạng thái</option>
                <option value="active" {{ request('status') === 'active'   ? 'selected' : '' }}>Hoạt động</option>
                <option value="locked" {{ request('status') === 'locked'   ? 'selected' : '' }}>Tạm khóa</option>
            </select>

            <button type="submit" class="filter-btn">
                <i class="fa-solid fa-filter"></i> Lọc
            </button>
        </div>
    </form>
</div>

{{-- DANH SÁCH NGƯỜI DÙNG --}}
<div class="panel">
    <div class="panel-header">
        <h2>Danh sách người dùng</h2>
    </div>

    <div class="user-table-wrapper">
        @if($accounts->count() > 0)
        <div class="user-table-scroll">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>Người dùng</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Đơn hàng</th>
                        <th>Ngày tham gia</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accounts as $account)
                    @php
                    // avatar = ký tự đầu tên
                    $initial = mb_strtoupper(mb_substr($account->name ?? 'U', 0, 1, 'UTF-8'));

                    // vai trò
                    $roleLabel = 'Khách hàng';
                    $roleClass = 'role-badge-customer';

                    // trạng thái
                    $status = $account->status ?? 'active';
                    switch ($status) {
                    case 'locked':
                    $statusLabel = 'Tạm khóa';
                    $statusClass = 'status-chip-locked';
                    break;
                    default:
                    $statusLabel = 'Hoạt động';
                    $statusClass = 'status-chip-active';
                    break;
                    }

                    // số đơn (nếu chưa có thì coi như 0)
                    $ordersCount = $account->orders_count ?? 0;

                    // ngày tham gia
                    $joined = $account->created_at
                    ? \Carbon\Carbon::parse($account->created_at)->format('d/m/Y')
                    : '-';
                    @endphp

                    <tr>
                        {{-- Người dùng --}}
                        <td class="col-user">
                            <div class="user-cell">
                                <div class="user-avatar">{{ $initial }}</div>
                                <div class="user-info">
                                    <div class="user-name">{{ $account->name }}</div>
                                    <div class="user-email">{{ $account->email }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Vai trò --}}
                        <td class="col-role">
                            <span class="role-badge {{ $roleClass }}">{{ $roleLabel }}</span>
                        </td>

                        {{-- Trạng thái --}}
                        <td class="col-status">
                            <span class="status-chip {{ $statusClass }}">{{ $statusLabel }}</span>
                        </td>

                        {{-- Đơn hàng --}}
                        <td class="col-orders">
                            {{ $ordersCount }}
                        </td>

                        {{-- Ngày tham gia --}}
                        <td class="col-joined">
                            {{ $joined }}
                        </td>

                        {{-- Thao tác --}}
                        <td class="col-actions">
                            <div class="table-actions">
                                {{-- Nút sửa --}}
                                <button type="button"
                                        class="icon-btn edit btnEditUser"
                                        title="Chỉnh sửa"
                                        data-id="{{ $account->id }}"
                                        data-name="{{ $account->name }}"
                                        data-email="{{ $account->email }}"  
                                        data-status="{{ $status }}">   {{-- active / locked --}}
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </button>

                                {{-- Xóa --}}
                                <form action="{{ route('admin.users_delete', $account->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Xóa tài khoản này? Thông tin liên quan người dùng cũng sẽ bị xóa!');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="icon-btn delete" title="Xóa">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- PHÂN TRANG --}}
        @if ($accounts->hasPages())
        <div class="pagination-wrapper">
            {{ $accounts->links('pagination::bootstrap-4') }}
        </div>
        @endif
        @else
        <p class="empty">Không có tài khoản nào!</p>
        @endif
    </div>
</div>
    {{-- MODAL THÊM NGƯỜI DÙNG MỚI --}}
    <div class="user-modal-overlay" id="userCreateModal" data-has-errors="{{ $errors->any() ? '1' : '0' }}">
        <div class="user-modal">
            <div class="user-modal-header">
                <h2>Thêm Người Dùng Mới</h2>
                <button type="button" class="user-modal-close" id="btnCloseUserModal">
                    &times;
                </button>
            </div>

            <form action="{{ route('admin.users_store') }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf

                <div class="user-modal-body">
                    {{-- Cột trái + giữa: thông tin tài khoản --}}
                    <div class="user-modal-left">
                        <div class="user-modal-grid">

                            {{-- Tên người dùng --}}
                            <div class="form-group">
                                <label for="name">Tên Người Dùng <span class="required">*</span></label>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       value="{{ old('name') }}"
                                       placeholder="Nhập tên người dùng">
                            </div>

                            {{-- Email --}}
                            <div class="form-group">
                                <label for="email">Email <span class="required">*</span></label>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       placeholder="Nhập email đăng nhập">
                            </div>

                            {{-- Mật khẩu --}}
                            <div class="form-group">
                                <label for="password">Mật khẩu <span class="required">*</span></label>
                                <input type="password"
                                       id="password"
                                       name="password"
                                       placeholder="Nhập mật khẩu">
                            </div>

                            {{-- Nhập lại mật khẩu --}}
                            <div class="form-group">
                                <label for="password_confirmation">Nhập lại mật khẩu <span class="required">*</span></label>
                                <input type="password"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       placeholder="Nhập lại mật khẩu">
                            </div>

                            {{-- Vai trò --}}

                            {{-- Trạng thái --}}
                            <div class="form-group">
                                <label for="status">Trạng thái</label>
                                <select id="status" name="status">
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="locked" {{ old('status') === 'locked' ? 'selected' : '' }}>Tạm khóa</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Cột phải: Avatar + ghi chú (giống layout ảnh sản phẩm + lưu ý) --}}
                    <div class="user-modal-right">
                        <div class="user-avatar-card">
                            <h3>Ảnh Đại Diện (tuỳ chọn)</h3>
                            <div class="user-avatar-dropzone">
                                <span class="icon">
                                    <i class="fa-regular fa-image"></i>
                                </span>
                                <p>Nhấp để tải ảnh lên</p>
                                <small>PNG, JPG (tối đa 5MB)</small>
                                <input type="file"
                                       name="avatar"
                                       accept=".png,.jpg,.jpeg">
                            </div>
                        </div>

                        <div class="user-note-card">
                            <h4><i class="fa-solid fa-circle-info"></i> Lưu ý</h4>
                            <ul>
                                <li>Điền đầy đủ thông tin bắt buộc.</li>
                                <li>Email phải là duy nhất, không trùng với tài khoản đã có.</li>
                                <li>Mật khẩu tối thiểu 6 ký tự để đảm bảo an toàn.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Hiển thị lỗi nếu có --}}
                @if ($errors->any())
                    <div class="user-modal-errors">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="user-modal-footer">
                    <button type="button" class="btn-outline" id="btnCancelUserModal">
                        Hủy
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu Người Dùng
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- MODAL CẬP NHẬT NGƯỜI DÙNG --}}
<div class="user-modal-overlay" id="userEditModal">
    <div class="user-modal">
        <div class="user-modal-header">
            <h2>Cập nhật Người Dùng</h2>
            <button type="button" class="user-modal-close" id="btnCloseUserEditModal">
                &times;
            </button>
        </div>

        <form id="userEditForm" method="POST">
            @csrf
            @method('PUT')

            <div class="user-modal-body">
                {{-- Cột trái + giữa: thông tin tài khoản --}}
                <div class="user-modal-left">
                    <div class="user-modal-grid">

                        {{-- Tên người dùng --}}
                        <div class="form-group">
                            <label for="edit_name">Tên Người Dùng <span class="required">*</span></label>
                            <input type="text"
                                   id="edit_name"
                                   name="name"
                                   value=""
                                   placeholder="Nhập tên người dùng">
                        </div>

                        {{-- Email --}}
                        <div class="form-group">
                            <label for="edit_email">Email <span class="required">*</span></label>
                            <input type="email"
                                   id="edit_email"
                                   name="email"
                                   value=""
                                   placeholder="Nhập email đăng nhập">
                        </div>

                        {{-- Vai trò --}}

                        {{-- Trạng thái --}}
                        <div class="form-group">
                            <label for="edit_status">Trạng thái</label>
                            <select id="edit_status" name="status">
                                <option value="active">Hoạt động</option>
                                <option value="locked">Tạm khóa</option>
                            </select>
                        </div>

                        {{-- (Không cho sửa mật khẩu ở đây, nếu cần thì làm popup riêng) --}}
                    </div>
                </div>

                {{-- Cột phải: Ghi chú --}}
                <div class="user-modal-right">
                    <div class="user-note-card">
                        <h4><i class="fa-solid fa-circle-info"></i> Lưu ý</h4>
                        <ul>
                            <li>Chỉ sửa những thông tin cần thiết.</li>
                            <li>Tạm khóa tài khoản sẽ không cho phép người dùng đăng nhập.</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Nếu có lỗi validate khi update, bạn có thể hiển thị ở đây (tuỳ bạn muốn xử lý) --}}

            <div class="user-modal-footer">
                <button type="button" class="btn-outline" id="btnCancelUserEditModal">
                    Hủy
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu Thay Đổi
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/admin_users.js') }}"></script>
    <script src="{{ asset('assets/js/flash_toast.js') }}"></script>
@endpush
