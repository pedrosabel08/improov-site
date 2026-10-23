(function () {
  "use strict";
  const supportsObserver = "IntersectionObserver" in window;

  function initFilms() {
    const films = Array.from(document.querySelectorAll(".case-v3-film__video"));
    const observer = supportsObserver
      ? new IntersectionObserver(
          (entries) => {
            entries.forEach(({ target, isIntersecting }) => {
              if (
                !isIntersecting &&
                document.fullscreenElement !== target &&
                !target.webkitDisplayingFullscreen
              )
                target.pause();
            });
          },
          { threshold: 0 },
        )
      : null;
    films.forEach((video) => {
      observer?.observe(video);
      const trigger = video.parentElement.querySelector(
        "[data-case-film-play]",
      );
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
              try {
                video.webkitEnterFullscreen();
              } catch {
                trigger.hidden = true;
              }
            };
            if (video.readyState > 0) enter();
            else
              video.addEventListener("loadedmetadata", enter, { once: true });
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
        films.forEach((other) => {
          if (other !== video) other.pause();
        });
      });
    });
    document.addEventListener("visibilitychange", () => {
      if (document.hidden) films.forEach((video) => video.pause());
    });
  }

  document.addEventListener("DOMContentLoaded", initFilms);
})();
