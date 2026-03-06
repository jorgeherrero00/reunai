// popup.js - Lógica de la UI del popup

// ──────────────────────────────────────────────
// Estado local del popup
// ──────────────────────────────────────────────
let currentTab = null;
let meetingTitle = '';
let timerInterval = null;
let recordingStartTime = null;

// ──────────────────────────────────────────────
// Init
// ──────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
  bindButtons();
  await init();
  listenForUpdates();
});

async function init() {
  const { apiToken, serverUrl } = await chrome.storage.local.get(['apiToken', 'serverUrl']);

  // Sin configuración → mostrar setup
  if (!apiToken || !serverUrl) {
    showView('setup');
    return;
  }

  // Obtener el tab activo
  const [tab] = await chrome.tabs.query({ active: true, currentWindow: true });
  currentTab = tab;

  const isMeetTab = tab?.url?.includes('meet.google.com/') &&
                    /\/[a-z]{3}-[a-z]{4}-[a-z]{3}/i.test(tab.url);

  if (!isMeetTab) {
    showView('not-meet');
    return;
  }

  // Intentar obtener título de la reunión desde el content script
  try {
    const info = await chrome.tabs.sendMessage(tab.id, { action: 'getMeetingInfo' });
    meetingTitle = info?.title || '';
  } catch {
    meetingTitle = '';
  }

  // Consultar estado de grabación al background
  const status = await chrome.runtime.sendMessage({ action: 'getStatus' });

  if (status.isRecording) {
    recordingStartTime = status.startTime;
    setRecordingUI();
    startTimer();
  } else {
    setReadyUI();
  }
}

// ──────────────────────────────────────────────
// Botones
// ──────────────────────────────────────────────
function bindButtons() {
  document.getElementById('btnSaveSetup').addEventListener('click', saveSetup);
  document.getElementById('btnStartRecording').addEventListener('click', startRecording);
  document.getElementById('btnStopRecording').addEventListener('click', stopRecording);
  document.getElementById('btnRetry').addEventListener('click', () => init());
  document.getElementById('btnSettings').addEventListener('click', () => showView('setup'));
}

async function saveSetup() {
  const serverUrl = document.getElementById('inputUrl').value.trim().replace(/\/$/, '');
  const apiToken  = document.getElementById('inputToken').value.trim();

  if (!serverUrl || !apiToken) {
    flashError('Por favor, completa todos los campos.');
    return;
  }

  if (!/^https?:\/\//i.test(serverUrl)) {
    flashError('La URL debe empezar por https:// o http://');
    return;
  }

  await chrome.storage.local.set({ apiToken, serverUrl });
  await init();
}

async function startRecording() {
  if (!currentTab) return;

  setBtn('btnStartRecording', true, 'Iniciando...');

  const resp = await chrome.runtime.sendMessage({
    action: 'startRecording',
    tabId: currentTab.id,
    title: meetingTitle,
  });

  setBtn('btnStartRecording', false, 'Iniciar grabación');

  if (!resp.success) {
    showError(resp.error || 'No se pudo iniciar la grabación.');
    return;
  }

  recordingStartTime = Date.now();
  setRecordingUI();
  startTimer();
}

async function stopRecording() {
  setBtn('btnStopRecording', true, 'Deteniendo...');

  const resp = await chrome.runtime.sendMessage({ action: 'stopRecording' });

  setBtn('btnStopRecording', false, 'Detener y subir');
  stopTimer();

  if (!resp.success) {
    showError(resp.error || 'Error al detener la grabación.');
    return;
  }

  showView('uploading');
  updateProgress(0);
}

// ──────────────────────────────────────────────
// Escuchar actualizaciones en tiempo real del background
// ──────────────────────────────────────────────
function listenForUpdates() {
  chrome.runtime.onMessage.addListener((message) => {
    switch (message.action) {
      case 'statusUpdate':
        if (message.isRecording) {
          recordingStartTime = message.startTime;
          setRecordingUI();
          startTimer();
        } else {
          stopTimer();
        }
        break;

      case 'uploadProgress':
        updateProgress(message.percent);
        break;

      case 'uploadDone':
        stopTimer();
        if (message.success) {
          // Breve confirmación y volver al estado ready
          showView('ready');
          setReadyUI();
          showTemporaryMessage('Reunión subida correctamente.');
        } else {
          showError(message.error || 'Error al subir la grabación.');
        }
        break;
    }
  });
}

// ──────────────────────────────────────────────
// Timer de grabación
// ──────────────────────────────────────────────
function startTimer() {
  stopTimer();
  timerInterval = setInterval(updateTimer, 1000);
  updateTimer();
}

function stopTimer() {
  if (timerInterval) {
    clearInterval(timerInterval);
    timerInterval = null;
  }
}

function updateTimer() {
  if (!recordingStartTime) return;
  const elapsed = Math.floor((Date.now() - recordingStartTime) / 1000);
  const h = Math.floor(elapsed / 3600).toString().padStart(2, '0');
  const m = Math.floor((elapsed % 3600) / 60).toString().padStart(2, '0');
  const s = (elapsed % 60).toString().padStart(2, '0');
  const el = document.getElementById('timerDisplay');
  if (el) el.textContent = `${h}:${m}:${s}`;
}

// ──────────────────────────────────────────────
// Helpers UI
// ──────────────────────────────────────────────
function showView(id) {
  document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
  const v = document.getElementById(`view-${id}`);
  if (v) v.classList.add('active');
}

function setReadyUI() {
  showView('ready');
  const el = document.getElementById('readyMeetingName');
  if (el) el.textContent = meetingTitle || 'Reunión de Google Meet';
}

function setRecordingUI() {
  showView('recording');
  const el = document.getElementById('recordingMeetingName');
  if (el) el.textContent = meetingTitle || 'Reunión de Google Meet';
}

function updateProgress(percent) {
  const fill = document.getElementById('progressFill');
  const label = document.getElementById('progressPercent');
  if (fill) fill.style.width = `${percent}%`;
  if (label) label.textContent = `${percent}%`;
}

function showError(msg) {
  showView('error');
  const el = document.getElementById('errorMessage');
  if (el) el.textContent = msg;
}

function flashError(msg) {
  // Resaltar campos sin cambiar de vista
  alert(msg); // simplificado; podría ser un toast
}

function setBtn(id, disabled, text) {
  const btn = document.getElementById(id);
  if (!btn) return;
  btn.disabled = disabled;
  const svg = btn.querySelector('svg');
  const svgHtml = svg ? svg.outerHTML : '';
  // Reemplazar texto manteniendo el icono
  btn.innerHTML = `${svgHtml} ${text}`;
  btn.disabled = disabled;
}

function showTemporaryMessage(msg) {
  const el = document.createElement('div');
  el.style.cssText = `
    position: fixed; bottom: 12px; left: 50%; transform: translateX(-50%);
    background: #064e3b; border: 1px solid #065f46; color: #6ee7b7;
    padding: 8px 16px; border-radius: 8px; font-size: 12px; z-index: 100;
    white-space: nowrap; pointer-events: none;
  `;
  el.textContent = msg;
  document.body.appendChild(el);
  setTimeout(() => el.remove(), 2500);
}

// Cargar valores guardados en el formulario de setup
(async () => {
  const { apiToken, serverUrl } = await chrome.storage.local.get(['apiToken', 'serverUrl']);
  if (serverUrl) document.getElementById('inputUrl').value = serverUrl;
  if (apiToken)  document.getElementById('inputToken').value = apiToken;
})();
