// Minimal API base configuration for web pages
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
      var origin = (window.location && window.location.origin) || '';
      var backend = origin ? origin + '/api' : '/api';
      window.API_BASE = String(backend).replace(/\/+$/, '');
      window.FIREBASE_API_BASE = window.API_BASE + '/firebase';
      return;
    }

    // Default to mock API served by current origin
    var origin = (window.location && window.location.origin) || '';
    var base = origin ? origin + '/mock-api' : '/mock-api';
    window.API_BASE = base;
  }
})();
