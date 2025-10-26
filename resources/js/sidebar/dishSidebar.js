      document.addEventListener("DOMContentLoaded", () => {
        const openBtn = document.getElementById("openFilters");
        const drawer = document.getElementById("filtersDrawer");
        const panel = document.getElementById("filtersPanel");
        const closeBtn = document.getElementById("closeFilters");
        const overlay = document.getElementById("filtersOverlay");

        if (!openBtn || !drawer || !panel || !overlay) {
          console.warn("Filter drawer elements missing. Check your IDs.");
          return;
        }

        // If button is inside a form, prevent accidental submit
        openBtn.setAttribute("type", "button");

        function openDrawer() {
          drawer.classList.remove("hidden");
          requestAnimationFrame(() =>
            panel.classList.remove("-translate-x-full")
          );
        }
        function closeDrawer() {
          panel.classList.add("-translate-x-full");
          setTimeout(() => drawer.classList.add("hidden"), 300);
        }

        openBtn.addEventListener("click", openDrawer);
        overlay.addEventListener("click", closeDrawer);
        closeBtn?.addEventListener("click", closeDrawer);

        // Esc to close
        document.addEventListener("keydown", (e) => {
          if (e.key === "Escape" && !drawer.classList.contains("hidden"))
            closeDrawer();
        });
      });