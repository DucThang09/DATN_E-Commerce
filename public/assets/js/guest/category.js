document.addEventListener("DOMContentLoaded", () => {
  // LEFT (chỉ init nếu tồn tại)
  const left1 = document.querySelector(".banner-swiper-left");
  if (left1) {
    new Swiper(left1, {
      loop: true,
      slidesPerView: 1,
      spaceBetween: 0,
      autoplay: { delay: 3000, disableOnInteraction: false },
      navigation: {
        nextEl: ".banner-next-left",
        prevEl: ".banner-prev-left",
      },
      pagination: {
        el: ".banner-pagination",
        clickable: true,
      },
    });
  }

  // Nếu bạn có swiper thứ 2 tên ".banner-swiper" thì cũng bọc tương tự
  const left2 = document.querySelector(".banner-swiper");
  if (left2) {
    new Swiper(left2, {
      loop: true,
      spaceBetween: 12,
      autoplay: { delay: 3000, disableOnInteraction: false },
      navigation: {
        nextEl: ".banner-next-left",
        prevEl: ".banner-prev-left",
      },
      pagination: {
        el: ".banner-pagination",
        clickable: true,
      },
    });
  }

  // RIGHT (chỉ init nếu tồn tại)
  const right = document.querySelector(".banner-swiper-right");
  if (right) {
    new Swiper(right, {
      loop: true,
      slidesPerView: 1,
      spaceBetween: 0,
      autoplay: { delay: 3200, disableOnInteraction: false },
      navigation: {
        nextEl: ".banner-next-right",
        prevEl: ".banner-prev-right",
      },
      pagination: {
        el: ".banner-pagination",
        clickable: true,
      },
    });
  }
});
(function () {
  const chips = document.querySelector(".criteria__chips");
  if (!chips) return; // tránh lỗi nếu trang nào không có block này

  function closeAll() {
    chips.querySelectorAll("[data-dd]").forEach((btn) =>
      btn.setAttribute("aria-expanded", "false")
    );
    chips.querySelectorAll("[data-menu]").forEach((menu) => (menu.hidden = true));
  }

  chips.addEventListener("click", (e) => {
    const btn = e.target.closest("[data-dd]");
    if (!btn) return;

    const key = btn.getAttribute("data-dd");
    const menu = chips.querySelector(`[data-menu="${key}"]`);
    if (!menu) return;

    const expanded = btn.getAttribute("aria-expanded") === "true";

    closeAll();
    btn.setAttribute("aria-expanded", String(!expanded));
    menu.hidden = expanded;
  });

  document.addEventListener("click", (e) => {
    if (!e.target.closest(".chipWrap")) closeAll();
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeAll();
  });
})();

