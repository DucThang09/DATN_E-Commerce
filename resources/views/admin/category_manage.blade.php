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
            <div class="alert-success">
                {{ session('success') }}
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

        {{-- ========== THẺ THỐNG KÊ (demo số) ========== --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-regular fa-folder"></i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Tổng danh mục</p>
                    <p class="stat-value">{{ $categories->count() }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Tổng sản phẩm</p>
                    <p class="stat-value">{{ $totalProducts }}</p> {{-- nếu có $totalProducts thì dùng biến --}}
                </div>
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
                                        {{-- Nếu sau có route edit / show thì thêm vào --}}
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
@endpush
