(function () {
  "use strict";

  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");

  function loadVideo(video) {
    if (!video || video.dataset.loaded || !video.dataset.videoSource) return;
    const source = document.createElement("source");
    source.src = video.dataset.videoSource;
    source.type = "video/mp4";
    video.append(source);
    video.dataset.loaded = "true";
    video.load();
  }

  function initReveals() {
    const elements = document.querySelectorAll("[data-case-reveal]");
    if (reducedMotion.matches || !("IntersectionObserver" in window)) {
      elements.forEach((element) => element.classList.add("is-visible"));
      return;
    }
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add("is-visible");
        observer.unobserve(entry.target);
      });
    }, { threshold: 0.1, rootMargin: "0px 0px -4%" });
    elements.forEach((element) => observer.observe(element));
  }

  function initVideos() {
    const videos = document.querySelectorAll("[data-case-video]");
    if (!("IntersectionObserver" in window)) return;
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        const video = entry.target;
        const pill = video.closest(".case-v2-pill");
        if (entry.isIntersecting) {
          loadVideo(video);
          if ((pill || video.dataset.videoMode === "animation") && !reducedMotion.matches) {
            const play = video.play();
            if (play) play.catch(() => {});
          }
        } else if (!video.paused) {
          video.pause();
        }
      });
    }, { rootMargin: "320px 0px", threshold: 0.01 });
    videos.forEach((video) => observer.observe(video));
  }

  function initRail() {
    document.querySelectorAll("[data-case-rail]").forEach((rail) => {
      const items = Array.from(rail.querySelectorAll(".case-v2-rail__item"));
      const collection = rail.closest(".case-v2-collection");
      const counter = collection?.querySelector("[data-case-rail-count]");
      const updateCounter = () => {
        if (!counter || !items.length) return;
        const center = rail.scrollLeft + rail.clientWidth / 2;
        const active = items.reduce((best, item, index) => Math.abs(item.offsetLeft + item.offsetWidth / 2 - center) < Math.abs(items[best].offsetLeft + items[best].offsetWidth / 2 - center) ? index : best, 0);
        counter.textContent = `${String(active + 1).padStart(2, "0")} — ${String(items.length).padStart(2, "0")}`;
      };
      let mouseDrag = null;
      const release = () => {
        if (!mouseDrag) return;
        mouseDrag.rail.classList.remove("is-dragging");
        mouseDrag = null;
      };
      rail.addEventListener("mousedown", (event) => {
        if (event.button !== 0 || rail.scrollWidth <= rail.clientWidth) return;
        event.preventDefault();
        mouseDrag = { rail, startX: event.clientX, startScroll: rail.scrollLeft };
        rail.classList.add("is-dragging");
      });
      document.addEventListener("mousemove", (event) => {
        if (!mouseDrag) return;
        event.preventDefault();
        mouseDrag.rail.scrollLeft = mouseDrag.startScroll - (event.clientX - mouseDrag.startX);
      }, { passive: false });
      document.addEventListener("mouseup", release);
      rail.addEventListener("dragstart", (event) => event.preventDefault());
      rail.addEventListener("selectstart", (event) => { if (mouseDrag) event.preventDefault(); });
      rail.addEventListener("wheel", (event) => {
        if (!event.shiftKey || Math.abs(event.deltaY) <= Math.abs(event.deltaX) || rail.scrollWidth <= rail.clientWidth) return;
        event.preventDefault();
        rail.scrollLeft += event.deltaY;
      }, { passive: false });
      rail.addEventListener("scroll", updateCounter, { passive: true });
      updateCounter();
    });
  }

  function initPlans() {
    document.querySelectorAll("[data-case-plans]").forEach((plans) => {
      const controls = plans.querySelectorAll("[data-case-plan]");
      const panels = plans.querySelectorAll("[data-case-plan-panel]");
      controls.forEach((button) => button.addEventListener("click", () => {
        const target = button.dataset.casePlan;
        controls.forEach((item) => item.setAttribute("aria-selected", String(item === button)));
        panels.forEach((panel) => panel.classList.toggle("is-active", panel.dataset.casePlanPanel === target));
      }));
    });
  }

  function initChapterNavigation() {
    const navigation = document.querySelector("[data-case-chapter-navigation]");
    if (!navigation || !("IntersectionObserver" in window)) return;
    const links = navigation.querySelectorAll("[data-case-chapter-link]");
    const setActive = (id) => links.forEach((link) => link.classList.toggle("is-active", link.dataset.caseChapterLink === id));
    const observer = new IntersectionObserver((entries) => {
      const active = entries.filter((entry) => entry.isIntersecting).sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];
      if (active) setActive(active.target.dataset.caseChapter);
    }, { rootMargin: "-25% 0px -64%", threshold: [0.01, 0.35] });
    document.querySelectorAll("[data-case-chapter]").forEach((chapter) => observer.observe(chapter));
  }

  function initFilmDialog() {
    const dialog = document.querySelector("[data-case-film-dialog]");
    if (!dialog) return;
    const player = dialog.querySelector("[data-case-film-player]");
    const title = dialog.querySelector("[data-case-film-title]");
    const close = () => {
      player.pause();
      player.removeAttribute("src");
      player.load();
      dialog.close();
    };
    document.querySelectorAll("[data-case-film-open]").forEach((trigger) => trigger.addEventListener("click", () => {
      player.poster = trigger.dataset.videoPoster || "";
      player.src = trigger.dataset.videoSource || "";
      title.textContent = trigger.dataset.videoTitle || "Film";
      dialog.showModal();
      player.focus();
    }));
    dialog.querySelector("[data-case-film-close]")?.addEventListener("click", close);
    dialog.addEventListener("click", (event) => { if (event.target === dialog) close(); });
    dialog.addEventListener("cancel", (event) => { event.preventDefault(); close(); });
  }

  document.addEventListener("DOMContentLoaded", () => {
    if (!document.querySelector("[data-case-detail]")) return;
    initReveals();
    initVideos();
    initRail();
    initPlans();
    initChapterNavigation();
    initFilmDialog();
  });
})();
