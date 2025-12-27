(function () {
  const btn = document.getElementById("loadMoreBtn");
  const grid = document.getElementById("productsGrid");
  if (!btn || !grid) return;

  const baseUrl = btn.getAttribute("data-base-url");

  btn.addEventListener("click", async () => {
    const nextPage = btn.getAttribute("data-next-page");
    if (!nextPage) return;

    btn.disabled = true;
    btn.classList.add("is-loading");

    const params = new URLSearchParams(window.location.search);
    params.set("page", nextPage);

    try {
      const res = await fetch(`${baseUrl}?${params.toString()}`, {
        headers: { "X-Requested-With": "XMLHttpRequest" },
      });

      const data = await res.json();

      if (data.html) {
        grid.insertAdjacentHTML("beforeend", data.html);
      }

      if (data.has_more) {
        btn.setAttribute("data-next-page", data.next_page);
        btn.innerHTML = `Xem thêm ${data.remaining} sản phẩm <i class="fa-solid fa-chevron-down"></i>`;
      } else {
        btn.remove();
      }
    } catch (e) {
      console.error(e);
    } finally {
      btn.disabled = false;
      btn.classList.remove("is-loading");
    }
  });
})();
