// HausTap cancel reason modal helper
// Presents a Bootstrap modal with radio options and resolves to the selected reason
(function(){
  var DEFAULT_REASONS = [
    'Change of Schedule',
    'Service No Longer Needed',
    'Incorrect Booking Details',
    'Price Concerns',
    'Payment Issues',
    'Health/Safety Concerns',
    'Emergency/Personal Reasons'
  ];

  function ensureStyles(){
    if (document.getElementById('htCancelReasonStyles')) return;
    var css = [
      '.ht-cancel-modal .modal-content{border-radius:12px}',
      '.ht-cancel-modal .modal-title{font-weight:700;color:#222}',
      '.ht-cancel-modal .modal-body{color:#444}',
      '.ht-cancel-modal .reason-list label{display:flex;align-items:center;gap:8px;padding:6px 0}',
      '.ht-cancel-modal .note{font-size:13px;color:#666;margin-bottom:10px}',
      '.btn-ht-accent{background:#3dbfc3;color:#fff;border:none;box-shadow:0 2px 8px #3dbfc340}',
      '.btn-ht-accent:hover{background:#2d8a8e;color:#fff}',
      '.btn-ht-outline{border:1px solid #ccc;color:#222}'
    ].join('\n');
    var style = document.createElement('style');
    style.id = 'htCancelReasonStyles';
    style.type = 'text/css';
    style.appendChild(document.createTextNode(css));
    document.head.appendChild(style);
  }

  function ensureModal(opts){
    var existing = document.getElementById('htCancelReasonModal');
    if (!existing) {
      var html = ''+
        '<div class="modal fade" id="htCancelReasonModal" tabindex="-1" aria-hidden="true">'+
        '  <div class="modal-dialog modal-dialog-centered">'+
        '    <div class="modal-content ht-cancel-modal">'+
        '      <div class="modal-header">'+
        '        <h5 class="modal-title">Select Cancellation Reason</h5>'+
        '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>'+
        '      </div>'+
        '      <div class="modal-body">'+
        '        <p class="note">Once cancelled, the service provider will be notified and this action cannot be undone.</p>'+
        '        <div class="reason-list"></div>'+
        '      </div>'+
        '      <div class="modal-footer">'+
        '        <button type="button" class="btn btn-ht-outline ht-cancel" data-bs-dismiss="modal">No, Keep Booking</button>'+
        '        <button type="button" class="btn btn-ht-accent ht-ok" disabled>Cancel Booking</button>'+
        '      </div>'+
        '    </div>'+
        '  </div>'+
        '</div>';
      var wrap = document.createElement('div');
      wrap.innerHTML = html;
      document.body.appendChild(wrap.firstChild);
      existing = document.getElementById('htCancelReasonModal');
    }
    return existing;
  }

  function renderReasons(container, reasons){
    var list = container.querySelector('.reason-list');
    list.innerHTML = '';
    (reasons || DEFAULT_REASONS).forEach(function(label, i){
      var id = 'cancel-reason-'+i;
      var wrap = document.createElement('label');
      var input = document.createElement('input'); input.type = 'radio'; input.name = 'ht-cancel-reason'; input.value = label; input.id = id;
      var span = document.createElement('span'); span.textContent = label;
      wrap.appendChild(input); wrap.appendChild(span);
      list.appendChild(wrap);
    });
    // Select first option by default to mirror design mock
    var first = list.querySelector('input[name="ht-cancel-reason"]');
    if (first) { first.checked = true; }
  }

  function htCancelReason(opts){
    ensureStyles();
    var modalEl = ensureModal(opts||{});
    modalEl.querySelector('.modal-title').textContent = (opts && opts.title) || 'Select Cancellation Reason';
    renderReasons(modalEl, (opts && opts.reasons) || DEFAULT_REASONS);
    var ok = modalEl.querySelector('.ht-ok');
    var cancel = modalEl.querySelector('.ht-cancel');
    var bsModal = window.bootstrap && window.bootstrap.Modal ? new window.bootstrap.Modal(modalEl) : null;
    return new Promise(function(resolve){
      var done = false;
      function cleanup(){ ok.onclick = null; cancel.onclick = null; modalEl.removeEventListener('hidden.bs.modal', onHidden); inputs.forEach(function(r){ r.onchange = null; }); }
      function onHidden(){ cleanup(); if (!done) resolve(null); }
      modalEl.addEventListener('hidden.bs.modal', onHidden);
      var inputs = Array.prototype.slice.call(modalEl.querySelectorAll('input[name="ht-cancel-reason"]'));
      inputs.forEach(function(radio){
        radio.onchange = function(){ ok.disabled = !radio.checked; };
      });
      // Enable OK if default selection exists
      if (inputs.length && inputs[0].checked) { ok.disabled = false; }
      ok.onclick = function(){
        var sel = (modalEl.querySelector('input[name="ht-cancel-reason"]:checked')||{}).value || null;
        if (!sel) { ok.disabled = true; return; }
        done = true; bsModal && bsModal.hide(); cleanup(); resolve(sel);
      };
      cancel.onclick = function(){ done = true; bsModal && bsModal.hide(); cleanup(); resolve(null); };
      if (bsModal) bsModal.show(); else {
        // Fallback prompt: list reasons and accept text
        var fallback = prompt('Enter a cancellation reason:', 'Change of Schedule');
        resolve(fallback || null);
      }
    });
  }

  window.htCancelReason = htCancelReason;
})();
