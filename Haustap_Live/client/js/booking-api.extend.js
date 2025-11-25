// Extension to HausTapBookingAPI: add clearCancelled bulk action
(function(){
  function attach(){
    var API_BASE = (window.API_BASE || '').replace(/\/+$/, '') || '';
    // Simple booking draft storage to support guest -> login -> resume
    var DRAFT_KEY = 'ht_booking_draft';
    function saveDraft(payload){
      try { localStorage.setItem(DRAFT_KEY, JSON.stringify(payload || {})); } catch(e) {}
    }
    function loadDraft(){
      try {
        var raw = localStorage.getItem(DRAFT_KEY);
        return raw ? JSON.parse(raw) : null;
      } catch(e) { return null; }
    }
    function clearDraft(){ try { localStorage.removeItem(DRAFT_KEY); } catch(e) {} }
    async function request(path, opts) {
      const url = `${API_BASE}${path}`;
      const headers = Object.assign({
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      }, opts && opts.headers);
      const res = await fetch(url, Object.assign({ method: 'POST', headers }, opts));
      const ct = res.headers.get('content-type') || '';
      let data = null;
      if (ct.includes('application/json')) {
        data = await res.json();
      } else {
        const text = await res.text();
        try { data = JSON.parse(text); } catch { data = { message: text }; }
      }
      if (!res.ok) {
        const message = (data && (data.message || data.error)) || `HTTP ${res.status}`;
        const err = new Error(message);
        err.response = res;
        err.data = data;
        throw err;
      }
      return data;
    }
    async function clearCancelled(){
      return request('/clear-cancelled', { method: 'POST', body: JSON.stringify({}) });
    }
    // Create booking with draft support: if not logged in (401), persist draft and bubble error
    async function createBookingWithDraft(payload){
      const token = (localStorage.getItem('ht_token') || '').trim();
      const headers = token ? { Authorization: 'Bearer ' + token } : {};
      try {
        const res = await request('/bookings/', { method: 'POST', headers, body: JSON.stringify(payload || {}) });
        clearDraft();
        return res;
      } catch (err) {
        if (err && err.response && err.response.status === 401) {
          saveDraft(payload);
        }
        throw err;
      }
    }

    // After successful login, auto-resume any saved draft
    async function resumeDraftIfAny(){
      const draft = loadDraft();
      if (!draft) return null;
      const token = (localStorage.getItem('ht_token') || '').trim();
      if (!token) return null;
      try {
        const res = await request('/bookings/', { method: 'POST', headers: { Authorization: 'Bearer ' + token }, body: JSON.stringify(draft) });
        clearDraft();
        return res;
      } catch (e) {
        // keep draft on any non-auth errors for user to fix
        return null;
      }
    }
    if (window.HausTapBookingAPI) {
      window.HausTapBookingAPI.clearCancelled = clearCancelled;
      window.HausTapBookingAPI.saveDraftBooking = saveDraft;
      window.HausTapBookingAPI.createBookingWithDraft = createBookingWithDraft;
      window.HausTapBookingAPI.resumeDraftIfAny = resumeDraftIfAny;
    }
  }

  if (window.HausTapBookingAPI) {
    attach();
  } else {
    var tries = 0;
    var t = setInterval(function(){
      tries++;
      if (window.HausTapBookingAPI) {
        clearInterval(t);
        attach();
      }
      if (tries > 50) { clearInterval(t); }
    }, 100);
  }
})();
