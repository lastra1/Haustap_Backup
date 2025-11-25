// Lightweight Chat API helper
(function(){
  const API_BASE = (window.API_BASE || '').replace(/\/+$/, '') || '';

  async function request(path, opts){
    const url = `${API_BASE}${path}`;
    const headers = Object.assign({
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    }, opts && opts.headers);
    const res = await fetch(url, Object.assign({ method: 'GET', headers }, opts));
    const ct = res.headers.get('content-type') || '';
    let data = null;
    if (ct.includes('application/json')) { data = await res.json(); }
    else { const t = await res.text(); try { data = JSON.parse(t); } catch { data = { message: t }; } }
    if (!res.ok) {
      const message = (data && (data.message || data.error)) || `HTTP ${res.status}`;
      const err = new Error(message); err.response = res; err.data = data; throw err;
    }
    return data;
  }

  async function openConversation(booking_id, meta){
    return request(`/chat/open`, { method: 'POST', body: JSON.stringify(Object.assign({ booking_id }, meta||{})) });
  }
  async function listMessages(booking_id, since){
    const qs = since ? `?since=${encodeURIComponent(since)}` : '';
    return request(`/chat/${booking_id}/messages${qs}`, { method: 'GET' });
  }
  async function sendMessage(booking_id, payload){
    return request(`/chat/${booking_id}/messages`, { method: 'POST', body: JSON.stringify(payload) });
  }

  window.HausTapChatAPI = { openConversation, listMessages, sendMessage };
})();
