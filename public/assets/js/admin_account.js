document.addEventListener('DOMContentLoaded', function () {
    const openBtn   = document.getElementById('btnOpenAdminModal');
    const modal     = document.getElementById('adminCreateModal');
    const closeBtn  = document.getElementById('btnCloseAdminModal');
    const cancelBtn = document.getElementById('btnCancelAdminModal');

    function openModal() {
        if (!modal) return;
        modal.classList.add('is-open');
    }

    function closeModal() {
        if (!modal) return;
        modal.classList.remove('is-open');
    }

    if (openBtn)   openBtn.addEventListener('click', openModal);
    if (closeBtn)  closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    // Click ra ngoài để tắt
    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    }

    // Nếu có lỗi validate -> tự mở modal lại
    if (modal && modal.dataset.hasErrors === '1') {
        openModal();
    }
});
document.addEventListener('DOMContentLoaded', function () {
    // ===== POPUP THÊM ADMIN =====
    const openBtn   = document.getElementById('btnOpenAdminModal');
    const createModal     = document.getElementById('adminCreateModal');
    const closeBtn  = document.getElementById('btnCloseAdminModal');
    const cancelBtn = document.getElementById('btnCancelAdminModal');

    function openCreateModal() {
        if (!createModal) return;
        createModal.classList.add('is-open');
    }

    function closeCreateModal() {
        if (!createModal) return;
        createModal.classList.remove('is-open');
    }

    if (openBtn)   openBtn.addEventListener('click', openCreateModal);
    if (closeBtn)  closeBtn.addEventListener('click', closeCreateModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeCreateModal);

    if (createModal) {
        createModal.addEventListener('click', function (e) {
            if (e.target === createModal) {
                closeCreateModal();
            }
        });

        // Nếu có lỗi validate form create -> tự mở popup create
        if (createModal.dataset.hasErrors === '1') {
            openCreateModal();
        }
    }

    // ===== POPUP EDIT ADMIN =====
    const editModal         = document.getElementById('adminEditModal');
    const editForm          = document.getElementById('adminEditForm');
    const editNameInput     = document.getElementById('edit_admin_name');
    const editStatusSelect  = document.getElementById('edit_admin_status');
    const closeEditBtn      = document.getElementById('btnCloseAdminEditModal');
    const cancelEditBtn     = document.getElementById('btnCancelAdminEditModal');
    const editButtons       = document.querySelectorAll('.btnEditAdmin');

    function openEditModal() {
        if (!editModal) return;
        editModal.classList.add('is-open');
    }

    function closeEditModal() {
        if (!editModal) return;
        editModal.classList.remove('is-open');
    }

    editButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            if (!editForm || !editNameInput || !editStatusSelect) return;

            const name      = this.dataset.name || '';
            const status    = this.dataset.status || 'active';
            const updateUrl = this.dataset.updateUrl || '';

            editNameInput.value = name;
            editStatusSelect.value = status;
            if (updateUrl) {
                editForm.action = updateUrl;
            }

            openEditModal();
        });
    });

    if (closeEditBtn)  closeEditBtn.addEventListener('click', closeEditModal);
    if (cancelEditBtn) cancelEditBtn.addEventListener('click', closeEditModal);

    if (editModal) {
        editModal.addEventListener('click', function (e) {
            if (e.target === editModal) {
                closeEditModal();
            }
        });

        // Nếu có lỗi validate form edit -> mở popup edit
        if (editModal.dataset.hasErrors === '1') {
            openEditModal();
        }
    }
});
