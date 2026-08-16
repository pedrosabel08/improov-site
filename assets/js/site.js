(function () {
  "use strict";
  document.addEventListener("DOMContentLoaded", function () {
    const header = document.querySelector("[data-site-header]");
    const toggle = document.querySelector(".menu-toggle");
    const menu = document.querySelector(".mobile-menu");
    const setOpen = function (open) {
      if (!toggle || !menu) return;
      toggle.setAttribute("aria-expanded", String(open));
      menu.setAttribute("aria-hidden", String(!open));
      menu.classList.toggle("is-open", open);
      document.body.classList.toggle("menu-open", open);
      if (window.ImproovI18n)
        toggle.setAttribute(
          "aria-label",
          window.ImproovI18n.translate(open ? "menu.close" : "menu.open"),
        );
    };
    if (toggle && menu) {
      toggle.addEventListener("click", () =>
        setOpen(toggle.getAttribute("aria-expanded") !== "true"),
      );
      menu
        .querySelectorAll("a")
        .forEach((link) =>
          link.addEventListener("click", () => setOpen(false)),
        );
      document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") setOpen(false);
      });
    }
    const onScroll = () =>
      header?.classList.toggle("is-scrolled", window.scrollY > 24);
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
    document
      .querySelectorAll(".section, .project-card")
      .forEach((element) => element.setAttribute("data-reveal", ""));
    if (
      !("IntersectionObserver" in window) ||
      matchMedia("(prefers-reduced-motion: reduce)").matches
    )
      document
        .querySelectorAll("[data-reveal]")
        .forEach((el) => el.classList.add("is-visible"));
    else {
      const observer = new IntersectionObserver(
        (entries) =>
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              entry.target.classList.add("is-visible");
              observer.unobserve(entry.target);
            }
          }),
        { threshold: 0.08 },
      );
      document
        .querySelectorAll("[data-reveal]")
        .forEach((el) => observer.observe(el));
    }
  });
})();
