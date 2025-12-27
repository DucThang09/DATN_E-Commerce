(function () {
    const tabs = Array.from(document.querySelectorAll(".ptab"));
    const panels = Array.from(document.querySelectorAll(".ptab-panel"));

    function activate(targetSel) {
        tabs.forEach(t => t.classList.toggle("is-active", t.dataset.target === targetSel));
        panels.forEach(p => p.classList.toggle("is-active", "#" + p.id === targetSel));
    }

    tabs.forEach(btn => {
        btn.addEventListener("click", () => {
            activate(btn.dataset.target);
        });
    });

    // Toggle edit in Personal panel
    const personalPanel = document.getElementById("tab-personal");
    const btnEdit = document.querySelector(".js-toggle-edit");
    const btnCancel = document.querySelector(".js-cancel-edit");

    if (btnEdit && personalPanel) {
        btnEdit.addEventListener("click", () => {
            personalPanel.classList.add("is-editing");
        });
    }

    if (btnCancel && personalPanel) {
        btnCancel.addEventListener("click", () => {
            personalPanel.classList.remove("is-editing");
        });
    }
})();
