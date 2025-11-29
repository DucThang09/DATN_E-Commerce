document.addEventListener('DOMContentLoaded', function () {
    // ====== MODAL THÊM NGƯỜI DÙNG ======
    const openBtn   = document.getElementById('btnOpenUserModal');
    const modal     = document.getElementById('userCreateModal');
    const closeBtn  = document.getElementById('btnCloseUserModal');
    const cancelBtn = document.getElementById('btnCancelUserModal');

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

    // ====== MODAL CẬP NHẬT NGƯỜI DÙNG ======
    const editModal        = document.getElementById('userEditModal');
    const editForm         = document.getElementById('userEditForm');
    const closeEditBtn     = document.getElementById('btnCloseUserEditModal');
    const cancelEditBtn    = document.getElementById('btnCancelUserEditModal');
    const editButtons      = document.querySelectorAll('.btnEditUser');

    const inputEditName    = document.getElementById('edit_name');
    const inputEditEmail   = document.getElementById('edit_email');
    const selectEditRole   = document.getElementById('edit_role');
    const selectEditStatus = document.getElementById('edit_status');

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
            if (!editForm || !editModal) return;

            const id        = this.dataset.id;
            const name      = this.dataset.name || '';
            const email     = this.dataset.email || '';
            const role      = this.dataset.role || 'customer';
            const status    = this.dataset.status || 'active';
            const updateUrl = this.dataset.updateUrl || `/admin/users/${id}`;

            // Set action cho form update
            editForm.action = updateUrl;

            // Fill dữ liệu
            if (inputEditName)    inputEditName.value = name;
            if (inputEditEmail)   inputEditEmail.value = email;
            if (selectEditRole)   selectEditRole.value = role;
            if (selectEditStatus) selectEditStatus.value = status;

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
    }
});
document.addEventListener('DOMContentLoaded', function () {
    // ... (phần code modal thêm / sửa người dùng của bạn ở trên)

    // ===== TỰ ĐỘNG ẨN ALERT SAU 3 GIÂY =====
        // ===== TOAST TỰ ĐỘNG ẨN =====
    const toasts = document.querySelectorAll('.js-auto-hide-toast');
    if (toasts.length > 0) {
        setTimeout(() => {
            toasts.forEach(toast => {
                toast.classList.add('is-hidden');
                // Xoá hẳn khỏi DOM sau khi transition xong
                setTimeout(() => toast.remove(), 400);
            });
        }, 3000); // 3 giây rồi ẩn, thích lâu hơn thì tăng số
    }

});
