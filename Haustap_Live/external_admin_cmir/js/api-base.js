// Minimal API base configuration for Admin pages (docroot-local)
(function () {
  var rawOverride = window.API_BASE_OVERRIDE;
  var override = typeof rawOverride === 'string' ? rawOverride.trim() : '';
  if (override) { window.API_BASE = override.replace(/\/+$/, ''); return; }
  var base = (window.BACKEND_BASE || 'http://127.0.0.1:8001/api').replace(/\/+$/, '');
  window.API_BASE = base;
})();