// offscreen.js - Grabación de audio del tab y subida a la API
// Se ejecuta en un documento offscreen (sin UI visible)

let mediaRecorder = null;
let audioChunks = [];
let currentStream = null;
let uploadConfig = {};

// ──────────────────────────────────────────────
// Listener de mensajes desde background.js
// ──────────────────────────────────────────────

chrome.runtime.onMessage.addListener((message, _sender, sendResponse) => {
  switch (message.action) {
    case 'startOffscreenRecording':
      startRecording(message)
        .then(() => sendResponse({ ok: true }))
        .catch(err => {
          console.error('[Meetlyze offscreen] Error iniciando grabación:', err);
          sendResponse({ ok: false, error: err.message });
        });
      return true;

    case 'stopOffscreenRecording':
      stopRecording();
      sendResponse({ ok: true });
      return false;
  }
});

// ──────────────────────────────────────────────
// Grabación
// ──────────────────────────────────────────────

async function startRecording({ streamId, title, apiToken, serverUrl }) {
  // Guardar configuración para usar al subir
  uploadConfig = { title, apiToken, serverUrl };

  // Obtener el stream de audio del tab mediante su ID
  currentStream = await navigator.mediaDevices.getUserMedia({
    audio: {
      mandatory: {
        chromeMediaSource: 'tab',
        chromeMediaSourceId: streamId,
      },
    },
    video: false,
  });

  audioChunks = [];

  const mimeType = getBestMimeType();
  mediaRecorder = new MediaRecorder(currentStream, { mimeType });

  mediaRecorder.ondataavailable = (e) => {
    if (e.data && e.data.size > 0) {
      audioChunks.push(e.data);
    }
  };

  mediaRecorder.onstop = async () => {
    // Detener tracks del stream
    currentStream?.getTracks().forEach(t => t.stop());
    currentStream = null;

    await chrome.runtime.sendMessage({ action: 'recordingStopped' });

    // Unir todos los chunks en un solo Blob y subir
    const blob = new Blob(audioChunks, { type: mimeType });
    audioChunks = [];

    await uploadRecording(blob, uploadConfig);
  };

  // Recoger chunks cada 10 segundos para no perder todo si algo falla
  mediaRecorder.start(10_000);

  await chrome.runtime.sendMessage({ action: 'recordingStarted' });
}

function stopRecording() {
  if (mediaRecorder && mediaRecorder.state !== 'inactive') {
    mediaRecorder.stop();
  }
}

// ──────────────────────────────────────────────
// Subida a la API
// ──────────────────────────────────────────────

async function uploadRecording(blob, { title, apiToken, serverUrl }) {
  if (!apiToken || !serverUrl) {
    await chrome.runtime.sendMessage({
      action: 'uploadComplete',
      success: false,
      error: 'Configuración incompleta: falta token API o URL del servidor.',
    });
    return;
  }

  const formData = new FormData();
  // El servidor acepta el campo 'audio' con extensión webm
  formData.append('audio', blob, 'reunion.webm');
  formData.append('titulo', title);

  try {
    // Subida con seguimiento de progreso mediante XHR
    const result = await uploadWithProgress(
      `${serverUrl}/api/upload`,
      apiToken,
      formData
    );

    await chrome.runtime.sendMessage({
      action: 'uploadComplete',
      success: result.success,
      error: result.error || null,
    });
  } catch (err) {
    await chrome.runtime.sendMessage({
      action: 'uploadComplete',
      success: false,
      error: err.message,
    });
  }
}

function uploadWithProgress(url, apiToken, formData) {
  return new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();

    xhr.open('POST', url);
    xhr.setRequestHeader('Authorization', `Bearer ${apiToken}`);
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.timeout = 600_000; // 10 minutos máximo de subida

    xhr.upload.onprogress = (e) => {
      if (e.lengthComputable) {
        const percent = Math.round((e.loaded / e.total) * 100);
        chrome.runtime.sendMessage({ action: 'uploadProgress', percent }).catch(() => {});
      }
    };

    xhr.onload = () => {
      try {
        const data = JSON.parse(xhr.responseText);
        if (xhr.status >= 200 && xhr.status < 300) {
          resolve({ success: data.success ?? true, error: data.error });
        } else {
          resolve({ success: false, error: data.error || `Error HTTP ${xhr.status}` });
        }
      } catch {
        resolve({ success: false, error: `Respuesta inesperada del servidor (${xhr.status})` });
      }
    };

    xhr.onerror = () => reject(new Error('Error de red al subir la grabación.'));
    xhr.ontimeout = () => reject(new Error('Tiempo de espera agotado al subir la grabación.'));

    xhr.send(formData);
  });
}

// ──────────────────────────────────────────────
// Helper: mejor formato soportado
// ──────────────────────────────────────────────

function getBestMimeType() {
  const candidates = [
    'audio/webm;codecs=opus',
    'audio/webm',
    'audio/ogg;codecs=opus',
    'audio/mp4',
  ];
  return candidates.find(t => MediaRecorder.isTypeSupported(t)) || '';
}
