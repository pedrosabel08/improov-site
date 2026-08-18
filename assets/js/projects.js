(function () {
  "use strict";
  function readProjects() {
    const node = document.getElementById("projects-data");
    if (!node) return [];
    try {
      return JSON.parse(node.textContent).projects || [];
    } catch (_) {
      return [];
    }
  }
  function value(map, lang) {
    return map?.[lang] || map?.["pt-BR"] || "";
  }
  function apply(event) {
    const lang =
      event?.detail?.language || window.ImproovI18n?.getLanguage() || "pt-BR";
    const projects = readProjects();
    document.querySelectorAll("[data-project-slug]").forEach((card) => {
      const project = projects.find(
        (item) => item.slug === card.dataset.projectSlug,
      );
      if (!project) return;
      const title = card.querySelector("[data-project-title]");
      const location = card.querySelector("[data-project-location]");
      const image = card.querySelector("img");
      if (title) title.textContent = value(project.title, lang);
      const locationText = location?.querySelector("[data-project-location-text]");
      if (locationText) locationText.textContent = value(project.location, lang);
      if (image) image.alt = value(project.media.hero.alt, lang);
    });
    const detail = document.querySelector("[data-project-detail]");
    if (!detail) return;
    const project = projects.find(
      (item) => item.slug === detail.dataset.projectDetail,
    );
    if (!project) return;
    detail.querySelector("[data-project-title]").textContent = value(
      project.title,
      lang,
    );
    const detailLocation = detail.querySelector("[data-project-location]");
    const detailLocationText = detailLocation?.querySelector(
      "[data-project-location-text]",
    );
    if (detailLocationText) {
      detailLocationText.textContent = value(project.location, lang);
    } else if (detailLocation) {
      detailLocation.textContent = value(project.location, lang);
    }
    detail.querySelector("[data-project-subtitle]").textContent = value(
      project.detail.subtitle,
      lang,
    );
    const detailHero = detail.querySelector(".project-detail-hero img");
    if (detailHero) detailHero.alt = value(project.media.hero.alt, lang);
    detail.querySelectorAll(".project-gallery img").forEach((image, index) => {
      image.alt = value(project.media.hero.alt, lang) + " — " + (index + 1);
    });
    const description = detail.querySelector("[data-project-description]");
    if (description) {
      description.replaceChildren();
      (
        project.detail.description[lang] ||
        project.detail.description["pt-BR"] ||
        []
      ).forEach((text) => {
        const p = document.createElement("p");
        p.textContent = text;
        description.appendChild(p);
      });
    }
  }
  document.addEventListener("improov:languagechange", apply);
  document.addEventListener("DOMContentLoaded", apply);
})();
