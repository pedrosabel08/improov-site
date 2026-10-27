(function () {
  "use strict";
  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  const behavior = () => reducedMotion.matches ? "auto" : "smooth";

  document.querySelectorAll("[data-service-carousel]").forEach((carousel) => {
    const track = carousel.querySelector(".service-carousel__track");
    const cards = Array.from(track.children);
    const previous = carousel.querySelector("[data-carousel-prev]");
    const next = carousel.querySelector("[data-carousel-next]");
    const current = carousel.querySelector("[data-carousel-current]");
    const controls = carousel.querySelector(".service-carousel__controls");
    const looping = carousel.hasAttribute("data-carousel-loop") && cards.length > 1;
    if (looping) {
      const clone = (card) => {
        const copy = card.cloneNode(true);
        copy.dataset.carouselClone = "true";
        copy.setAttribute("aria-hidden", "true");
        copy.inert = true;
        return copy;
      };
      track.prepend(clone(cards[cards.length - 1]));
      track.append(clone(cards[0]));
    }
    const slides = Array.from(track.children);
    const target = (card) => card.offsetLeft - (looping ? (track.clientWidth - card.offsetWidth) / 2 : 0);
    const nearest = () => slides.reduce((best, card, index) =>
      Math.abs(target(card) - track.scrollLeft) < Math.abs(target(slides[best]) - track.scrollLeft) ? index : best, 0);
    let settleTimer;
    let requestedCard = null;
    let drag = null;
    let suppressClick = false;
    let observedWidth = track.clientWidth;
    const normalizeLoop = () => {
      if (!looping) return;
      const index = nearest();
      if (index === 0) {
        track.scrollLeft = target(cards[cards.length - 1]);
        if (requestedCard === slides[0]) requestedCard = cards[cards.length - 1];
      } else if (index === slides.length - 1) {
        track.scrollLeft = target(cards[0]);
        if (requestedCard === slides[slides.length - 1]) requestedCard = cards[0];
      }
    };
    const update = () => {
      const index = nearest();
      const realIndex = looping ? (index - 1 + cards.length) % cards.length : index;
      slides.forEach((card, position) => card.classList.toggle("is-current", position === index));
      if (looping) {
        const padding = parseFloat(getComputedStyle(track).paddingBottom) || 0;
        track.style.height = `${slides[index].offsetHeight + padding}px`;
      }
      if (track.dataset.activeIndex !== String(realIndex)) {
        track.dataset.activeIndex = String(realIndex);
        carousel.dispatchEvent(new CustomEvent("service:slidechange"));
      }
      current.textContent = String(realIndex + 1).padStart(2, "0");
      previous.disabled = !looping && track.scrollLeft <= 2;
      next.disabled = !looping && track.scrollLeft >= track.scrollWidth - track.clientWidth - 2;
      window.clearTimeout(settleTimer);
      settleTimer = window.setTimeout(() => {
        if (drag?.moved) return;
        normalizeLoop();
        requestedCard = null;
      }, 160);
    };
    const move = (direction) => {
      normalizeLoop();
      const base = requestedCard ? slides.indexOf(requestedCard) : nearest();
      const index = Math.max(0, Math.min(slides.length - 1, base + direction));
      requestedCard = slides[index];
      track.scrollTo({ left: target(requestedCard), behavior: behavior() });
    };
    previous.addEventListener("click", () => move(-1));
    next.addEventListener("click", () => move(1));
    track.addEventListener("keydown", (event) => {
      if (event.target !== track || !["ArrowLeft", "ArrowRight", "Home", "End"].includes(event.key)) return;
      event.preventDefault();
      if (event.key === "Home" || event.key === "End") {
        const card = event.key === "Home" ? cards[0] : cards[cards.length - 1];
        track.scrollTo({ left: target(card), behavior: behavior() });
      } else move(event.key === "ArrowRight" ? 1 : -1);
    });
    track.addEventListener("scroll", update, { passive: true });
    track.addEventListener("pointerdown", (event) => {
      requestedCard = null;
      // Touch keeps the browser's native swipe and vertical-page gestures.
      if (event.pointerType === "touch" || event.button !== 0 || event.target.closest("video")) return;
      drag = { id: event.pointerId, x: event.clientX, y: event.clientY, scroll: track.scrollLeft, start: nearest(), moved: false };
    });
    track.addEventListener("pointermove", (event) => {
      if (!drag || event.pointerId !== drag.id) return;
      const dx = event.clientX - drag.x;
      const dy = event.clientY - drag.y;
      if (!drag.moved && Math.abs(dx) > 8 && Math.abs(dx) > Math.abs(dy)) {
        drag.moved = true;
        suppressClick = true;
        track.classList.add("is-dragging");
        track.setPointerCapture(event.pointerId);
      }
      if (drag.moved) {
        event.preventDefault();
        track.scrollLeft = drag.scroll - dx;
      }
    });
    const finishDrag = (event) => {
      if (!drag || event.pointerId !== drag.id) return;
      const finished = drag;
      drag = null;
      if (!finished.moved) return;
      track.classList.remove("is-dragging");
      const dx = event.clientX - finished.x;
      const direction = dx < 0 ? 1 : -1;
      let index = nearest();
      if (Math.abs(dx) >= 40 && index === finished.start) {
        index = Math.max(0, Math.min(slides.length - 1, finished.start + direction));
      }
      requestedCard = slides[index];
      track.scrollTo({ left: target(requestedCard), behavior: behavior() });
      window.setTimeout(() => { suppressClick = false; }, 0);
    };
    track.addEventListener("pointerup", finishDrag);
    track.addEventListener("pointercancel", finishDrag);
    track.addEventListener("click", (event) => {
      if (!suppressClick) return;
      event.preventDefault();
      event.stopImmediatePropagation();
      suppressClick = false;
    }, true);
    track.addEventListener("dragstart", (event) => event.preventDefault());
    track.addEventListener("wheel", () => { requestedCard = null; }, { passive: true });
    const resize = () => {
      const index = Math.min(cards.length - 1, Number(track.dataset.activeIndex || 0));
      track.scrollLeft = target(cards[index]);
      update();
    };
    if ("ResizeObserver" in window) new ResizeObserver(() => {
      if (track.clientWidth === observedWidth) return;
      observedWidth = track.clientWidth;
      resize();
    }).observe(track);
    else window.addEventListener("resize", resize, { passive: true });
    carousel.classList.add("is-enhanced");
    controls.hidden = false;
    resize();
  });

  const videos = Array.from(document.querySelectorAll("[data-service-video]"));
  const visibleAnimations = new Set();
  const loadVideo = (video) => {
    if (video.dataset.serviceLoaded) return;
    const source = video.querySelector("source[data-src]");
    if (source) source.src = source.dataset.src;
    video.dataset.serviceLoaded = "true";
    video.load();
  };
  const activeAnimation = (video) => {
    const track = video.closest(".service-carousel__track");
    const cards = track && track.querySelectorAll(".service-card:not([data-carousel-clone])");
    return cards && cards[Number(track.dataset.activeIndex || 0)] === video.closest(".service-card");
  };
  const syncAnimations = () => {
    videos.filter((video) => video.hasAttribute("data-service-animation")).forEach((video) => {
      if (reducedMotion.matches || document.hidden || !visibleAnimations.has(video) || !activeAnimation(video)) {
        if (document.fullscreenElement !== video && !video.webkitDisplayingFullscreen) video.pause();
        return;
      }
      loadVideo(video);
      video.play()?.catch(() => {});
    });
  };
  videos.forEach((video) => {
    const trigger = video.parentElement.querySelector("[data-service-play]");
    // Hydrate before the shared fullscreen player's click handler runs.
    trigger?.addEventListener("click", () => loadVideo(video), { capture: true });
    video.addEventListener("pointerdown", () => loadVideo(video), { once: true });
    video.addEventListener("keydown", () => loadVideo(video), { once: true });
    video.addEventListener("error", () => { if (trigger) trigger.hidden = true; });
  });
  if ("IntersectionObserver" in window) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(({ target, isIntersecting, intersectionRatio }) => {
        if (target.hasAttribute("data-service-animation")) {
          if (isIntersecting && intersectionRatio >= 0.55) visibleAnimations.add(target);
          else visibleAnimations.delete(target);
        }
      });
      syncAnimations();
    }, { threshold: [0, 0.55] });
    videos.forEach((video) => observer.observe(video));
  }
  document.querySelectorAll("[data-service-carousel]").forEach((carousel) => carousel.addEventListener("service:slidechange", syncAnimations));
  reducedMotion.addEventListener("change", syncAnimations);
  document.addEventListener("visibilitychange", () => {
    if (document.hidden) videos.forEach((video) => video.pause());
  });

  document.querySelectorAll("[data-service-tour]").forEach((tour) => {
    tour.querySelector("[data-tour-launch]").addEventListener("click", () => {
      const frame = document.createElement("iframe");
      frame.src = tour.dataset.tourSrc;
      frame.title = window.ImproovI18n?.translate("services.rdoTour") || "Tour 360º do RDO";
      frame.allow = "fullscreen; accelerometer; gyroscope";
      frame.allowFullscreen = true;
      frame.referrerPolicy = "strict-origin-when-cross-origin";
      tour.replaceChildren(frame);
      frame.focus();
    });
  });

  const dialog = document.querySelector("[data-service-lightbox]");
  if (dialog && typeof dialog.showModal === "function") {
    let gallery = [];
    let imageIndex = 0;
    let opener = null;
    const image = dialog.querySelector("[data-lightbox-image]");
    const render = () => {
      const selected = gallery[imageIndex];
      image.src = selected.dataset.imageSrc;
      image.alt = selected.dataset.imageTitle;
      const title = dialog.querySelector("[data-lightbox-title]");
      title.textContent = selected.dataset.imageTitle;
      title.hidden = selected.dataset.imageCaption === "false";
      dialog.querySelector("[data-lightbox-counter]").textContent = (imageIndex + 1) + " / " + gallery.length;
    };
    const move = (step) => {
      imageIndex = (imageIndex + step + gallery.length) % gallery.length;
      render();
    };
    document.querySelectorAll(".service-card:not([data-carousel-clone]) [data-service-image]").forEach((button) => button.addEventListener("click", () => {
      gallery = Array.from(button.closest("[data-service-carousel]").querySelectorAll(".service-card:not([data-carousel-clone]) [data-service-image]"));
      imageIndex = gallery.indexOf(button);
      opener = button;
      render();
      videos.forEach((video) => video.pause());
      document.body.classList.add("service-lightbox-open");
      dialog.showModal();
    }));
    dialog.querySelector("[data-lightbox-close]").addEventListener("click", () => dialog.close());
    dialog.querySelector("[data-lightbox-prev]").addEventListener("click", () => move(-1));
    dialog.querySelector("[data-lightbox-next]").addEventListener("click", () => move(1));
    dialog.addEventListener("keydown", (event) => {
      if (!["ArrowLeft", "ArrowRight", "Home", "End"].includes(event.key)) return;
      event.preventDefault();
      if (event.key === "Home" || event.key === "End") {
        imageIndex = event.key === "Home" ? 0 : gallery.length - 1;
        render();
      } else move(event.key === "ArrowRight" ? 1 : -1);
    });
    dialog.addEventListener("click", (event) => { if (event.target === dialog) dialog.close(); });
    dialog.addEventListener("close", () => {
      document.body.classList.remove("service-lightbox-open");
      opener?.focus({ preventScroll: true });
    });
  }

  const navigation = document.querySelector("[data-services-nav]");
  if (!navigation) return;
  const list = navigation.querySelector("ol");
  const links = Array.from(navigation.querySelectorAll('a[href^="#"]'));
  const sections = links.map((link) => document.getElementById(link.hash.slice(1)));
  const mobile = window.matchMedia("(max-width: 767px)");
  let activeId = "";
  let pending = false;
  const updateNavigation = () => {
    pending = false;
    const headerHeight = document.querySelector("[data-site-header]")?.getBoundingClientRect().height || 76;
    const offset = headerHeight + (mobile.matches ? navigation.offsetHeight + 30 : 48);
    let active = sections[0];
    sections.forEach((section) => {
      if (section.getBoundingClientRect().top <= offset) active = section;
    });
    // The final, deliberately empty section can be shorter than the viewport.
    const last = sections[sections.length - 1];
    if (last.getBoundingClientRect().bottom <= window.innerHeight - 64) active = last;
    if (activeId === active.id) return;
    activeId = active.id;
    links.forEach((link) => {
      const selected = link.hash === "#" + activeId;
      if (selected) link.setAttribute("aria-current", "location");
      else link.removeAttribute("aria-current");
      if (selected && mobile.matches) {
        list.scrollTo({ left: link.parentElement.offsetLeft - list.offsetLeft - 4, behavior: behavior() });
      }
    });
  };
  const scheduleUpdate = () => {
    if (pending) return;
    pending = true;
    window.requestAnimationFrame(updateNavigation);
  };
  window.addEventListener("scroll", scheduleUpdate, { passive: true });
  window.addEventListener("resize", () => { activeId = ""; scheduleUpdate(); }, { passive: true });
  window.addEventListener("hashchange", scheduleUpdate);
  document.addEventListener("improov:languagechange", scheduleUpdate);
  updateNavigation();
})();
