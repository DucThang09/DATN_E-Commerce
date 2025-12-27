@extends('components.admin_header')

@section('title', 'Quản lý sản phẩm')

@push('styles')
    {{-- CSS riêng cho trang sản phẩm --}}
    <link rel="stylesheet" href="{{ asset('assets') }}/css/admin/admin_product.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/admin/flash_toast.css">
@endpush

@section('content')
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
    {{-- HEADER + NÚT THÊM --}}
    <div class="content-header">
        <div>
            <h1>Quản lý sản phẩm</h1>
            <p>Thêm mới và quản lý danh sách sản phẩm.</p>
        </div>
        <div class="header-actions">
            <button type="button" class="primary-btn" id="openAddProductModal">
                <i class="fa-solid fa-plus"></i> Thêm Sản Phẩm
            </button>
        </div>
    </div>

    {{-- THANH TÌM KIẾM + BỘ LỌC --}}
    <div class="product-filters">
        <form method="GET" action="{{ route('admin.products') }}" class="search-bar" id="productFilterForm">

            {{-- Ô tìm kiếm --}}
            <div class="search-input-wrapper">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" placeholder="Tìm kiếm sản phẩm..." value="{{ request('search') }}">
            </div>

            {{-- NÚT MỞ BỘ LỌC --}}
            <button type="button" class="filter-btn" id="openFilterPanel">
                <i class="fa-solid fa-filter"></i> Lọc
            </button>

            {{-- PANEL BỘ LỌC --}}
            <div class="filter-panel" id="filterPanel">
                <div class="filter-panel-header">
                    <span class="filter-panel-title">Bộ lọc sản phẩm</span>
                    <button type="button" class="filter-panel-close" id="closeFilterPanel">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="filter-panel-body">
                    {{-- Danh mục --}}
                    <div class="filter-group">
                        <label>
                            <span>Danh mục</span>
                            <select name="category_filter">
                                <option value="">Tất cả danh mục</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->category_id }}"
                                        {{ (string) request('category_filter') === (string) $category->category_id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    {{-- Trạng thái tồn kho --}}
                    <div class="filter-group">
                        <label>
                            <span>Trạng thái</span>
                            <select name="status_filter">
                                <option value="">Tất cả trạng thái</option>
                                <option value="in_stock" {{ request('status_filter') == 'in_stock' ? 'selected' : '' }}>Còn
                                    hàng</option>
                                <option value="low_stock" {{ request('status_filter') == 'low_stock' ? 'selected' : '' }}>
                                    Sắp hết</option>
                                <option value="out_stock" {{ request('status_filter') == 'out_stock' ? 'selected' : '' }}>
                                    Hết hàng</option>
                            </select>
                        </label>
                    </div>

                    {{-- Khoảng giá --}}
                    <div class="filter-group">
                        <label>
                            <span>Khoảng giá</span>
                            <select name="price_filter">
                                <option value="">Tất cả mức giá</option>
                                <option value="lt_5m" {{ request('price_filter') == 'lt_5m' ? 'selected' : '' }}>
                                    < 5.000.000đ</option>
                                <option value="5_15m" {{ request('price_filter') == '5_15m' ? 'selected' : '' }}>
                                    5.000.000 – 15.000.000đ</option>
                                <option value="gt_15m" {{ request('price_filter') == 'gt_15m' ? 'selected' : '' }}>>
                                    15.000.000đ</option>
                            </select>
                        </label>
                    </div>

                    {{-- Mức tồn kho --}}
                    <div class="filter-group">
                        <label>
                            <span>Mức tồn kho</span>
                            <select name="inventory_filter">
                                <option value="">Tất cả mức tồn</option>
                                <option value="lt_5" {{ request('inventory_filter') == 'lt_5' ? 'selected' : '' }}>
                                    < 5</option>
                                <option value="5_20" {{ request('inventory_filter') == '5_20' ? 'selected' : '' }}>5 –
                                    20</option>
                                <option value="gt_20" {{ request('inventory_filter') == 'gt_20' ? 'selected' : '' }}>> 20
                                </option>
                            </select>
                        </label>
                    </div>
                </div>

                <div class="filter-panel-footer">
                    <button type="button" class="btn-light" id="resetFilters">
                        Xóa bộ lọc
                    </button>
                    <button type="submit" class="btn-primary">
                        Áp dụng
                    </button>
                </div>
            </div>
            {{-- end .filter-panel --}}
        </form>
    </div>


    {{-- DANH SÁCH SẢN PHẨM (BẢNG) --}}
    <div class="panel">
        <div class="panel-header panel-header-with-actions">
            <h2>Danh sách sản phẩm</h2>
            <form method="GET" action="{{ route('admin.products') }}" class="sort-form">
                {{-- giữ lại các filter hiện tại --}}
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="category_filter" value="{{ request('category_filter') }}">
                <input type="hidden" name="status_filter" value="{{ request('status_filter') }}">
                <input type="hidden" name="price_filter" value="{{ request('price_filter') }}">
                <input type="hidden" name="inventory_filter" value="{{ request('inventory_filter') }}">

                <select name="sort" onchange="this.form.submit()">
                    <option value="">Sắp xếp</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>
                        Tên A → Z
                    </option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>
                        Tên Z → A
                    </option>
                </select>
            </form>


            <div class="panel-actions">
                {{-- NÚT BẬT/TẮT CHẾ ĐỘ CHỌN --}}
                <button type="button" class="secondary-btn" id="toggleSelectMode">
                    <i class="fa-regular fa-square-check"></i> Chọn
                </button>

                {{-- NÚT XÓA SẢN PHẨM ĐÃ CHỌN (mặc định ẩn / disabled) --}}
                <form action="{{ route('admin.products_delete_selected') }}" method="POST" id="bulkDeleteForm"
                    onsubmit="return confirm('Xóa các sản phẩm đã chọn?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="danger-btn" id="deleteSelectedBtn" disabled style="display:none;">
                        <i class="fa-regular fa-trash-can"></i> Xóa sản phẩm đã chọn
                    </button>
                </form>
            </div>
        </div>

        <div class="product-table-wrapper">
            @if ($products->count() > 0)
                {{-- KHUNG CÓ SCROLL CHỈ ÔM TABLE --}}
                <div class="product-table-scroll">
                    <table class="product-table" id="productTable">
                        <thead>
                            <tr>
                                <th class="select-col">
                                    <input type="checkbox" id="selectAllProducts" class="select-checkbox">
                                </th>
                                <th>Sản phẩm</th>
                                <th>Danh mục</th>
                                <th>Giá</th>
                                <th>Tồn kho</th>
                                <th>Đã bán</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td class="select-col">
                                        <input type="checkbox" class="select-checkbox product-checkbox"
                                            name="selected_products[]" form="bulkDeleteForm"
                                            value="{{ $product->id }}">
                                    </td>

                                    <td class="col-product">
                                        <div class="product-cell">
                                            @php
                                                $img = $product->v_image_01 ?? '';
                                                $img = ltrim($img, '/');
                                                $src = $img
                                                    ? asset('storage/' . $img)
                                                    : asset('assets/images/no-image.png');
                                            @endphp

                                            <img src="{{ $src }}" alt="{{ $product->name }}"
                                                class="product-thumb">

                                            <div class="product-info">
                                                <div class="product-name">{{ $product->name }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        {{ optional($categories->firstWhere('category_id', $product->category_id))->category_name }}
                                    </td>

                                    <td class="col-price">
                                        {{ number_format($product->price, 0, ',', '.') }}đ
                                    </td>

                                    {{-- Tồn kho --}}
                                    {{-- Tồn kho (tổng tất cả màu) --}}
                                    <td>{{ $product->total_inventory }}</td>
                                    <td>{{ $product->total_qty_sold }}</td>


                                    {{-- Trạng thái tồn kho --}}
                                    <td>
                                        @if (($product->total_inventory ?? 0) > 0)
                                            <span class="status-badge status-instock">Còn hàng</span>
                                        @else
                                            <span class="status-badge status-outstock">Hết hàng</span>
                                        @endif

                                    </td>

                                    {{-- Thao tác --}}
                                    <td>
                                        <div class="table-actions">
                                            <a href="#" class="icon-btn edit js-open-update" title="Cập nhật"
                                                data-update-url="{{ route('products.update', ['id' => $product->id]) }}"
                                                data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                                data-price="{{ $product->price }}"
                                                data-purchase-price="{{ $product->purchase_price }}"
                                                data-category-id="{{ $product->category_id }}"
                                                data-company="{{ $product->company }}"
                                                data-color="{{ $product->v_color }}"
                                                data-color-product-id="{{ $product->colorProduct_id }}"
                                                data-variant-id="{{ $product->variant_id }}"
                                                data-inventory="{{ $product->total_inventory }}"
                                                data-qty-sold="{{ $product->total_qty_sold }}"
                                                data-discount="{{ $product->discount }}"
                                                data-details="{{ e($product->details) }}"
                                                data-image1-url="{{ asset('storage/' . $product->v_image_01) }}"
                                                data-image2-url="{{ asset('storage/' . $product->v_image_02) }}"
                                                data-image3-url="{{ asset('storage/' . $product->v_image_03) }}"
                                                data-image1-path="{{ $product->v_image_01 }}"
                                                data-image2-path="{{ $product->v_image_02 }}"
                                                data-image3-path="{{ $product->v_image_03 }}"
                                                data-specs='@json($product->specs ?? [])'>
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
                                            {{-- ✅ THÊM NÚT NÀY: Thêm màu --}}
                                            <a href="#" class="icon-btn add js-open-add-variant" title="Thêm màu"
                                                data-product-id="{{ $product->id }}"
                                                data-product-name="{{ $product->name }}">
                                                <i class="fa-solid fa-palette"></i>
                                            </a>

                                            <a href="{{ route('admin.products_delete', $product->id) }}"
                                                class="icon-btn delete" onclick="return confirm('Xóa sản phẩm này?');"
                                                title="Xóa">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </a>

                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

                {{-- PHÂN TRANG ĐỨNG RIÊNG, KHÔNG BỊ SCROLL --}}
                <div class="pagination-wrapper">
                    {{ $products->links('pagination::bootstrap-4') }}
                </div>
            @else
                <p class="empty">Không tìm thấy sản phẩm nào!</p>
            @endif
        </div>

    </div>

    {{-- MODAL THÊM SẢN PHẨM --}}
    <div class="modal-overlay" id="addProductModal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h2>Thêm Sản Phẩm Mới</h2>
                <button type="button" class="modal-close" id="closeAddProductModal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('admin.products_store') }}" method="post" enctype="multipart/form-data"
                class="modal-form">
                @csrf

                <div class="modal-body">
                    <div class="modal-grid">
                        {{-- CỘT TRÁI: THÔNG TIN SẢN PHẨM --}}
                        <div class="modal-left">

                            <div class="inputRow">
                                <div class="inputBox">
                                    <span>Tên Sản Phẩm (bắt buộc)</span>
                                    <input type="text" class="box" required maxlength="100"
                                        placeholder="Nhập tên sản phẩm" name="name" value="{{ old('name') }}">
                                </div>
                            </div>

                            <div class="inputRow">
                                <div class="inputBox">
                                    <span>Danh Mục Sản Phẩm (bắt buộc)</span>
                                    <select name="category_id" class="box" required>
                                        <option value="" disabled selected>Chọn danh mục sản phẩm</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->category_id }}"
                                                data-slug="{{ \Illuminate\Support\Str::slug($category->category_name) }}"
                                                {{ (string) old('category_id') === (string) $category->category_id ? 'selected' : '' }}>
                                                {{ $category->category_name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                                <div class="inputBox">
                                    <span>Thương Hiệu (bắt buộc)</span>
                                    <select name="company" class="box" required>
                                        <option value="" disabled selected>Chọn thương hiệu của sản phẩm</option>
                                        @foreach ($brand as $company)
                                            <option value="{{ $company->brand_name }}">{{ $company->brand_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="inputRow">
                                <div class="inputBox">
                                    <span>Giá bán (VND) (bắt buộc)</span>
                                    <input type="number" min="0" class="box" required max="9999999999"
                                        placeholder="Nhập giá bán sản phẩm" name="price" value="{{ old('price') }}">
                                </div>
                                <div class="inputBox">
                                    <span>Giá nhập (bắt buộc)</span>
                                    <input type="number" min="0" class="box" required max="9999999999"
                                        placeholder="Nhập giá nhập sản phẩm" name="purchase_price"
                                        value="{{ old('purchase_price') }}">
                                </div>
                            </div>

                            <div class="inputRow">
                                <div class="inputBox">
                                    <span>Số Lượng Tồn Kho (bắt buộc)</span>
                                    <input type="number" name="inventory" class="box" required min="0"
                                        placeholder="Nhập số lượng trong kho" value="{{ old('inventory') }}">
                                </div>
                                <div class="inputBox">
                                    <span>Mã giảm giá(%)</span>
                                    <input name="discount" type="number" min="0" class="box" max="100"
                                        placeholder="Nhập mã giảm giá" value="{{ old('discount') }}">
                                </div>
                            </div>

                            <div class="inputRow">
                                <div class="inputBox">
                                    <span>Màu Sắc Sản Phẩm (bắt buộc)</span>
                                    <select name="colorProduct_id" class="box" required>
                                        <option value="" disabled selected>Chọn màu sắc sản phẩm</option>
                                        @foreach ($color as $colorP)
                                            <option value="{{ $colorP->colorProduct_id }}">
                                                {{ $colorP->colorProduct }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="inputRow">
                                <div class="inputBox inputBox-full">
                                    <span>Chi Tiết Sản Phẩm (bắt buộc)</span>
                                    <textarea name="details" placeholder="Nhập chi tiết sản phẩm" class="box" required maxlength="500"
                                        cols="30" rows="4">{{ old('details') }}</textarea>
                                </div>
                            </div>
                            <div class="inputRow">
                                <div class="inputBox inputBox-full">
                                    <span>Thông số kỹ thuật</span>

                                    {{-- JS sẽ render input theo danh mục --}}
                                    <div id="addSpecFields"></div>

                                    <button type="button" class="btn-light" id="addSpecRowBtn"
                                        style="margin-top:10px;">
                                        + Thêm dòng thông số
                                    </button>

                                    <small style="display:block; margin-top:8px; color:#6b7280;">
                                        Chọn danh mục để tự hiện form thông số. Bạn vẫn có thể thêm dòng tùy ý.
                                    </small>
                                </div>
                            </div>

                        </div>

                        {{-- CỘT PHẢI: ẢNH & LƯU Ý --}}
                        <div class="modal-right">
                            <div class="image-card">
                                <span class="image-card-title">Hình Ảnh Sản Phẩm</span>

                                <label class="image-dropzone">
                                    <i class="fa-regular fa-image"></i>
                                    <span>Nhấp để tải ảnh lên</span>
                                    <small>PNG, JPG (tối đa 5MB)</small>
                                    {{-- ảnh 01 chính --}}
                                    <input type="file" name="image_01" accept="image/*" required>

                                </label>

                                <div class="image-extra-inputs">
                                    <label>
                                        <span>Ảnh 02 (bắt buộc)</span>
                                        <input type="file" name="image_02" accept="image/*" class="box" required>
                                    </label>
                                    <label>
                                        <span>Ảnh 03 (bắt buộc)</span>
                                        <input type="file" name="image_03" accept="image/*" class="box" required>
                                    </label>
                                </div>
                            </div>

                            <div class="note-card">
                                <div class="note-title">
                                    <i class="fa-solid fa-circle-info"></i> Lưu ý
                                </div>
                                <ul>
                                    <li>Điền đầy đủ thông tin bắt buộc.</li>
                                    <li>Sử dụng ảnh sản phẩm chất lượng cao.</li>
                                    <li>Mô tả chi tiết giúp khách hiểu rõ hơn.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-light" id="cancelAddProduct">
                        Hủy
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fa-regular fa-floppy-disk"></i> Lưu Sản Phẩm
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- MODAL CẬP NHẬT SẢN PHẨM --}}
    <div class="modal-overlay" id="updateProductModal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h2>Cập Nhật Sản Phẩm</h2>
                <button type="button" class="modal-close" id="closeUpdateProductModal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="updateProductForm" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- các hidden giống form cũ --}}
                <input type="hidden" name="pid" id="upd_pid">
                <input type="hidden" name="variant_id" id="upd_variant_id">
                <input type="hidden" name="old_image_01" id="upd_old_image_01">
                <input type="hidden" name="old_image_02" id="upd_old_image_02">
                <input type="hidden" name="old_image_03" id="upd_old_image_03">

                <div class="modal-body">
                    <div class="modal-grid">
                        {{-- ==== CỘT TRÁI ==== --}}
                        <div class="modal-left">
                            <div class="inputRow">
                                <div class="inputBox inputBox-full">
                                    <span>Tên Sản Phẩm *</span>
                                    <input type="text" class="box" id="upd_name" name="name" required
                                        maxlength="100">
                                </div>
                            </div>

                            <div class="inputRow">
                                <div class="inputBox">
                                    <span>Danh Mục *</span>
                                    <select name="category_id" id="upd_category_id" class="box" required>
                                        <option value="" disabled>Chọn danh mục sản phẩm</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->category_id }}"
                                                data-slug="{{ \Illuminate\Support\Str::slug($category->category_name) }}">
                                                {{ $category->category_name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="inputBox">
                                    <span>Trạng thái</span>
                                    <select class="box" id="upd_status" disabled>
                                        <option>Còn hàng</option>
                                    </select>
                                </div>
                            </div>

                            <div class="inputRow">
                                <div class="inputBox">
                                    <span>Giá bán (VND) *</span>
                                    <input type="number" min="0" max="9999999999" class="box" id="upd_price"
                                        name="price" required>
                                </div>
                                <div class="inputBox">
                                    <span>Mã giảm giá</span>
                                    <input type="number" min="0" max="100" class="box"
                                        id="upd_discount" name="discount">
                                </div>

                            </div>

                            <div class="inputRow">
                                <div class="inputBox">
                                    <span>Giá nhập *</span>
                                    <input type="number" min="0" max="9999999999" class="box"
                                        id="upd_purchase_price" name="purchase_price" required>
                                </div>
                            </div>
                            <div class="inputRow">
                                <div class="inputBox">
                                    <span>Thương Hiệu *</span>
                                    <select name="company" id="upd_company" class="box" required>
                                        <option value="" disabled>Chọn thương hiệu sản phẩm</option>
                                        @foreach ($brand as $company)
                                            <option value="{{ $company->brand_name }}">
                                                {{ $company->brand_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="inputRow">
                                <div class="inputBox inputBox-full">
                                    <span>Mô tả sản phẩm *</span>
                                    <textarea class="box" id="upd_details" name="details" rows="4" maxlength="500" required></textarea>
                                </div>
                            </div>
                            <div class="inputRow">
                                <div class="inputBox inputBox-full">
                                    <span>Thông số kỹ thuật</span>
                                    <div id="updSpecFields"></div>
                                    <button type="button" class="btn-light" id="updAddSpecRowBtn"
                                        style="margin-top:10px;">
                                        + Thêm dòng thông số
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- ==== CỘT PHẢI: ẢNH ==== --}}
                        <div class="modal-right">
                            <div class="image-card">
                                <span class="image-card-title">Hình Ảnh Sản Phẩm</span>

                                {{-- SLIDER ẢNH CHÍNH --}}
                                <div class="image-slider">
                                    <button type="button" class="slider-arrow" id="upd_prev_image">
                                        <i class="fa-solid fa-chevron-left"></i>
                                    </button>

                                    <div class="slider-viewport">
                                        <div class="image-preview-main">
                                            <img id="upd_image_main" src="" alt="">
                                        </div>
                                    </div>

                                    <button type="button" class="slider-arrow" id="upd_next_image">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </button>
                                </div>

                                {{-- DẢI THUMBNAIL NGANG --}}
                                <div class="image-preview-thumbs" id="upd_thumbs_wrapper">
                                    <img id="upd_image_thumb1" class="thumb-item" src="" alt="">
                                    <img id="upd_image_thumb2" class="thumb-item" src="" alt="">
                                    <img id="upd_image_thumb3" class="thumb-item" src="" alt="">
                                </div>

                                <div class="image-extra-inputs">
                                    <label class="image-change">
                                        <span>Thay đổi ảnh 1</span>
                                        <input type="file" name="image_01" accept="image/*">
                                    </label>
                                    <label class="image-change">
                                        <span>Thay đổi ảnh 2</span>
                                        <input type="file" name="image_02" accept="image/*">
                                    </label>
                                    <label class="image-change">
                                        <span>Thay đổi ảnh 3</span>
                                        <input type="file" name="image_03" accept="image/*">
                                    </label>
                                </div>
                            </div>

                            <div class="note-card">
                                <div class="note-title">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    Cập nhật sản phẩm
                                </div>
                                <ul>
                                    <li>Kiểm tra lại thông tin trước khi lưu.</li>
                                    <li>Cập nhật giá theo thị trường nếu cần.</li>
                                    <li>Điều chỉnh số lượng tồn kho chính xác.</li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-light" id="cancelUpdateProduct">
                        Hủy
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fa-regular fa-floppy-disk"></i> Cập Nhật Sản Phẩm
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal-overlay" id="addVariantModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog--wide">
            <div class="modal-header">
                <div>
                    <h2>Quản lý Màu Sắc</h2>
                    <p class="modal-subtitle">Thêm màu & xem danh sách màu của sản phẩm</p>
                </div>

                <button type="button" class="modal-close" id="closeAddVariantModal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="modal-grid modal-grid--2col">

                    {{-- LEFT: Thêm màu (giữ nguyên form store của bạn) --}}
                    <div class="modal-left">
                        <div class="panel panel--tint">
                            <h3 class="panel-title">Thêm Màu Mới</h3>

                            <form id="addVariantForm" method="POST" enctype="multipart/form-data"
                                action="{{ route('variants.store') }}">
                                @csrf
                                <input type="hidden" name="product_id" id="av_product_id">

                                <div class="inputRow">
                                    <div class="inputBox">
                                        <span>Màu sắc *</span>
                                        <select name="colorProduct_id" id="av_colorProduct_id" class="box" required>
                                            <option value="" disabled selected>Chọn màu</option>
                                            @foreach ($color as $c)
                                                <option value="{{ $c->colorProduct_id }}">{{ $c->colorProduct }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="inputBox">
                                        <span>Tồn kho *</span>
                                        <input type="number" name="inventory" class="box" min="0"
                                            value="0" required>
                                    </div>
                                </div>

                                <div class="inputRow">
                                    <div class="inputBox inputBox-full">
                                        <span>Ảnh 01 *</span>
                                        <input type="file" name="image_01" accept="image/*" class="box" required>
                                    </div>
                                </div>

                                <div class="inputRow">
                                    <div class="inputBox inputBox-full">
                                        <span>Ảnh 02 *</span>
                                        <input type="file" name="image_02" accept="image/*" class="box" required>
                                    </div>
                                </div>

                                <div class="inputRow">
                                    <div class="inputBox inputBox-full">
                                        <span>Ảnh 03 *</span>
                                        <input type="file" name="image_03" accept="image/*" class="box" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn-primary btn-primary--block">
                                    <i class="fa-solid fa-plus"></i> Thêm Màu
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- RIGHT: Danh sách màu của sản phẩm --}}
                    {{-- RIGHT --}}
                    <div class="panel">
                        <div class="panel-head">
                            <h3 class="panel-title">
                                Danh Sách Màu (<span id="mcCount">0</span>)
                            </h3>
                        </div>

                        {{-- LIST --}}
                        <div class="color-list" id="mcList"></div>

                        {{-- EDIT (ẩn mặc định) --}}
                        <div class="mc-edit" id="mcEditWrap" hidden>
                            <div class="mc-edit-head">
                                <div class="mc-edit-title">Chỉnh sửa</div>
                                <button type="button" class="mc-edit-close" id="mcEditClose" title="Đóng">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>

                            <form id="mcEditForm" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <input type="hidden" id="mc_edit_vid" name="variant_id">

                                {{-- Row: Tên màu + mã màu (giống ảnh) --}}
                                <div class="mc-row">
                                    <select class="mc-input" id="mc_edit_color_id" name="colorProduct_id" required>
                                        <option value="" disabled>Chọn màu</option>
                                        @foreach ($color as $c)
                                            <option value="{{ $c->colorProduct_id }}"
                                                data-hex="{{ $c->colorHex ?? '' }}">
                                                {{ $c->colorProduct }}
                                            </option>
                                        @endforeach
                                    </select>


                                    <div class="mc-colorbox">
                                        <span class="mc-swatch-mini" id="mc_edit_swatch"></span>
                                        <input type="text" class="mc-input mc-input--hex" id="mc_edit_hex"
                                            placeholder="#FFFFFF" readonly>
                                    </div>
                                </div>

                                {{-- Bạn có thể chỉnh tồn kho ở đây (nên có) --}}
                                <div class="mc-row">
                                    <label class="mc-label">Tồn kho</label>
                                    <input type="number" class="mc-input" name="inventory" id="mc_edit_inventory"
                                        min="0">
                                </div>

                                <div class="mc-label" style="margin-top:10px;">Hình Ảnh</div>

                                {{-- IMAGE 01 --}}
                                <div class="mc-imgrow">
                                    <input type="file" class="mc-input" name="image_01" id="mc_edit_img1"
                                        accept="image/*">
                                    <a class="mc-preview" id="mc_prev1_link" href="#" target="_blank"
                                        hidden>Preview</a>
                                    <button type="button" class="mc-trash" data-remove="1" title="Xoá ảnh 1">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                                <input type="hidden" name="remove_image_01" id="mc_remove_img1" value="0">

                                {{-- IMAGE 02 --}}
                                <div class="mc-imgrow">
                                    <input type="file" class="mc-input" name="image_02" id="mc_edit_img2"
                                        accept="image/*">
                                    <a class="mc-preview" id="mc_prev2_link" href="#" target="_blank"
                                        hidden>Preview</a>
                                    <button type="button" class="mc-trash" data-remove="2" title="Xoá ảnh 2">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                                <input type="hidden" name="remove_image_02" id="mc_remove_img2" value="0">

                                {{-- IMAGE 03 --}}
                                <div class="mc-imgrow">
                                    <input type="file" class="mc-input" name="image_03" id="mc_edit_img3"
                                        accept="image/*">
                                    <a class="mc-preview" id="mc_prev3_link" href="#" target="_blank"
                                        hidden>Preview</a>
                                    <button type="button" class="mc-trash" data-remove="3" title="Xoá ảnh 3">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                                <input type="hidden" name="remove_image_03" id="mc_remove_img3" value="0">

                                <div class="mc-actions-bottom">
                                    <button type="submit" class="mc-btn mc-btn--save">Lưu</button>
                                    <button type="button" class="mc-btn mc-btn--cancel" id="mcEditCancel">Huỷ</button>
                                </div>
                            </form>
                        </div>
                    </div>


                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-light" id="cancelAddVariant">Đóng</button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets') }}/js/admin_product.js"></script>
    <script src="{{ asset('assets/js/flash_toast.js') }}"></script>
    <script>
        window.MC_ROUTES = {
            index: @json(route('variants.index_by_product', ['product' => '__ID__'])),
            store: @json(route('variants.store')),
            destroy: @json(route('variants.destroy', ['variant' => '__VID__'])),
            update: @json(route('variants.update', ['variant' => '__VID__'])),
        };
    </script>
@endpush
