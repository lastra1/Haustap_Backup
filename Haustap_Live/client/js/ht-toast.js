// Haustap-themed toast notifications
(function(){
  var container = null;

  function ensureContainer(){
    if (container && document.body.contains(container)) return container;
    container = document.createElement('div');
    container.className = 'ht-toast-container';
    container.setAttribute('aria-live', 'polite');
    container.setAttribute('aria-atomic', 'true');
    document.body.appendChild(container);
    return container;
  }

  function iconFor(type){
    switch(type){
      case 'success': return '\u2713'; // check
      case 'error': return '\u26A0';   // warning triangle
      case 'warning': return '\u26A0';
      default: return 'i';
    }
  }

  function showToast(opts){
    opts = opts || {};
    var type = opts.type || 'info';
    var title = opts.title || (type === 'success' ? 'Success' : type === 'error' ? 'Error' : type === 'warning' ? 'Warning' : 'Notice');
    var body = opts.body || '';
    var timeout = typeof opts.timeout === 'number' ? opts.timeout : 2600;

    var wrap = document.createElement('div');
    wrap.className = 'ht-toast ht-toast--' + type;

    var icon = document.createElement('div');
    icon.className = 'ht-toast__icon';
    icon.textContent = iconFor(type);

    var content = document.createElement('div');
    content.className = 'ht-toast__content';
    var h = document.createElement('p');
    h.className = 'ht-toast__title';
    h.textContent = title;
    var p = document.createElement('p');
    p.className = 'ht-toast__body';
    p.textContent = body;
    content.appendChild(h);
    content.appendChild(p);

    var close = document.createElement('button');
    close.className = 'ht-toast__close';
    close.setAttribute('aria-label', 'Close');
    close.innerHTML = '&times;';
    close.addEventListener('click', function(){
      hideToast(wrap);
    });

    wrap.appendChild(icon);
    wrap.appendChild(content);
    wrap.appendChild(close);
    ensureContainer().appendChild(wrap);

    // enter animation
    requestAnimationFrame(function(){ wrap.classList.add('show'); });

    // auto-dismiss
    if (timeout && timeout > 0) {
      setTimeout(function(){ hideToast(wrap); }, timeout);
    }
    return wrap;
  }

  function hideToast(el){
    if (!el) return;
    el.classList.remove('show');
    setTimeout(function(){
      if (el.parentNode) el.parentNode.removeChild(el);
    }, 160);
  }

  // Public API
  window.htToast = {
    show: function(message, opts){ return showToast(Object.assign({ body: message }, opts)); },
    success: function(message, opts){ return showToast(Object.assign({ body: message, type: 'success' }, opts)); },
    info: function(message, opts){ return showToast(Object.assign({ body: message, type: 'info' }, opts)); },
    warning: function(message, opts){ return showToast(Object.assign({ body: message, type: 'warning' }, opts)); },
    error: function(message, opts){ return showToast(Object.assign({ body: message, type: 'error' }, opts)); }
  };

  // Optional: gently override window.alert on pages that include this file
  if (!window.__HT_TOAST_ALERT_PATCHED__) {
    try {
      var nativeAlert = window.alert;
      window.alert = function(msg){
        try { window.htToast.info(String(msg || '')); }
        catch(e){ nativeAlert(msg); }
      };
      window.__HT_TOAST_ALERT_PATCHED__ = true;
    } catch (e) {}
  }
})();

