// Simple Toast notifications for HausTap web app
// Usage: window.HausTapToast.show({ title, message, type, timeout })
(function(){
  var container = null;

  function ensureContainer(){
    if (container && document.body.contains(container)) return container;
    container = document.createElement('div');
    container.className = 'toast-container';
    container.setAttribute('aria-live', 'polite');
    container.setAttribute('aria-atomic', 'true');
    document.body.appendChild(container);
    return container;
  }

  function removeToast(el){
    if (!el) return;
    try {
      el.classList.add('hide');
      setTimeout(function(){ if (el && el.parentNode) el.parentNode.removeChild(el); }, 250);
    } catch (e) {}
  }

  function render(opts){
    var c = ensureContainer();
    var el = document.createElement('div');
    el.className = 'toast';
    var type = (opts && opts.type) || 'info';
    el.setAttribute('data-type', type);

    var title = (opts && opts.title) || '';
    var message = (opts && opts.message) || (typeof opts === 'string' ? String(opts) : '');
    var timeout = Math.max(1500, Math.min(10000, (opts && opts.timeout) || 3500));

    var inner = document.createElement('div');
    inner.className = 'toast-inner';
    var h = '';
    if (title) { h += '<div class="toast-title">'+escapeHtml(title)+'</div>'; }
    if (message) { h += '<div class="toast-message">'+escapeHtml(message)+'</div>'; }
    inner.innerHTML = h;
    el.appendChild(inner);

    var closeBtn = document.createElement('button');
    closeBtn.className = 'toast-close';
    closeBtn.setAttribute('aria-label','Close');
    closeBtn.innerHTML = '&times;';
    closeBtn.addEventListener('click', function(){ removeToast(el); });
    el.appendChild(closeBtn);

    c.appendChild(el);
    // Auto-remove after timeout
    setTimeout(function(){ removeToast(el); }, timeout);
    return el;
  }

  function escapeHtml(str){
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/\"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  window.HausTapToast = {
    show: function(arg, opts){
      if (typeof arg === 'string') {
        return render({ message: arg, type: (opts && opts.type) || 'info', timeout: (opts && opts.timeout) || 3500 });
      }
      return render(arg || {});
    }
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', ensureContainer);
  } else {
    ensureContainer();
  }
})();

