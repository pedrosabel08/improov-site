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
