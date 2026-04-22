document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.querySelector(".sidebar");
    const overlay = document.createElement("div");
    const menuButtons = document.querySelectorAll(".menu-toggle, .menu-btn");
    const menuItems = document.querySelectorAll(".nav-menu li");
    const menuLinks = document.querySelectorAll(".nav-menu a");
    const isMobile = () => window.innerWidth <= 980;

    overlay.className = "overlay";
    document.body.appendChild(overlay);

    function syncActiveMenu() {
        const currentPage = window.location.pathname.split("/").pop() || "index.html";

        menuItems.forEach((item) => item.classList.remove("active"));

        menuLinks.forEach((link) => {
            const href = link.getAttribute("href");
            const item = link.closest("li");

            if (!item || !href || href === "#") {
                return;
            }

            if (href === currentPage) {
                item.classList.add("active");
            }
        });
    }

    function setSidebarState(isOpen) {
        if (!sidebar) {
            return;
        }

        sidebar.classList.toggle("active", isOpen);
        overlay.classList.toggle("active", isOpen);
        document.body.style.overflow = isOpen ? "hidden" : "";
    }

    menuButtons.forEach((button) => {
        button.addEventListener("click", () => {
            if (!isMobile()) {
                return;
            }

            setSidebarState(!sidebar?.classList.contains("active"));
        });
    });

    overlay.addEventListener("click", () => setSidebarState(false));

    syncActiveMenu();

    menuLinks.forEach((link) => {
        link.addEventListener("click", () => {
            if (isMobile()) {
                setSidebarState(false);
            }
        });
    });

    window.addEventListener("resize", () => {
        if (!isMobile()) {
            setSidebarState(false);
        }
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
            setSidebarState(false);
        }
    });

    const calendarNav = document.querySelectorAll(".calendar-nav button");
    const monthSpan = document.querySelector(".calendar-nav span");
    const months = [
        "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember"
    ];
    let currentMonth = 2;

    if (calendarNav.length === 2 && monthSpan) {
        calendarNav.forEach((button, index) => {
            button.addEventListener("click", () => {
                currentMonth = index === 0
                    ? (currentMonth === 0 ? 11 : currentMonth - 1)
                    : (currentMonth === 11 ? 0 : currentMonth + 1);

                monthSpan.textContent = months[currentMonth];
            });
        });
    }

    const chartBars = document.querySelectorAll(".chart-bar");
    chartBars.forEach((bar) => {
        bar.addEventListener("mouseenter", () => {
            bar.style.opacity = "0.88";
        });

        bar.addEventListener("mouseleave", () => {
            bar.style.opacity = "1";
        });
    });

    const searchForm = document.querySelector(".search-form");
    const searchBtn = document.querySelector(".btn-search");
    const noData = document.querySelector(".no-data");

    searchBtn?.addEventListener("click", (event) => {
        event.preventDefault();

        if (!searchForm || !noData) {
            return;
        }

        const inputs = searchForm.querySelectorAll("input");
        const hasValue = Array.from(inputs).some((input) => input.value.trim());

        noData.textContent = hasValue ? "Mencari data..." : "Tidak Ada Data";

        if (hasValue) {
            window.setTimeout(() => {
                noData.textContent = "Tidak Ada Data";
            }, 1000);
        }
    });
});
