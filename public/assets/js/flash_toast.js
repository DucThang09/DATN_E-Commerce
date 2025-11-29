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
// ===================== POPUP CHI TIẾT ĐƠN HÀNG =====================
document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('orderDetailOverlay');
    const closeTop = document.getElementById('orderDetailClose');
    const closeBottom = document.getElementById('orderDetailCloseBottom');
    const buttons = document.querySelectorAll('.btn-order-detail');

    if (!overlay || !buttons.length) return;

    function openModal(data) {
        const title = document.getElementById('modalOrderTitle');
        const subtitle = document.getElementById('modalOrderSubtitle');
        const nameEl = document.getElementById('modalCustomerName');
        const emailEl = document.getElementById('modalCustomerEmail');
        const phoneEl = document.getElementById('modalCustomerPhone');
        const addrEl = document.getElementById('modalCustomerAddress');
        const codeEl = document.getElementById('modalOrderCode');
        const totalEl = document.getElementById('modalOrderTotal');
        const methodEl = document.getElementById('modalOrderMethod');
        const timeEl = document.getElementById('modalOrderPlacedOn');
        const statusChip = document.getElementById('modalOrderStatusChip');
        const itemsWrap = document.getElementById('modalOrderItems');

        if (title) title.textContent = 'Đơn hàng #' + data.id;
        if (subtitle) subtitle.textContent = `${data.name} vừa đặt đơn hàng trị giá ${data.total}`;

        if (nameEl) nameEl.textContent = data.name || '—';
        if (emailEl) emailEl.textContent = data.email || '—';
        if (phoneEl) phoneEl.textContent = data.number || '—';
        if (addrEl) addrEl.textContent = data.address || '—';

        if (codeEl) codeEl.textContent = '#' + data.id;
        if (totalEl) totalEl.textContent = data.total || '—';
        if (methodEl) methodEl.textContent = data.method === 'cash on delivery'
            ? 'Thanh toán khi nhận hàng (COD)'
            : (data.method || '—');
        if (timeEl) timeEl.textContent = data.placed_on || '—';

        if (statusChip) {
            statusChip.textContent = data.statusLabel || '';
            statusChip.classList.toggle('is-done', data.status !== 'pending');
        }

        // ==== RENDER SẢN PHẨM ====
        if (itemsWrap) {
            itemsWrap.innerHTML = '';

            if (Array.isArray(data.items) && data.items.length) {
                data.items.forEach(item => {
                    const row = document.createElement('div');
                    row.className = 'order-item-row';

                    row.innerHTML = `
                    <div class="order-item-main">
                        <div class="order-item-name">${item.product_name}</div>
                        <div class="order-item-qty">Số lượng: ${item.quantity}</div>
                    </div>
                    <div class="order-item-price">
                        ${item.total_price}
                    </div>
                `;

                    itemsWrap.appendChild(row);
                });
            } else {
                itemsWrap.innerHTML =
                    '<p class="order-items-empty">Không có sản phẩm trong đơn này.</p>';
            }
        }

        overlay.classList.remove('order-modal-hidden');
        document.body.classList.add('no-scroll');
    }


    function closeModal() {
        overlay.classList.add('order-modal-hidden');
        document.body.classList.remove('no-scroll');
    }

    buttons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const url = this.dataset.detailUrl;   // 👈 lấy từ data-detail-url
            if (!url) return;

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.json())
                .then(data => {
                    openModal(data); // hàm fill dữ liệu + mở popup
                })
                .catch(err => console.error(err));
        });
    });

    if (closeTop) closeTop.addEventListener('click', closeModal);
    if (closeBottom) closeBottom.addEventListener('click', closeModal);

    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !overlay.classList.contains('order-modal-hidden')) {
            closeModal();
        }
    });
});

