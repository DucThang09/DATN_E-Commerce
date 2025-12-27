(function () {
  const routes = window.CART_ROUTES || {};
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

  const items = Array.from(document.querySelectorAll(".cartitem"));
  const summary = document.querySelector(".cartsummary");

  const elSubtotal = document.querySelector(".js-subtotal");
  const elShipping = document.querySelector(".js-shipping");
  const elVat = document.querySelector(".js-vat");
  const elTotal = document.querySelector(".js-total");

  const vatRate = parseFloat(summary?.dataset.vatRate || "0.1");
  const shipping = parseInt(summary?.dataset.shipping || "0", 10);

  // ✅ SELECT ALL
  const selectAll = document.querySelector(".js-select-all");

  function formatVND(n) {
    try {
      return n.toLocaleString("vi-VN") + " đ";
    } catch {
      return String(n) + " đ";
    }
  }

  function getSelectedIds() {
    return Array.from(document.querySelectorAll(".js-select-item:checked"))
      .map(cb => cb.value);
  }

  // ✅ Sync trạng thái checkbox "chọn tất cả"
  function syncSelectAllState() {
    if (!selectAll) return;

    // ✅ chỉ tính các checkbox thuộc sản phẩm CÒN HÀNG (inv > 0)
    const cbsEligible = Array.from(document.querySelectorAll(".js-select-item"))
      .filter(cb => {
        if (cb.disabled) return false;
        const row = cb.closest(".cartitem");
        const inv = parseInt(row?.dataset?.inv || "0", 10);
        return inv > 0;
      });

    // Nếu không có món nào còn hàng => không thể "chọn tất cả"
    if (cbsEligible.length === 0) {
      selectAll.checked = false;
      selectAll.indeterminate = false;
      return;
    }

    const allChecked = cbsEligible.every(cb => cb.checked);
    const anyChecked = cbsEligible.some(cb => cb.checked);

    selectAll.checked = allChecked;
    selectAll.indeterminate = !allChecked && anyChecked;
  }



  function calcBaseSubtotal() {
    // ✅ Chỉ tính theo sản phẩm đã tick
    const selected = new Set(getSelectedIds());

    // ✅ Không tick gì -> subtotal = 0
    if (selected.size === 0) return 0;

    let subtotal = 0;
    items.forEach(row => {
      const id = row.dataset.id;
      if (!selected.has(id)) return;

      const price = parseInt(row.dataset.price || "0", 10);
      const qty = parseInt(row.dataset.qty || "1", 10);
      subtotal += price * qty;
    });

    return subtotal;
  }

  function updateLineTotal(row) {
    const price = parseInt(row.dataset.price || "0", 10);
    const qty = parseInt(row.dataset.qty || "1", 10);
    const line = row.querySelector(".js-line-total");
    if (line) line.textContent = formatVND(price * qty);
  }

  function updateSummary() {
    const subtotal = calcBaseSubtotal();

    // ✅ Không chọn gì -> ship = 0, vat = 0
    const ship = subtotal > 0 ? shipping : 0;
    const vat = subtotal > 0 ? Math.round(subtotal * vatRate) : 0;

    const total = subtotal + ship + vat;

    if (elSubtotal) elSubtotal.textContent = formatVND(subtotal);
    if (elShipping) elShipping.textContent = formatVND(ship);
    if (elVat) elVat.textContent = formatVND(vat);
    if (elTotal) elTotal.textContent = formatVND(total);
  }

  async function postJSON(url, data) {
    if (!url) return { ok: false };
    const res = await fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrf || "",
        "Accept": "application/json",
      },
      body: JSON.stringify(data),
    });
    return res.json().catch(() => ({ ok: res.ok }));
  }

  function submitSelectedTo(url) {
    const selected = getSelectedIds();
    if (selected.length === 0) {
      alert("Vui lòng chọn ít nhất một sản phẩm.");
      return;
    }

    const form = document.createElement("form");
    form.method = "POST";
    form.action = url;

    const token = document.createElement("input");
    token.type = "hidden";
    token.name = "_token";
    token.value = csrf || "";
    form.appendChild(token);

    selected.forEach(id => {
      const input = document.createElement("input");
      input.type = "hidden";
      input.name = "selected_items[]";
      input.value = id;
      form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
  }

  // Init line totals + summary
  items.forEach(updateLineTotal);
  updateSummary();
  syncSelectAllState(); // ✅ init trạng thái select all
  items.forEach(row => {
    const inv = parseInt(row.dataset.inv || "0", 10);
    if (inv <= 0) {
      row.querySelectorAll(".js-qty[data-action='inc']").forEach(b => b.disabled = true);
    }
  });

  document.querySelectorAll(".js-select-item:checked")
    .forEach(cb => cb.dispatchEvent(new Event("change")));
  updateSummary();
  syncSelectAllState();


  // ✅ Click "Chọn tất cả"
  selectAll?.addEventListener("change", () => {
    const checked = selectAll.checked;

    document.querySelectorAll(".js-select-item").forEach(cb => {
      const row = cb.closest(".cartitem");
      const inv = parseInt(row?.dataset?.inv || "0", 10);

      // ✅ hết hàng/disabled: không tick
      if (cb.disabled || inv <= 0) {
        cb.checked = false;
        return;
      }

      cb.checked = checked;
    });

    updateSummary();
    syncSelectAllState();
  });




  // Checkbox change -> update summary + sync select all
  document.addEventListener("change", (e) => {
    if (!e.target.classList.contains("js-select-item")) return;

    const row = e.target.closest(".cartitem");
    const inv = parseInt(row?.dataset.inv || "0", 10);

    if (e.target.checked && inv <= 0) {
      alert("Sản phẩm đã hết hàng.");
      // nếu bạn muốn vẫn cho tick thì bỏ dòng dưới
      e.target.checked = false;
    }

    updateSummary();
    syncSelectAllState();
  });


  // +/- quantity
  document.addEventListener("click", async (e) => {
    const btn = e.target.closest(".js-qty");
    if (!btn) return;

    const row = btn.closest(".cartitem");
    if (!row) return;

    const action = btn.dataset.action;
    let qty = parseInt(row.dataset.qty || "1", 10);
    const inv = parseInt(row.dataset.inv || "0", 10);

    if (action === "inc" && inv <= 0) {
      alert("Sản phẩm đang hết hàng, không thể tăng số lượng.");
      return;
    }
    if (action === "dec") qty = Math.max(1, qty - 1);
    if (action === "inc") qty = Math.min(99, qty + 1);

    row.dataset.qty = String(qty);
    const input = row.querySelector(".js-qty-input");
    if (input) input.value = String(qty);

    updateLineTotal(row);
    updateSummary();
    syncSelectAllState(); // ✅ nếu đang chọn tất cả mà chỉnh qty vẫn ok

    // lưu xuống DB (AJAX)
    if (routes.updateQty) {
      try {
        await postJSON(routes.updateQty, { cart_id: row.dataset.id, qty });
      } catch (_) { }
    }
  });

  // Remove one (xóa 1 dòng)
  document.addEventListener("click", (e) => {
    const btn = e.target.closest(".js-remove-one");
    if (!btn) return;

    const id = btn.dataset.id;
    if (!id) return;

    const form = document.createElement("form");
    form.method = "POST";
    form.action = routes.removeSelected;

    const token = document.createElement("input");
    token.type = "hidden";
    token.name = "_token";
    token.value = csrf || "";
    form.appendChild(token);

    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "selected_items[]";
    input.value = id;
    form.appendChild(input);

    document.body.appendChild(form);
    form.submit();
  });

  // Remove selected
  document.querySelector(".js-remove-selected")?.addEventListener("click", () => {
    submitSelectedTo(routes.removeSelected);
  });

  document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".js-select-item:checked")
      .forEach(cb => cb.dispatchEvent(new Event("change")));
  });

})();

