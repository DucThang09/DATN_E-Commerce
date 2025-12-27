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
        updateForm.action = btn.dataset.updateUrl || '';

        // helper set value an toàn
        const setVal = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.value = (val ?? '');
        };

        // hidden
        setVal('upd_pid', btn.dataset.id);

        // text / number
        setVal('upd_name', btn.dataset.name);
        setVal('upd_price', btn.dataset.price);
        setVal('upd_purchase_price', btn.dataset.purchasePrice);

        // ✅ QUAN TRỌNG: input tồn kho có thể đã bị bạn xoá khỏi view
        setVal('upd_inventory', btn.dataset.inventory);

        setVal('upd_variant_id', btn.dataset.variantId);
        setVal('upd_discount', btn.dataset.discount || 0);
        setVal('upd_details', btn.dataset.details);

        const elQtySold = document.getElementById('upd_qty_sold');
        if (elQtySold) elQtySold.value = btn.dataset.qtySold || 0;

        // select
        const catEl = document.getElementById('upd_category_id');
        if (catEl) catEl.value = btn.dataset.categoryId || '';

        setSelectValue('upd_company', btn.dataset.company);
        setSelectValue('upd_colorProduct_id', btn.dataset.colorProductId);

        // trạng thái chỉ hiển thị
        const statusSel = document.getElementById('upd_status');
        if (statusSel) {
            const inv = parseInt(btn.dataset.inventory || '0', 10);
            statusSel.options[0].text = (inv > 0) ? 'Còn hàng' : 'Hết hàng';
        }

        // ====== PHẦN SLIDER ẢNH ======
        updImages = [];
        if (btn.dataset.image1Url) updImages.push(btn.dataset.image1Url);
        if (btn.dataset.image2Url) updImages.push(btn.dataset.image2Url);
        if (btn.dataset.image3Url) updImages.push(btn.dataset.image3Url);

        if (thumb1) thumb1.src = btn.dataset.image1Url || '';
        if (thumb2) thumb2.src = btn.dataset.image2Url || '';
        if (thumb3) thumb3.src = btn.dataset.image3Url || '';

        if (updImages.length) {
            showImageAt(0);
        } else if (mainImg) {
            mainImg.src = '';
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
(function () {
    // escape để an toàn
    function escapeHtml(str) {
        return (str || "")
            .toString()
            .replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;")
            .replaceAll('"', "&quot;")
            .replaceAll("'", "&#039;");
    }

    // ✅ build row có thể nhận sẵn label/value
    function buildSpecRow(label = "", value = "") {
        return `
      <div class="inputRow spec-row">
        <div class="spec-fields">
          <div class="inputBox">
            <span>Tên thông số</span>
            <input type="text" class="box" name="spec_label[]" value="${escapeHtml(label)}"
              placeholder="Ví dụ: Màn hình">
          </div>

          <div class="inputBox">
            <span>Giá trị</span>
            <input type="text" class="box" name="spec_value[]" value="${escapeHtml(value)}"
              placeholder="Ví dụ: 6.7&quot; OLED">
          </div>
        </div>

        <button type="button" class="spec-remove" title="Xóa dòng">
          <i class="fa-regular fa-trash-can"></i>
        </button>
      </div>
    `;
    }

    function clearSpecFields(container) {
        if (!container) return;
        container.innerHTML = "";
    }

    // ✅ add row có thể truyền label/value
    function addSpecRow(container, label = "", value = "") {
        if (!container) return;
        container.insertAdjacentHTML("beforeend", buildSpecRow(label, value));
    }

    // ✅ xóa 1 dòng
    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".spec-remove");
        if (!btn) return;
        const row = btn.closest(".spec-row");
        if (row) row.remove();
    });

    // ===== ADD MODAL =====
    const addSpecFields = document.getElementById("addSpecFields");
    const addSpecRowBtn = document.getElementById("addSpecRowBtn");
    const openAddModalBtn = document.getElementById("openAddProductModal");
    const addCategorySelect = document.querySelector('#addProductModal select[name="category_id"]');

    if (openAddModalBtn) {
        openAddModalBtn.addEventListener("click", () => {
            clearSpecFields(addSpecFields);
        });
    }
    if (addCategorySelect) {
        addCategorySelect.addEventListener("change", () => {
            clearSpecFields(addSpecFields);
        });
    }
    if (addSpecRowBtn && addSpecFields) {
        addSpecRowBtn.addEventListener("click", () => addSpecRow(addSpecFields));
    }

    // ===== UPDATE MODAL =====
    const updSpecFields = document.getElementById("updSpecFields");
    const updAddSpecRowBtn = document.getElementById("updAddSpecRowBtn");
    const updCategorySelect = document.getElementById("upd_category_id");

    // ✅ Khi click nút edit (js-open-update) -> đổ specs cũ vào form update
    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".js-open-update");
        if (!btn) return;

        // luôn clear trước
        clearSpecFields(updSpecFields);

        // lấy specs từ data-specs
        let specs = [];
        try {
            specs = JSON.parse(btn.getAttribute("data-specs") || "[]");
        } catch (err) {
            specs = [];
        }

        // render specs
        if (Array.isArray(specs) && specs.length) {
            specs.forEach((it) => {
                const label = (it.label ?? it.spec_label ?? "").toString().trim().replace(/:$/, "");
                const value = (it.value ?? it.spec_value ?? "").toString().trim();
                if (label && value) addSpecRow(updSpecFields, label, value);
            });
        }
    });

    // (tuỳ bạn) đổi category update có reset specs hay không
    if (updCategorySelect) {
        updCategorySelect.addEventListener("change", () => {
            // Nếu muốn đổi danh mục là reset specs thì mở dòng dưới:
            // clearSpecFields(updSpecFields);
        });
    }

    // nút + thêm dòng trong update
    if (updAddSpecRowBtn && updSpecFields) {
        updAddSpecRowBtn.addEventListener("click", () => addSpecRow(updSpecFields));
    }
})();
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('addVariantModal');
    const closeBtn = document.getElementById('closeAddVariantModal');
    const cancelBtn = document.getElementById('cancelAddVariant');
    const pidInput = document.getElementById('av_product_id');

    // RIGHT LIST
    const listEl = document.getElementById('mcList');     // required
    const countEl = document.getElementById('mcCount');   // optional
    const productNameEl = document.getElementById('mc_product_name'); // optional

    // LEFT FORM (bạn đang dùng form này)
    const addVariantForm = document.getElementById('addVariantForm');

    const routes = window.MC_ROUTES || {};
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

    function build(url, token, value) {
        if (!url) return '';
        return url.replace(token, value);
    }

    function openModal(pid, pname) {
        if (pidInput) pidInput.value = pid;
        if (productNameEl && pname) productNameEl.textContent = pname;

        modal?.classList.add('is-open');

        hideEdit(); // ✅ luôn quay về list trước

        if (listEl && routes.index) loadVariants(pid);
    }


    function closeModal() {
        modal?.classList.remove('is-open');
    }

    // ====== LOAD LIST VARIANTS (product_variants)
    async function loadVariants(pid) {
        try {
            const url = build(routes.index, '__ID__', pid);
            const res = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            const data = await res.json();

            // ✅ API nên trả variants
            const variants = data.variants || [];

            if (countEl) countEl.textContent = String(variants.length);

            if (!variants.length) {
                listEl.innerHTML = `<div style="padding:10px;color:#6b7280;">Chưa có màu nào.</div>`;
                return;
            }

            listEl.innerHTML = variants.map(v => {
                const thumb = v.thumb_url || v.image_01_url || v.image_02_url || v.image_03_url || '';

                const name = v.color_name || v.name || '';
                const inv = (v.inventory ?? 0);

                return `
    <div class="mc-item" data-id="${v.id}">
      <div class="mc-swatch">
        ${thumb ? `<img src="${thumb}" alt="">` : ``}
      </div>

      <div class="mc-meta">
        <div class="mc-name">${escapeHtml(name)}</div>
        <div class="mc-hex">Tồn kho: ${escapeHtml(String(inv))}</div>
      </div>

      <div class="mc-actions">
  <button type="button"
  class="mc-iconbtn mc-iconbtn--edit js-edit"
  data-vid="${v.id}"
  data-color-id="${v.colorProduct_id ?? ''}"
  data-inventory="${v.inventory ?? 0}"
  data-img1="${escapeHtml(v.image_01_url || '')}"
  data-img2="${escapeHtml(v.image_02_url || '')}"
  data-img3="${escapeHtml(v.image_03_url || '')}"
  title="Sửa">  
  <i class="fa-solid fa-pen"></i>
</button>


  <button type="button"
    class="mc-iconbtn mc-iconbtn--del js-del"
    data-vid="${v.id}"
    title="Xoá">
    <i class="fa-regular fa-trash-can"></i>
  </button>
</div>

    </div>
  `;
            }).join('');

        } catch (err) {
            console.error(err);
            if (listEl) {
                listEl.innerHTML = `<div style="padding:10px;color:#ef4444;">Không tải được danh sách màu.</div>`;
            }
        }
    }

    // ====== DELETE VARIANT
    listEl?.addEventListener('click', async function (e) {
        const delBtn = e.target.closest('.js-del');
        if (!delBtn) return;

        const vid = delBtn.dataset.vid;
        const pid = pidInput?.value;

        if (!vid || !pid) return;

        if (!routes.destroy) {
            alert('Thiếu route destroy (window.MC_ROUTES.destroy)');
            return;
        }

        if (!confirm('Xoá màu này?')) return;

        try {
            const url = build(routes.destroy, '__VID__', vid);
            const res = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const data = await res.json();
            if (!res.ok) {
                alert(data.message || 'Xoá thất bại!');
                return;
            }

            await loadVariants(pid);
        } catch (err) {
            console.error(err);
            alert('Có lỗi khi xoá.');
        }
    });
    const editWrap = document.getElementById('mcEditWrap');
    const editForm = document.getElementById('mcEditForm');

    const editVid = document.getElementById('mc_edit_vid');

    const editHex = document.getElementById('mc_edit_hex');
    const editSwatch = document.getElementById('mc_edit_swatch');
    const editInv = document.getElementById('mc_edit_inventory');

    const prev1 = document.getElementById('mc_prev1_link');
    const prev2 = document.getElementById('mc_prev2_link');
    const prev3 = document.getElementById('mc_prev3_link');

    const rm1 = document.getElementById('mc_remove_img1');
    const rm2 = document.getElementById('mc_remove_img2');
    const rm3 = document.getElementById('mc_remove_img3');

    document.getElementById('mcEditClose')?.addEventListener('click', hideEdit);
    document.getElementById('mcEditCancel')?.addEventListener('click', hideEdit);

    function showEdit() {
        if (listEl) listEl.hidden = true;
        if (editWrap) editWrap.hidden = false;
    }

    function hideEdit() {
        if (editWrap) editWrap.hidden = true;
        if (listEl) listEl.hidden = false;
        if (rm1) rm1.value = '0';
        if (rm2) rm2.value = '0';
        if (rm3) rm3.value = '0';
    }

    function setPreview(aEl, url) {
        if (!aEl) return;
        if (url) { aEl.href = url; aEl.hidden = false; }
        else { aEl.href = '#'; aEl.hidden = true; }
    }

    listEl?.addEventListener('click', function (e) {
        const btn = e.target.closest('.js-edit');
        if (!btn) return;

        const vid = btn.dataset.vid;
        if (editVid) editVid.value = vid;

        const editColorId = document.getElementById('mc_edit_color_id');
        if (editColorId) editColorId.value = btn.dataset.colorId || '';

        if (editHex) editHex.value = btn.dataset.hex || '';
        if (editInv) editInv.value = btn.dataset.inventory || '0';

        if (editSwatch) editSwatch.style.background = (btn.dataset.hex || '#ffffff');

        setPreview(prev1, btn.dataset.img1 || '');
        setPreview(prev2, btn.dataset.img2 || '');
        setPreview(prev3, btn.dataset.img3 || '');

        if (routes.update && editForm) {
            editForm.action = routes.update.replace('__VID__', vid);
        }

        showEdit();
    });

    editWrap?.addEventListener('click', function (e) {
        const btn = e.target.closest('.mc-trash');
        if (!btn) return;
        const which = btn.dataset.remove;

        if (which === '1') { rm1.value = '1'; setPreview(prev1, ''); }
        if (which === '2') { rm2.value = '1'; setPreview(prev2, ''); }
        if (which === '3') { rm3.value = '1'; setPreview(prev3, ''); }
    });
    editForm?.addEventListener('submit', async function (e) {
        e.preventDefault();

        const pid = pidInput?.value;
        if (!pid) return;

        const url = editForm.action;
        const fd = new FormData(editForm);

        const res = await fetch(url, {
            method: 'POST', // dùng POST + _method=PUT do Blade @method('PUT')
            headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
            body: fd,
        });

        const data = await res.json();

        if (!res.ok) {
            alert(data.message || 'Cập nhật thất bại!');
            return;
        }

        hideEdit();
        await loadVariants(pid);
    });

    // ====== OPEN MODAL (delegate) — chắc chắn click được
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.js-open-add-variant');
        if (!btn) return;

        e.preventDefault();
        openModal(btn.dataset.productId, btn.dataset.productName);
    });

    // ====== close actions
    closeBtn?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);

    modal?.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    // ====== OPTIONAL: sau khi submit form (POST bình thường) thì reload list bằng AJAX
    // Nếu bạn submit thường và page reload thì không cần. Nhưng nếu bạn muốn submit AJAX thì nói mình.
    addVariantForm?.addEventListener('submit', function () {
        // để submit bình thường => không preventDefault
        // Nếu sau này bạn chuyển sang AJAX, mình sẽ sửa đoạn này.
    });

    function escapeHtml(s) {
        return String(s)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }
});
