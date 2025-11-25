// User mode helper: toggles between 'client' and 'provider' without changing UI
(function () {
  var KEY = 'HT_USER_MODE';
  var DEFAULT_MODE = 'client';

  function getMode() {
    try {
      var m = (localStorage.getItem(KEY) || '').trim().toLowerCase();
      return m === 'provider' ? 'provider' : DEFAULT_MODE;
    } catch (e) {
      return DEFAULT_MODE;
    }
  }

  function setMode(mode) {
    var m = (mode || '').trim().toLowerCase();
    try {
      localStorage.setItem(KEY, m === 'provider' ? 'provider' : DEFAULT_MODE);
    } catch (e) {}
    window.USER_MODE = getMode();
    try {
      var ev = new CustomEvent('haustap:mode_changed', { detail: { mode: window.USER_MODE } });
      window.dispatchEvent(ev);
    } catch (e) {}

    // Optional: mirror to backend if we know the email
    try {
      var email = (window.currentUser && window.currentUser.email) || localStorage.getItem('HT_USER_EMAIL') || '';
      var base = (window.API_BASE || '').replace(/\/+$/, '');
      if (email && base) {
        fetch(base + '/auth/mode', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email: email, mode: window.USER_MODE }),
        }).catch(function () {});
      }
    } catch (e) {}
  }

  function syncFromBackend() {
    try {
      var email = (window.currentUser && window.currentUser.email) || localStorage.getItem('HT_USER_EMAIL') || '';
      var base = (window.API_BASE || '').replace(/\/+$/, '');
      if (!email || !base) return;
      fetch(base + '/auth/mode?email=' + encodeURIComponent(email))
        .then(function (r) { return r.ok ? r.json() : null; })
        .then(function (j) {
          if (j && j.success && j.mode) {
            setMode(j.mode);
          }
        }).catch(function () {});
    } catch (e) {}
  }

  // Public API
  window.UserMode = {
    get: getMode,
    set: setMode,
    isClient: function () { return getMode() === 'client'; },
    isProvider: function () { return getMode() === 'provider'; },
    sync: syncFromBackend,
  };

  // Init
  window.USER_MODE = getMode();

  // Non-invasive: attach if a toggle element exists
  var btn = document.getElementById('haustap-partner-toggle');
  if (btn) {
    btn.addEventListener('click', function () {
      var next = UserMode.isProvider() ? 'client' : 'provider';
      UserMode.set(next);
    });
  }
})();