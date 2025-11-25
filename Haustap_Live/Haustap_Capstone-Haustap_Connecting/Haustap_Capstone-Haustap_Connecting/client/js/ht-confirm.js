// HausTap themed confirm modal helper
// Injects minimal CSS and uses Bootstrap Modal when available
(function(){
  function ensureStyles(){
    if (document.getElementById('htConfirmStyles')) return;
    var css = [
      '.ht-modal .modal-content{border-radius:12px}',
      '.ht-modal .modal-header{border-bottom:1px solid #eee}',
      '.ht-modal .modal-title{font-weight:700;color:#222}',
      '.ht-modal .modal-body{color:#444}',
      '.btn-ht-accent{background:#3dbfc3;color:#fff;border:none;box-shadow:0 2px 8px #3dbfc340}',
      '.btn-ht-accent:hover{background:#2d8a8e;color:#fff}',
      '.btn-ht-outline{border:1px solid #ccc;color:#222}'
    ].join('\n');
    var style = document.createElement('style');
    style.id = 'htConfirmStyles';
    style.type = 'text/css';
    style.appendChild(document.createTextNode(css));
    document.head.appendChild(style);
  }

  function ensureModal(opts){
    var existing = document.getElementById('htConfirmModal');
    if (!existing) {
      var html = ''+
        '<div class="modal fade" id="htConfirmModal" tabindex="-1" aria-hidden="true">'+
        '  <div class="modal-dialog modal-dialog-centered">'+
        '    <div class="modal-content ht-modal">'+
        '      <div class="modal-header">'+
        '        <h5 class="modal-title">'+(opts && opts.title || 'Confirm Action')+'</h5>'+
        '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>'+
        '      </div>'+
        '      <div class="modal-body"></div>'+
        '      <div class="modal-footer">'+
        '        <button type="button" class="btn btn-ht-outline ht-cancel" data-bs-dismiss="modal">'+(opts && opts.cancelText || 'Cancel')+'</button>'+
        '        <button type="button" class="btn btn-ht-accent ht-ok">'+(opts && opts.okText || 'OK')+'</button>'+
        '      </div>'+
        '    </div>'+
        '  </div>'+
        '</div>';
      var wrap = document.createElement('div');
      wrap.innerHTML = html;
      document.body.appendChild(wrap.firstChild);
      existing = document.getElementById('htConfirmModal');
    }
    return existing;
  }

  function htConfirm(message, opts){
    ensureStyles();
    var modalEl = ensureModal(opts||{});
    modalEl.querySelector('.modal-title').textContent = (opts && opts.title) || 'Confirm Action';
    modalEl.querySelector('.modal-body').textContent = message || '';
    var ok = modalEl.querySelector('.ht-ok');
    var cancel = modalEl.querySelector('.ht-cancel');
    if (opts && opts.okText) ok.textContent = opts.okText;
    if (opts && opts.cancelText) cancel.textContent = opts.cancelText;
    var bsModal = window.bootstrap && window.bootstrap.Modal ? new window.bootstrap.Modal(modalEl) : null;
    return new Promise(function(resolve){
      var done = false;
      function cleanup(){ ok.onclick = null; cancel.onclick = null; modalEl.removeEventListener('hidden.bs.modal', onHidden); }
      function onHidden(){ cleanup(); if (!done) resolve(false); }
      modalEl.addEventListener('hidden.bs.modal', onHidden);
      ok.onclick = function(){ done = true; bsModal && bsModal.hide(); cleanup(); resolve(true); };
      cancel.onclick = function(){ done = true; bsModal && bsModal.hide(); cleanup(); resolve(false); };
      if (bsModal) bsModal.show(); else resolve(window.confirm(message||''));
    });
  }

  window.htConfirm = htConfirm;
})();

