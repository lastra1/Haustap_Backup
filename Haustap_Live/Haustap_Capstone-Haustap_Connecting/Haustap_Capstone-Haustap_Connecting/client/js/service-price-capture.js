// Capture selected service price across service pages and persist to localStorage
(function(){
  function parsePriceText(txt){
    if (!txt) return null;
    var cleaned = String(txt).replace(/[\s₱PHPphp]/g,'').replace(/,/g,'');
    var m = cleaned.match(/(\d+(?:\.\d+)?)/);
    return m ? Number(m[1]) : null;
  }

  function findPriceFromCard(card){
    if (!card) return null;
    // Prefer explicit price classes, but fall back to any class containing "price"
    var el = card.querySelector('.service-price') || card.querySelector('.cleaning-price') || card.querySelector('[class*="price"]');
    return parsePriceText(el ? el.textContent : '');
  }

  function labelFromCard(card){
    if (!card) return '';
    var h3 = card.querySelector('.service-title') || card.querySelector('.cleaning-title') || card.querySelector('.house-title') || card.querySelector('.package-title') || card.querySelector('.service-content h3') || card.querySelector('[class*="title"]');
    return h3 ? String(h3.textContent||'').trim() : '';
  }

  function savePrice(price){
    if (price == null || isNaN(price)) return;
    try { localStorage.setItem('selected_service_price', String(price)); } catch(e){}
  }

  function persistMulti(){
    var checked = Array.prototype.slice.call(document.querySelectorAll('.service-card input[type="checkbox"]:checked, .cleaning-card input[type="checkbox"]:checked, .house-card input[type="checkbox"]:checked, .cleaning-package input[type="checkbox"]:checked, label.service-card input[type="checkbox"]:checked'));
    var names = [];
    var prices = [];
    checked.forEach(function(inp){
      var card = inp.closest('.service-card, .cleaning-card, .house-card, .cleaning-package, label.service-card, .card');
      var nm = labelFromCard(card);
      var pr = findPriceFromCard(card);
      if (nm) names.push(nm);
      if (pr!=null && !isNaN(pr)) prices.push(pr);
    });
    var sum = prices.reduce(function(a,b){ return a + (Number(b)||0); }, 0);
    try {
      localStorage.setItem('selected_services_names', JSON.stringify(names));
      localStorage.setItem('selected_services_prices', JSON.stringify(prices));
      localStorage.setItem('selected_service_name', names[0]||'');
      localStorage.setItem('selected_service_price', String(sum));
    } catch(e){}
  }

  function onChange(ev){
    var input = ev.target;
    if (!input || input.tagName !== 'INPUT') return;
    var t = String(input.type).toLowerCase();
    if (t === 'radio') {
      var card = input.closest('.service-card, .cleaning-card, .cleaning-package, label.service-card, .card');
      var price = findPriceFromCard(card);
      if (price == null) {
        var sib = card || input.parentElement;
        if (sib) {
          var like = sib.querySelector('[class*="price"]');
          price = parsePriceText(like ? like.textContent : '');
        }
      }
      savePrice(price);
    }
    if (t === 'checkbox') {
      persistMulti();
    }
  }

  function init(){
    document.addEventListener('change', onChange, true);
    // Initialize from any pre-checked selection across supported containers
    var checked = document.querySelector(
      '.service-card input[type="radio"]:checked, .cleaning-card input[type="radio"]:checked, .house-card input[type="radio"]:checked, .cleaning-package input[type="radio"]:checked, label.service-card input[type="radio"]:checked, .service-radio:checked, .cleaning-radio:checked, .package-radio:checked'
    );
    if (checked) {
      var card = checked.closest('.service-card, .cleaning-card, .cleaning-package, label.service-card, .card');
      var price = findPriceFromCard(card);
      savePrice(price);
    }
    persistMulti();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
