<?php
// Shared header partial for both guest and client contexts with runtime toggle
$context = isset($context) ? $context : 'guest';
$current = $_SERVER['REQUEST_URI'] ?? '';
$isActive = function(array $patterns) use ($current) {
  foreach ($patterns as $p) {
    if (stripos($current, $p) === 0) { return ' class="active"'; }
  }
  return '';
};

$logoGuest = ['/guest/homepage.php', '/guest/images/logo.png'];
$logoClient = ['/client/homepage.php', '/client/images/logo.png'];

$navGuest = [
  ['/guest/homepage.php', 'Home', ['/guest/homepage.php','/index.php']],
  ['/guest/services.php', 'Services', ['/guest/services']],
  ['/login', 'Bookings', ['/bookings']],
  ['/guest/About.php', 'About', ['/guest/About.php']],
  ['/guest/Contact.php', 'Contact', ['/guest/Contact.php']],
];

$navClient = [
  ['/client/homepage.php', 'Home', ['/client/homepage.php','/client/index.php','/index.php']],
  ['/client/services.php', 'Services', ['/client/services']],
  ['/bookings/booking.php', 'Bookings', ['/bookings']],
  ['/client/About.php', 'About', ['/client/About.php']],
  ['/client/contact_client.php', 'Contact', ['/client/contact_client.php']],
];

$initialGuest = ($context !== 'client');
?>
<style>
  .hidden{display:none !important}
  .auth-links{display:flex;align-items:center;gap:8px;margin-left:16px}
  .auth-links a{color:#00ADB5;text-decoration:none;font-weight:600}
  .auth-links a:hover{text-decoration:underline}
  .auth-links span{color:#9aa2a8}
</style>
<header class="header">
  <!-- Logo (both variants; toggle visibility) -->
  <a href="<?= htmlspecialchars($logoGuest[0]) ?>" class="logo-link logo-guest <?= $initialGuest ? '' : 'hidden' ?>">
    <img src="<?= htmlspecialchars($logoGuest[1]) ?>" alt="HausTap" class="logo-img">
  </a>
  <a href="<?= htmlspecialchars($logoClient[0]) ?>" class="logo-link logo-client <?= $initialGuest ? 'hidden' : '' ?>">
    <img src="<?= htmlspecialchars($logoClient[1]) ?>" alt="HausTap" class="logo-img">
  </a>

  <!-- Navigation: render both; toggle by class -->
  <nav class="nav nav-guest <?= $initialGuest ? '' : 'hidden' ?>">
    <?php foreach ($navGuest as $item): list($href, $label, $patterns) = $item; ?>
      <a href="<?= htmlspecialchars($href) ?>"<?= $isActive($patterns) ?>><?= htmlspecialchars($label) ?></a>
    <?php endforeach; ?>
  </nav>
  <nav class="nav nav-client <?= $initialGuest ? 'hidden' : '' ?>">
    <?php foreach ($navClient as $item): list($href, $label, $patterns) = $item; ?>
      <a href="<?= htmlspecialchars($href) ?>"<?= $isActive($patterns) ?>><?= htmlspecialchars($label) ?></a>
    <?php endforeach; ?>
  </nav>

  <!-- Right side -->
  <div class="header-right">
    <!-- Search -->
    <div class="search-box">
      <input type="text" placeholder="Search services..." aria-label="Search services">
      <button class="search-btn" aria-label="Search"><i class="fa-solid fa-search"></i></button>
    </div>

    <div class="auth-links <?= $initialGuest ? '' : 'hidden' ?>">
      <div class="signup-link"><a href="/signup">Sign up</a></div>
      <span>|</span>
      <div class="login-link"><a href="/login">Login</a></div>
    </div>

    <div class="client-links <?= $initialGuest ? 'hidden' : '' ?>">
      <a href="#" id="notifBellBtn" class="icon-link" aria-expanded="false" aria-controls="notifDropdown" title="Notifications">
        <i class="fa-solid fa-bell"></i>
        <span id="notifCount" style="display:none;background:#3dbfc3;color:#fff;border-radius:10px;font-size:12px;padding:0 6px;margin-left:4px;">0</span>
      </a>
      <a href="/account" class="account-link" title="My Account">
        <i class="fa-solid fa-user account-icon"></i>
        <span class="account-name">My Account</span>
      </a>
      <div id="notifDropdown" class="hidden" style="position:absolute;right:48px;top:72px;background:#fff;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.1);width:320px;z-index:1000;">
        <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;border-bottom:1px solid #f1f5f9;">
          <strong>Notifications</strong>
          <button id="notifMarkAll" style="background:transparent;border:none;color:#3dbfc3;cursor:pointer">Mark all read</button>
        </div>
        <ul id="notifList" style="list-style:none;margin:0;padding:0;max-height:360px;overflow:auto"></ul>
      </div>
    </div>
  </div>
</header>

<script>
  (function(){
    try {
      var isLoggedIn = !!localStorage.getItem('haustap_token');
      var toggle = function(sel, on){ var el = document.querySelector(sel); if (!el) return; el.classList[on ? 'remove' : 'add']('hidden'); };
      toggle('.logo-guest', !isLoggedIn);
      toggle('.nav-guest', !isLoggedIn);
      toggle('.auth-links', !isLoggedIn);
      toggle('.logo-client', isLoggedIn);
      toggle('.nav-client', isLoggedIn);
      toggle('.client-links', isLoggedIn);
    } catch(e) {}
  })();
</script>
<script>
  (function(){
    function slugify(s){ return String(s||'').toLowerCase().replace(/\s+/g,'-'); }
    function numFromText(t){ var n=Number(String(t||'').replace(/[^0-9.]/g,'')); return isNaN(n)?0:n; }
    function onReady(){
      if ((window.location && window.location.pathname || '').indexOf('/Homecleaning/') !== 0) return;
      var typeBtn = document.querySelector('.cleaning-type-btn');
      function getHouse(){ var txt = typeBtn ? (typeBtn.textContent||'').trim() : ''; return slugify(txt); }
      function getSelectedCard(){ var r=document.querySelector('input.cleaning-radio:checked'); return r ? r.closest('.cleaning-card') : null; }
      function getCleaning(){ var r=document.querySelector('input.cleaning-radio:checked'); var id=r ? r.id : ''; return id ? id.replace('-cleaning','') : ''; }
      function getPrice(){ var c=getSelectedCard(); var el=c? c.querySelector('.cleaning-price'):null; return numFromText(el? el.textContent:''); }
      function getLabel(){ var houseTxt = typeBtn ? (typeBtn.textContent||'').trim() : ''; var c=getSelectedCard(); var t=c? c.querySelector('.cleaning-title'):null; var titleTxt=t? (t.textContent||'').trim() : ''; return (houseTxt && titleTxt) ? (houseTxt+' - '+titleTxt) : (houseTxt||titleTxt); }
      function persist(){ try {
        localStorage.setItem('selected_house_slug', getHouse());
        localStorage.setItem('selected_cleaning_type', getCleaning());
        localStorage.setItem('selected_service_name', getLabel());
        localStorage.setItem('selected_service_price', String(getPrice()));
      } catch(e){} }
      var btn = document.getElementById('ht-float-proceed');
      if (btn) btn.addEventListener('click', function(){
        var h=getHouse(); var c=getCleaning(); if (!c) { alert('Please select a cleaning type first.'); return; }
        persist();
        window.location.href = '/booking_process/booking_location.php?house='+encodeURIComponent(h)+'&cleaning='+encodeURIComponent(c);
      });
    }
    if (document.readyState==='loading') document.addEventListener('DOMContentLoaded', onReady); else onReady();
  })();
</script>

<!-- Global Proceed + Total floating bar -->
<style>
  .ht-float-bar{position:fixed;right:22px;bottom:22px;z-index:1000;display:flex;align-items:center;gap:10px}
  .ht-float-bar.hidden{display:none!important}
  #ht-float-proceed{background:#00ADB5;color:#fff;border:none;border-radius:8px;padding:10px 16px;font-weight:700;box-shadow:0 2px 6px rgba(0,0,0,.15);cursor:pointer}
  .ht-total{background:#fff;border:1.5px solid #e0e0e0;border-radius:8px;padding:8px 12px;font-weight:700;color:#333;box-shadow:0 2px 6px rgba(0,0,0,.08)}
  .ht-float-details{position:fixed;right:22px;bottom:72px;z-index:999;background:#fff;border:1px solid #e0e0e0;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,.08);padding:10px 12px;min-width:220px}
  .ht-float-details.hidden{display:none!important}
  .ht-float-details ul{margin:6px 0 0 0;padding:0;list-style:none}
  .ht-float-details li{font-size:14px;color:#555;margin-bottom:4px}
</style>
<div id="ht-float-bar" class="ht-float-bar hidden">
  <button id="ht-float-proceed">Proceed</button>
  <div class="ht-total">Total: ₱<span id="ht-total-val">0.00</span></div>
</div>
<div id="ht-float-details" class="ht-float-details hidden">
  <div style="font-size:13px;color:#666">Selected</div>
  <ul id="ht-float-list"></ul>
</div>
<script>
  (function(){
    function parseNum(v){
      if (v==null) return 0; var s=String(v).replace(/[^0-9.]/g,''); var n=Number(s); return isNaN(n)?0:n;
    }
    function fmt(n){ return (Number(n)||0).toFixed(2); }
    function lsGet(k){ try { return localStorage.getItem(k); } catch(e){ return null; } }
    function lsSet(k,v){ try { localStorage.setItem(k,v); } catch(e){} }
    function getNames(){
      try { var raw = lsGet('selected_services_names'); if (raw) { var a=JSON.parse(raw); if (Array.isArray(a)) return a.map(String); } } catch(e){}
      var single = lsGet('selected_service_name'); return single ? [single] : [];
    }
    function getTotal(){
      try { var raw = lsGet('selected_services_prices'); if (raw) { var a=JSON.parse(raw); if (Array.isArray(a)) return a.reduce(function(s,x){ return s+parseNum(x); },0); } } catch(e){}
      return parseNum(lsGet('selected_service_price'));
    }
    function update(){
      var bar = document.getElementById('ht-float-bar');
      var totalEl = document.getElementById('ht-total-val');
      var details = document.getElementById('ht-float-details');
      var list = document.getElementById('ht-float-list');
      if (!bar || !totalEl || !details || !list) return;
      var names = getNames();
      var total = getTotal();
      totalEl.textContent = fmt(total);
      list.innerHTML = '';
      names.forEach(function(n){ var li=document.createElement('li'); li.textContent = n; list.appendChild(li); });
      var hasSel = names.length > 0;
      bar.classList[hasSel ? 'remove' : 'add']('hidden');
      details.classList[hasSel ? 'remove' : 'add']('hidden');
    }
    function proceed(){
      var names = getNames();
      var total = getTotal();
      var svcName = names[0] || '';
      var svc = encodeURIComponent(svcName);
      var price = encodeURIComponent(String(total));
      var path = (window.location && window.location.pathname) || '';
      var goto = null;
      // Determine next page based on current location
      if (path.indexOf('/booking_process/booking_location.php') === 0 || path.indexOf('/booking/location') === 0) {
        goto = '/booking/schedule';
      } else if (path.indexOf('/booking_process/booking_schedule.php') === 0 || path.indexOf('/booking/schedule') === 0) {
        goto = '/booking/overview';
      } else if (path.indexOf('/booking_process/booking_overview.php') === 0 || path.indexOf('/booking/overview') === 0) {
        goto = '/booking_process/confirm_booking.php';
      } else if (path.indexOf('/Homecleaning/') === 0) {
        var house = '';
        var cleaning = '';
        try { house = (localStorage.getItem('selected_house_slug')||''); } catch(e){}
        try { cleaning = (localStorage.getItem('selected_cleaning_type')||''); } catch(e){}
        if (house && cleaning) {
          goto = '/booking_process/booking_location.php?house=' + encodeURIComponent(house) + '&cleaning=' + encodeURIComponent(cleaning);
        } else {
          alert('Please select a cleaning type to proceed.');
          return;
        }
      } else {
        // From category pages or homepage, start at booking location
        if (!svcName) { alert('Please select a service first.'); return; }
        goto = '/booking_process/booking_location.php?service=' + svc + '&price=' + price;
      }
      // Persist the selected service name/price to ensure continuity
      try {
        if (svcName) localStorage.setItem('selected_service_name', svcName);
        localStorage.setItem('selected_service_price', String(total));
      } catch(e){}
      window.location.href = goto;
    }
    function init(){
      update();
      document.addEventListener('change', function(){ setTimeout(update, 0); }, true);
      var btn = document.getElementById('ht-float-proceed');
      if (btn) btn.addEventListener('click', proceed);
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
  })();
</script>

<script>
  (function(){
    function formatPHP(v){ return (Number(v)||0).toFixed(2); }
    function parseNum(v){ var s=String(v||'').replace(/[^0-9.]/g,''); var n=Number(s); return isNaN(n)?0:n; }
    function lsGet(k){ try { return localStorage.getItem(k); } catch(e){ return null; } }
    function lsSet(k,v){ try { localStorage.setItem(k,v); } catch(e){} }
    function titleFromCard(card){
      if (!card) return '';
      var t = card.querySelector('.service-title, .cleaning-title, .house-title, [class*="title"]');
      return t ? (t.textContent||'').trim() : '';
    }
    function priceFromCard(card){
      if (!card) return 0;
      var el = card.querySelector('.service-price, .cleaning-price, [class*="price"]');
      return parseNum(el ? el.textContent : '');
    }
    function recompute(){
      var boxes = document.querySelectorAll('.service-card input[type="checkbox"], .cleaning-card input[type="checkbox"], .house-card input[type="checkbox"]');
      var names = [], prices = [];
      boxes.forEach(function(b){ if (b.checked){ var card = b.closest('.service-card, .cleaning-card, .house-card'); var nm = titleFromCard(card) || (b.value||'').trim(); names.push(nm); prices.push(priceFromCard(card)); } });
      var sum = prices.reduce(function(a,b){ return a + (Number(b)||0); }, 0);
      lsSet('selected_services_names', JSON.stringify(names));
      lsSet('selected_services_prices', JSON.stringify(prices));
      lsSet('selected_service_name', names[0]||'');
      lsSet('selected_service_price', String(sum));
      var totalEl = document.getElementById('ht-total-val'); if (totalEl) totalEl.textContent = formatPHP(sum);
      var list = document.getElementById('ht-float-list'); if (list){ list.innerHTML=''; names.forEach(function(n){ var li=document.createElement('li'); li.textContent=n; list.appendChild(li); }); }
      var bar = document.getElementById('ht-float-bar'); var det = document.getElementById('ht-float-details'); var hasSel = names.length > 0; if (bar) bar.classList[hasSel?'remove':'add']('hidden'); if (det) det.classList[hasSel?'remove':'add']('hidden');
    }
    function convertRadios(){
      var radios = document.querySelectorAll('.service-card input[type="radio"], .cleaning-card input[type="radio"], .house-card input[type="radio"]');
      radios.forEach(function(r){ r.setAttribute('type','checkbox'); });
    }
    function init(){
      convertRadios();
      document.addEventListener('change', function(ev){
        var t = ev.target;
        if (t && t.matches('.service-card input[type="checkbox"], .cleaning-card input[type="checkbox"], .house-card input[type="checkbox"]')){
          ev.stopImmediatePropagation(); ev.stopPropagation();
          recompute();
        }
      }, true);
      var cards = document.querySelectorAll('.service-card, .cleaning-card, .house-card');
      cards.forEach(function(card){
        card.addEventListener('click', function(e){
          var input = card.querySelector('input[type="checkbox"], input[type="radio"]');
          if (!input) return;
          if (e.target === input) { return; }
          if (input.type.toLowerCase() === 'radio') { input.type = 'checkbox'; }
          input.checked = !input.checked;
          recompute();
        }, true);
      });
      recompute();
    }
    if (document.readyState==='loading') document.addEventListener('DOMContentLoaded', init); else init();
  })();
</script>
