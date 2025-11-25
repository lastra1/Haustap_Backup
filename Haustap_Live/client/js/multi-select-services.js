// Global enhancer: turn single-select service radios into multi-select checkboxes
// and add a Proceed button that aggregates selected items and price.
(function(){
  function normalizeText(s){ return String(s||'').replace(/\s+/g,' ').trim(); }
  function parsePriceText(txt){
    if (!txt) return 0;
    var cleaned = String(txt).replace(/[₱PHPphp]/g,'').replace(/,/g,'');
    var m = cleaned.match(/(\d+(?:\.\d+)?)/);
    return m ? Number(m[1]) : 0;
  }
  function findPrice(card){
    if (!card) return 0;
    var el = card.querySelector('.service-price')
          || card.querySelector('.cleaning-price')
          || card.querySelector('.price')
          || card.querySelector('[class*="price"]');
    return parsePriceText(el ? el.textContent : '');
  }
  function cardTitle(card){
    var h = card && card.querySelector('h3');
    return normalizeText(h ? h.textContent : '');
  }
  function activeSubcategory(){
    var li = document.querySelector('.subcategory-nav li.active');
    var btn = document.querySelector('.subcategory-btn');
    var cat = document.querySelector('.category-title');
    var section = document.querySelector('.section-title');
    return normalizeText((li && li.textContent) || (btn && btn.textContent) || (cat && cat.textContent) || (section && section.textContent) || 'Selected Services');
  }

  function convertRadiosToCheckboxes(){
    var inputs = Array.prototype.slice.call(document.querySelectorAll(
      '.service-card input[type="radio"], .cleaning-package input[type="radio"], label.service-card input[type="radio"], .card input[type="radio"], .garden-card input[type="radio"]'
    ));
    if (!inputs.length) return false;
    inputs.forEach(function(r){
      try {
        // Preserve checked state; convert type
        var wasChecked = !!r.checked;
        r.type = 'checkbox';
        if (wasChecked) r.checked = false; // default to none selected for multi-select
        // Ensure clicking the label toggles the checkbox, not old radio behaviour
      } catch(e) {}
    });
    return true;
  }

  function ensureActions(){
    if (document.querySelector('.ht-multi-actions')) return;
    var container = document.querySelector('.services-container') || document.querySelector('main') || document.body;
    if (!container) return;
    var wrap = document.createElement('div');
    wrap.className = 'ht-multi-actions';
    wrap.innerHTML = '<button class="ht-preview">Preview</button><button class="ht-proceed">Proceed</button><div class="ht-total">Total: ₱0.00</div>';
    container.appendChild(wrap);

    var style = document.createElement('style');
    style.textContent = [
      '.ht-multi-actions{position:fixed;right:18px;bottom:18px;display:flex;gap:10px;align-items:center;z-index:1000}',
      '.ht-multi-actions .ht-proceed{background:#00c4cc;color:#fff;border:none;border-radius:8px;padding:10px 14px;font-weight:600;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.2)}',
      '.ht-multi-actions .ht-preview{background:#fff;color:#00c4cc;border:2px solid #00c4cc;border-radius:8px;padding:10px 14px;font-weight:600;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.1)}',
      '.ht-multi-actions .ht-total{background:#fff;border:2px solid #00c4cc;color:#333;border-radius:8px;padding:8px 12px;font-weight:600;box-shadow:0 2px 8px rgba(0,0,0,0.1)}',
      '.service-card input[type="checkbox"]{width:18px;height:18px;margin:10px;accent-color:#00c4cc;}'
    ].join('\n');
    document.head.appendChild(style);

    var panel = document.createElement('div');
    panel.className = 'ht-preview-panel';
    panel.style.cssText = 'position:fixed;right:18px;bottom:72px;max-width:380px;width:90vw;max-height:50vh;overflow:auto;background:#fff;border:2px solid #00c4cc;border-radius:10px;box-shadow:0 6px 16px rgba(0,0,0,0.15);padding:12px;display:none;z-index:1001';
    panel.innerHTML = '<div class="ht-preview-title" style="font-weight:700;color:#009999;margin-bottom:8px">Selected Items</div><div class="ht-preview-list"></div><div class="ht-preview-total" style="margin-top:10px;font-weight:700">Total: ₱0.00</div>';
    container.appendChild(panel);
  }

  function renderTotal(){
    var totalEl = document.querySelector('.ht-multi-actions .ht-total');
    if (!totalEl) return;
    var checks = Array.prototype.slice.call(document.querySelectorAll('.service-card input[type="checkbox"]:checked, .cleaning-package input[type="checkbox"]:checked, .card input[type="checkbox"]:checked, .garden-card input[type="checkbox"]:checked'));
    var sum = checks.reduce(function(acc, c){ var card = c.closest('.service-card, .cleaning-package, .card, label.service-card, .garden-card'); return acc + findPrice(card); }, 0);
    totalEl.textContent = 'Total: ₱' + Number(sum||0).toFixed(2);
    var pvTotal = document.querySelector('.ht-preview-panel .ht-preview-total');
    if (pvTotal) pvTotal.textContent = 'Total: ₱' + Number(sum||0).toFixed(2);
    try { localStorage.setItem('selected_service_price', String(sum||0)); } catch(e){}
  }

  function renderPreview(){
    var listEl = document.querySelector('.ht-preview-panel .ht-preview-list');
    if (!listEl) return;
    var subcat = activeSubcategory();
    var checks = Array.prototype.slice.call(document.querySelectorAll('.service-card input[type="checkbox"]:checked, .cleaning-package input[type="checkbox"]:checked, .card input[type="checkbox"]:checked, .garden-card input[type="checkbox"]:checked'));
    var items = checks.map(function(c){ var card = c.closest('.service-card, .cleaning-package, .card, label.service-card, .garden-card'); var title = cardTitle(card); var price = findPrice(card); return { label: subcat + ' - ' + title, price: price }; });
    listEl.innerHTML = items.length ? items.map(function(it){ return '<div style="display:flex;justify-content:space-between;gap:8px;margin:4px 0"><span>' + it.label + '</span><b>₱' + Number(it.price||0).toFixed(2) + '</b></div>'; }).join('') : '<i style="color:#777">No items selected</i>';
    try {
      localStorage.setItem('selected_services_names', JSON.stringify(items.map(function(it){ return it.label; })));
      localStorage.setItem('selected_services_prices', JSON.stringify(items.map(function(it){ return Number(it.price||0); })));
      localStorage.setItem('selected_service_name', items.length ? items[0].label : '');
    } catch(e){}
  }

  function persistSelectionAndProceed(){
    var checks = Array.prototype.slice.call(document.querySelectorAll('.service-card input[type="checkbox"]:checked, .cleaning-package input[type="checkbox"]:checked, .card input[type="checkbox"]:checked, .garden-card input[type="checkbox"]:checked'));
    if (!checks.length){ alert('Please select at least one service.'); return; }
    var subcat = activeSubcategory();
    var items = checks.map(function(c){ var card = c.closest('.service-card, .cleaning-package, .card, label.service-card, .garden-card'); return subcat + ' - ' + cardTitle(card); });
    var total = checks.reduce(function(acc, c){ var card = c.closest('.service-card, .cleaning-package, .card, label.service-card, .garden-card'); return acc + findPrice(card); }, 0);
    try {
      localStorage.setItem('selected_service_name', 'Multiple Services');
      localStorage.setItem('selected_services_names', JSON.stringify(items));
      localStorage.setItem('selected_service_price', String(total));
    } catch(e) {}
    var nextUrl = '/booking_process/booking_location.php?service=' + encodeURIComponent('Multiple Services') + '&price=' + encodeURIComponent(String(total));
    window.location.href = nextUrl;
  }

  function init(){
    // Only act on pages that show service cards
    var hasCards = !!document.querySelector('.service-card input[type="radio"], .cleaning-package input[type="radio"], label.service-card input[type="radio"], .card input[type="radio"], .garden-card input[type="radio"]');
    if (!hasCards) return;
    var converted = convertRadiosToCheckboxes();
    if (!converted) return;
    ensureActions();
    renderTotal();
    renderPreview();
    // Update total when users select/deselect
    // Intercept click/change on service checkboxes to suppress legacy single-select handlers
    document.addEventListener('click', function(ev){
      var t = ev.target;
      if (t && t.tagName === 'INPUT' && String(t.type).toLowerCase() === 'checkbox' && t.closest('.service-card, .cleaning-package, .card, label.service-card, .garden-card')) {
        try { ev.stopImmediatePropagation(); } catch(e){}
      }
    }, true);
    document.addEventListener('change', function(ev){
      var t = ev.target;
      if (t && t.tagName === 'INPUT' && String(t.type).toLowerCase() === 'checkbox') {
        try { ev.stopImmediatePropagation(); } catch(e){}
        renderTotal();
        renderPreview();
      }
    }, true);
    var btn = document.querySelector('.ht-proceed');
    if (btn) btn.addEventListener('click', persistSelectionAndProceed);
    var pvBtn = document.querySelector('.ht-preview');
    var panel = document.querySelector('.ht-preview-panel');
    if (pvBtn && panel) pvBtn.addEventListener('click', function(){ panel.style.display = panel.style.display === 'none' ? 'block' : 'none'; });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
