/**
 * Meta Pixel â€” Improov
 * Pixel ID: 1653623989542944
 *
 * Eventos:
 * - PageView: carregamento da pÃ¡gina
 * - Lead: envio confirmado do formulÃ¡rio de contato
 * - Contact: clique em links do WhatsApp
 *
 * AtenÃ§Ã£o: carregar somente apÃ³s o consentimento de marketing,
 * quando exigido. O evento Lead deve ser disparado pelo cÃ³digo
 * do formulÃ¡rio apÃ³s a confirmaÃ§Ã£o de sucesso do servidor.
 */

(function (window, document) {
  "use strict";

  const PIXEL_ID = "1653623989542944";

  // Evita inicializaÃ§Ã£o duplicada.
  if (window.fbq) return;

  // InicializaÃ§Ã£o oficial do Meta Pixel.
  !(function (f, b, e, v, n, t, s) {
    if (f.fbq) return;

    n = f.fbq = function () {
      n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments);
    };

    if (!f._fbq) f._fbq = n;

    n.push = n;
    n.loaded = true;
    n.version = "2.0";
    n.queue = [];

    t = b.createElement(e);
    t.async = true;
    t.src = v;

    s = b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t, s);
  })(
    window,
    document,
    "script",
    "https://connect.facebook.net/en_US/fbevents.js",
  );

  // Inicializa o Pixel.
  window.fbq("init", PIXEL_ID);

  // Registra visualizaÃ§Ã£o da pÃ¡gina.
  window.fbq("track", "PageView");

  // Disponibiliza o evento Lead para o formulÃ¡rio.
  // Chamar window.improovTrackLead() apÃ³s sucesso confirmado.
  window.improovTrackLead = function () {
    window.fbq("track", "Lead", {
      content_name: "Formulario de contato",
    });
  };

  // Registra cliques em links do WhatsApp.
  document.addEventListener(
    "click",
    function (event) {
      const target = event.target;

      if (!(target instanceof Element)) return;

      const link = target.closest("a[href]");

      if (!link) return;

      let url;

      try {
        url = new URL(link.href, window.location.href);
      } catch {
        return;
      }

      const hostname = url.hostname.toLowerCase();

      const isWhatsApp =
        hostname === "wa.me" || hostname === "api.whatsapp.com";

      if (!isWhatsApp) return;

      window.fbq("track", "Contact", {
        content_name: "WhatsApp",
      });
    },
    true,
  );
})(window, document);
