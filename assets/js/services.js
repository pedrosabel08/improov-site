(function () {
  "use strict";
  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  const behavior = () => (reducedMotion.matches ? "auto" : "smooth");

  document.querySelectorAll("[data-service-carousel]").forEach((carousel) => {
    const track = carousel.querySelector(".service-carousel__track");
    const cards = Array.from(track.children);
    const previous = carousel.querySelector("[data-carousel-prev]");
    const next = carousel.querySelector("[data-carousel-next]");
    const current = carousel.querySelector("[data-carousel-current]");
    const slides = cards;
    const target = (card) =>
      card.offsetLeft - (track.clientWidth - card.offsetWidth) / 2;
    const nearest = () =>
      slides.reduce(
        (best, card, index) =>
          Math.abs(target(card) - track.scrollLeft) <
          Math.abs(target(slides[best]) - track.scrollLeft)
            ? index
            : best,
        0,
      );
    let settleTimer;
    let requestedCard = null;
    let drag = null;
    let suppressClick = false;
    let observedWidth = track.clientWidth;
    const update = () => {
      const index = nearest();
      const realIndex = index;
      slides.forEach((card, position) => {
        card.classList.toggle("is-current", position === index);
        card.classList.toggle("is-active", position === index);
        if (position === index) card.setAttribute("aria-current", "true");
        else card.removeAttribute("aria-current");
      });
      if (track.dataset.activeIndex !== String(realIndex)) {
        track.dataset.activeIndex = String(realIndex);
        carousel.dispatchEvent(new CustomEvent("service:slidechange"));
      }
      // Keep offscreen slide controls out of the keyboard focus order.
      cards.forEach((card) => {
        const left = card.offsetLeft - track.scrollLeft;
        card.inert =
          left + card.offsetWidth <= 2 || left >= track.clientWidth - 2;
      });
      current.textContent = String(realIndex + 1).padStart(2, "0");
      previous.disabled = realIndex === 0;
      next.disabled = realIndex === cards.length - 1;
      window.clearTimeout(settleTimer);
      settleTimer = window.setTimeout(() => {
        if (drag?.moved) return;
        requestedCard = null;
      }, 160);
    };
    const move = (direction) => {
      const base = requestedCard ? slides.indexOf(requestedCard) : nearest();
      const index = Math.max(0, Math.min(slides.length - 1, base + direction));
      requestedCard = slides[index];
      track.scrollTo({ left: target(requestedCard), behavior: behavior() });
    };
    previous.addEventListener("click", () => move(-1));
    next.addEventListener("click", () => move(1));
    track.addEventListener("keydown", (event) => {
      if (
        event.target !== track ||
        !["ArrowLeft", "ArrowRight", "Home", "End"].includes(event.key)
      )
        return;
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
      if (
        event.pointerType === "touch" ||
        event.button !== 0 ||
        event.target.closest("video")
      )
        return;
      drag = {
        id: event.pointerId,
        x: event.clientX,
        y: event.clientY,
        scroll: track.scrollLeft,
        start: nearest(),
        moved: false,
      };
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
        index = Math.max(
          0,
          Math.min(slides.length - 1, finished.start + direction),
        );
      }
      requestedCard = slides[index];
      track.scrollTo({ left: target(requestedCard), behavior: behavior() });
      window.setTimeout(() => {
        suppressClick = false;
      }, 0);
    };
    track.addEventListener("pointerup", finishDrag);
    track.addEventListener("pointercancel", finishDrag);
    track.addEventListener(
      "click",
      (event) => {
        if (!suppressClick) return;
        event.preventDefault();
        event.stopImmediatePropagation();
        suppressClick = false;
      },
      true,
    );
    track.addEventListener("dragstart", (event) => event.preventDefault());
    track.addEventListener(
      "wheel",
      () => {
        requestedCard = null;
      },
      { passive: true },
    );
    const resize = () => {
      const index = Math.min(
        cards.length - 1,
        Number(track.dataset.activeIndex || 0),
      );
      track.scrollLeft = target(cards[index]);
      update();
    };
    if ("ResizeObserver" in window)
      new ResizeObserver(() => {
        if (track.clientWidth === observedWidth) return;
        observedWidth = track.clientWidth;
        resize();
      }).observe(track);
    else window.addEventListener("resize", resize, { passive: true });
    carousel.classList.add("is-enhanced");
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
    const cards =
      track &&
      track.querySelectorAll(".service-card:not([data-carousel-clone])");
    return (
      cards &&
      cards[Number(track.dataset.activeIndex || 0)] ===
        video.closest(".service-card")
    );
  };
  const syncAnimations = () => {
    videos
      .filter((video) => video.hasAttribute("data-service-animation"))
      .forEach((video) => {
        const isActive = activeAnimation(video);
        if (!document.hidden && visibleAnimations.has(video) && isActive) {
          const nextVideo = video
            .closest(".service-card")
            ?.nextElementSibling?.querySelector("[data-service-animation]");
          if (nextVideo) {
            nextVideo.preload = "auto";
            loadVideo(nextVideo);
          }
        }
        if (
          reducedMotion.matches ||
          navigator.connection?.saveData ||
          document.hidden ||
          !visibleAnimations.has(video) ||
          !isActive
        ) {
          if (
            document.fullscreenElement !== video &&
            !video.webkitDisplayingFullscreen
          )
            video.pause();
          return;
        }
        loadVideo(video);
        video.play()?.catch(() => {});
      });
  };
  videos.forEach((video) => {
    if (video.hasAttribute("data-service-animation")) return;
    const trigger = video.parentElement.querySelector(
      "[data-service-inline-play]",
    );
    trigger?.addEventListener("click", () => {
      loadVideo(video);
      video.play()?.catch(() => {});
    });
    video.addEventListener("play", () => {
      if (trigger) trigger.hidden = true;
    });
    video.addEventListener("pause", () => {
      if (trigger) trigger.hidden = false;
    });
    video.addEventListener("ended", () => {
      if (trigger) trigger.hidden = false;
    });
    video.addEventListener("pointerdown", () => loadVideo(video), {
      once: true,
    });
    video.addEventListener("keydown", () => loadVideo(video), { once: true });
    video.addEventListener("error", () => {
      if (trigger) trigger.hidden = true;
    });
  });
  if ("IntersectionObserver" in window) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach(({ target, isIntersecting, intersectionRatio }) => {
          if (target.hasAttribute("data-service-animation")) {
            if (isIntersecting && intersectionRatio >= 0.55)
              visibleAnimations.add(target);
            else visibleAnimations.delete(target);
          }
        });
        syncAnimations();
      },
      { threshold: [0, 0.55] },
    );
    videos.forEach((video) => observer.observe(video));
  }
  document
    .querySelectorAll("[data-service-carousel]")
    .forEach((carousel) =>
      carousel.addEventListener("service:slidechange", syncAnimations),
    );
  reducedMotion.addEventListener("change", syncAnimations);
  document.addEventListener("visibilitychange", () => {
    if (document.hidden) videos.forEach((video) => video.pause());
    else syncAnimations();
  });

  document.querySelectorAll("[data-service-tour]").forEach((tour) => {
    tour.querySelector("[data-tour-launch]").addEventListener("click", () => {
      const frame = document.createElement("iframe");
      frame.src = tour.dataset.tourSrc;
      frame.title =
        window.ImproovI18n?.translate("services.rdoTour") || "Tour 360º do RDO";
      frame.allow = "fullscreen";
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
      const project = selected.dataset.imageProject;
      const prefix = project + " — ";
      title.replaceChildren();
      if (project && selected.dataset.imageTitle.startsWith(prefix)) {
        const name = document.createElement("strong");
        name.textContent = project;
        title.append(
          name,
          document.createTextNode(
            " — " + selected.dataset.imageTitle.slice(prefix.length),
          ),
        );
      } else title.textContent = selected.dataset.imageTitle;
      title.hidden = selected.dataset.imageCaption === "false";
      dialog.querySelector("[data-lightbox-counter]").textContent =
        imageIndex + 1 + " / " + gallery.length;
    };
    const move = (step) => {
      imageIndex = (imageIndex + step + gallery.length) % gallery.length;
      render();
    };
    const openImage = (button, trigger = button) => {
      gallery = Array.from(
        button
          .closest("[data-service-carousel]")
          .querySelectorAll(
            ".service-card:not([data-carousel-clone]) [data-service-image]",
          ),
      );
      imageIndex = gallery.indexOf(button);
      opener = trigger;
      render();
      videos.forEach((video) => video.pause());
      document.body.classList.add("service-lightbox-open");
      dialog.showModal();
    };
    document
      .querySelectorAll("[data-service-image]")
      .forEach((button) =>
        button.addEventListener("click", () => openImage(button)),
      );
    let swipe = null;
    image.addEventListener("pointerdown", (event) => {
      if (!event.isPrimary) {
        swipe = null;
        return;
      }
      if (event.button !== 0) return;
      swipe = { x: event.clientX, y: event.clientY, time: event.timeStamp };
      image.setPointerCapture(event.pointerId);
    });
    image.addEventListener("pointerup", (event) => {
      if (!swipe) return;
      const dx = event.clientX - swipe.x;
      const dy = event.clientY - swipe.y;
      if (
        Math.abs(dx) > 45 &&
        Math.abs(dx) > Math.abs(dy) * 1.2 &&
        event.timeStamp - swipe.time < 700
      )
        move(dx < 0 ? 1 : -1);
      swipe = null;
    });
    image.addEventListener("pointercancel", () => {
      swipe = null;
    });
    dialog
      .querySelector("[data-lightbox-close]")
      .addEventListener("click", () => dialog.close());
    dialog
      .querySelector("[data-lightbox-prev]")
      .addEventListener("click", () => move(-1));
    dialog
      .querySelector("[data-lightbox-next]")
      .addEventListener("click", () => move(1));
    dialog.addEventListener("keydown", (event) => {
      if (!["ArrowLeft", "ArrowRight", "Home", "End"].includes(event.key))
        return;
      event.preventDefault();
      if (event.key === "Home" || event.key === "End") {
        imageIndex = event.key === "Home" ? 0 : gallery.length - 1;
        render();
      } else move(event.key === "ArrowRight" ? 1 : -1);
    });
    dialog.addEventListener("click", (event) => {
      if (event.target === dialog) dialog.close();
    });
    dialog.addEventListener("close", () => {
      swipe = null;
      document.body.classList.remove("service-lightbox-open");
      opener?.focus({ preventScroll: true });
    });
  }

  const navigation = document.querySelector("[data-services-nav]");
  if (!navigation) return;
  const list = navigation.querySelector("ol");
  const links = Array.from(navigation.querySelectorAll('a[href^="#"]'));
  const sections = links.map((link) =>
    document.getElementById(link.hash.slice(1)),
  );
  let activeId = "";
  let pending = false;
  const updateNavigation = () => {
    pending = false;
    const headerHeight =
      document.querySelector("[data-site-header]")?.getBoundingClientRect()
        .height || 76;
    const offset = headerHeight + navigation.offsetHeight + 32;
    let active = sections[0];
    sections.forEach((section) => {
      if (section.getBoundingClientRect().top <= offset) active = section;
    });
    if (activeId === active.id) return;
    activeId = active.id;
    links.forEach((link) => {
      const selected = link.hash === "#" + activeId;
      if (selected) link.setAttribute("aria-current", "location");
      else link.removeAttribute("aria-current");
      if (selected) {
        const edge =
          link.getBoundingClientRect().left - list.getBoundingClientRect().left;
        if (edge < 0 || edge + link.offsetWidth > list.clientWidth) {
          list.scrollTo({
            left:
              list.scrollLeft +
              edge -
              list.clientWidth / 2 +
              link.offsetWidth / 2,
            behavior: behavior(),
          });
        }
      }
    });
  };
  const scheduleUpdate = () => {
    if (pending) return;
    pending = true;
    window.requestAnimationFrame(updateNavigation);
  };
  window.addEventListener("scroll", scheduleUpdate, { passive: true });
  window.addEventListener(
    "resize",
    () => {
      activeId = "";
      scheduleUpdate();
    },
    { passive: true },
  );
  window.addEventListener("hashchange", scheduleUpdate);
  document.addEventListener("improov:languagechange", scheduleUpdate);
  updateNavigation();
})();
