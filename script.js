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
