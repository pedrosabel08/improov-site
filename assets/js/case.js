(function () {
  "use strict";

  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
  const supportsObserver = "IntersectionObserver" in window;

  const setStillMotionState = (video, active) => {
    const stage = video.closest("[data-case-still-motion]");
    if (!stage) return;
    stage.classList.toggle("is-active", active);
    stage.setAttribute("aria-pressed", String(active));
  };

  const media = {
    active: null,
    load(video) {
      if (!video || video.dataset.loaded === "true") return;
      const sourceUrl = video.dataset.caseVideoSource;
      if (!sourceUrl) return;
      const source = document.createElement("source");
      source.src = sourceUrl;
      source.type = "video/mp4";
      video.append(source);
      video.dataset.loaded = "true";
      video.load();
    },
    activate(video, options = {}) {
      if (!video || (reducedMotion.matches && !options.interactive)) return;
      if (this.active && this.active !== video) this.deactivate(this.active);
      this.load(video);
      this.active = video;
      setStillMotionState(video, true);
      const play = video.play();
      if (play) play.catch(() => {});
    },
    deactivate(video) {
      if (!video) return;
      video.pause();
      if (video.dataset.caseMediaKind === "moment") {
        video.muted = true;
        video.dataset.caseHoverSound = "false";
      }
      setStillMotionState(video, false);
      if (this.active === video) this.active = null;
    },
  };

  function initReveals() {
    const elements = document.querySelectorAll("[data-case-reveal]");
    if (reducedMotion.matches || !supportsObserver) {
      elements.forEach((element) => element.classList.add("is-visible"));
      return;
    }
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          entry.target.classList.add("is-visible");
          observer.unobserve(entry.target);
        });
      },
      { threshold: 0.1, rootMargin: "0px 0px -4%" },
    );
    elements.forEach((element) => observer.observe(element));
  }

  function initHeroMedia() {
    const heroVideo = document.querySelector("[data-case-hero-video]");
    if (!heroVideo) return;
    const activate = () => {
      if (reducedMotion.matches) return;
      const play = heroVideo.play();
      if (play) play.catch(() => {});
    };
    const deactivate = () => heroVideo.pause();
    if (!supportsObserver) {
      activate();
      return;
    }
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            activate();
          } else {
            deactivate();
          }
        });
      },
      { threshold: 0.15 },
    );
    observer.observe(heroVideo);
  }

  function initMedia() {
    const videos = Array.from(document.querySelectorAll("[data-case-video]"));
    if (!supportsObserver) return;

    const loader = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) media.load(entry.target);
        });
      },
      { rootMargin: "300px 0px", threshold: 0.01 },
    );
    videos.forEach((video) => loader.observe(video));

    const motionObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          const video = entry.target;
          if (entry.isIntersecting && entry.intersectionRatio >= 0.55) {
            media.activate(video);
          } else if (!entry.isIntersecting || entry.intersectionRatio < 0.2) {
            media.deactivate(video);
          }
        });
      },
      { threshold: [0, 0.2, 0.55, 0.8] },
    );
    videos
      .filter((video) => video.dataset.caseMediaKind === "motion")
      .forEach((video) => motionObserver.observe(video));

    const momentObserver = new IntersectionObserver(
      (entries) => {
        const dominant = entries
          .filter(
            (entry) => entry.isIntersecting && entry.intersectionRatio >= 0.65,
          )
          .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];
        if (dominant) {
          media.activate(dominant.target);
        }
        entries
          .filter(
            (entry) => !entry.isIntersecting || entry.intersectionRatio < 0.2,
          )
          .forEach((entry) => media.deactivate(entry.target));
      },
      { threshold: [0, 0.2, 0.65, 0.9] },
    );
    videos
      .filter((video) => video.dataset.caseMediaKind === "moment")
      .forEach((video) => momentObserver.observe(video));
  }

  function initStillMotion() {
    const hoverEnabled = window.matchMedia(
      "(hover: hover) and (pointer: fine)",
    );
    document.querySelectorAll("[data-case-still-motion]").forEach((stage) => {
      const video = stage.querySelector("[data-case-video]");
      if (!video) return;
      const activate = () => media.activate(video, { interactive: true });
      const deactivate = () => media.deactivate(video);
      stage.addEventListener("click", () => {
        if (stage.classList.contains("is-active")) {
          deactivate();
        } else {
          activate();
        }
      });
      stage.addEventListener("focusin", () => {
        if (!reducedMotion.matches) activate();
      });
      stage.addEventListener("focusout", () => {
        if (!stage.matches(":hover")) deactivate();
      });
      if (hoverEnabled.matches) {
        stage.addEventListener("pointerenter", activate);
        stage.addEventListener("pointerleave", deactivate);
      }
    });
  }

  function initGalleryCarousels() {
    document
      .querySelectorAll("[data-case-gallery-carousel]")
      .forEach((carousel) => {
        const rail = carousel.querySelector("[data-case-gallery-rail]");
        const slides = Array.from(
          carousel.querySelectorAll("[data-case-gallery-slide]"),
        );
        const label = carousel.querySelector("[data-case-gallery-label]");
        const count = carousel.querySelector("[data-case-gallery-count]");
        if (!rail || !slides.length) return;

        let activeIndex = 0;
        const select = (index, shouldScroll = false) => {
          activeIndex = Math.max(0, Math.min(index, slides.length - 1));
          slides.forEach((slide, slideIndex) => {
            const active = slideIndex === activeIndex;
            slide.classList.toggle("is-active", active);
            slide.setAttribute("aria-current", active ? "true" : "false");
          });
          if (label)
            label.textContent = slides[activeIndex].dataset.galleryLabel || "";
          if (count)
            count.textContent =
              String(activeIndex + 1).padStart(2, "0") +
              " — " +
              String(slides.length).padStart(2, "0");
          if (shouldScroll)
            slides[activeIndex].scrollIntoView({
              behavior: reducedMotion.matches ? "auto" : "smooth",
              block: "nearest",
              inline: "center",
            });
        };
        const updateFromScroll = () => {
          const center = rail.scrollLeft + rail.clientWidth / 2;
          const closest = slides.reduce((best, slide, index) => {
            const distance = Math.abs(
              slide.offsetLeft + slide.offsetWidth / 2 - center,
            );
            const bestDistance = Math.abs(
              slides[best].offsetLeft + slides[best].offsetWidth / 2 - center,
            );
            return distance < bestDistance ? index : best;
          }, 0);
          if (closest !== activeIndex) select(closest);
        };
        let frame = 0;
        rail.addEventListener(
          "scroll",
          () => {
            if (frame) return;
            frame = window.requestAnimationFrame(() => {
              updateFromScroll();
              frame = 0;
            });
          },
          { passive: true },
        );
        rail.addEventListener("case-gallery-snap", (event) => {
          const detail = event.detail || {};
          const baseIndex = Number.isInteger(detail.index)
            ? detail.index
            : activeIndex;
          const direction = Number.isInteger(detail.direction)
            ? detail.direction
            : 0;
          select(baseIndex + direction, true);
        });
        rail.addEventListener("keydown", (event) => {
          if (!["ArrowLeft", "ArrowRight", "Home", "End"].includes(event.key))
            return;
          event.preventDefault();
          if (event.key === "Home") return select(0, true);
          if (event.key === "End") return select(slides.length - 1, true);
          select(activeIndex + (event.key === "ArrowRight" ? 1 : -1), true);
        });
        carousel
          .querySelector("[data-case-gallery-previous]")
          ?.addEventListener("click", () => select(activeIndex - 1, true));
        carousel
          .querySelector("[data-case-gallery-next]")
          ?.addEventListener("click", () => select(activeIndex + 1, true));
        select(0);
      });
  }

  function initDragRails() {
    document
      .querySelectorAll("[data-case-gallery-rail], [data-case-moments]")
      .forEach((rail) => {
        let drag = null;
        let suppressClick = false;
        const slides = Array.from(
          rail.querySelectorAll("[data-case-gallery-slide]"),
        );
        const nearestSlide = () => {
          if (!slides.length) return 0;
          const center = rail.scrollLeft + rail.clientWidth / 2;
          return slides.reduce((best, slide, index) => {
            const distance = Math.abs(
              slide.offsetLeft + slide.offsetWidth / 2 - center,
            );
            const bestDistance = Math.abs(
              slides[best].offsetLeft + slides[best].offsetWidth / 2 - center,
            );
            return distance < bestDistance ? index : best;
          }, 0);
        };
        const release = (event) => {
          if (!drag || (event && event.pointerId !== drag.pointerId)) return;
          const deltaX = (event?.clientX ?? drag.lastX) - drag.startX;
          const distance = Math.abs(deltaX);
          const elapsed = Math.max(
            1,
            (event?.timeStamp ?? performance.now()) - drag.startTime,
          );
          const velocity = Math.max(distance / elapsed, drag.velocity || 0);
          const slideWidth =
            slides[drag.startIndex]?.offsetWidth || rail.clientWidth;
          const shouldAdvance =
            distance >= slideWidth * 0.12 || velocity >= 0.35;
          const direction =
            shouldAdvance && deltaX !== 0 ? (deltaX < 0 ? 1 : -1) : 0;
          if (drag.moved) {
            suppressClick = true;
            event?.preventDefault();
            rail.dispatchEvent(
              new CustomEvent("case-gallery-snap", {
                detail: { index: drag.startIndex, direction },
              }),
            );
          }
          rail.classList.remove("is-dragging");
          rail.style.removeProperty("scroll-snap-type");
          rail.style.removeProperty("scroll-behavior");
          drag = null;
          if (event && rail.hasPointerCapture?.(event.pointerId))
            rail.releasePointerCapture(event.pointerId);
        };
        rail.addEventListener("pointerdown", (event) => {
          if (event.pointerType === "mouse" && event.button !== 0) return;
          const startTime =
            event.timeStamp > 0 ? event.timeStamp : performance.now();
          drag = {
            pointerId: event.pointerId,
            startX: event.clientX,
            lastX: event.clientX,
            lastTime: startTime,
            startScroll: rail.scrollLeft,
            startTime,
            startIndex: nearestSlide(),
            velocity: 0,
            moved: false,
          };
          rail.classList.add("is-dragging");
          rail.style.scrollSnapType = "none";
          rail.style.scrollBehavior = "auto";
          rail.setPointerCapture?.(event.pointerId);
        });
        rail.addEventListener("pointermove", (event) => {
          if (!drag || event.pointerId !== drag.pointerId) return;
          const now = event.timeStamp > 0 ? event.timeStamp : performance.now();
          const elapsed = Math.max(1, now - drag.lastTime);
          drag.velocity = Math.abs((event.clientX - drag.lastX) / elapsed);
          drag.lastX = event.clientX;
          drag.lastTime = now;
          const distance = event.clientX - drag.startX;
          if (Math.abs(distance) > 4) drag.moved = true;
          if (!drag.moved) return;
          event.preventDefault();
          rail.scrollLeft = drag.startScroll - distance;
        });
        rail.addEventListener("pointerup", release);
        rail.addEventListener("pointercancel", release);
        rail.addEventListener("lostpointercapture", release);
        rail.addEventListener("dragstart", (event) => event.preventDefault());
        rail.addEventListener(
          "click",
          (event) => {
            if (!suppressClick) return;
            event.preventDefault();
            event.stopPropagation();
            suppressClick = false;
          },
          true,
        );
      });
  }

  function initMomentSound() {
    const hoverEnabled = window.matchMedia(
      "(hover: hover) and (pointer: fine)",
    );
    if (!hoverEnabled.matches) return;
    document.querySelectorAll(".case-v3-moment").forEach((moment) => {
      const video = moment.querySelector("[data-case-video]");
      if (!video) return;
      moment.addEventListener("pointerenter", (event) => {
        if (event.pointerType !== "mouse") return;
        video.dataset.caseHoverSound = "true";
        const enableSound = () => {
          if (video.dataset.caseHoverSound !== "true") return;
          video.muted = false;
          video.volume = 1;
        };
        if (video.paused) {
          video.addEventListener("playing", enableSound, { once: true });
        } else {
          enableSound();
        }
        media.activate(video, { interactive: true });
      });
      moment.addEventListener("pointerleave", () => {
        video.dataset.caseHoverSound = "false";
        video.muted = true;
      });
    });
  }

  function initPlans() {
    document.querySelectorAll("[data-case-plans]").forEach((plans) => {
      const controls = Array.from(plans.querySelectorAll("[data-case-plan]"));
      const panels = Array.from(
        plans.querySelectorAll("[data-case-plan-panel]"),
      );
      const activate = (index, shouldFocus = false) => {
        controls.forEach((control, controlIndex) => {
          const selected = controlIndex === index;
          control.setAttribute("aria-selected", String(selected));
          control.tabIndex = selected ? 0 : -1;
          if (selected && shouldFocus) control.focus();
        });
        panels.forEach((panel, panelIndex) => {
          panel.hidden = panelIndex !== index;
        });
      };
      controls.forEach((control, index) => {
        control.addEventListener("click", () => activate(index));
        control.addEventListener("keydown", (event) => {
          let next = null;
          if (event.key === "ArrowRight" || event.key === "ArrowDown")
            next = (index + 1) % controls.length;
          if (event.key === "ArrowLeft" || event.key === "ArrowUp")
            next = (index - 1 + controls.length) % controls.length;
          if (event.key === "Home") next = 0;
          if (event.key === "End") next = controls.length - 1;
          if (next === null) return;
          event.preventDefault();
          activate(next, true);
        });
      });
    });
  }

  function initChapterNavigation() {
    const navigation = document.querySelector("[data-case-chapter-navigation]");
    if (!navigation || !supportsObserver) return;
    const links = Array.from(
      navigation.querySelectorAll("[data-case-chapter-link]"),
    );
    const setActive = (id) =>
      links.forEach((link) =>
        link.classList.toggle("is-active", link.dataset.caseChapterLink === id),
      );
    const observer = new IntersectionObserver(
      (entries) => {
        const current = entries
          .filter((entry) => entry.isIntersecting)
          .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];
        if (current) setActive(current.target.id);
      },
      { rootMargin: "-24% 0px -64%", threshold: [0.01, 0.35] },
    );
    document
      .querySelectorAll("[data-case-chapter]")
      .forEach((chapter) => observer.observe(chapter));
  }

  function initFilmDialog() {
    const dialog = document.querySelector("[data-case-film-dialog]");
    if (!dialog) return;
    const player = dialog.querySelector("[data-case-film-player]");
    const title = dialog.querySelector("[data-case-film-title]");
    let origin = null;
    const close = () => {
      player.pause();
      player.removeAttribute("src");
      player.load();
      if (dialog.open) dialog.close();
      origin?.focus();
      origin = null;
    };
    document.addEventListener("click", (event) => {
      const trigger = event.target.closest("[data-case-film-open]");
      if (!trigger) return;
      origin = trigger;
      player.poster = trigger.dataset.videoPoster || "";
      player.src = trigger.dataset.videoSource || "";
      title.textContent = trigger.dataset.videoTitle || "Film";
      dialog.showModal();
      player.focus();
      const play = player.play();
      if (play) play.catch(() => {});
      const fullscreen = player.requestFullscreen?.();
      if (fullscreen) fullscreen.catch(() => {});
    });
    dialog
      .querySelector("[data-case-film-close]")
      ?.addEventListener("click", close);
    dialog.addEventListener("click", (event) => {
      if (event.target === dialog) close();
    });
    dialog.addEventListener("cancel", (event) => {
      event.preventDefault();
      close();
    });
  }

  document.addEventListener("DOMContentLoaded", () => {
    if (!document.querySelector("[data-case-detail]")) return;
    initReveals();
    initHeroMedia();
    initMedia();
    initStillMotion();
    initGalleryCarousels();
    initDragRails();
    initMomentSound();
    initPlans();
    initChapterNavigation();
    initFilmDialog();
  });
})();
