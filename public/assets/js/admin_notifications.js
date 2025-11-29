// ================== TAB LỌC THÔNG BÁO (TẤT CẢ / CHƯA ĐỌC / ...) ==================
document.addEventListener('DOMContentLoaded', function () {
    const tabsWrapper = document.getElementById('notifyTabs');
    if (!tabsWrapper) return;

    const tabs = tabsWrapper.querySelectorAll('.notify-tab');
    const listWrapper = document.getElementById('notifyListWrapper');
    if (!tabs.length || !listWrapper) return;

    const baseUrl = tabsWrapper.dataset.url || '';

    // tab hiện tại (lúc load trang)
    let currentTab = 'all';
    const activeBtn = tabsWrapper.querySelector('.notify-tab.active');
    if (activeBtn && activeBtn.dataset.tab) {
        currentTab = activeBtn.dataset.tab;
    }

    // Hàm load lại list cho 1 tab
    function loadTab(tab, scroll = false) {
        if (!baseUrl) return;

        const url = baseUrl + '?tab=' + encodeURIComponent(tab);

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .then((res) => res.text())
            .then((html) => {
                listWrapper.innerHTML = html;

                if (scroll) {
                    listWrapper.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start',
                    });
                }
            })
            .catch((err) => console.error('Lỗi tải notifications:', err));
    }

    // Click tab -> đổi tab + load AJAX 1 lần
    tabs.forEach((btn) => {
        btn.addEventListener('click', function () {
            const tab = this.dataset.tab || 'all';
            currentTab = tab;

            // đổi active tab
            tabs.forEach((b) => b.classList.remove('active'));
            this.classList.add('active');

            loadTab(currentTab, true);
        });
    });

    // ========== AUTO REFRESH: 10 GIÂY LOAD LẠI TAB HIỆN TẠI ==========
    setInterval(function () {
        // không scroll, chỉ âm thầm cập nhật nội dung
        loadTab(currentTab, false);
    }, 5000); // 10000ms = 10s
});
document.addEventListener('DOMContentLoaded', function () {
    const listWrapper = document.getElementById('notifyListWrapper');
    if (!listWrapper) return;

    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : null;

    // Event delegation: click vào bất kỳ notify-card nào
    listWrapper.addEventListener('click', function (e) {
        const card = e.target.closest('.notify-card');
        if (!card) return;

        // Nếu click vào nút "Đánh dấu đã đọc" hoặc nút Xóa thì để form handle bình thường
        if (e.target.closest('form') || e.target.closest('button') || e.target.closest('a.btn-outline-sm')) {
            return;
        }

        const markUrl = card.dataset.markUrl;
        const linkUrl = card.dataset.linkUrl || '';

        // Nếu đã đọc rồi thì chỉ điều hướng (nếu có link_url)
        if (!card.classList.contains('is-unread')) {
            if (linkUrl) {
                window.location.href = linkUrl;
            }
            return;
        }

        if (!markUrl || !csrfToken) {
            // fallback: nếu thiếu thì điều hướng luôn cho đỡ lỗi
            if (linkUrl) window.location.href = linkUrl;
            return;
        }

        // Gửi AJAX mark-read
        fetch(markUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
        })
            .then(res => res.ok ? res.json() : null)
            .then(data => {
                // Nếu backend trả về success = true
                if (data && data.success) {
                    // 1) cập nhật UI của card
                    card.classList.remove('is-unread');

                    const dot = card.querySelector('.notify-dot');
                    const label = card.querySelector('.notify-priority');
                    if (dot) dot.remove();
                    if (label) label.remove();

                    // 1.1) XÓA LUÔN NÚT "Đánh dấu đã đọc"
                    const markForm = card.querySelector('.mark-read-form');
                    if (markForm) {
                        markForm.remove();
                    }

                    // 2) cập nhật số "chưa đọc" trên đầu trang
                    const unreadStrong = document.querySelector('.content-header p strong');
                    if (unreadStrong) {
                        let current = parseInt(unreadStrong.textContent || '0', 10);
                        if (!Number.isNaN(current) && current > 0) {
                            current--;
                            unreadStrong.textContent = current.toString();
                        }
                    }

                    // 3) cập nhật pill "Chưa đọc" bên tab
                    const pill = document.querySelector('.notify-tab[data-tab="unread"] .pill-count');
                    if (pill) {
                        let current = parseInt(pill.textContent || '0', 10);
                        if (!Number.isNaN(current) && current > 0) {
                            current--;
                            if (current <= 0) {
                                pill.remove();
                            } else {
                                pill.textContent = current.toString();
                            }
                        }
                    }

                    // 4) cập nhật badge đỏ trên icon chuông (topbar)
                    const bellBadge = document.querySelector('.notify-btn .notify-badge');
                    if (bellBadge) {
                        let current = parseInt(bellBadge.textContent.replace('+', '') || '0', 10);
                        if (!Number.isNaN(current) && current > 0) {
                            current--;
                            if (current <= 0) {
                                bellBadge.remove();
                            } else {
                                bellBadge.textContent = current > 9 ? '9+' : current.toString();
                            }
                        }
                    }
                }


                // 5) cuối cùng, nếu thông báo có link, điều hướng tới đó
                if (linkUrl) {
                    window.location.href = linkUrl;
                }
            })
            .catch(err => {
                console.error('Mark read error:', err);
                // Nếu lỗi vẫn cho điều hướng để user không bị kẹt
                if (linkUrl) window.location.href = linkUrl;
            });
    });
});
document.addEventListener('DOMContentLoaded', function () {
    const listWrapper = document.getElementById('notifyListWrapper');
    if (!listWrapper) return;

    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : null;

    listWrapper.addEventListener('submit', function (e) {
        const form = e.target.closest('.notify-delete-form');
        if (!form) return;

        e.preventDefault();   // chặn reload trang

        const card = form.closest('.notify-card');
        if (!card || !csrfToken) {
            // fallback nếu thiếu token: để Laravel xử lý bình thường
            form.submit();
            return;
        }

        const url = form.action;

        fetch(url, {
            method: 'POST',                // để _method=DELETE trong FormData hoạt động
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: new FormData(form),
        })
            .then(res => res.ok ? res.json() : null)
            .then(data => {
                if (!data || !data.success) {
                    console.error('Xóa thông báo thất bại từ server', data);
                    return;
                }

                // --- Animation trượt + mờ ---
                card.classList.add('is-removing');

                // Sau 250ms (khớp với CSS transition) thì remove khỏi DOM
                setTimeout(() => {
                    card.remove();
                }, 260);
            })
            .catch(err => {
                console.error('Delete notification error:', err);
            });
    });
});
