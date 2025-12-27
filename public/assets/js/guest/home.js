document.addEventListener('DOMContentLoaded', () => {

    /* ========= HERO BANNER (home-slider) ========= */
    const homeSliderEl = document.querySelector('.home-slider');
    if (homeSliderEl) {
        new Swiper(homeSliderEl, {
            loop: true,
            spaceBetween: 16,
            autoplay: {
                delay: 3500,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            pagination: {
                el: homeSliderEl.querySelector('.swiper-pagination'),
                clickable: true,
            },
        });
    }

    /* ========= GỢI Ý CHO BẠN (suggest-slider) ========= */
    const suggestEl = document.querySelector('.suggest-slider');
    if (suggestEl) {
        new Swiper(suggestEl, {
            loop: false,
            spaceBetween: 16,
            navigation: {
                nextEl: ".suggest-next",
                prevEl: ".suggest-prev",
            },
            breakpoints: {
                0: {
                    slidesPerView: 1.15,
                    spaceBetween: 12
                },
                480: {
                    slidesPerView: 1.6,
                    spaceBetween: 14
                },
                768: {
                    slidesPerView: 2.2,
                    spaceBetween: 16
                },
                992: {
                    slidesPerView: 3.2,
                    spaceBetween: 16
                },
                1200: {
                    slidesPerView: 5,
                    spaceBetween: 16
                },
            },
        });
    }

    /* ========= SẢN PHẨM (products-slider) - có NHIỀU slider trên home =========
       ✅ Mỗi slider tự dùng pagination của chính nó => không lỗi, không đè nhau
    */
    document.querySelectorAll('.products-slider').forEach((el) => {
        const pagEl = el.querySelector('.swiper-pagination');

        new Swiper(el, {
            loop: false,
            spaceBetween: 20,
            pagination: {
                el: pagEl,
                clickable: true,
            },
            breakpoints: {
                0: {
                    slidesPerView: 1.15,
                    spaceBetween: 12
                },
                480: {
                    slidesPerView: 2,
                    spaceBetween: 14
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 16
                },
                992: {
                    slidesPerView: 4,
                    spaceBetween: 18
                },
                1200: {
                    slidesPerView: 5,
                    spaceBetween: 20
                },
            },
        });
    });

    /* ========= THƯƠNG HIỆU ĐỐI TÁC (category-slider) - auto chạy =========
       (slider brand logo của bạn đang dùng class .category-slider)
    */
    document.querySelectorAll('.category-slider').forEach((el) => {
        const pagEl = el.querySelector('.swiper-pagination');

        const brandSwiper = new Swiper(el, {
            loop: true,
            spaceBetween: 18,
            slidesPerView: 6,
            speed: 4000, // tốc độ chạy mượt
            autoplay: {
                delay: 0, // chạy liên tục
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            freeMode: true,
            freeModeMomentum: false,
            pagination: pagEl ? {
                el: pagEl,
                clickable: true
            } : undefined,
            breakpoints: {
                0: {
                    slidesPerView: 2.4,
                    spaceBetween: 12
                },
                480: {
                    slidesPerView: 3.2,
                    spaceBetween: 14
                },
                768: {
                    slidesPerView: 4.2,
                    spaceBetween: 16
                },
                992: {
                    slidesPerView: 5.2,
                    spaceBetween: 18
                },
                1200: {
                    slidesPerView: 6,
                    spaceBetween: 18
                },
            },
        });

        // (tuỳ chọn) dừng khi hover: Swiper đã có pauseOnMouseEnter ở trên
    });

});
document.addEventListener("DOMContentLoaded", () => {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

  function showToast(msg) {
    const t = document.createElement("div");
    t.textContent = msg || "Đã thêm vào giỏ";
    t.style.position = "fixed";
    t.style.right = "16px";
    t.style.bottom = "16px";
    t.style.padding = "10px 12px";
    t.style.background = "#111827";
    t.style.color = "#fff";
    t.style.borderRadius = "10px";
    t.style.zIndex = "9999";
    t.style.fontSize = "14px";
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 2300);
  }

  // ✅ (nếu bạn muốn toast xanh top-center thì gọi hàm này thay showToast)
  function topToastSuccess(message, ms = 2300) {
    const root = document.getElementById("top-toast-root");
    if (!root) return;

    const el = document.createElement("div");
    el.className = "top-toast success";
    el.innerHTML = `
      <span class="toast-ico"><i class="fa-solid fa-check"></i></span>
      <span>${message || "Thêm vào giỏ hàng thành công"}</span>
    `;

    root.appendChild(el);
    requestAnimationFrame(() => el.classList.add("show"));

    setTimeout(() => {
      el.classList.add("hide");
      setTimeout(() => el.remove(), 180);
    }, ms);
  }

  async function ajaxAddToCart(form) {
    const btn = form.querySelector('input[type="submit"],button[type="submit"]');
    const oldText = btn ? (btn.tagName === "INPUT" ? btn.value : btn.textContent) : "";

    try {
      if (btn) {
        btn.disabled = true;
        if (btn.tagName === "INPUT") btn.value = "Đang thêm...";
        else btn.textContent = "Đang thêm...";
      }

      const res = await fetch(form.action, {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "Accept": "application/json",
          "X-CSRF-TOKEN": csrf,
        },
        body: new FormData(form),
      });

      const data = await res.json().catch(() => ({}));

      if (!res.ok) {
        const msg =
          data?.message ||
          (res.status === 419 ? "CSRF hết hạn, hãy F5 trang" : "Thêm giỏ hàng thất bại");
        throw new Error(msg);
      }

      // ✅ thông báo
      // showToast(data?.message || "Đã thêm vào giỏ");
      topToastSuccess(data?.message || "Thêm vào giỏ hàng thành công", 2300);

      if (data?.cart_count != null) {
        const el = document.querySelector(".js-cart-count");
        if (el) el.textContent = data.cart_count;
      }
    } catch (e) {
      // showToast(e.message || "Có lỗi xảy ra");
      topToastSuccess(e.message || "Có lỗi xảy ra", 2300);
    } finally {
      if (btn) {
        btn.disabled = false;
        if (btn.tagName === "INPUT") btn.value = oldText;
        else btn.textContent = oldText;
      }
    }
  }

  // ✅ CHỐT: chặn submit chắc chắn để KHÔNG reload trang
  document.addEventListener(
    "submit",
    (ev) => {
      const form = ev.target;
      if (!(form instanceof HTMLFormElement)) return;

      // Cách 1: chỉ chặn form có data-ajax-cart="1"
      // if (form.dataset.ajaxCart !== "1") return;

      // ✅ Cách 2 (mình khuyên dùng): chặn form add-to-cart theo action route
      // (đỡ quên gắn data-ajax-cart)
      const action = form.getAttribute("action") || "";
      if (!action.includes("/cart") && !action.includes("cart.add")) return; 
      // ↑ nếu route cart.add của bạn khác, đổi điều kiện này theo URL thực tế.

      ev.preventDefault();
      ev.stopPropagation(); // ✅ chặn luôn các listener khác submit
      ajaxAddToCart(form);
    },
    true // capture phase
  );
});
