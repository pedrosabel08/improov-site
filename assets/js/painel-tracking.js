/**
 * PAINEL IMPROOV · rastreamento do site (improov.com.br)
 *
 * Instalar em todas as páginas, antes de </body>:
 *   <script src="/assets/js/painel-tracking.js" defer></script>
 * e preencher PAINEL_WEBHOOK com a URL do App da Web do Apps Script (termina em /exec).
 *
 * O que faz:
 *  - guarda as UTMs, o gclid e o fbclid de quem chega por anúncio (30 dias, último clique);
 *  - quando o formulário de contato é enviado com sucesso, manda o lead para a planilha;
 *  - registra cliques no WhatsApp como evento.
 * Não altera o formulário nem o envio que o site já faz para /api/contacto.php.
 */
(function () {
  'use strict';

  var PAINEL_WEBHOOK = 'https://script.google.com/macros/s/AKfycbzTXLrM_uJd-E-_CCF22Zbv9lSz6xeLN9Dfh0EZ7c48jGlrJQ9rSr_PAraTQzIl-FL6/exec';
  var CHAVE = 'improov_painel_utm';
  var PENDENTE = 'improov_painel_lead';
  var VALIDADE_DIAS = 30;
  var PARAMS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'gclid', 'fbclid'];

  function ler(chave, storage) {
    try { return JSON.parse((storage || localStorage).getItem(chave) || 'null'); } catch (e) { return null; }
  }
  function gravar(chave, valor, storage) {
    try { (storage || localStorage).setItem(chave, JSON.stringify(valor)); } catch (e) {}
  }
  function apagar(chave, storage) {
    try { (storage || localStorage).removeItem(chave); } catch (e) {}
  }

  /* 1. Atribuição: salva UTMs da URL (último clique vale) */
  function capturarUtms() {
    var url = new URLSearchParams(window.location.search);
    var achou = {};
    var tem = false;
    PARAMS.forEach(function (p) {
      var v = url.get(p);
      if (v) { achou[p] = v.slice(0, 300); tem = true; }
    });
    if (achou.gclid && !achou.utm_source) { achou.utm_source = 'google'; achou.utm_medium = 'cpc'; }
    if (achou.fbclid && !achou.utm_source) { achou.utm_source = 'meta'; achou.utm_medium = 'paid_social'; }
    if (tem) {
      achou.salvo_em = Date.now();
      achou.pagina_entrada = window.location.pathname;
      gravar(CHAVE, achou);
    }
  }

  function atribuicaoAtual() {
    var a = ler(CHAVE);
    if (!a) return {};
    if (Date.now() - (a.salvo_em || 0) > VALIDADE_DIAS * 86400000) { apagar(CHAVE); return {}; }
    return a;
  }

  /* 2. Envio para a planilha (não bloqueia a navegação) */
  function enviar(dados) {
    var corpo = JSON.stringify(dados);
    try {
      if (navigator.sendBeacon) {
        var ok = navigator.sendBeacon(PAINEL_WEBHOOK, new Blob([corpo], { type: 'text/plain;charset=UTF-8' }));
        if (ok) return;
      }
    } catch (e) {}
    try {
      fetch(PAINEL_WEBHOOK, {
        method: 'POST', mode: 'no-cors', keepalive: true,
        headers: { 'Content-Type': 'text/plain;charset=UTF-8' }, body: corpo
      });
    } catch (e) {}
  }

  function novoId() {
    return 'site_' + Date.now().toString(36) + '_' + Math.random().toString(36).slice(2, 10);
  }

  /* 3. Formulário de contato: guarda os dados no envio, manda quando der sucesso */
  function valor(form, nome) {
    var el = form.querySelector('[name="' + nome + '"]');
    return el ? String(el.value || '').trim() : '';
  }

  function prepararLead(form) {
    if (valor(form, 'website')) return; // campo isca anti-spam do site
    var a = atribuicaoAtual();
    var lead = {
      tipo: 'lead',
      lead_id: novoId(),
      nome: valor(form, 'nome'),
      telefone: valor(form, 'telefone'),
      email: valor(form, 'email'),
      empresa: valor(form, 'empresa'),
      tipo_empresa: valor(form, 'tipo_empresa'),
      pagina: window.location.pathname,
      enviado: false
    };
    PARAMS.forEach(function (p) { lead[p] = a[p] || ''; });
    if (!lead.utm_source) { lead.utm_source = document.referrer ? 'referencia' : 'direto'; }
    gravar(PENDENTE, lead, sessionStorage);
  }

  function enviarPendente() {
    var lead = ler(PENDENTE, sessionStorage);
    if (!lead || lead.enviado) return;
    lead.enviado = true;
    gravar(PENDENTE, lead, sessionStorage);
    var copia = {};
    Object.keys(lead).forEach(function (k) { if (k !== 'enviado') copia[k] = lead[k]; });
    enviar(copia);
  }

  document.addEventListener('submit', function (ev) {
    var form = ev.target;
    if (!form || form.tagName !== 'FORM') return;
    var action = form.getAttribute('action') || '';
    if (action.indexOf('contacto') === -1 && !form.classList.contains('form-card')) return;
    prepararLead(form);
  }, true);

  // o site dispara este evento quando o envio dá certo (antes de ir para /obrigado)
  document.addEventListener('improov:form-success', enviarPendente);

  /* 4. Cliques no WhatsApp */
  document.addEventListener('click', function (ev) {
    var a = ev.target && ev.target.closest ? ev.target.closest('a[href]') : null;
    if (!a) return;
    var href = a.getAttribute('href') || '';
    if (!/wa\.me|whatsapp\.com|api\.whatsapp/i.test(href)) return;
    var at = atribuicaoAtual();
    enviar({
      tipo: 'evento', evento: 'whatsapp_click',
      utm_source: at.utm_source || '', utm_campaign: at.utm_campaign || '', utm_content: at.utm_content || '',
      pagina: window.location.pathname
    });
  }, true);

  capturarUtms();
  // garantia: se o evento de sucesso não chegou, a página /obrigado completa o envio
  if (/\/obrigado\/?$/.test(window.location.pathname)) enviarPendente();
})();
