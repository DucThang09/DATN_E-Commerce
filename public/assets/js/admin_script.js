document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('sidebarToggle');
    const layout = document.querySelector('.admin-layout');
    const sidebarGroups = document.querySelectorAll('.sidebar-group');

    sidebarGroups.forEach(group => {
        const parent = group.querySelector('.sidebar-parent');
        if (!parent) return;

        parent.addEventListener('click', function () {
            group.classList.toggle('open');
        });
    });
    if (toggleBtn && layout) {
        toggleBtn.addEventListener('click', function () {
            layout.classList.toggle('sidebar-collapsed');
        });
    }
});
document.addEventListener('DOMContentLoaded', function () {
    const userToggle = document.getElementById('adminUserToggle');
    const userMenu = document.getElementById('adminUserMenu');

    if (!userToggle || !userMenu) return;

    userToggle.addEventListener('click', function (e) {
        e.stopPropagation();
        userMenu.classList.toggle('is-open');
    });

    // Click ra ngoài thì đóng
    document.addEventListener('click', function () {
        userMenu.classList.remove('is-open');
    });
});
// ========== NOTIFICATION DROPDOWN ==========
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('notifyToggle');
    const dropdown = document.getElementById('notifyDropdown');
    const list = document.getElementById('notifyList');
    const badge = document.querySelector('.notify-badge');

    if (!toggleBtn || !dropdown || !list || !badge) return;

    let lastId = parseInt(toggleBtn.dataset.latestId || '0', 10);
    const notiUrl = toggleBtn.dataset.notiUrl || '';

    /* ========== TOGGLE DROPDOWN ========== */
    // Bấm chuông -> mở/đóng dropdown
    toggleBtn.addEventListener('click', function (e) {
        e.stopPropagation();                     // không cho nổi lên document
        dropdown.classList.toggle('is-open');    // thêm / bỏ class hiển thị
    });

    // Bấm ra ngoài -> đóng dropdown
    document.addEventListener('click', function (e) {
        if (!dropdown.contains(e.target) && !toggleBtn.contains(e.target)) {
            dropdown.classList.remove('is-open');
        }
    });

    /* ========== POLLING NOTIFICATION ========== */
    if (!notiUrl) {
        // Không có URL thì thôi không poll, nhưng dropdown vẫn hoạt động bình thường
        return;
    }

    async function fetchNewNotifications() {
        try {
            const res = await fetch(notiUrl + '?after_id=' + lastId, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!res.ok) return;

            const data = await res.json();
            const items = data.notifications || [];

            if (!items.length) return;

            // cập nhật lastId
            lastId = Math.max(...items.map(n => n.id));

            // tăng badge
            let current = parseInt(badge.textContent.replace('+', '') || '0', 10);
            current += items.length;
            badge.textContent = current > 9 ? '9+' : current;

            // nếu đang có dòng "Không có thông báo nào." thì xóa đi
            const emptyEl = list.querySelector('.notify-empty');
            if (emptyEl) {
                emptyEl.remove();
            }

            // prepend vào danh sách
            items.forEach(n => {
                const item = document.createElement('div');
                item.className = 'notify-item is-unread';

                let iconClass = '';
                let iconName = 'fa-bell';
                let tagText = 'Hệ thống';

                if (n.type === 'user_registered') {
                    iconClass = 'user';
                    iconName = 'fa-user-plus';
                    tagText = 'Khách hàng';
                } else if (n.type === 'order_created') {
                    iconClass = 'order';
                    iconName = 'fa-receipt';
                    tagText = 'Đơn hàng';
                }

                item.innerHTML = `
                    <div class="notify-icon ${iconClass}">
                        <i class="fa-solid ${iconName}"></i>
                    </div>
                    <div class="notify-content">
                        <div class="notify-title">${n.title}</div>
                        ${n.message ? `<div class="notify-text">${n.message}</div>` : ''}
                        <div class="notify-meta">
                            <span>Vừa xong</span>
                            <span class="notify-tag">${tagText}</span>
                        </div>
                    </div>
                `;

                if (list.firstChild) {
                    list.insertBefore(item, list.firstChild);
                } else {
                    list.appendChild(item);
                }
            });

            // hiện toast nhẹ
            if (items[0] && items[0].title) {
                showToast(items[0].title, 'success');
            }
        } catch (err) {
            console.error('Fetch notifications error', err);
        }
    }

    // Gọi 5 giây một lần
    setInterval(fetchNewNotifications, 5000);

    // Toast đơn giản, dùng class .toast .toast-success/.toast-error bạn đã CSS
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type} is-hidden`;
        toast.innerHTML = `
            <i class="fa-solid ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <span>${message}</span>
        `;

        document.body.appendChild(toast);

        // kích hoạt animation
        requestAnimationFrame(() => {
            toast.classList.remove('is-hidden');
        });

        setTimeout(() => {
            toast.classList.add('is-hidden');
            setTimeout(() => toast.remove(), 400);
        }, 3000);
    }
});


