(function () {
  const DATA_URL = "/improov-site/assets/projetos/projetos.json";

  const projectHero = document.getElementById("projectHero");
  const heroTitle = document.getElementById("heroTitle");
  const heroSub = document.getElementById("heroSub");
  const projectDescription = document.getElementById("projectDescription");
  const projectInfo = document.getElementById("projectInfo");
  const carouselTrack = document.getElementById("carouselTrack");
  const relatedProjects = document.getElementById("relatedProjects");
  const projectFilm = document.getElementById("projectFilm");
  const animationsList = document.getElementById("animationsList");

  function tr(key) {
    return window.ImproovI18n ? window.ImproovI18n.translate(key) : key;
  }

  function localizeProject(project) {
    if (!window.ImproovI18n || !project) return project;
    const language = window.ImproovI18n.getLanguage();
    const translations = window.ImproovI18n.projectTranslations || {};
    const localized =
      translations[project.slug] && translations[project.slug][language];
    return localized ? { ...project, ...localized } : project;
  }

  function getModalElements() {
    return {
      mediaModal: document.getElementById("mediaModal"),
      modalVideo: document.getElementById("modalVideo"),
      modalClose: document.getElementById("modalClose"),
      modalBackdrop: document.getElementById("modalBackdrop"),
    };
  }

  function getSlugFromUrl() {
    // Path-based: /improov-site/Projetos/slug
    var pathMatch = window.location.pathname.match(/\/Projetos\/([^\/]+)\/?$/i);
    if (pathMatch && pathMatch[1] && pathMatch[1] !== "index.html") {
      return pathMatch[1];
    }
    // Fallback: ?slug= query string
    var params = new URLSearchParams(window.location.search);
    return params.get("slug");
  }

  function normalizeProjects(data) {
    if (Array.isArray(data)) return data;
    if (Array.isArray(data?.projects)) return data.projects;
    return [];
  }

  function renderInfo(info = {}) {
    const labelMap = {
      localizacao: tr("project.location"),
      cliente: tr("project.client"),
      arquiteto: tr("project.architect"),
      ano: tr("project.year"),
    };

    const entries = Object.entries(info).filter(([, value]) => value);
    if (!entries.length) {
      projectInfo.innerHTML = `<dt>${tr("project.info")}</dt><dd>${tr("project.comingSoon")}</dd>`;
      return;
    }

    projectInfo.innerHTML = entries
      .map(([key, value]) => {
        const label = labelMap[key] || key;
        return `<dt>${label}</dt><dd>${value}</dd>`;
      })
      .join("");
  }

  function renderDescription(description = []) {
    if (!Array.isArray(description) || !description.length) {
      projectDescription.innerHTML = `<p>${tr("project.descriptionSoon")}</p>`;
      return;
    }

    projectDescription.innerHTML = description
      .map((text) => `<p>${text}</p>`)
      .join("");
  }

  function renderGallery(gallery = []) {
    if (!Array.isArray(gallery) || !gallery.length) {
      carouselTrack.innerHTML = "";
      return;
    }

    const slides = [...gallery, ...gallery];
    carouselTrack.innerHTML = slides
      .map(
        (imageSrc, index) => `
          <figure class="carousel-item">
            <img src="${window.ImproovMedia ? window.ImproovMedia.thumb(imageSrc, 1200, 80) : imageSrc}" alt="${tr("project.image")} ${index + 1}" loading="lazy" />
          </figure>`,
      )
      .join("");
  }

  function renderFilm(videoSrc) {
    if (!videoSrc) {
      projectFilm.innerHTML = `<p>${tr("project.videoUnavailable")}</p>`;
      return;
    }

    projectFilm.innerHTML = `
      <video controls playsinline preload="metadata">
        <source src="${videoSrc}" type="video/mp4">
        ${tr("project.browserUnsupported")}
      </video>`;
  }

  function renderAnimations(animations = []) {
    if (!Array.isArray(animations) || !animations.length) {
      animationsList.innerHTML = `<p>${tr("project.animationsUnavailable")}</p>`;
      return;
    }

    animationsList.innerHTML = animations
      .map(
        (src, i) => `
        <div class="animation-item">
          <video playsinline muted preload="metadata" width="320">
            <source src="${src}" type="video/mp4">
          ${tr("project.browserUnsupported")}
          </video>
        </div>
      `,
      )
      .join("");
  }

  function attachAnimationHandlers() {
    const vids = document.querySelectorAll(".animation-item video");
    vids.forEach((v) => {
      const parent = v.closest(".animation-item");

      v.addEventListener("mouseenter", () => {
        try {
          v.muted = true;
          v.loop = true;
          v.play().catch(() => {});
        } catch (e) {}
        parent && parent.classList.add("playing");
      });

      v.addEventListener("mouseleave", () => {
        try {
          v.pause();
          v.currentTime = 0;
        } catch (e) {}
        parent && parent.classList.remove("playing");
      });

      v.addEventListener("click", (e) => {
        e.stopPropagation();
        const src =
          v.currentSrc ||
          (v.querySelector("source") && v.querySelector("source").src);
        if (src) openModal(src);
      });

      if (parent) {
        parent.addEventListener("pointerdown", (ev) => {
          ev.stopPropagation();
          const src =
            v.currentSrc ||
            (v.querySelector("source") && v.querySelector("source").src);
          if (src) openModal(src);
        });
      }
    });
  }

  function openModal(src) {
    const { mediaModal, modalVideo } = getModalElements();
    if (!mediaModal || !modalVideo) return;
    // pause any playing thumbnails
    document.querySelectorAll(".animation-item video").forEach((x) => {
      try {
        x.pause();
        x.currentTime = 0;
        x.closest(".animation-item")?.classList.remove("playing");
      } catch (e) {}
    });
    modalVideo.src = src;
    modalVideo.muted = false;
    mediaModal.classList.add("open");
    mediaModal.setAttribute("aria-hidden", "false");
    modalVideo.play().catch(() => {});
  }

  function closeModal() {
    const { mediaModal, modalVideo } = getModalElements();
    if (!mediaModal || !modalVideo) return;
    try {
      modalVideo.pause();
    } catch (e) {}
    modalVideo.removeAttribute("src");
    try {
      modalVideo.load();
    } catch (e) {}
    mediaModal.classList.remove("open");
    mediaModal.setAttribute("aria-hidden", "true");
  }

  function renderRelated(currentProject, projects) {
    const relatedSlugs = Array.isArray(currentProject.related)
      ? currentProject.related.slice(0, 2)
      : [];

    const related = relatedSlugs
      .map((slug) => projects.find((item) => item.slug === slug))
      .filter(Boolean);

    if (!related.length) {
      relatedProjects.innerHTML = `<p>${tr("project.noRelated")}</p>`;
      return;
    }

    relatedProjects.innerHTML = related
      .map(
        (project) => `
          <a class="galeria-item" href="/improov-site/Projetos/${project.slug}">
            <img src="${window.ImproovMedia ? window.ImproovMedia.thumb(project.thumbnail || project.heroImage, 700, 78) : project.thumbnail || project.heroImage}" alt="${project.title}" loading="lazy" />
            <span class="project-name">${project.title}</span>
          </a>`,
      )
      .join("");
  }

  function enableCarouselDrag() {
    const carousel = document.querySelector(".carousel");
    if (!carousel) return;

    let isDown = false;
    let startX = 0;
    let scrollLeft = 0;

    carousel.classList.add("draggable");

    carousel.addEventListener("pointerdown", (event) => {
      isDown = true;
      carousel.setPointerCapture(event.pointerId);
      startX = event.clientX;
      scrollLeft = carousel.scrollLeft;
      carousel.style.cursor = "grabbing";
      event.preventDefault();
    });

    carousel.addEventListener("pointermove", (event) => {
      if (!isDown) return;
      const walk = startX - event.clientX;
      carousel.scrollLeft = scrollLeft + walk;
    });

    function resetPointer(event) {
      if (!isDown) return;
      isDown = false;
      try {
        carousel.releasePointerCapture(event.pointerId);
      } catch (_err) {}
      carousel.style.cursor = "grab";
    }

    carousel.addEventListener("pointerup", resetPointer);
    carousel.addEventListener("pointercancel", resetPointer);
    carousel.addEventListener("pointerleave", resetPointer);
  }

  async function init() {
    try {
      const response = await fetch(DATA_URL);
      if (!response.ok) throw new Error("Falha ao carregar projetos.json");

      const data = await response.json();
      const projects = normalizeProjects(data);

      if (!projects.length) throw new Error("Nenhum projeto encontrado");

      const slug = getSlugFromUrl();
      const sourceProject =
        projects.find((item) => item.slug === slug) || projects[0];
      const project = localizeProject(sourceProject);
      const localizedProjects = projects.map(localizeProject);

      document.title = `Improov - ${project.title}`;
      heroTitle.textContent = project.title || "Projeto";
      heroSub.textContent = project.subtitle || "";

      if (project.heroImage) {
        const heroImage = window.ImproovMedia
          ? window.ImproovMedia.thumb(project.heroImage, 1920, 82)
          : project.heroImage;
        projectHero.style.backgroundImage = `url('${heroImage}')`;
      }

      renderDescription(project.description);
      renderInfo(project.info);
      renderGallery(project.gallery);
      renderFilm(project.media?.video || project.video);
      renderAnimations(project.media?.animations || project.animations);
      // attach hover/click handlers to animations (must run after render)
      attachAnimationHandlers();
      // modal close handlers
      const { modalClose, modalBackdrop } = getModalElements();
      if (modalClose) modalClose.addEventListener("click", closeModal);
      if (modalBackdrop) modalBackdrop.addEventListener("click", closeModal);
      document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeModal();
      });
      renderRelated(project, localizedProjects);
      enableCarouselDrag();
    } catch (error) {
      console.error(error);
      heroTitle.textContent = tr("project.unavailable");
      heroSub.textContent = tr("project.loadError");
      projectDescription.innerHTML = `<p>${tr("project.loadError")}</p>`;
    }
  }

  init();
  document.addEventListener("improov:languagechange", init);
})();
