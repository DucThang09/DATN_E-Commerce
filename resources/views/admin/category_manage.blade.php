@extends('components.admin_header')

@section('title', 'Quản lý sản phẩm')

@push('styles')
    {{-- CSS riêng cho trang sản phẩm --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/admin/category_manage.css">
@endpush

@section('content')
    <div class="category-page">
        {{-- Thông báo --}}
        @if (session('success'))
            <div class="alert-success js-flash">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- ========== HEADER + TAB ========== --}}
        <div class="page-top">
            <div class="page-title">
                <h1>Quản lý danh mục</h1>
                <p>Thêm, sửa, xóa và quản lý danh mục / thương hiệu / màu sắc sản phẩm.</p>
            </div>

            <div class="page-tabs">
                <button class="tab-btn active" data-target="#tab-category">
                    <i class="fa-regular fa-folder"></i>
                    <span>Danh mục</span>
                </button>
                <button class="tab-btn" data-target="#tab-brand">
                    <i class="fa-solid fa-tags"></i>
                    <span>Thương hiệu</span>
                </button>
                <button class="tab-btn" data-target="#tab-color">
                    <i class="fa-solid fa-droplet"></i>
                    <span>Màu sắc</span>
                </button>
            </div>
        </div>

        {{-- ================== TAB DANH MỤC ================== --}}
        <div id="tab-category" class="tab-pane active">
            <div class="pane-header">
                <h2>Quản lý danh mục</h2>

                <div class="pane-actions">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" placeholder="Tìm kiếm danh mục..." class="table-search"
                            data-target-table="#category-table">
                    </div>

                    <button type="button" class="btn-primary" data-toggle-form="#form-category">
                        <i class="fa-solid fa-plus"></i>
                        <span>Thêm danh mục</span>
                    </button>
                </div>
            </div>

            {{-- Form thêm danh mục --}}
            <div id="form-category" class="form-card">
                <form action="{{ route('admin.category.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Tên Danh Mục <span class="required">*</span></label>
                            <input type="text" class="input" required maxlength="100" placeholder="Nhập tên danh mục"
                                name="name" value="{{ old('name') }}">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Lưu danh mục</button>
                    </div>
                </form>
            </div>
            {{-- Bảng danh mục --}}
            <div class="table-wrapper">
                @if ($categories->count() > 0)
                    <table class="nice-table" id="category-table">
                        <thead>
                            <tr>
                                <th>Danh mục</th>
                                <th>Slug</th>
                                <th>Mô tả</th>
                                <th>Số sản phẩm</th>
                                <th class="text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td class="cell-name">
                                        <div class="cell-icon">
                                            <i class="fa-solid fa-mobile-screen-button"></i>
                                        </div>
                                        <div>
                                            <p class="main-text">{{ $category->category_name }}</p>
                                            <p class="sub-text">ID: {{ $category->category_id }}</p>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="slug-pill">
                                            {{ $category->slug ?? 'chưa có' }}
                                        </span>
                                    </td>

                                    <td class="cell-desc">
                                        {{ $category->description ?? 'Chưa có mô tả' }}
                                    </td>

                                    <td class="cell-number">
                                        {{ $category->product_count ?? 0 }}
                                    </td>
                                    <td class="cell-actions">
                                        <button type="button" class="action-btn edit js-edit" title="Sửa"
                                            data-type="category" data-id="{{ $category->category_id }}"
                                            data-name="{{ $category->category_name }}"
                                            data-action="{{ route('admin.category.update', $category->category_id) }}">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>
                                        <a href="{{ route('admin.category.delete', ['category_id' => $category->category_id]) }}"
                                            class="action-btn delete" onclick="return confirm('Xóa danh mục này?');"
                                            title="Xóa">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="empty">Chưa có danh mục nào được thêm!</p>
                @endif
            </div>
        </div>

        {{-- ================== TAB THƯƠNG HIỆU ================== --}}
        <div id="tab-brand" class="tab-pane">
            <div class="pane-header">
                <h2>Quản lý thương hiệu</h2>

                <div class="pane-actions">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" placeholder="Tìm kiếm thương hiệu..." class="table-search"
                            data-target-table="#brand-table">
                    </div>

                    <button type="button" class="btn-primary" data-toggle-form="#form-brand">
                        <i class="fa-solid fa-plus"></i>
                        <span>Thêm thương hiệu</span>
                    </button>
                </div>
            </div>

            <div id="form-brand" class="form-card">
                <form action="{{ route('admin.brand.store_brand') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Tên Thương Hiệu <span class="required">*</span></label>
                            <input type="text" class="input" required maxlength="100"
                                placeholder="Nhập tên thương hiệu" name="name" value="{{ old('name') }}">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Lưu thương hiệu</button>
                    </div>
                </form>
            </div>

            <div class="table-wrapper">
                @if ($brands->count() > 0)
                    <table class="nice-table" id="brand-table">
                        <thead>
                            <tr>
                                <th>Thương hiệu</th>
                                <th class="text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($brands as $brand)
                                <tr>
                                    <td class="cell-name">
                                        <div class="cell-icon">
                                            <i class="fa-solid fa-tag"></i>
                                        </div>
                                        <div>
                                            <p class="main-text">{{ $brand->brand_name }}</p>
                                            <p class="sub-text">ID: {{ $brand->brand_id }}</p>
                                        </div>
                                    </td>

                                    <td class="cell-actions">
                                        <button type="button" class="action-btn edit js-edit" title="Sửa"
                                            data-type="brand" data-id="{{ $brand->brand_id }}"
                                            data-name="{{ $brand->brand_name }}"
                                            data-action="{{ route('admin.brand.update_brand', $brand->brand_id) }}">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>
                                        <a href="{{ route('admin.brand.delete_brand', $brand->brand_id) }}"
                                            class="action-btn delete" onclick="return confirm('Xóa thương hiệu này?');"
                                            title="Xóa">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="empty">Chưa có thương hiệu nào được thêm!</p>
                @endif
            </div>
        </div>

        {{-- ================== TAB MÀU SẮC ================== --}}
        <div id="tab-color" class="tab-pane">
            <div class="pane-header">
                <h2>Quản lý màu sản phẩm</h2>

                <div class="pane-actions">
                    <div class="search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" placeholder="Tìm kiếm màu..." class="table-search"
                            data-target-table="#color-table">
                    </div>

                    <button type="button" class="btn-primary" data-toggle-form="#form-color">
                        <i class="fa-solid fa-plus"></i>
                        <span>Thêm màu</span>
                    </button>
                </div>
            </div>

            <div id="form-color" class="form-card">
                <form action="{{ route('admin.color.store_color') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Tên Màu Sản Phẩm <span class="required">*</span></label>
                            <input type="text" class="input" required maxlength="100"
                                placeholder="Nhập màu sản phẩm" name="name" value="{{ old('name') }}">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Lưu màu</button>
                    </div>
                </form>
            </div>

            <div class="table-wrapper">
                @if ($color->count() > 0)
                    <table class="nice-table" id="color-table">
                        <thead>
                            <tr>
                                <th>Màu</th>
                                <th class="text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($color as $colorP)
                                <tr>
                                    <td class="cell-name">
                                        <div class="color-circle"></div>
                                        <div>
                                            <p class="main-text">{{ $colorP->colorProduct }}</p>
                                            <p class="sub-text">ID: {{ $colorP->colorProduct_id }}</p>
                                        </div>
                                    </td>

                                    <td class="cell-actions">
                                        <button type="button" class="action-btn edit js-edit" title="Sửa"
                                            data-type="color" data-id="{{ $colorP->colorProduct_id }}"
                                            data-name="{{ $colorP->colorProduct }}"
                                            data-action="{{ route('admin.color.update_color', $colorP->colorProduct_id) }}">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>
                                        <a href="{{ route('admin.color.delete_color', $colorP->colorProduct_id) }}"
                                            class="action-btn delete" onclick="return confirm('Xóa màu này?');"
                                            title="Xóa">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="empty">Chưa có màu nào được thêm!</p>
                @endif
            </div>
        </div>
    </div> {{-- .category-page --}}
    {{-- ===== MODAL EDIT (dùng chung) ===== --}}
    <div id="editModal" class="a-modal" aria-hidden="true">
        <div class="a-modal__backdrop js-close-modal"></div>

        <div class="a-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="editModalTitle">
            <button type="button" class="a-modal__x js-close-modal" title="Đóng">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="a-modal__head">
                <h2 id="editModalTitle" class="a-modal__title">Cập nhật</h2>
                <p class="a-modal__sub">Chỉnh sửa thông tin và bấm “Cập nhật” để lưu.</p>
            </div>

            <form id="editForm" method="POST" class="a-modal__body">
                @csrf
                <input type="hidden" name="tab" id="editTab" value="category">

                <div class="a-modal__grid">
                    {{-- LEFT --}}
                    <div class="a-card">
                        <div class="a-card__title">Thông tin</div>

                        <div class="a-field">
                            <label class="a-label">Tên <span class="required">*</span></label>
                            <input id="editName" name="name" type="text" class="a-input" required
                                maxlength="100" placeholder="Nhập tên..." />
                        </div>

                        {{-- nếu sau này bạn muốn thêm trường khác theo type thì thêm tại đây --}}
                    </div>

                    {{-- RIGHT --}}
                    <div class="a-card a-card--hint">
                        <div class="a-card__title" id="editHintTitle">Gợi ý</div>
                        <ul class="a-hint">
                            <li>Đặt tên ngắn gọn, dễ hiểu.</li>
                            <li>Tránh trùng tên đã có.</li>
                            <li>Bấm “Cập nhật” để lưu thay đổi.</li>
                        </ul>
                    </div>
                </div>

                <div class="a-modal__foot">
                    <button type="button" class="btn-ghost js-close-modal">Hủy</button>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        // ====== Chuyển tab ======
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.getAttribute('data-target');

                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));

                btn.classList.add('active');
                document.querySelector(target).classList.add('active');
            });
        });
        (function() {
            const params = new URLSearchParams(window.location.search);
            const tab = params.get('tab'); // category | brand | color

            const map = {
                category: '#tab-category',
                brand: '#tab-brand',
                color: '#tab-color',
            };

            if (!tab || !map[tab]) return;

            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));

            const btn = document.querySelector(`.tab-btn[data-target="${map[tab]}"]`);
            const pane = document.querySelector(map[tab]);

            if (btn) btn.classList.add('active');
            if (pane) pane.classList.add('active');
        })();

        // ====== Toggle form thêm mới ======
        document.querySelectorAll('[data-toggle-form]').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.getAttribute('data-toggle-form');
                const form = document.querySelector(target);
                form.classList.toggle('open');
            });
        });

        // ====== Search filter trong bảng ======
        document.querySelectorAll('.table-search').forEach(input => {
            input.addEventListener('input', () => {
                const keyword = input.value.toLowerCase();
                const tableSelector = input.getAttribute('data-target-table');
                const rows = document.querySelectorAll(tableSelector + ' tbody tr');

                rows.forEach(row => {
                    const text = row.innerText.toLowerCase();
                    row.style.display = text.includes(keyword) ? '' : 'none';
                });
            });
        });
    </script>
    <script>
        (function() {
            const modal = document.getElementById('editModal');
            const form = document.getElementById('editForm');
            const titleEl = document.getElementById('editModalTitle');
            const hintTitleEl = document.getElementById('editHintTitle');
            const inputName = document.getElementById('editName');
            const inputTab = document.getElementById('editTab');

            function openModal() {
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                setTimeout(() => inputName?.focus(), 0);
            }

            function closeModal() {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                form.setAttribute('action', '');
                inputName.value = '';
            }

            // open
            document.querySelectorAll('.js-edit').forEach(btn => {
                btn.addEventListener('click', () => {
                    const type = btn.dataset.type; // category | brand | color
                    const name = btn.dataset.name || '';
                    const action = btn.dataset.action || '';

                    form.setAttribute('action', action);
                    inputName.value = name;
                    inputTab.value = type; // ✅ category|brand|color


                    // đổi tiêu đề theo loại
                    if (type === 'category') {
                        titleEl.textContent = 'Cập nhật danh mục';
                        hintTitleEl.textContent = 'Gợi ý cho danh mục';
                        inputName.placeholder = 'Nhập tên danh mục...';
                    } else if (type === 'brand') {
                        titleEl.textContent = 'Cập nhật thương hiệu';
                        hintTitleEl.textContent = 'Gợi ý cho thương hiệu';
                        inputName.placeholder = 'Nhập tên thương hiệu...';
                    } else {
                        titleEl.textContent = 'Cập nhật màu sắc';
                        hintTitleEl.textContent = 'Gợi ý cho màu sắc';
                        inputName.placeholder = 'Nhập tên màu...';
                    }

                    openModal();
                });
            });

            // close
            modal.querySelectorAll('.js-close-modal').forEach(el => {
                el.addEventListener('click', closeModal);
            });

            // ESC to close
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
            });

        })();
    </script>
    <script>
        (function() {
            const flash = document.querySelector('.js-flash');
            if (!flash) return;
            // tự ẩn sau 3 giây
            setTimeout(() => {
                flash.style.transition = 'opacity .25s ease, transform .25s ease';
                flash.style.opacity = '0';
                flash.style.transform = 'translateY(-6px)';
                setTimeout(() => flash.remove(), 260);
            }, 3000);
        })();
    </script>
@endpush
