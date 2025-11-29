@extends('components.admin_header')

@section('title', 'Quản lý tài khoản quản trị viên')

@push('styles')
    {{-- Tái sử dụng CSS layout người dùng cho nhanh, sau muốn tách thì đổi file khác --}}
    <link rel="stylesheet" href="{{ asset('assets') }}/css/admin/admin_users.css">
@endpush

@section('content')

    {{-- HEADER --}}
    <div class="content-header">
        <div>
            <h1>Quản Lý Tài Khoản Quản Trị</h1>
            <p>Danh sách các tài khoản có quyền quản trị hệ thống.</p>
        </div>

        <div class="header-actions">
            {{-- Nút mở popup thêm admin --}}
            <button type="button" class="primary-btn" id="btnOpenAdminModal">
               <i class="fa-solid fa-user-shield"></i> Thêm Quản Trị Viên
            </button>
        </div>
    </div>

    {{-- DANH SÁCH QUẢN TRỊ VIÊN --}}
    <div class="panel">
        <div class="panel-header">
            <h2>Danh sách quản trị viên</h2>
        </div>

        <div class="user-table-wrapper">
            @if($accounts->count() > 0)
                <div class="user-table-scroll">
                    <table class="user-table">
                        <thead>
                        <tr>
                            <th>Quản trị viên</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($accounts as $admin)
                            @php
                                // avatar = ký tự đầu tên
                                $initial = mb_strtoupper(
                                    mb_substr($admin->name ?? 'A', 0, 1, 'UTF-8')
                                );

                                // trạng thái (nếu có cột status trong bảng admins)
                                $status = $admin->status ?? 'active';
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

                                // ngày tạo
                                $created = $admin->created_at
                                    ? \Carbon\Carbon::parse($admin->created_at)->format('d/m/Y')
                                    : '-';
                            @endphp

                            <tr>
                                {{-- Quản trị viên --}}
                                <td class="col-user">
                                    <div class="user-cell">
                                        <div class="user-avatar">{{ $initial }}</div>
                                        <div class="user-info">
                                            <div class="user-name">{{ $admin->name }}</div>
                                            @if(!empty($admin->email))
                                                <div class="user-email">{{ $admin->email }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Trạng thái --}}
                                <td class="col-status">
                                    <span class="status-chip {{ $statusClass }}">{{ $statusLabel }}</span>
                                </td>

                                {{-- Ngày tạo --}}
                                <td class="col-joined">
                                    {{ $created }}
                                </td>

                                {{-- Thao tác --}}
                                <td class="col-actions">
                                    <div class="table-actions">
                                        {{-- Sửa: dùng data-* để đổ vào modal update --}}
                                        <button type="button"
                                                class="icon-btn edit btnEditAdmin"
                                                title="Chỉnh sửa thông tin quản trị viên"
                                                data-id="{{ $admin->id }}"
                                                data-name="{{ $admin->name }}"
                                                data-status="{{ $status }}"
                                                data-update-url="{{ route('admin.accounts.update', $admin->id) }}">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>

                                        {{-- Xóa --}}
                                        <a href="{{ route('admin.accounts.delete', $admin->id) }}"
                                           class="icon-btn delete"
                                           title="Xóa quản trị viên"
                                           onclick="return confirm('Xóa tài khoản quản trị viên này?');">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="empty">Không có tài khoản quản trị viên nào!</p>
            @endif
        </div>
    </div>

    {{-- ========== MODAL THÊM QUẢN TRỊ VIÊN ========== --}}
    <div class="user-modal-overlay" id="adminCreateModal"
         data-has-errors="{{ session('form') === 'create' && $errors->any() ? '1' : '0' }}">
        <div class="user-modal">
            <div class="user-modal-header">
                <h2>Thêm Quản Trị Viên Mới</h2>
                <button type="button" class="user-modal-close" id="btnCloseAdminModal">
                    &times;
                </button>
            </div>

            <form action="{{ route('admin.register_submit') }}" method="POST">
                @csrf
                <input type="hidden" name="form" value="create">

                <div class="user-modal-body">
                    <div class="user-modal-left">
                        <div class="user-modal-grid">

                            {{-- Tên quản trị viên --}}
                            <div class="form-group">
                                <label for="admin_name">Tên Quản Trị Viên <span class="required">*</span></label>
                                <input type="text"
                                       id="admin_name"
                                       name="name"
                                       value="{{ old('name') }}"
                                       placeholder="Nhập tên quản trị viên"
                                       oninput="this.value = this.value.replace(/\s/g, '')">
                            </div>

                            {{-- Mật khẩu --}}
                            <div class="form-group">
                                <label for="admin_pass">Mật khẩu <span class="required">*</span></label>
                                <input type="password"
                                       id="admin_pass"
                                       name="pass"
                                       placeholder="Nhập mật khẩu"
                                       oninput="this.value = this.value.replace(/\s/g, '')">
                            </div>

                            {{-- Nhập lại mật khẩu --}}
                            <div class="form-group">
                                <label for="admin_pass_confirmation">Nhập lại mật khẩu <span class="required">*</span></label>
                                <input type="password"
                                       id="admin_pass_confirmation"
                                       name="pass_confirmation"
                                       placeholder="Nhập lại mật khẩu"
                                       oninput="this.value = this.value.replace(/\s/g, '')">
                            </div>

                            {{-- Trạng thái --}}
                            <div class="form-group">
                                <label for="admin_status">Trạng thái</label>
                                <select id="admin_status" name="status">
                                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="locked" {{ old('status') === 'locked' ? 'selected' : '' }}>Tạm khóa</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="user-modal-right">
                        <div class="user-note-card">
                            <h4><i class="fa-solid fa-circle-info"></i> Lưu ý</h4>
                            <ul>
                                <li>Chỉ tạo tài khoản cho người có quyền quản lý hệ thống.</li>
                                <li>Mật khẩu tối thiểu 6 ký tự để đảm bảo an toàn.</li>
                                <li>Có thể tạm khóa tài khoản nếu không sử dụng.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Hiển thị lỗi nếu lỗi thuộc form create --}}
                @if (session('form') === 'create' && $errors->any())
                    <div class="user-modal-errors">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="user-modal-footer">
                    <button type="button" class="btn-outline" id="btnCancelAdminModal">
                        Hủy
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu Quản Trị Viên
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========== MODAL CẬP NHẬT QUẢN TRỊ VIÊN ========== --}}
    <div class="user-modal-overlay" id="adminEditModal"
         data-has-errors="{{ session('form') === 'edit' && $errors->any() ? '1' : '0' }}">
        <div class="user-modal">
            <div class="user-modal-header">
                <h2>Cập nhật Quản Trị Viên</h2>
                <button type="button" class="user-modal-close" id="btnCloseAdminEditModal">
                    &times;
                </button>
            </div>

            <form id="adminEditForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="form" value="edit">

                <div class="user-modal-body">
                    <div class="user-modal-left">
                        <div class="user-modal-grid">

                            {{-- Tên quản trị viên --}}
                            <div class="form-group">
                                <label for="edit_admin_name">Tên Quản Trị Viên <span class="required">*</span></label>
                                <input type="text"
                                       id="edit_admin_name"
                                       name="name"
                                       value=""
                                       placeholder="Nhập tên quản trị viên"
                                       oninput="this.value = this.value.replace(/\s/g, '')">
                            </div>

                            {{-- Trạng thái --}}
                            <div class="form-group">
                                <label for="edit_admin_status">Trạng thái</label>
                                <select id="edit_admin_status" name="status">
                                    <option value="active">Hoạt động</option>
                                    <option value="locked">Tạm khóa</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="user-modal-right">
                        <div class="user-note-card">
                            <h4><i class="fa-solid fa-circle-info"></i> Lưu ý</h4>
                            <ul>
                                <li>Chỉ sửa những thông tin cần thiết.</li>
                                <li>Tạm khóa tài khoản sẽ không cho phép quản trị viên đăng nhập.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Nếu có lỗi validate cho form edit --}}
                @if (session('form') === 'edit' && $errors->any())
                    <div class="user-modal-errors">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="user-modal-footer">
                    <button type="button" class="btn-outline" id="btnCancelAdminEditModal">
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
    <script src="{{ asset('assets/js/admin_account.js') }}"></script>
@endpush
