document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggleSelectMode');
    const table = document.getElementById('productTable');
    const selectAll = document.getElementById('selectAllProducts');
    const deleteBtn = document.getElementById('deleteSelectedBtn');

    if (!toggleBtn || !table) return;

    const getItemCheckboxes = () => table.querySelectorAll('.product-checkbox');

    function updateDeleteButton() {
        const itemCheckboxes = getItemCheckboxes();
        let anyChecked = false;
        itemCheckboxes.forEach(cb => { if (cb.checked) anyChecked = true; });

        if (anyChecked) {
            deleteBtn.style.display = 'inline-flex';
            deleteBtn.disabled = false;
        } else {
            deleteBtn.disabled = true;
            deleteBtn.style.display = 'none';
        }
    }

    // Bật / tắt chế độ chọn (hiện/ẩn cột checkbox + đổi chữ Chọn/Bỏ chọn)
    toggleBtn.addEventListener('click', function () {
        table.classList.toggle('selection-mode');
        const isOn = table.classList.contains('selection-mode');

        if (isOn) {
            // Đang BẬT chế độ chọn → nút hiển thị "Bỏ chọn"
            toggleBtn.innerHTML = '<i class="fa-regular fa-circle-xmark"></i> Bỏ chọn';
        } else {
            // TẮT chế độ chọn → nút trở lại "Chọn" và reset checkbox
            toggleBtn.innerHTML = '<i class="fa-regular fa-square-check"></i> Chọn';

            if (selectAll) selectAll.checked = false;
            getItemCheckboxes().forEach(cb => cb.checked = false);
            updateDeleteButton();
        }
    });

    // "Chọn tất cả" ở header
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            const itemCheckboxes = getItemCheckboxes();
            itemCheckboxes.forEach(cb => cb.checked = selectAll.checked);
            updateDeleteButton();
        });
    }

    // Thay đổi từng checkbox item
    table.addEventListener('change', function (e) {
        if (e.target.classList.contains('product-checkbox')) {
            // Nếu bỏ tick 1 cái mà "chọn tất cả" đang bật -> bỏ tick luôn "chọn tất cả"
            if (selectAll && !e.target.checked) {
                selectAll.checked = false;
            }
            updateDeleteButton();
        }
    });

    // ===== MODAL THÊM SẢN PHẨM =====
    const openModalBtn = document.getElementById('openAddProductModal');
    const modal = document.getElementById('addProductModal');
    const closeModalBtn = document.getElementById('closeAddProductModal');
    const cancelBtn = document.getElementById('cancelAddProduct');

    function openModal() {
        if (modal) modal.classList.add('is-open');
    }

    function closeModal() {
        if (modal) modal.classList.remove('is-open');
    }

    if (openModalBtn && modal) {
        openModalBtn.addEventListener('click', openModal);
    }
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }
    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeModal);
    }
    // click ra nền tối để đóng
    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    }

});
document.addEventListener('DOMContentLoaded', function () {
    // ====== BỘ LỌC SẢN PHẨM ======
    const filterBtn = document.getElementById('openFilterPanel');
    const filterPanel = document.getElementById('filterPanel');
    const closeFilter = document.getElementById('closeFilterPanel');
    const resetBtn = document.getElementById('resetFilters');
    const filterForm = document.getElementById('productFilterForm');

    if (filterBtn && filterPanel) {
        filterBtn.addEventListener('click', function () {
            filterPanel.classList.toggle('is-open');
        });
    }

    if (closeFilter && filterPanel) {
        closeFilter.addEventListener('click', function () {
            filterPanel.classList.remove('is-open');
        });
    }

    // Click ra ngoài để đóng panel
    document.addEventListener('click', function (e) {
        if (!filterPanel) return;
        if (!filterPanel.contains(e.target) && !filterBtn.contains(e.target)) {
            filterPanel.classList.remove('is-open');
        }
    });

    // Xóa bộ lọc (chỉ reset các select, giữ nguyên ô search)
    if (resetBtn && filterForm) {
        resetBtn.addEventListener('click', function () {
            filterForm.querySelectorAll('select').forEach(function (sel) {
                sel.selectedIndex = 0;
            });
        });
    }
});
document.addEventListener('DOMContentLoaded', function () {
    // ===== MODAL CẬP NHẬT SẢN PHẨM =====
    const updateModal = document.getElementById('updateProductModal');
    const openUpdateBtns = document.querySelectorAll('.js-open-update');
    const closeUpdateBtn = document.getElementById('closeUpdateProductModal');
    const cancelUpdateBtn = document.getElementById('cancelUpdateProduct');
    const updateForm = document.getElementById('updateProductForm');

    // ====== BIẾN SLIDER ẢNH (NEW) ======
    let updImages = [];     // mảng url ảnh
    let updIndex = 0;      // index ảnh đang hiển thị

    const mainImg = document.getElementById('upd_image_main');
    const thumb1 = document.getElementById('upd_image_thumb1');
    const thumb2 = document.getElementById('upd_image_thumb2');
    const thumb3 = document.getElementById('upd_image_thumb3');
    const prevBtn = document.getElementById('upd_prev_image');
    const nextBtn = document.getElementById('upd_next_image');

    // ===== HÀM TIỆN DỤNG =====
    function setSelectValue(selectId, value) {
        const sel = document.getElementById(selectId);
        if (!sel) return;
        Array.from(sel.options).forEach(opt => {
            opt.selected = (opt.value === value);
        });
    }

    // (NEW) tô active cho thumbnail
    function setActiveThumb() {
        [thumb1, thumb2, thumb3].forEach((t, i) => {
            if (!t) return;
            if (!updImages[i]) {
                t.style.display = 'none';
                t.classList.remove('thumb-active');
            } else {
                t.style.display = 'block';
                t.classList.toggle('thumb-active', i === updIndex);
            }
        });
    }

    // (NEW) hiển thị ảnh theo index
    // áp dụng ảnh hiện tại + hiệu ứng trượt
    function applyImageWithAnim(direction) {
        if (!updImages.length || !mainImg) return;

        // reset class để restart animation
        mainImg.classList.remove('update-img-slide-next', 'update-img-slide-prev');

        // đổi src
        mainImg.src = updImages[updIndex];

        // force reflow để browser apply animation lại
        void mainImg.offsetWidth;

        // thêm class theo hướng
        if (direction === 'prev') {
            mainImg.classList.add('update-img-slide-prev');
        } else {
            mainImg.classList.add('update-img-slide-next');
        }

        setActiveThumb();
    }

    // chuyển ảnh theo step (±1) – dùng cho nút mũi tên
    function changeImage(step, direction) {
        if (!updImages.length) return;
        updIndex = (updIndex + step + updImages.length) % updImages.length;
        applyImageWithAnim(direction);
    }

    // nhảy tới index cụ thể – dùng khi click thumbnail
    function showImageAt(index) {
        if (!updImages.length || !mainImg) return;
        const oldIndex = updIndex;
        updIndex = index;

        let direction = 'next';
        if (index < oldIndex) direction = 'prev';

        applyImageWithAnim(direction);
    }

    function openUpdateModal(btn) {
        if (!updateModal || !updateForm) return;

        // mở modal
        updateModal.classList.add('is-open');

        // set action cho form
        updateForm.action = btn.dataset.updateUrl;

        // hidden
        document.getElementById('upd_pid').value = btn.dataset.id;
        document.getElementById('upd_old_image_01').value = btn.dataset.image1Path;
        document.getElementById('upd_old_image_02').value = btn.dataset.image2Path;
        document.getElementById('upd_old_image_03').value = btn.dataset.image3Path;

        // text / number
        document.getElementById('upd_name').value = btn.dataset.name;
        document.getElementById('upd_price').value = btn.dataset.price;
        document.getElementById('upd_purchase_price').value = btn.dataset.purchasePrice;
        document.getElementById('upd_inventory').value = btn.dataset.inventory;
        document.getElementById('upd_qty_sold').value = btn.dataset.qtySold;
        document.getElementById('upd_discount').value = btn.dataset.discount || 0;
        document.getElementById('upd_details').value = btn.dataset.details;

        // select
        setSelectValue('upd_category', btn.dataset.category);
        setSelectValue('upd_company', btn.dataset.company);
        setSelectValue('upd_color', btn.dataset.color);

        // trạng thái chỉ hiển thị
        const statusSel = document.getElementById('upd_status');
        if (statusSel) {
            statusSel.options[0].text =
                (parseInt(btn.dataset.inventory, 10) > 0) ? 'Còn hàng' : 'Hết hàng';
        }

        // ====== PHẦN SLIDER ẢNH (NEW) ======
        // gom URL ảnh vào mảng
        updImages = [];
        if (btn.dataset.image1Url) updImages.push(btn.dataset.image1Url);
        if (btn.dataset.image2Url) updImages.push(btn.dataset.image2Url);
        if (btn.dataset.image3Url) updImages.push(btn.dataset.image3Url);

        // gán src cho thumbnail
        if (thumb1) thumb1.src = btn.dataset.image1Url || '';
        if (thumb2) thumb2.src = btn.dataset.image2Url || '';
        if (thumb3) thumb3.src = btn.dataset.image3Url || '';

        // hiển thị ảnh đầu tiên
        if (updImages.length) {
            showImageAt(0);
        } else if (mainImg) {
            mainImg.src = ''; // không có ảnh
        }
    }

    function closeUpdateModal() {
        if (updateModal) {
            updateModal.classList.remove('is-open');
        }
    }

    // gán sự kiện mở modal
    if (openUpdateBtns.length) {
        openUpdateBtns.forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                openUpdateModal(this);
            });
        });
    }

    if (closeUpdateBtn) {
        closeUpdateBtn.addEventListener('click', closeUpdateModal);
    }
    if (cancelUpdateBtn) {
        cancelUpdateBtn.addEventListener('click', closeUpdateModal);
    }

    // click ra nền tối để đóng
    if (updateModal) {
        updateModal.addEventListener('click', function (e) {
            if (e.target === updateModal) {
                closeUpdateModal();
            }
        });
    }

    // ====== EVENT CHO NÚT MŨI TÊN & THUMB (NEW) ======
    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            changeImage(-1, 'prev');
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            changeImage(1, 'next');
        });
    }

    // click thumbnail → nhảy trực tiếp tới ảnh đó
    if (thumb1) thumb1.addEventListener('click', () => showImageAt(0));
    if (thumb2) thumb2.addEventListener('click', () => showImageAt(1));
    if (thumb3) thumb3.addEventListener('click', () => showImageAt(2));

});