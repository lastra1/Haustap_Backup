// Lightweight user mode helper for Admin panel
// Mirrors active mode to backend and persists locally.
(function () {
  var LS_KEY = 'HT_ACTIVE_MODE';
  var EMAIL_KEY = 'HT_USER_EMAIL';

  function apiBase() { return (window.API_BASE || 'http://127.0.0.1:8001/api').replace(/\/+$/, ''); }

  function getEmail() {
    var e = (window.currentUser && window.currentUser.email) || localStorage.getItem(EMAIL_KEY) || '';
    return (typeof e === 'string') ? e.trim() : '';
  }

  function getMode() { return localStorage.getItem(LS_KEY) || 'client'; }

  async function sync() {
    var email = getEmail();
    if (!email) return { mode: getMode(), email: '' };
    try {
      var res = await fetch(apiBase() + '/auth/mode?email=' + encodeURIComponent(email), { method: 'GET' });
      var data = await res.json();
      if (data && data.success && data.activeMode) {
        localStorage.setItem(LS_KEY, data.activeMode);
        return { mode: data.activeMode, email: email };
      }
    } catch (e) {}
    return { mode: getMode(), email: email };
  }

  async function setMode(mode) {
    var next = (mode === 'provider') ? 'provider' : 'client';
    localStorage.setItem(LS_KEY, next);
    var email = getEmail();
    if (!email) return { success: false, reason: 'no_email', mode: next };
    try {
      var res = await fetch(apiBase() + '/auth/mode', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: email, mode: next })
      });
      var data = await res.json();
      return { success: !!(data && data.success), mode: next };
    } catch (e) { return { success: false, mode: next }; }
  }

  window.AdminUserMode = { getMode: getMode, setMode: setMode, sync: sync };
})();