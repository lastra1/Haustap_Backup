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

  function savePrice(price){
    if (price == null || isNaN(price)) return;
    try { localStorage.setItem('selected_service_price', String(price)); } catch(e){}
  }

  function onChange(ev){
    var input = ev.target;
    if (!input || input.tagName !== 'INPUT') return;
    if (String(input.type).toLowerCase() !== 'radio') return;
    // Support various service pages: service-card, cleaning-package, generic card wrappers
    var card = input.closest('.service-card, .cleaning-card, .cleaning-package, label.service-card, .card');
    var price = findPriceFromCard(card);
    if (price == null) {
      // Fallback: look for any nearby element with a price-like class
      var sib = card || input.parentElement;
      if (sib) {
        var like = sib.querySelector('[class*="price"]');
        price = parsePriceText(like ? like.textContent : '');
      }
    }
    savePrice(price);
  }

  function init(){
    document.addEventListener('change', onChange, true);
    // Initialize from any pre-checked selection across supported containers
    var checked = document.querySelector(
      '.service-card input[type="radio"]:checked, .cleaning-card input[type="radio"]:checked, .cleaning-package input[type="radio"]:checked, label.service-card input[type="radio"]:checked, .service-radio:checked, .cleaning-radio:checked, .package-radio:checked'
    );
    if (checked) {
      var card = checked.closest('.service-card, .cleaning-card, .cleaning-package, label.service-card, .card');
      var price = findPriceFromCard(card);
      savePrice(price);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
