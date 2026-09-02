(function () {
  "use strict";
  document.addEventListener("DOMContentLoaded", function () {
    const initLazyVideos = () => {
      const videos = Array.from(document.querySelectorAll("[data-lazy-video]"));
      if (!videos.length) return;
      const reducedMotion = matchMedia("(prefers-reduced-motion: reduce)");
      const hydrate = (video) => {
        if (video.dataset.loaded === "true") return;
        const src = video.dataset.videoSrc;
        if (!src) return;
        video.src = src;
        video.dataset.loaded = "true";
        video.load();
      };
      const play = (video) => {
        if (reducedMotion.matches || video.dataset.videoAutoplay === "false") return;
        hydrate(video);
        const result = video.play();
        if (result) result.catch(() => {});
      };
      const pause = (video) => video.pause();
      const priority = videos.filter((video) => video.hasAttribute("data-lazy-video-priority"));

      priority.forEach((video) => {
        hydrate(video);
        play(video);
      });
      if (!("IntersectionObserver" in window)) {
        videos.filter((video) => !priority.includes(video)).forEach(hydrate);
        return;
      }

      const loader = new IntersectionObserver(
        (entries) => entries.forEach((entry) => {
          if (entry.isIntersecting) {
            hydrate(entry.target);
            loader.unobserve(entry.target);
          }
        }),
        { rootMargin: "200px 120px", threshold: 0.01 },
      );
      const player = new IntersectionObserver(
        (entries) => entries.forEach((entry) => {
          if (entry.isIntersecting && entry.intersectionRatio >= 0.35) play(entry.target);
          else if (!entry.isIntersecting || entry.intersectionRatio < 0.15) pause(entry.target);
        }),
        { threshold: [0, 0.15, 0.35, 0.75] },
      );
      videos.forEach((video) => {
        video.addEventListener("pointerdown", () => hydrate(video), {
          passive: true,
        });
        video.addEventListener("focus", () => hydrate(video), { passive: true });
        if (priority.includes(video)) return;
        loader.observe(video);
        player.observe(video);
      });
    };

    initLazyVideos();
    const initCareersBanner = () => {
      const section = document.querySelector(".editorial-hero--careers");
      const content = section?.querySelector(".editorial-hero__content-inner");
      const media = section?.querySelector(".editorial-hero__media--careers");
      if (!content || !media) return;
      const mobile = matchMedia("(max-width: 1023px)");
      const sync = () => {
        if (mobile.matches) {
          media.style.removeProperty("height");
          return;
        }
        media.style.height = `${Math.ceil(content.getBoundingClientRect().height)}px`;
      };
      sync();
      window.addEventListener("resize", sync, { passive: true });
      mobile.addEventListener?.("change", sync);
      if ("ResizeObserver" in window) new ResizeObserver(sync).observe(content);
    };
    initCareersBanner();
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
