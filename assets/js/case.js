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
    const videos = Array.from(
      document.querySelectorAll("[data-case-video]"),
    ).filter(
      (video) =>
        !["animation", "carousel"].includes(video.dataset.caseMediaKind),
    );
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
      .filter((video) =>
        ["motion", "interlude", "editorial"].includes(
          video.dataset.caseMediaKind,
        ),
      )
      .forEach((video) => motionObserver.observe(video));
  }

  function initAnimationsStorytelling() {
    document.querySelectorAll("[data-case-animations]").forEach((section) => {
      const scroll = section.querySelector(".case-v3-animations__scroll");
      const steps = Array.from(
        section.querySelectorAll("[data-case-animation-step]"),
      );
      const counter = section.querySelector("[data-case-animation-counter]");
      const videos = Array.from(
        section.querySelectorAll('[data-case-media-kind="animation"]'),
      );
      // Tablet and mobile use the horizontal animation rail; desktop alone
      // keeps the sticky, viewport-sized storytelling sequence.
      const mobile = window.matchMedia("(max-width: 1024px)");
      if (!scroll || !steps.length) return;

      let activeIndex = -1;
      let inView = false;
      let frame = 0;

      const pauseAllExcept = (allowed) => {
        videos.forEach((video) => {
          if (!allowed.has(video)) video.pause();
        });
      };

      const setActive = (index, shouldPlay = true) => {
        const nextIndex = Math.max(0, Math.min(index, steps.length - 1));
        const nextStep = steps[nextIndex];
        const nextVideos = new Set(
          nextStep.querySelectorAll('[data-case-media-kind="animation"]'),
        );
        steps.forEach((step, stepIndex) => {
          const active = stepIndex === nextIndex;
          step.classList.toggle("is-active", active);
          step.setAttribute("aria-hidden", String(!active));
        });
        if (counter) {
          counter.textContent =
            String(nextIndex + 1).padStart(2, "0") +
            " — " +
            String(steps.length).padStart(2, "0");
        }
        pauseAllExcept(nextVideos);
        if (shouldPlay || activeIndex >= 0) {
          nextVideos.forEach((video) => {
            media.load(video);
            if (reducedMotion.matches || !shouldPlay) return;
            const play = video.play();
            if (play) play.catch(() => {});
          });
        }
        activeIndex = nextIndex;
      };

      const prepare = (index) => {
        const step = steps[index];
        if (!step) return;
        step
          .querySelectorAll('[data-case-media-kind="animation"]')
          .forEach((video) => media.load(video));
      };

      const getStickyTop = () => {
        const value = parseFloat(
          window.getComputedStyle(
            section.querySelector(".case-v3-animations__sticky"),
          ).top,
        );
        return Number.isFinite(value) ? value : 0;
      };

      const updateDesktop = () => {
        if (mobile.matches || !inView) return;
        const stepDistance = window.innerHeight * 0.6;
        const sectionTop = window.scrollY + scroll.getBoundingClientRect().top;
        const start = sectionTop - getStickyTop();
        const progress = Math.max(0, window.scrollY - start);
        const index = Math.min(
          steps.length - 1,
          Math.floor(progress / Math.max(1, stepDistance)),
        );
        setActive(index);
        if (
          index < steps.length - 1 &&
          progress % stepDistance > stepDistance * 0.45
        ) {
          prepare(index + 1);
        }
      };

      const scheduleDesktopUpdate = () => {
        if (frame) return;
        frame = window.requestAnimationFrame(() => {
          frame = 0;
          updateDesktop();
        });
      };

      videos.forEach((video) => {
        if (reducedMotion.matches) video.controls = true;
      });

      if (supportsObserver) {
        const sectionObserver = new IntersectionObserver(
          (entries) => {
            entries.forEach((entry) => {
              inView = entry.isIntersecting;
              if (inView) {
                if (mobile.matches) return;
                updateDesktop();
              } else {
                pauseAllExcept(new Set());
              }
            });
          },
          { threshold: [0, 0.08], rootMargin: "-5% 0px -5%" },
        );
        sectionObserver.observe(scroll);

        const mobileObserver = new IntersectionObserver(
          (entries) => {
            if (!mobile.matches) return;
            const visible = entries
              .filter((entry) => entry.isIntersecting)
              .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];
            if (!visible) return;
            const index = steps.indexOf(visible.target);
            if (index >= 0) setActive(index);
          },
          { threshold: [0.2, 0.55, 0.8], rootMargin: "-12% 0px -12%" },
        );
        steps.forEach((step) => mobileObserver.observe(step));
      } else {
        inView = true;
        videos.forEach((video) => {
          media.load(video);
          video.controls = true;
        });
      }

      window.addEventListener("scroll", scheduleDesktopUpdate, {
        passive: true,
      });
      window.addEventListener("resize", scheduleDesktopUpdate, {
        passive: true,
      });
      setActive(0, false);
    });
  }

  function initFocusScrollStories() {
    document.querySelectorAll("[data-case-focus-scroll]").forEach((section) => {
      const track = section.querySelector("[data-case-focus-track]");
      const sticky = section.querySelector("[data-case-focus-sticky]");
      const steps = Array.from(
        section.querySelectorAll("[data-case-focus-step]"),
      );
      const controls = Array.from(section.querySelectorAll("[data-case-plan]"));
      const mobile = window.matchMedia("(max-width: 767px)");
      if (!track || !sticky || !steps.length) return;

      let activeIndex = -1;
      let inView = false;
      let frame = 0;

      const setActive = (index) => {
        const nextIndex = Math.max(0, Math.min(index, steps.length - 1));
        steps.forEach((step, stepIndex) => {
          const active = stepIndex === nextIndex;
          step.classList.toggle("is-active", active);
          step.setAttribute(
            "aria-hidden",
            section.hasAttribute("data-case-plans") && mobile.matches
              ? "false"
              : String(!active),
          );
          if (section.hasAttribute("data-case-plans")) {
            // Plan panels become carousel slides on mobile, so none of them
            // may be removed from layout by the hidden attribute.
            step.hidden = false;
          }
        });
        controls.forEach((control, controlIndex) => {
          const selected = controlIndex === nextIndex;
          control.setAttribute("aria-selected", String(selected));
          control.tabIndex = selected ? 0 : -1;
        });
        activeIndex = nextIndex;
      };

      const getStickyTop = () => {
        const value = parseFloat(window.getComputedStyle(sticky).top);
        return Number.isFinite(value) ? value : 0;
      };

      const updateDesktop = () => {
        if (mobile.matches || !inView) return;
        const stepDistance = window.innerHeight * 0.6;
        const trackTop = window.scrollY + track.getBoundingClientRect().top;
        const start = trackTop - getStickyTop();
        const progress = Math.max(0, window.scrollY - start);
        const index = Math.min(
          steps.length - 1,
          Math.floor(progress / Math.max(1, stepDistance)),
        );
        setActive(index);
      };

      const scheduleDesktopUpdate = () => {
        if (frame) return;
        frame = window.requestAnimationFrame(() => {
          frame = 0;
          updateDesktop();
        });
      };

      if (supportsObserver) {
        const observer = new IntersectionObserver(
          (entries) => {
            entries.forEach((entry) => {
              inView = entry.isIntersecting;
              if (inView) updateDesktop();
            });
          },
          { threshold: [0, 0.08], rootMargin: "-5% 0px -5%" },
        );
        observer.observe(track);
      } else {
        inView = true;
      }

      window.addEventListener("scroll", scheduleDesktopUpdate, {
        passive: true,
      });
      window.addEventListener(
        "resize",
        () => {
          setActive(activeIndex < 0 ? 0 : activeIndex);
          scheduleDesktopUpdate();
        },
        { passive: true },
      );
      setActive(0);
    });
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

  function initFilms() {
    const films = Array.from(document.querySelectorAll(".case-v3-film__video"));
    const observer = supportsObserver
      ? new IntersectionObserver(
          (entries) => {
            entries.forEach(({ target, isIntersecting }) => {
              if (!isIntersecting && document.fullscreenElement !== target &&
                  !target.webkitDisplayingFullscreen)
                target.pause();
            });
          },
          { threshold: 0 },
        )
      : null;
    films.forEach((video) => {
      observer?.observe(video);
      const trigger = video.parentElement.querySelector("[data-case-film-play]");
      let wasFullscreen = false;
      if (trigger) {
        trigger.hidden = false;
        trigger.addEventListener("click", () => {
          const play = video.play();
          if (play) play.catch(() => {});
          if (video.requestFullscreen) {
            video.requestFullscreen().catch(() => {
              trigger.hidden = true;
            });
          } else if (video.webkitEnterFullscreen) {
            const enter = () => {
              try { video.webkitEnterFullscreen(); }
              catch { trigger.hidden = true; }
            };
            if (video.readyState > 0) enter();
            else video.addEventListener("loadedmetadata", enter, { once: true });
          } else {
            trigger.hidden = true;
          }
        });
      }
      const leaveFullscreen = () => {
        video.pause();
        if (trigger) trigger.hidden = false;
      };
      document.addEventListener("fullscreenchange", () => {
        if (document.fullscreenElement === video) wasFullscreen = true;
        else if (wasFullscreen) {
          wasFullscreen = false;
          leaveFullscreen();
        }
      });
      video.addEventListener("webkitendfullscreen", leaveFullscreen);
      video.addEventListener("play", () => {
        if (media.active) media.deactivate(media.active);
        films.forEach((other) => {
          if (other !== video) other.pause();
        });
      });
    });
    document.addEventListener("visibilitychange", () => {
      if (document.hidden) films.forEach((video) => video.pause());
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
        if (!rail || !slides.length) return;

        let activeIndex = 0;
        let carouselVisible = false;
        const preloadAdjacentImages = (index) => {
          [index - 1, index, index + 1].forEach((candidate) => {
            const slide = slides[candidate];
            if (!slide) return;
            slide.querySelectorAll("img[loading='lazy']").forEach((image) => {
              image.loading = "eager";
            });
          });
        };
        const carouselVideos = slides
          .map((slide) =>
            slide.querySelector('[data-case-media-kind="carousel"]'),
          )
          .filter(Boolean);
        const syncVideo = () => {
          const activeVideo = slides[activeIndex].querySelector(
            '[data-case-media-kind="carousel"]',
          );
          carouselVideos.forEach((video) => {
            if (carouselVisible && video === activeVideo) {
              media.activate(video);
            } else {
              media.deactivate(video);
            }
          });
        };
        const select = (index, shouldScroll = false) => {
          activeIndex = Math.max(0, Math.min(index, slides.length - 1));
          preloadAdjacentImages(activeIndex);
          slides.forEach((slide, slideIndex) => {
            const active = slideIndex === activeIndex;
            slide.classList.toggle("is-active", active);
            slide.setAttribute("aria-current", active ? "true" : "false");
          });
          if (shouldScroll)
            slides[activeIndex].scrollIntoView({
              behavior: reducedMotion.matches ? "auto" : "smooth",
              block: "nearest",
              inline: "center",
            });
          syncVideo();
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
        if (supportsObserver && carouselVideos.length) {
          const observer = new IntersectionObserver(
            (entries) => {
              carouselVisible = entries[0]?.isIntersecting || false;
              syncVideo();
            },
            { threshold: 0.35 },
          );
          observer.observe(carousel);
        } else {
          carouselVisible = true;
        }
        select(0);
      });
  }

  function initMomentsCarousels() {
    document.querySelectorAll("[data-case-moments]").forEach((rail) => {
      const slides = Array.from(
        rail.querySelectorAll("[data-case-moment-slide]"),
      );
      if (!slides.length) return;

      let activeIndex = 0;
      const select = (index, shouldScroll = false) => {
        activeIndex = Math.max(0, Math.min(index, slides.length - 1));
        slides.forEach((slide, slideIndex) => {
          const active = slideIndex === activeIndex;
          slide.classList.toggle("is-active", active);
          slide.setAttribute("aria-current", active ? "true" : "false");
        });
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
      rail.addEventListener("keydown", (event) => {
        if (!["ArrowLeft", "ArrowRight", "Home", "End"].includes(event.key))
          return;
        event.preventDefault();
        if (event.key === "Home") return select(0, true);
        if (event.key === "End") return select(slides.length - 1, true);
        select(activeIndex + (event.key === "ArrowRight" ? 1 : -1), true);
      });
      select(0);
    });
  }

  function initDragRails() {
    const DRAG_THRESHOLD = 8;
    document
      .querySelectorAll("[data-case-gallery-rail], [data-case-moments]")
      .forEach((rail) => {
        let drag = null;
        let suppressClick = false;
        const slides = Array.from(
          rail.querySelectorAll("[data-case-gallery-slide]"),
        );
        rail.querySelectorAll("img").forEach((image) => {
          image.draggable = false;
        });
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
          const activeDrag = drag;
          drag = null;
          const deltaX = (event?.clientX ?? activeDrag.lastX) - activeDrag.startX;
          const distance = Math.abs(deltaX);
          const elapsed = Math.max(
            1,
            (event?.timeStamp ?? performance.now()) - activeDrag.startTime,
          );
          const velocity = Math.max(distance / elapsed, activeDrag.velocity || 0);
          const slideWidth =
            slides[activeDrag.startIndex]?.offsetWidth || rail.clientWidth;
          const shouldAdvance =
            distance >= slideWidth * 0.12 || velocity >= 0.35;
          const direction =
            shouldAdvance && deltaX !== 0 ? (deltaX < 0 ? 1 : -1) : 0;
          if (activeDrag.isDragging) {
            suppressClick = true;
            rail.dispatchEvent(
              new CustomEvent("case-gallery-snap", {
                detail: { index: activeDrag.startIndex, direction },
              }),
            );
            rail.classList.remove("is-dragging");
            rail.style.removeProperty("scroll-snap-type");
            rail.style.removeProperty("scroll-behavior");
          }
          if (event && rail.hasPointerCapture?.(event.pointerId))
            rail.releasePointerCapture(event.pointerId);
        };
        rail.addEventListener("pointerdown", (event) => {
          if (event.pointerType === "mouse" && event.button !== 0) return;
          // A new gesture starts a fresh click/drag decision. If the browser
          // did not emit a click after the previous drag, do not carry that
          // suppression into this next intentional interaction.
          suppressClick = false;
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
            isDragging: false,
          };
        });
        rail.addEventListener("pointermove", (event) => {
          if (!drag || event.pointerId !== drag.pointerId) return;
          const now = event.timeStamp > 0 ? event.timeStamp : performance.now();
          const elapsed = Math.max(1, now - drag.lastTime);
          drag.velocity = Math.abs((event.clientX - drag.lastX) / elapsed);
          drag.lastX = event.clientX;
          drag.lastTime = now;
          const distance = event.clientX - drag.startX;
          if (!drag.isDragging && Math.abs(distance) > DRAG_THRESHOLD) {
            drag.isDragging = true;
            rail.classList.add("is-dragging");
            rail.style.scrollSnapType = "none";
            rail.style.scrollBehavior = "auto";
            rail.setPointerCapture?.(event.pointerId);
          }
          if (!drag.isDragging) return;
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
            suppressClick = false;
            event.preventDefault();
            event.stopPropagation();
          },
          true,
        );
      });
  }

  function initPillPlayback() {
    const hoverEnabled = window.matchMedia(
      "(hover: hover) and (pointer: fine)",
    );
    document
      .querySelectorAll("[data-case-moments] .case-v3-moment")
      .forEach((moment) => {
        const video = moment.querySelector("[data-case-video]");
        if (!video) return;
        const stop = () => {
          media.deactivate(video);
          if (video.readyState > 0) {
            try {
              video.currentTime = 0;
            } catch (_) {}
          }
        };
        const start = () => {
          video.muted = true;
          video.playsInline = true;
          media.activate(video, { interactive: true });
        };
        if (hoverEnabled.matches) {
          moment.addEventListener("pointerenter", (event) => {
            if (event.pointerType !== "mouse") return;
            start();
          });
          moment.addEventListener("pointerleave", () => {
            stop();
          });
        } else {
          moment.addEventListener("click", () => {
            if (media.active === video && !video.paused) stop();
            else start();
          });
        }
      });
  }

  function initVerticalMomentSound() {
    const hoverEnabled = window.matchMedia(
      "(hover: hover) and (pointer: fine)",
    );
    document.querySelectorAll("[data-case-vertical-moment]").forEach((moment) => {
      const video = moment.querySelector('[data-case-media-kind="vertical-moment"]');
      if (!video) return;

      const mute = () => {
        video.muted = true;
      };
      const playMuted = () => {
        media.load(video);
        video.muted = true;
        video.volume = 1;
        const play = video.play();
        if (play) play.catch(() => {});
      };
      const unmute = () => {
        media.load(video);
        const wasPlaying = !video.paused;
        video.muted = false;
        window.requestAnimationFrame(() => {
          if (!wasPlaying || !video.paused) return;
          const resume = video.play();
          if (resume) {
            resume.catch(() => {
              video.muted = true;
              const resumeMuted = video.play();
              if (resumeMuted) resumeMuted.catch(() => {});
            });
          }
        });
      };

      if (hoverEnabled.matches) {
        moment.addEventListener("pointerenter", (event) => {
          if (event.pointerType === "mouse") unmute();
        });
        moment.addEventListener("pointerleave", mute);
        moment.addEventListener("focusin", unmute);
        moment.addEventListener("focusout", mute);
      } else {
        moment.addEventListener("click", () => {
          video.muted = !video.muted;
        });
      }
      if (supportsObserver) {
        const observer = new IntersectionObserver(
          (entries) => {
            entries.forEach((entry) => {
              if (entry.isIntersecting && entry.intersectionRatio >= 0.35) {
                playMuted();
              } else if (!entry.isIntersecting || entry.intersectionRatio < 0.2) {
                mute();
                video.pause();
              }
            });
          },
          { threshold: [0, 0.2, 0.35, 0.6] },
        );
        observer.observe(moment);
      } else {
        playMuted();
      }
      document.addEventListener("visibilitychange", () => {
        if (document.hidden) {
          mute();
          video.pause();
        }
      });
    });
  }

  function initPlans() {
    document.querySelectorAll("[data-case-plans]").forEach((plans) => {
      const controls = Array.from(plans.querySelectorAll("[data-case-plan]"));
      const panels = Array.from(
        plans.querySelectorAll("[data-case-plan-panel]"),
      );
      const stage = plans.querySelector(".case-v3-floorplans__stage");
      const mobileTitle = plans.querySelector("[data-case-floorplan-title]");
      const mobileCount = plans.querySelector("[data-case-floorplan-count]");
      const mobile = window.matchMedia("(max-width: 767px)");
      let scrollFrame = 0;
      const activate = (index, shouldFocus = false) => {
        const nextIndex = Math.max(0, Math.min(index, panels.length - 1));
        controls.forEach((control, controlIndex) => {
          const selected = controlIndex === nextIndex;
          control.setAttribute("aria-selected", String(selected));
          control.tabIndex = selected ? 0 : -1;
          if (selected && shouldFocus) control.focus();
        });
        panels.forEach((panel, panelIndex) => {
          const selected = panelIndex === nextIndex;
          // Mobile presents every plan as a horizontal carousel slide;
          // desktop keeps the single focused panel behavior.
          panel.hidden = mobile.matches ? false : !selected;
          panel.classList.toggle("is-active", selected);
          panel.setAttribute(
            "aria-hidden",
            mobile.matches ? "false" : String(!selected),
          );
        });
        const activePlan =
          panels[nextIndex]?.querySelector("[data-image-label]");
        if (mobileTitle) {
          mobileTitle.textContent = activePlan?.dataset.imageLabel || "Planta";
        }
        if (mobileCount) {
          mobileCount.textContent = `${String(nextIndex + 1).padStart(2, "0")} / ${String(panels.length).padStart(2, "0")}`;
        }
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
      const updateFromScroll = () => {
        scrollFrame = 0;
        if (!mobile.matches || !stage) return;
        const stageLeft = stage.getBoundingClientRect().left;
        let activeIndex = 0;
        let nearestDistance = Number.POSITIVE_INFINITY;
        panels.forEach((panel, index) => {
          const distance = Math.abs(
            panel.getBoundingClientRect().left - stageLeft - stage.clientLeft,
          );
          if (distance < nearestDistance) {
            nearestDistance = distance;
            activeIndex = index;
          }
        });
        activate(activeIndex);
      };
      const scheduleFromScroll = () => {
        if (scrollFrame) return;
        scrollFrame = window.requestAnimationFrame(updateFromScroll);
      };
      stage?.addEventListener("scroll", scheduleFromScroll, { passive: true });
      activate(0);
      mobile.addEventListener?.("change", () => {
        activate(0);
        scheduleFromScroll();
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

  function initAnimationsCarousel() {
    document.querySelectorAll("[data-case-animations]").forEach((section) => {
      const rail = section.querySelector("[data-case-animation-rail]");
      const cards = Array.from(
        section.querySelectorAll(".case-v3-animation-card"),
      );
      const counter = section.querySelector("[data-case-animation-counter]");
      if (!rail || !cards.length) return;

      const compact = window.matchMedia("(max-width: 1024px)");
      let frame = 0;
      const updateCounter = () => {
        frame = 0;
        if (!compact.matches || !counter) return;
        const left = rail.scrollLeft;
        let activeIndex = 0;
        let nearestDistance = Number.POSITIVE_INFINITY;
        cards.forEach((card, index) => {
          const distance = Math.abs(card.offsetLeft - left);
          if (distance < nearestDistance) {
            nearestDistance = distance;
            activeIndex = index;
          }
        });
        counter.textContent = `${String(activeIndex + 1).padStart(2, "0")} / ${String(cards.length).padStart(2, "0")}`;
      };
      const scheduleCounter = () => {
        if (frame) return;
        frame = window.requestAnimationFrame(updateCounter);
      };

      rail.addEventListener("scroll", scheduleCounter, { passive: true });
      window.addEventListener("resize", scheduleCounter, { passive: true });
      updateCounter();

      const loadObserver = supportsObserver
        ? new IntersectionObserver(
            (entries) => {
              entries.forEach((entry) => {
                const video = entry.target;
                if (entry.isIntersecting) media.load(video);
                if (entry.isIntersecting && entry.intersectionRatio >= 0.55) {
                  media.activate(video);
                } else if (
                  !entry.isIntersecting ||
                  entry.intersectionRatio < 0.2
                ) {
                  media.deactivate(video);
                }
              });
            },
            { rootMargin: "240px 0px", threshold: [0, 0.2, 0.55, 0.8] },
          )
        : null;
      cards.forEach((card) => {
        const video = card.querySelector('[data-case-media-kind="animation"]');
        if (!video) return;
        if (loadObserver) loadObserver.observe(video);
        else {
          media.load(video);
          video.controls = true;
        }
      });
    });
  }

  function initImageDialog() {
    const caseRoot = document.querySelector("[data-case-detail]");
    const dialog = document.querySelector("[data-case-image-dialog]");
    if (!caseRoot || !dialog) return;
    const player = dialog.querySelector("[data-case-image-player]");
    const stage = dialog.querySelector("[data-case-image-stage]");
    const title = dialog.querySelector("[data-case-image-title]");
    const count = dialog.querySelector("[data-case-image-count]");
    const previous = dialog.querySelector("[data-case-image-previous]");
    const next = dialog.querySelector("[data-case-image-next]");
    let activeGroup = [];
    let activeIndex = 0;
    let origin = null;

    const update = (index) => {
      if (!activeGroup.length) return;
      activeIndex = (index + activeGroup.length) % activeGroup.length;
      const trigger = activeGroup[activeIndex];
      player.src = trigger.dataset.imageSrc || "";
      player.alt = trigger.dataset.imageAlt || "";
      player.dataset.humanizedPlan =
        trigger.dataset.imagePlan === "true" ? "true" : "false";
      title.textContent = trigger.dataset.imageLabel || "Imagem";
      count.textContent = `${String(activeIndex + 1).padStart(2, "0")} / ${String(activeGroup.length).padStart(2, "0")}`;
      previous.disabled = activeGroup.length < 2;
      next.disabled = activeGroup.length < 2;
    };
    const close = () => {
      if (document.fullscreenElement === dialog) {
        const exit = document.exitFullscreen?.();
        if (exit) exit.catch(() => {});
      }
      if (dialog.open) dialog.close();
      player.removeAttribute("src");
      player.dataset.humanizedPlan = "false";
      player.style.removeProperty("width");
      player.style.removeProperty("height");
      player.style.removeProperty("transform");
      origin?.focus();
      origin = null;
    };
    const open = (trigger) => {
      const key = trigger.dataset.imageSet || "gallery";
      activeGroup = Array.from(
        caseRoot.querySelectorAll("[data-case-image-open]"),
      ).filter((item) => (item.dataset.imageSet || "gallery") === key);
      if (!activeGroup.length) activeGroup = [trigger];
      origin = trigger;
      update(Math.max(0, activeGroup.indexOf(trigger)));
      if (!dialog.open) {
        dialog.showModal();
        const request = dialog.requestFullscreen?.();
        if (request) request.catch(() => {});
      }
      dialog.querySelector("[data-case-image-close]")?.focus();
    };

    caseRoot.addEventListener("click", (event) => {
      const trigger = event.target.closest("[data-case-image-open]");
      if (trigger) open(trigger);
    });
    document.addEventListener("keydown", (event) => {
      if (!dialog.open) {
        const trigger = event.target.closest?.("[data-case-image-open]");
        if (!trigger || (event.key !== "Enter" && event.key !== " ")) return;
        event.preventDefault();
        open(trigger);
        return;
      }
      if (event.key === "ArrowLeft") {
        event.preventDefault();
        update(activeIndex - 1);
      }
      if (event.key === "ArrowRight") {
        event.preventDefault();
        update(activeIndex + 1);
      }
    });
    previous?.addEventListener("click", () => update(activeIndex - 1));
    next?.addEventListener("click", () => update(activeIndex + 1));
    dialog
      .querySelector("[data-case-image-close]")
      ?.addEventListener("click", close);
    dialog.addEventListener("click", (event) => {
      if (event.target === dialog) close();
    });
    dialog.addEventListener("cancel", (event) => {
      event.preventDefault();
      close();
    });
  }

  function initMobileGalleryRails() {
    document
      .querySelectorAll("[data-case-mobile-gallery-rail]")
      .forEach((rail) => {
        const slides = Array.from(
          rail.querySelectorAll("[data-case-image-open]"),
        );
        const counter = rail
          .closest("[data-case-mobile-gallery-rail]")
          ?.parentElement?.querySelector("[data-case-mobile-gallery-count]");
        if (!slides.length || !counter) return;

        let frame = 0;
        const update = () => {
          frame = 0;
          const left = rail.scrollLeft;
          let activeIndex = 0;
          let nearestDistance = Number.POSITIVE_INFINITY;
          slides.forEach((slide, index) => {
            const distance = Math.abs(slide.offsetLeft - left);
            if (distance < nearestDistance) {
              nearestDistance = distance;
              activeIndex = index;
            }
          });
          counter.textContent = `${String(activeIndex + 1).padStart(2, "0")} / ${String(slides.length).padStart(2, "0")}`;
        };
        const scheduleUpdate = () => {
          if (frame) return;
          frame = window.requestAnimationFrame(update);
        };

        rail.addEventListener("scroll", scheduleUpdate, { passive: true });
        window.addEventListener("resize", scheduleUpdate, { passive: true });
        update();
      });
  }

  document.addEventListener("DOMContentLoaded", () => {
    if (!document.querySelector("[data-case-detail]")) return;
    initReveals();
    initHeroMedia();
    initMedia();
    initAnimationsStorytelling();
    initAnimationsCarousel();
    initFocusScrollStories();
    initStillMotion();
    initFilms();
    initGalleryCarousels();
    initMomentsCarousels();
    initDragRails();
    initPillPlayback();
    initVerticalMomentSound();
    initPlans();
    initChapterNavigation();
    initImageDialog();
    initMobileGalleryRails();
  });
})();
