// Minimal API base configuration for web pages
(function () {
  if (!window.API_BASE) {
    // Allow overriding via globals for easy env switching
    var rawOverride = window.API_BASE_OVERRIDE;
    var override = typeof rawOverride === 'string' ? rawOverride.trim() : '';
    if (override) {
      window.API_BASE = override;
      return;
    }

    // Target can be 'backend' or 'mock' (default: backend)
    var target = ((window.API_TARGET || 'backend') + '').toLowerCase();
    if (target === 'backend') {
      var backend = window.BACKEND_BASE;
      if (!backend || typeof backend !== 'string' || backend.trim() === '') {
        try {
          var host = (window.location && window.location.hostname) || '';
          if (host && host !== 'localhost' && host !== '127.0.0.1') {
            backend = 'http://' + host + ':8000/api';
          } else {
            backend = 'http://127.0.0.1:8000/api';
          }
        } catch (e) {
          backend = 'http://127.0.0.1:8000/api';
        }
      }
      window.API_BASE = String(backend).replace(/\/+$/, '');
      // Provide Firebase-specific base for booking UI
      window.FIREBASE_API_BASE = window.API_BASE + '/firebase';
      return;
    }

    // Default to mock API served by current origin
    var origin = (window.location && window.location.origin) || '';
    var base = origin ? origin + '/mock-api' : '/mock-api';
    window.API_BASE = base;
  }
})();
