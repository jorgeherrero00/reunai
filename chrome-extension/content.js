// content.js - Se inyecta en páginas de meet.google.com
// Detecta el título de la reunión y notifica cuando el usuario abandona

// ──────────────────────────────────────────────
// Responder a consultas del popup / background
// ──────────────────────────────────────────────

chrome.runtime.onMessage.addListener((message, _sender, sendResponse) => {
  if (message.action === 'getMeetingInfo') {
    sendResponse({
      title: getMeetingTitle(),
      inMeeting: isInMeeting(),
      url: location.href,
    });
    return false;
  }
});

// ──────────────────────────────────────────────
// Título de la reunión
// ──────────────────────────────────────────────

function getMeetingTitle() {
  // Google Meet muestra el nombre de la reunión en el <title> con formato:
  // "Nombre de la reunión - Google Meet" o simplemente el código
  const raw = document.title || '';
  const withoutSuffix = raw.replace(/\s*[-–]\s*Google Meet\s*$/i, '').trim();
  if (withoutSuffix && withoutSuffix.toLowerCase() !== 'google meet') {
    return withoutSuffix;
  }

  // Intentar leer el nombre del DOM (elemento visible en la reunión)
  const selectors = [
    '[data-meeting-title]',
    '[jsname="r4nke"]',      // Panel superior de Meet (puede cambiar)
    'div[data-call-name]',
  ];
  for (const sel of selectors) {
    const el = document.querySelector(sel);
    if (el?.textContent?.trim()) {
      return el.textContent.trim();
    }
  }

  // Fallback: extraer el código de la URL
  const match = location.pathname.match(/\/([a-z]{3}-[a-z]{4}-[a-z]{3})/i);
  return match ? match[1].toUpperCase() : 'Reunión de Google Meet';
}

// ──────────────────────────────────────────────
// Detectar si estamos dentro de una reunión activa
// ──────────────────────────────────────────────

function isInMeeting() {
  return /\/[a-z]{3}-[a-z]{4}-[a-z]{3}/i.test(location.pathname);
}

// ──────────────────────────────────────────────
// Detectar cuando el usuario abandona la reunión
// (Google Meet es una SPA; la URL cambia sin recargar)
// ──────────────────────────────────────────────

let lastPath = location.pathname;

const urlObserver = new MutationObserver(() => {
  if (location.pathname !== lastPath) {
    const wasInMeeting = /\/[a-z]{3}-[a-z]{4}-[a-z]{3}/i.test(lastPath);
    const nowInMeeting = isInMeeting();
    lastPath = location.pathname;

    if (wasInMeeting && !nowInMeeting) {
      // El usuario salió de la reunión → notificar para auto-detener grabación
      chrome.runtime.sendMessage({ action: 'meetingLeft' }).catch(() => {});
    }
  }
});

urlObserver.observe(document.body, { childList: true, subtree: true });

// También escuchar si el usuario hace clic en "Abandonar la llamada"
document.addEventListener('click', (e) => {
  const target = e.target?.closest('button');
  if (!target) return;

  // Google Meet usa aria-label en los botones de la llamada
  const label = (target.getAttribute('aria-label') || '').toLowerCase();
  if (label.includes('abandonar') || label.includes('leave') || label.includes('salir')) {
    chrome.runtime.sendMessage({ action: 'meetingLeft' }).catch(() => {});
  }
}, { capture: true });
