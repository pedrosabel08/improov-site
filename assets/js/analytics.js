(function () {
  "use strict";

  const UTM_KEYS = [
    "utm_source",
    "utm_medium",
    "utm_campaign",
    "utm_term",
    "utm_content",
  ];
  const params = new URLSearchParams(window.location.search);
  const attribution = Object.fromEntries(
    UTM_KEYS.filter((key) => params.has(key)).map((key) => [
      key,
      params.get(key),
    ]),
  );

  // Keep the existing first-party attribution flow for the commercial form.
  if (Object.keys(attribution).length) {
    sessionStorage.setItem("improov-attribution", JSON.stringify(attribution));
  }

  const savedAttribution =
    sessionStorage.getItem("improov-attribution") || "{}";
  document.querySelectorAll('[data-async-form="contact"]').forEach((form) => {
    if (form.querySelector('[name="utm_atribuicao"]')) return;
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "utm_atribuicao";
    input.value = savedAttribution;
    form.append(input);
  });

  const cleanText = (value, maxLength = 100) =>
    String(value || "")
      .replace(/\s+/g, " ")
      .trim()
      .slice(0, maxLength);

  const EVENT_PARAMETER_KEYS = {
    contact_form_success: ["form_type", "form_location"],
    whatsapp_click: ["button_location", "cta_name"],
    email_click: ["button_location"],
    cta_click: ["cta_name", "button_location", "destination"],
    project_select: ["project_slug", "project_name", "button_location"],
    case_content_interaction: [
      "project_slug",
      "content_type",
      "content_name",
      "interaction_action",
    ],
    video_start: ["project_slug", "media_name", "media_location"],
    careers_application_submit: ["form_type"],
    social_click: ["social_network", "button_location"],
  };

  const LEGACY_EVENT_PARAMETER_KEYS = {
    improov_whatsapp_click: ["page_path"],
    improov_contact_click: ["page_path"],
    improov_case_click: ["page_path", "target"],
    improov_form_submit: ["page_path", "form"],
  };

  function selectParams(params, allowedKeys) {
    return Object.fromEntries(
      allowedKeys
        .filter(
          (key) =>
            params[key] !== undefined &&
            params[key] !== null &&
            params[key] !== "",
        )
        .map((key) => [key, cleanText(params[key], 150)]),
    );
  }

  const pagePath = () => window.location.pathname || "/";

  function trackEvent(name, params = {}) {
    const allowedKeys = EVENT_PARAMETER_KEYS[name];
    if (!allowedKeys || typeof window.gtag !== "function") return false;
    try {
      window.gtag("event", name, {
        page_path: pagePath(),
        ...selectParams(params, allowedKeys),
      });
      return true;
    } catch (_error) {
      return false;
    }
  }

  // The Publya GTM container is external and cannot be verified from this repo.
  // Keep the old dataLayer event names until Publya confirms they are unused.
  function pushLegacyEvent(name, params = {}) {
    const allowedKeys = LEGACY_EVENT_PARAMETER_KEYS[name];
    if (!allowedKeys) return;
    try {
      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push({
        event: name,
        ...selectParams(params, allowedKeys),
      });
    } catch (_error) {
      // A broken external dataLayer must never interrupt site behavior.
    }
  }

  function safeDestination(link) {
    try {
      const url = new URL(
        link.getAttribute("href") || "",
        window.location.href,
      );
      if (url.origin !== window.location.origin) return url.hostname;
      return `${url.pathname}${url.hash}`;
    } catch (_error) {
      return "";
    }
  }

  function linkLocation(link) {
    return (
      link.dataset.analyticsLocation ||
      link.closest("footer")?.dataset.analyticsLocation ||
      link.closest("header")?.dataset.analyticsLocation ||
      "content"
    );
  }

  function linkText(link) {
    return cleanText(
      link.dataset.analyticsName ||
        link.getAttribute("aria-label") ||
        link.textContent,
    );
  }

  function projectSlug(element, link) {
    const slug =
      element?.dataset.projectSlug ||
      link?.dataset.projectSlug ||
      link?.closest("[data-project-slug]")?.dataset.projectSlug;
    if (slug) return cleanText(slug, 80);
    try {
      return cleanText(
        new URL(link?.href || "", window.location.href).pathname
          .split("/")
          .filter(Boolean)
          .pop(),
        80,
      );
    } catch (_error) {
      return "";
    }
  }

  function projectName(element, link) {
    return cleanText(
      element?.dataset.projectName ||
        link?.querySelector("[data-project-title]")?.textContent ||
        link?.querySelector(".case-v3-next__copy strong")?.textContent,
    );
  }

  function emitLegacyLinkEvent(link, href) {
    if (href.includes("wa.me")) {
      pushLegacyEvent("improov_whatsapp_click", { page_path: pagePath() });
    } else if (href.includes("/contato")) {
      pushLegacyEvent("improov_contact_click", { page_path: pagePath() });
    } else if (href.includes("/projetos/")) {
      pushLegacyEvent("improov_case_click", {
        page_path: pagePath(),
        target: href,
      });
    }
  }

  function handleLinkClick(link) {
    const href = link.getAttribute("href") || "";
    emitLegacyLinkEvent(link, href);

    const markedEvent = link.dataset.analyticsEvent;
    if (markedEvent === "whatsapp_click" || isWhatsAppLink(href)) {
      trackEvent("whatsapp_click", {
        button_location: linkLocation(link),
        cta_name: linkText(link) || "contact_whatsapp",
      });
      return;
    }
    if (
      markedEvent === "email_click" ||
      href.toLowerCase().startsWith("mailto:")
    ) {
      trackEvent("email_click", { button_location: linkLocation(link) });
      return;
    }
    if (markedEvent === "social_click") {
      trackEvent("social_click", {
        social_network: link.dataset.analyticsSocial,
        button_location: linkLocation(link),
      });
      return;
    }
    if (markedEvent === "project_select") {
      trackEvent("project_select", {
        project_slug: projectSlug(link, link),
        project_name: projectName(link, link),
        button_location: link.dataset.analyticsLocation || "project_catalog",
      });
      return;
    }
    if (markedEvent === "cta_click") {
      trackEvent("cta_click", {
        cta_name: link.dataset.analyticsName,
        button_location: linkLocation(link),
        destination: safeDestination(link),
      });
    }
  }

  function isWhatsAppLink(href) {
    try {
      const host = new URL(href, window.location.href).hostname.toLowerCase();
      return (
        host === "wa.me" ||
        host === "whatsapp.com" ||
        host.endsWith(".whatsapp.com")
      );
    } catch (_error) {
      return false;
    }
  }

  function handleCaseContentClick(target) {
    const image = target.closest("[data-case-image-open]");
    if (image) {
      const isFloorplan =
        image.hasAttribute("data-image-plan") ||
        Boolean(image.closest(".case-v3-floorplans"));
      trackEvent("case_content_interaction", {
        project_slug: currentProjectSlug(),
        content_type: isFloorplan ? "floorplan" : "gallery_image",
        content_name: image.dataset.imageLabel || image.dataset.imageAlt,
        interaction_action: "open",
      });
      return;
    }

    const plan = target.closest("[data-case-plan]");
    if (plan) {
      trackEvent("case_content_interaction", {
        project_slug: currentProjectSlug(),
        content_type: "floorplan",
        content_name: plan.textContent,
        interaction_action: "select",
      });
      return;
    }

    const motion = target.closest("[data-case-still-motion]");
    if (motion && motion.getAttribute("aria-pressed") === "true") {
      trackVideoStart(
        motion.dataset.analyticsMediaName || motion.getAttribute("aria-label"),
        "case_media",
      );
    }
  }

  function currentProjectSlug() {
    const detail = document.querySelector("[data-project-detail]");
    return cleanText(
      detail?.dataset.projectDetail ||
        window.location.pathname.split("/").filter(Boolean).pop(),
      80,
    );
  }

  function trackVideoStart(mediaName, mediaLocation) {
    trackEvent("video_start", {
      project_slug: currentProjectSlug(),
      media_name: cleanText(mediaName) || "video",
      media_location: mediaLocation,
    });
  }

  document.addEventListener("click", (click) => {
    const target = click.target instanceof Element ? click.target : null;
    if (!target) return;
    const link = target.closest("a");
    if (link) handleLinkClick(link);
    handleCaseContentClick(target);
  });

  document.addEventListener("improov:form-success", (formEvent) => {
    const formType = formEvent.detail?.form;
    if (formType === "contact") {
      trackEvent("contact_form_success", {
        form_type: "contact",
        form_location: "contact_section",
      });
      pushLegacyEvent("improov_form_submit", {
        form: "contact",
        page_path: pagePath(),
      });
    } else if (formType === "careers") {
      trackEvent("careers_application_submit", { form_type: "careers" });
      pushLegacyEvent("improov_form_submit", {
        form: "careers",
        page_path: pagePath(),
      });
    }
  });

  // Only the user-controlled project/case players are tracked. Autoplaying
  // hero, animation, and ambient videos do not have these selectors.
  document.addEventListener(
    "play",
    (playEvent) => {
      const video = playEvent.target;
      if (!(video instanceof HTMLVideoElement)) return;
      if (video.matches("[data-analytics-testimonial]") && video.controls) {
        trackEvent("video_start", {
          project_slug: video.dataset.analyticsProject,
          media_name: video.getAttribute("aria-label"),
          media_location: "home_testimonials",
        });
        return;
      }
      const isTrackedPlayer =
        video.matches(
          ".project-film video, .case-v3-film__video, .case-v3-viewport-audio__video, .case-v3-testimonial__video",
        ) && video.controls;
      if (!isTrackedPlayer) return;

      const label =
        video.getAttribute("aria-label") ||
        video
          .closest(".case-v3-film__media")
          ?.querySelector(".case-v3-film__label")?.textContent ||
        video.closest(".project-film")?.querySelector(".eyebrow")
          ?.textContent ||
        video.closest(".case-v3-section")?.querySelector(".case-v3-kicker")
          ?.textContent;
      const location = video.closest(".project-film")
        ? "content"
        : "case_media";
      trackVideoStart(label, location);
    },
    true,
  );

  window.ImproovAnalytics = { attribution };
})();
