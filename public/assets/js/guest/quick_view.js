(function () {
  document.addEventListener("DOMContentLoaded", () => {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || "";

    // ========= TOAST (giống home.js) =========
    function topToast(message, type = "success", ms = 2300) {
      const root = document.getElementById("top-toast-root");
      if (!root) return;

      const el = document.createElement("div");
      el.className = `top-toast ${type}`;
      el.innerHTML = `
        <span class="toast-ico">
          <i class="fa-solid ${type === "success" ? "fa-check" : "fa-triangle-exclamation"
        }"></i>
        </span>
        <span>${message || (type === "success" ? "Thành công" : "Có lỗi xảy ra")}</span>
      `;

      root.appendChild(el);
      requestAnimationFrame(() => el.classList.add("show"));

      setTimeout(() => {
        el.classList.add("hide");
        setTimeout(() => el.remove(), 180);
      }, ms);
    }

    async function ajaxAddToCart(form) {
      const btn =
        form.querySelector('button[type="submit"],input[type="submit"]') || null;
      const oldText = btn
        ? btn.tagName === "INPUT"
          ? btn.value
          : btn.textContent
        : "";

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
            Accept: "application/json",
            "X-CSRF-TOKEN": csrf,
          },
          body: new FormData(form),
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
          const msg =
            data?.message ||
            (res.status === 419
              ? "CSRF hết hạn, hãy F5 trang"
              : "Thêm giỏ hàng thất bại");
          throw new Error(msg);
        }

        topToast(data?.message || "Thêm vào giỏ hàng thành công", "success", 2300);

        // update badge số lượng giỏ (nếu server có trả cart_count)
        if (data?.cart_count != null) {
          const el = document.querySelector(".js-cart-count");
          if (el) el.textContent = data.cart_count;
        }
      } catch (e) {
        topToast(e?.message || "Có lỗi xảy ra", "error", 2300);
      } finally {
        if (btn) {
          btn.disabled = false;
          if (btn.tagName === "INPUT") btn.value = oldText;
          else btn.textContent = oldText;
        }
      }
    }

    function isAddToCartForm(form) {
      // ✅ Ưu tiên: bạn gắn data-ajax-cart="1" cho form add-to-cart
      if (form.dataset.ajaxCart === "1") return true;

      // ✅ Fallback: quick view thường chỉ có form trong .pd-actions và có nút name=add_to_cart
      if (form.closest(".pd-actions") && form.querySelector('[name="add_to_cart"]')) return true;

      // ✅ Fallback cuối: action có chứa /cart (bạn đổi nếu route khác)
      const action = form.getAttribute("action") || "";
      if (form.closest(".pd-actions") && action.includes("/cart")) return true;

      return false;
    }

    // ========= CHẶN SUBMIT ĐỂ KHÔNG RELOAD (giống home.js) =========
    // ✅ CHẶN SUBMIT CHỈ KHI BẤM "Thêm vào giỏ" (KHÔNG chặn "Mua ngay")
    document.addEventListener(
      "submit",
      (ev) => {
        const form = ev.target;
        if (!(form instanceof HTMLFormElement)) return;

        const btn = ev.submitter; // nút gây submit

        // ✅ Bấm "Mua ngay" => giữ bản cũ
        if (btn && btn.name === "buy_now") return;

        // ✅ Bấm "Thêm vào giỏ" => AJAX + toast + không reload
        if (btn && btn.name === "add_to_cart") {
          ev.preventDefault();
          ev.stopPropagation();
          ajaxAddToCart(form);
        }
      },
      true
    );


    // ========= PHẦN QUICK VIEW (màu/ảnh/tồn kho/qty) =========
    (function quickViewVariantLogic() {
      const wrap = document.getElementById("jsColors");
      const input = document.getElementById("jsColorInput"); // optional
      const text = document.getElementById("jsColorText");
      const mainImg = document.getElementById("jsMainImage");
      const thumbs = document.getElementById("jsThumbs");

      // qty elements
      const qtyMinus = document.getElementById("jsQtyMinus");
      const qtyPlus = document.getElementById("jsQtyPlus");
      const qtyInput = document.getElementById("jsQtyInput");
      const qtyHint = document.getElementById("jsQtyHint");
      const stockText = document.getElementById("jsStockText");

      // submit buttons (optional)
      const btnAddToCart = document.querySelector(
        ".pd-actions button[type='submit'][name='add_to_cart'], .pd-actions button[type='submit'].js-add-to-cart"
      );
      const btnBuyNow = document.querySelector(".pd-actions .pd-btn.pd-btn-solid");

      const variants = window.QV_VARIANTS || {};
      const storageBase = window.STORAGE_BASE || "/storage/";

      if (!wrap || !text) return;

      function toUrl(path) {
        if (!path) return "";
        if (path.startsWith("http://") || path.startsWith("https://")) return path;
        return storageBase + path.replace(/^\/+/, "");
      }

      function getInvFromVariant(v) {
        const inv = parseInt(v?.inventory ?? "0", 10);
        return Number.isFinite(inv) && inv > 0 ? inv : 0;
      }

      function clamp(n, min, max) {
        return Math.max(min, Math.min(max, n));
      }

      function setAddToCartDisabled(disabled) {
        if (!btnAddToCart) return;
        btnAddToCart.disabled = disabled;
        btnAddToCart.classList.toggle("is-disabled", disabled);
      }

      function setBuyNowDisabled(disabled) {
        if (!btnBuyNow) return;
        btnBuyNow.disabled = disabled;
        btnBuyNow.classList.toggle("is-disabled", disabled);
      }

      function getMaxQty() {
        const maxAttr = parseInt(qtyInput?.getAttribute("max") || "99", 10);
        if (Number.isFinite(maxAttr) && maxAttr > 0) return maxAttr;

        const inv = parseInt(qtyHint?.dataset?.inv || stockText?.dataset?.inv || "99", 10);
        return Number.isFinite(inv) && inv > 0 ? inv : 99;
      }

      function setQty(next) {
        if (!qtyInput) return;

        const max = getMaxQty();
        const val = clamp(parseInt(next || "1", 10) || 1, 1, max);

        qtyInput.value = String(val);
        if (qtyMinus) qtyMinus.disabled = val <= 1;
        if (qtyPlus) qtyPlus.disabled = val >= max;
      }

      function applyInventory(inv) {
        inv = parseInt(inv || "0", 10);
        if (!Number.isFinite(inv) || inv < 0) inv = 0;

        if (stockText) stockText.dataset.inv = String(inv);
        if (qtyHint) qtyHint.dataset.inv = String(inv);

        if (inv <= 0) {
          if (qtyInput) qtyInput.setAttribute("max", "1");
          if (qtyInput) qtyInput.value = "1";

          if (stockText) stockText.textContent = "Hết hàng";
          if (qtyHint) qtyHint.textContent = "0 sản phẩm có sẵn";

          if (qtyMinus) qtyMinus.disabled = true;
          if (qtyPlus) qtyPlus.disabled = true;

          // Bạn muốn hết hàng vẫn cho add-to-cart theo dõi:
          setAddToCartDisabled(false);
          setBuyNowDisabled(false); // cho click để báo
          return;
        }

        if (qtyInput) qtyInput.setAttribute("max", String(inv));
        if (stockText) stockText.textContent = `Còn ${inv} sản phẩm`;
        if (qtyHint) qtyHint.textContent = `${inv} sản phẩm có sẵn`;

        setAddToCartDisabled(false);
        setBuyNowDisabled(false);

        setQty(qtyInput?.value || "1");

        const q = parseInt(qtyInput?.value || "1", 10) || 1;
        if (qtyMinus) qtyMinus.disabled = q <= 1;
        if (qtyPlus) qtyPlus.disabled = q >= inv;
      }

      // Click Mua ngay khi hết hàng -> dùng toast cho giống home
      btnBuyNow?.addEventListener("click", function (e) {
        const inv = parseInt(stockText?.dataset?.inv || qtyHint?.dataset?.inv || "0", 10) || 0;
        if (inv <= 0) {
          e.preventDefault();
          topToast("Sản phẩm đã hết hàng", "error", 2300);
        }
      });

      if (qtyMinus && qtyPlus && qtyInput) {
        qtyMinus.addEventListener("click", function () {
          const inv = parseInt(qtyHint?.dataset?.inv || stockText?.dataset?.inv || "0", 10) || 0;
          if (inv <= 0) return;

          setQty((parseInt(qtyInput.value, 10) || 1) - 1);

          const q = parseInt(qtyInput.value || "1", 10) || 1;
          qtyMinus.disabled = q <= 1;
          qtyPlus.disabled = q >= inv;
        });

        qtyPlus.addEventListener("click", function () {
          const inv = parseInt(qtyHint?.dataset?.inv || stockText?.dataset?.inv || "0", 10) || 0;
          if (inv <= 0) return;

          setQty((parseInt(qtyInput.value, 10) || 1) + 1);

          const q = parseInt(qtyInput.value || "1", 10) || 1;
          qtyMinus.disabled = q <= 1;
          qtyPlus.disabled = q >= inv;
        });

        setQty(qtyInput.value);
      }

      function renderThumbs(v) {
        if (!thumbs) return;

        const urls = [v?.image_01, v?.image_02, v?.image_03].map(toUrl).filter(Boolean);

        thumbs.innerHTML = urls
          .map(
            (u, i) => `
          <button type="button" class="pd-thumb ${i === 0 ? "is-active" : ""}" data-src="${u}">
            <img src="${u}" alt="">
          </button>
        `
          )
          .join("");

        if (mainImg && urls[0]) mainImg.src = urls[0];
      }

      function applyVariant(color, v) {
        if (!v) return;

        renderThumbs(v);

        const hidVariant =
          document.getElementById("jsVariantInput") ||
          document.querySelector('input[name="variant_id"]');
        if (hidVariant) hidVariant.value = v?.variant_id || "";

        const hidImage = document.getElementById("jsImageInput");
        const imgPick = v?.image_01 || v?.image_02 || v?.image_03 || "";
        if (hidImage) hidImage.value = imgPick;

        const inv = getInvFromVariant(v);
        applyInventory(inv);

        if (qtyInput) qtyInput.setAttribute("max", String(inv > 0 ? inv : 1));
      }

      wrap.addEventListener("click", function (e) {
        const btn = e.target.closest(".pd-color");
        if (!btn) return;

        const color = (btn.getAttribute("data-color") || "").trim();
        if (!color) return;

        if (input) input.value = color;
        text.textContent = color;
        wrap.querySelectorAll(".pd-color").forEach((x) => x.classList.remove("is-active"));
        btn.classList.add("is-active");

        const v = variants[color];
        if (v) applyVariant(color, v);
      });

      thumbs?.addEventListener("click", function (e) {
        const btn = e.target.closest(".pd-thumb");
        if (!btn) return;

        const src = btn.getAttribute("data-src");
        if (src && mainImg) mainImg.src = src;

        this.querySelectorAll(".pd-thumb").forEach((x) => x.classList.remove("is-active"));
        btn.classList.add("is-active");
      });

      const firstActive = wrap.querySelector(".pd-color.is-active");
      const firstColor = (firstActive?.getAttribute("data-color") || "").trim();
      if (firstColor && variants[firstColor]) {
        applyVariant(firstColor, variants[firstColor]);
      } else {
        applyInventory(parseInt(stockText?.dataset?.inv || qtyHint?.dataset?.inv || "0", 10) || 0);
      }
    })();
  });
})();
