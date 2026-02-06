document
  .querySelectorAll(".galeria-item .hover-video")
  .forEach(function (video) {
    var container = video.closest(".galeria-item");
    if (!container) container = video.parentElement;
    container.addEventListener("mouseenter", function () {
      var p = video.play();
      if (p && p.catch) p.catch(function () {});
    });
    container.addEventListener("mouseleave", function () {
      video.pause();
      try {
        video.currentTime = 0;
      } catch (e) {}
    });
  });

// Scroll reveal for gallery items
(function () {
  var items = document.querySelectorAll(".galeria-item");
  if (!items || items.length === 0) return;

  function revealAllFallback() {
    items.forEach(function (el, idx) {
      el.style.setProperty("--reveal-delay", idx * 80 + "ms");
      el.classList.add("in-view");
    });
  }

  if ("IntersectionObserver" in window) {
    var obs = new IntersectionObserver(
      function (entries, observer) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            var el = entry.target;
            var idx = Array.prototype.indexOf.call(items, el);
            el.style.setProperty("--reveal-delay", idx * 80 + "ms");
            el.classList.add("in-view");
            observer.unobserve(el);
          }
        });
      },
      { threshold: 0.12 },
    );

    items.forEach(function (el) {
      obs.observe(el);
    });
  } else {
    revealAllFallback();
  }
})();

// Mobile hamburger toggle
(function () {
  var btn = document.querySelector(".hamburger");
  var nav = document.querySelector(".mobile-nav");
  var header = document.querySelector("header");
  if (!btn || !nav) return;

  function setOpen(open) {
    if (open) {
      nav.classList.add("open");
      btn.classList.add("is-active");
      btn.setAttribute("aria-expanded", "true");
      nav.setAttribute("aria-hidden", "false");
      document.body.style.overflow = "hidden";
      if (header) header.classList.remove("hidden");
    } else {
      nav.classList.remove("open");
      btn.classList.remove("is-active");
      btn.setAttribute("aria-expanded", "false");
      nav.setAttribute("aria-hidden", "true");
      document.body.style.overflow = "";
    }
  }

  btn.addEventListener("click", function (e) {
    e.stopPropagation();
    setOpen(!nav.classList.contains("open"));
  });

  // Close when clicking a link
  nav.querySelectorAll("a").forEach(function (a) {
    a.addEventListener("click", function () {
      setOpen(false);
    });
  });

  // Close when clicking outside
  document.addEventListener("click", function (e) {
    if (
      !nav.contains(e.target) &&
      !btn.contains(e.target) &&
      nav.classList.contains("open")
    ) {
      setOpen(false);
    }
  });

  // Close on escape
  document.addEventListener("keydown", function (ev) {
    if (ev.key === "Escape" && nav.classList.contains("open")) setOpen(false);
  });
})();

// Hide header on scroll down, show on scroll up; show footer on scroll down
(function () {
  var header = document.querySelector("header");
  var footer = document.querySelector("footer");
  if (!header || !footer) return;

  var lastScroll = window.pageYOffset || document.documentElement.scrollTop;
  var ticking = false;
  var threshold = 10; // px to ignore tiny scrolls

  window.addEventListener(
    "scroll",
    function () {
      var current = window.pageYOffset || document.documentElement.scrollTop;
      if (!ticking) {
        window.requestAnimationFrame(function () {
          var delta = current - lastScroll;
          if (Math.abs(delta) > threshold) {
            if (delta > 0) {
              // scrolling down
              header.classList.add("hidden");
              footer.classList.add("show-footer");
            } else {
              // scrolling up
              header.classList.remove("hidden");
              footer.classList.remove("show-footer");
            }
            lastScroll = current;
          }
          ticking = false;
        });
        ticking = true;
      }
    },
    { passive: true },
  );
})();
