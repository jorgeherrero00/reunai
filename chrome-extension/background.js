// background.js - Service Worker de Meetlyze Recorder

const OFFSCREEN_DOCUMENT_PATH = '/offscreen.html';

// Estado global (persiste mientras el service worker viva)
let isRecording = false;
let recordingStartTime = null;
let recordingTabId = null;

// ──────────────────────────────────────────────
// Gestión del documento offscreen
// ──────────────────────────────────────────────

async function hasOffscreenDocument() {
  const url = chrome.runtime.getURL(OFFSCREEN_DOCUMENT_PATH);
  const contexts = await chrome.runtime.getContexts({
    contextTypes: ['OFFSCREEN_DOCUMENT'],
    documentUrls: [url],
  });
  return contexts.length > 0;
}

async function setupOffscreenDocument() {
  if (await hasOffscreenDocument()) return;
  await chrome.offscreen.createDocument({
    url: OFFSCREEN_DOCUMENT_PATH,
    reasons: [chrome.offscreen.Reason.USER_MEDIA],
    justification: 'Grabación de audio de la reunión de Google Meet',
  });
}

async function closeOffscreenDocument() {
  if (!(await hasOffscreenDocument())) return;
  await chrome.offscreen.closeDocument();
}

// ──────────────────────────────────────────────
// Listener principal de mensajes
// ──────────────────────────────────────────────

chrome.runtime.onMessage.addListener((message, _sender, sendResponse) => {
  switch (message.action) {

    case 'startRecording':
      handleStartRecording(message.tabId, message.title)
        .then(sendResponse)
        .catch(err => sendResponse({ success: false, error: err.message }));
      return true; // async

    case 'stopRecording':
      handleStopRecording()
        .then(sendResponse)
        .catch(err => sendResponse({ success: false, error: err.message }));
      return true;

    case 'getStatus':
      sendResponse({ isRecording, startTime: recordingStartTime, tabId: recordingTabId });
      return false;

    // Eventos enviados desde offscreen.js
    case 'recordingStarted':
      isRecording = true;
      recordingStartTime = Date.now();
      broadcastStatus();
      sendResponse({ ok: true });
      return false;

    case 'recordingStopped':
      isRecording = false;
      recordingStartTime = null;
      broadcastStatus();
      sendResponse({ ok: true });
      return false;

    case 'uploadComplete':
      handleUploadComplete(message.success, message.error);
      sendResponse({ ok: true });
      return false;

    case 'uploadProgress':
      // Reenviar al popup si está abierto
      chrome.runtime.sendMessage({ action: 'uploadProgress', percent: message.percent }).catch(() => {});
      return false;

    // El content script detectó que el usuario abandonó la reunión
    case 'meetingLeft':
      if (isRecording) {
        handleStopRecording().catch(() => {});
      }
      return false;
  }
});

// ──────────────────────────────────────────────
// Lógica de grabación
// ──────────────────────────────────────────────

async function handleStartRecording(tabId, title) {
  if (isRecording) {
    return { success: false, error: 'Ya hay una grabación activa.' };
  }

  // Obtener streamId para captura del tab
  const streamId = await new Promise((resolve, reject) => {
    chrome.tabCapture.getMediaStreamId({ targetTabId: tabId }, (id) => {
      if (chrome.runtime.lastError) {
        reject(new Error(chrome.runtime.lastError.message));
      } else {
        resolve(id);
      }
    });
  });

  await setupOffscreenDocument();

  // Leer configuración
  const { apiToken, serverUrl } = await chrome.storage.local.get(['apiToken', 'serverUrl']);

  if (!apiToken) {
    await closeOffscreenDocument();
    return { success: false, error: 'Token API no configurado. Ve a ajustes de la extensión.' };
  }

  // Pequeña espera para que el offscreen termine de cargar
  await new Promise(r => setTimeout(r, 400));

  // Enviar comando al offscreen
  await chrome.runtime.sendMessage({
    action: 'startOffscreenRecording',
    streamId,
    title: title || `Reunión ${new Date().toLocaleDateString('es-ES')}`,
    apiToken,
    serverUrl: serverUrl || '',
  });

  recordingTabId = tabId;
  return { success: true };
}

async function handleStopRecording() {
  if (!isRecording) {
    return { success: false, error: 'No hay grabación activa.' };
  }
  await chrome.runtime.sendMessage({ action: 'stopOffscreenRecording' });
  recordingTabId = null;
  return { success: true };
}

async function handleUploadComplete(success, error) {
  await closeOffscreenDocument();

  isRecording = false;
  recordingStartTime = null;
  recordingTabId = null;
  broadcastStatus();

  if (success) {
    showNotification(
      'Meetlyze - Reunión enviada',
      'La grabación se está procesando. En unos minutos tendrás el resumen disponible.'
    );
  } else {
    showNotification(
      'Meetlyze - Error al subir',
      error || 'No se pudo enviar la grabación. Comprueba tu conexión y el token API.'
    );
  }
}

// ──────────────────────────────────────────────
// Helpers
// ──────────────────────────────────────────────

function broadcastStatus() {
  chrome.runtime.sendMessage({
    action: 'statusUpdate',
    isRecording,
    startTime: recordingStartTime,
  }).catch(() => {}); // El popup puede no estar abierto
}

function showNotification(title, message) {
  chrome.notifications.create({
    type: 'basic',
    iconUrl: chrome.runtime.getURL('icons/icon48.png'),
    title,
    message,
  });
}

// Auto-stop si el tab de Meet se cierra mientras graba
chrome.tabs.onRemoved.addListener((tabId) => {
  if (tabId === recordingTabId && isRecording) {
    handleStopRecording().catch(() => {});
  }
});
