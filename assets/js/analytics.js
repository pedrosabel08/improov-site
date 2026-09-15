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

  if (Object.keys(attribution).length) {
    sessionStorage.setItem("improov-attribution", JSON.stringify(attribution));
  }

  const savedAttribution =
    sessionStorage.getItem("improov-attribution") || "{}";
  document.querySelectorAll('[data-async-form="contact"]').forEach((form) => {
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "utm_atribuicao";
    input.value = savedAttribution;
    form.append(input);
  });

  const event = (name, detail = {}) => {
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({ event: name, ...detail });
    document.dispatchEvent(
      new CustomEvent("improov:analytics", { detail: { name, ...detail } }),
    );
  };

  document.addEventListener("click", (click) => {
    const link = click.target.closest("a");
    if (!link) return;
    const href = link.getAttribute("href") || "";
    if (href.includes("wa.me"))
      event("improov_whatsapp_click", { page_path: window.location.pathname });
    else if (href.includes("/contato"))
      event("improov_contact_click", { page_path: window.location.pathname });
    else if (href.includes("/projetos/"))
      event("improov_case_click", {
        page_path: window.location.pathname,
        target: href,
      });
  });

  document.addEventListener("improov:form-success", (formEvent) => {
    event("improov_form_submit", {
      form: formEvent.detail?.form || "unknown",
      page_path: window.location.pathname,
    });
  });

  window.ImproovAnalytics = { attribution, event };
})();
