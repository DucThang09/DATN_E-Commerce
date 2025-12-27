document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-order-card]").forEach(card => {
        const btn = card.querySelector("[data-order-toggle]");
        const detail = card.querySelector("[data-order-detail]");
        if (!btn || !detail) return;

        // ✅ default: THU GỌN (giống ảnh bạn gửi)
        card.dataset.open = "0";
        detail.style.display = "none";
        btn.innerHTML = 'Xem chi tiết <span class="chev">▼</span>';

        btn.addEventListener("click", () => {
            const open = card.dataset.open === "1";
            card.dataset.open = open ? "0" : "1";

            detail.style.display = open ? "none" : "block";
            btn.innerHTML = open
                ? 'Xem chi tiết <span class="chev">▼</span>'
                : 'Thu gọn <span class="chev">▲</span>';
        });
    });
});
