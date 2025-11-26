<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Booking Overview</title>
  <link rel="stylesheet" href="/css/global.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/booking_process/css/booking_overview.css" />
  <link rel="stylesheet" href="/client/css/homepage.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <script>window.API_BASE_OVERRIDE = ((window.location && window.location.origin) || '') + '/mock-api';</script>
  <script src="/login_sign up/js/api.js"></script>
  <script src="/client/js/booking-api.js"></script>
</head>

<body>
  <!-- HEADER -->
  <?php include dirname(__DIR__) . "/client/includes/header.php"; ?>
  <script>
    // Guests can review overview; login will be requested at final booking confirmation
  </script>

<main class="booking-overview">
  <h1 class="page-title">Booking Overview</h1>

  <section class="overview-box">
    <div class="service-header">
      <h2>Home Cleaning</h2>
      <h3><strong>Bungalow - Basic Cleaning</strong></h3>
      <hr>
    </div>

    <div class="details">
      <p><strong>Date:</strong> May 21, 2025</p>
      <p><strong>Time:</strong> 1:00 PM</p>
      <p class="multi-line">
        <strong>Address:</strong> B1 L50 Mango St. Phase 1 Saint Joseph Village 10,
        Barangay Langgam, City of San Pedro, Laguna 4023
      </p>
      <p class="multi-line">
        <strong>You selected:</strong> <b>Bungalow 80 - 150 sqm</b>
      </p>
      <p class="multi-line">
        <strong></strong> <b>Basic Cleaning - 1 Cleaner</b>
      </p>

      <div class="inclusions multi-line">
        <p>
          Inclusions:<br
          Living Room: walis, mop, dusting furniture, trash removal<br>
          Bedrooms: bed making, sweeping, dusting, trash removal<br>
          Hallways: mop & sweep, remove cobwebs<br>
          Windows & Mirrors: quick wipe
        </p>
      </div>
    </div>

    <div class="notes-section">
      <label for="notes" class="notes-label"><b>Notes:</b></label>
      <textarea id="notes"></textarea>
      <hr>
    </div>

    <div class="voucher-section">
      <div class="voucher-left">
        <i class="fa-solid fa-ticket icon"></i>
        <span>Add a Voucher</span>
      </div>
      <div class="voucher-toggle">
        <button class="toggle-btn">></button>
      </div>
    </div>

    <!-- Voucher form (collapsed by default) -->
    <div class="voucher-form" style="display:none;margin-top:8px">
      <label for="voucher-code" style="font-size:13px;color:#333">Voucher Code</label>
      <div class="voucher-input-row" style="display:flex;gap:6px;margin-top:6px;align-items:center">
        <input id="voucher-code" type="text" placeholder="Enter code (e.g., WELCOME-50)" style="flex:1;min-width:220px;padding:8px 10px;border:2px solid #c9c9c9;border-radius:6px;font-size:13px" />
        <button class="apply-voucher-btn" style="background:#00c4cc;color:#fff;border:none;border-radius:6px;padding:8px 12px;font-size:13px;cursor:pointer">Apply</button>
        <button class="remove-voucher-btn" style="background:#ddd;color:#333;border:none;border-radius:6px;padding:8px 12px;font-size:13px;cursor:pointer">Remove</button>
      </div>
      <div class="voucher-actions" style="margin-top:6px;display:flex;gap:8px;align-items:center">
        <button class="best-voucher-btn" style="background:transparent;border:none;color:#009999;font-weight:600;cursor:pointer">Use Best Available Voucher</button>
        <small class="voucher-error" style="color:#c62828;display:none"></small>
      </div>
    </div>

    <div class="summary">
  <div class="summary-row">
    <strong>Sub Total:</strong> <span>₱800.00</span>
  </div>
  <div class="summary-row">
    <strong>Voucher Discount:</strong> <span>₱0</span>
  </div>
  <div class="summary-row total">
    <strong>TOTAL:</strong> <span>₱800.00</span>
  </div>
</div>

    <p class="footer-note">
      Full payment will be collected directly by the service provider upon completion of the service.
    </p>

    <div class="terms-section" style="margin-top:14px;padding-top:8px;border-top:1px solid #e5e5e5;display:flex;align-items:center;gap:8px">
      <input type="checkbox" id="termsAgree" style="width:18px;height:18px;cursor:pointer" />
      <label for="termsAgree" style="cursor:pointer">I agree to the <a href="/client/terms.php" target="_blank">Terms and Conditions</a> and <a href="/client/privacy.php" target="_blank">Privacy Policy</a>.</label>
    </div>
  </section>

    <!-- PAGINATION -->
    <div class="pagination">
      <button>&lt;</button>
      
      
      
      
      
      <button>&gt;</button>
    </div>
  </main>


  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
  <script>
    (function(){
      // Read selections from localStorage without altering the markup or layout
      function get(k){ try { return localStorage.getItem(k); } catch(e){ return null; } }
      var TZ = 'Asia/Manila';
      function formatDate(iso){
        try {
          var d = new Date(iso);
          return new Intl.DateTimeFormat('en-US', { timeZone: TZ, year:'numeric', month:'short', day:'numeric' }).format(d);
        } catch(e){ return iso; }
      }
      function formatTime(hhmm){
        if (!hhmm) return '';
        var parts = String(hhmm).split(':');
        var h = parseInt(parts[0]||'0',10), m = parts[1]||'00';
        var ampm = h>=12 ? 'PM' : 'AM';
        var h12 = h%12; if (h12===0) h12=12;
        return h12 + ':' + (m.length===1?('0'+m):m) + ' ' + ampm;
      }

      var selectedDate = get('selected_date');
      var selectedTime = get('selected_time');
      var address = get('booking_address');
      var serviceName = get('selected_service_name');
      var servicesList = (function(){
        try { var raw = get('selected_services_names'); return raw ? JSON.parse(raw) : []; } catch(e){ return []; }
      })();
      var providerName = get('selected_provider_name');

      // Update service header text if available (content only)
      var h2 = document.querySelector('.service-header h2');
      var h3 = document.querySelector('.service-header h3');
      if (h3 && serviceName) { h3.innerHTML = '<strong>' + serviceName + '</strong>'; }
      if (h2 && providerName) { h2.textContent = providerName; }

      // Update details lines by matching labels; keep structure same
      var detailPs = Array.prototype.slice.call(document.querySelectorAll('.details p'));
      detailPs.forEach(function(p){
        var text = p.textContent || '';
        if (text.trim().startsWith('Date:') && selectedDate) {
          p.innerHTML = '<strong>Date:</strong> ' + formatDate(selectedDate);
        }
        if (text.trim().startsWith('Time:') && selectedTime) {
          p.innerHTML = '<strong>Time:</strong> ' + formatTime(selectedTime);
        }
        if (text.trim().startsWith('Address:') && address) {
          p.innerHTML = '<strong>Address:</strong> ' + address;
        }
      });

      // Update "You selected" line; support multiple selected services
      var multiLines = Array.prototype.slice.call(document.querySelectorAll('.details p.multi-line'));
      if (multiLines.length) {
        var selectedLine = multiLines[0];
        if (servicesList && servicesList.length) {
          selectedLine.innerHTML = '<strong>You selected:</strong> ' + servicesList.map(function(s){ return '<b>'+ s +'</b>'; }).join('<br>');
        } else {
          var bEl = selectedLine ? selectedLine.querySelector('b') : null;
          if (bEl && serviceName) { bEl.textContent = serviceName; }
        }
      }

      // Update subtotal and total to reflect selected price
      var price = (function(){
        try {
          var arrRaw = localStorage.getItem('selected_services_prices');
          if (arrRaw) {
            var arr = JSON.parse(arrRaw);
            if (Array.isArray(arr)) {
              return arr.reduce(function(a,b){ return a + (Number(b)||0); }, 0);
            }
          }
        } catch(e){}
        try { var v = localStorage.getItem('selected_service_price'); if (v) return Number(v)||0; } catch(_) {}
        // Fallback mapping based on cleaning type
        try {
          var name = localStorage.getItem('selected_service_name') || '';
          var type = localStorage.getItem('selected_cleaning_type') || '';
          var base = 0;
          var t = String(type||name||'').toLowerCase();
          if (t.indexOf('basic') !== -1) base = 1000;
          else if (t.indexOf('standard') !== -1) base = 2000;
          else if (t.indexOf('deep') !== -1) base = 3000;
          return base;
        } catch(e){}
        return 0;
      })();
      var summarySpans = Array.prototype.slice.call(document.querySelectorAll('.summary span'));
      var subtotalEl = summarySpans[0] || null;
      var discountEl = (function(){
        var rows = Array.prototype.slice.call(document.querySelectorAll('.summary .summary-row'));
        var found = rows.find(function(r){ return (r.textContent||'').toLowerCase().indexOf('voucher discount') !== -1; });
        return found ? found.querySelector('span') : null;
      })();
      var totalEl = summarySpans[summarySpans.length-1] || null;
      function formatPHP(v){ return '₱' + Number(v||0).toFixed(2); }
      function updateSummary(subtotal, discount){
        var total = Math.max(0, Number(subtotal||0) - Number(discount||0));
        if (subtotalEl) subtotalEl.textContent = formatPHP(subtotal);
        if (discountEl) discountEl.textContent = formatPHP(discount);
        if (totalEl) totalEl.textContent = formatPHP(total);
        try { localStorage.setItem('computed_subtotal', String(subtotal)); localStorage.setItem('computed_total', String(total)); } catch(e){}
      }
      updateSummary(price, 0);

      // If price still zero, try to look up from DB services endpoint
      (async function tryFetchPrice(){
        if (Number(price||0) > 0) return;
        var name = localStorage.getItem('selected_service_name') || '';
        var category = (name && name.toLowerCase().indexOf('cleaning') !== -1) ? 'Cleaning' : '';
        if (!name) return;
        try {
          var res = await fetch('/api/system/service-price?name=' + encodeURIComponent(name) + (category ? ('&category=' + encodeURIComponent(category)) : ''));
          var j = await res.json();
          if (j && j.success && j.data && j.data.price){ price = Number(j.data.price)||0; updateSummary(price, Number(localStorage.getItem('selected_voucher_amount')||'0')); return; }
          // Fallback: attempt auto-seed then retry once
          try { await fetch('/api/system/auto-seed-from-ui'); } catch(e){}
          var res2 = await fetch('/api/system/service-price?name=' + encodeURIComponent(name) + (category ? ('&category=' + encodeURIComponent(category)) : ''));
          var j2 = await res2.json();
          if (j2 && j2.success && j2.data && j2.data.price){ price = Number(j2.data.price)||0; updateSummary(price, Number(localStorage.getItem('selected_voucher_amount')||'0')); }
        } catch(e){}
      })();

      // Persist notes typing for use on confirm page
      var notesEl = document.getElementById('notes');
      if (notesEl) {
        var existing = get('booking_notes');
        if (existing) notesEl.value = existing;
        notesEl.addEventListener('input', function(){
          try { localStorage.setItem('booking_notes', notesEl.value || ''); } catch(e){}
        });
      }

      // Use pagination arrows for navigation without adding new UI
      var pag = document.querySelector('.pagination');
      if (pag) {
        var btns = Array.prototype.slice.call(pag.querySelectorAll('button'));
        if (btns.length) {
          var back = btns[0];
          var next = btns[btns.length-1];
          if (back) back.addEventListener('click', function(){ window.location.href = '/booking/schedule'; });
          var terms = document.getElementById('termsAgree');
          // Restore terms acceptance
          try { var agreed = localStorage.getItem('terms_agreed') === '1'; if (terms) terms.checked = agreed; } catch(e){}
          if (terms) {
            terms.addEventListener('change', function(){ try { localStorage.setItem('terms_agreed', terms.checked ? '1' : '0'); } catch(e){} });
          }
          if (next) next.addEventListener('click', function(){
            if (!terms || !terms.checked) { alert('Please agree to the Terms and Conditions to proceed.'); return; }
            window.location.href = '/booking_process/confirm_booking.php';
          });
        }
      }

      // --- Voucher logic ---
      var VOUCHER_KEY_CODE = 'selected_voucher_code';
      var VOUCHER_KEY_AMOUNT = 'selected_voucher_amount';
      var voucherSection = document.querySelector('.voucher-section');
      var voucherForm = document.querySelector('.voucher-form');
      var toggleBtn = document.querySelector('.voucher-toggle .toggle-btn');
      var codeInput = document.getElementById('voucher-code');
      var applyBtn = document.querySelector('.apply-voucher-btn');
      var removeBtn = document.querySelector('.remove-voucher-btn');
      var bestBtn = document.querySelector('.best-voucher-btn');
      var errEl = document.querySelector('.voucher-error');

      function showToast(msg, type){
        try { if (window.htToast) { window.htToast[type||'info'](msg); return; } } catch(e) {}
        try { if (window.HausTapToast && window.HausTapToast.show){ window.HausTapToast.show({ message: msg, type: type||'info' }); return; } } catch(e) {}
        try { alert(msg); } catch(e) {}
      }

      function currentEmail(){
        var u = null;
        try { u = JSON.parse(localStorage.getItem('haustap_user')||'null'); } catch(e){}
        if (!u) { try { u = JSON.parse(localStorage.getItem('htUser')||'null'); } catch(e){} }
        var em = (u && u.email) ? String(u.email).trim().toLowerCase() : '';
        return em || 'example@haustap.local';
      }

      async function fetchVouchers(){
        var email = currentEmail();
        var base = (window.FIREBASE_API_BASE || ((window.API_BASE||'') + '/firebase'));
        var url = base + '/vouchers?email=' + encodeURIComponent(email);
        var res = await fetch(url);
        var json = await res.json();
        if (!json.success) throw new Error(json.message||'Failed to fetch vouchers');
        return json.data;
      }

      function normalizeCode(raw){
        var c = String(raw||'').trim().toUpperCase();
        if (!c) return null;
        if (c.indexOf('WELCOME') !== -1) return { type:'welcome', code: c };
        if (c.indexOf('LOYALTY') !== -1) return { type:'loyalty', code: c };
        if (c.indexOf('REFERRAL') !== -1) return { type:'referral', code: c };
        // Unknown: treat as invalid for safety
        return null;
      }

      function computeAmountFor(type, vouchers){
        if (!vouchers) return 0;
        var priceNum = Number(price||0);
        if (type === 'welcome') {
          // First-time users only: no completed bookings
          var eligible = !!(vouchers.welcome && vouchers.welcome.eligible);
          var completed = Number((vouchers.loyalty && vouchers.loyalty.completed) || 0);
          if (!eligible || completed > 0) return 0;
          return Math.min(priceNum, Number(vouchers.welcome.reward_amount||0));
        }
        if (type === 'loyalty') {
          var req = Number((vouchers.loyalty && vouchers.loyalty.required) || 10);
          var done = Number((vouchers.loyalty && vouchers.loyalty.completed) || 0);
          if (done < req) return 0;
          return Math.min(priceNum, Number(vouchers.loyalty.reward_amount||0));
        }
        if (type === 'referral') {
          var earned = Number((vouchers.referral && vouchers.referral.earned) || 0);
          var reward = Number((vouchers.referral && vouchers.referral.reward_amount) || 0);
          if (earned <= 0) return 0;
          return Math.min(priceNum, reward);
        }
        return 0;
      }

      async function applyCode(raw){
        if (errEl) { errEl.style.display='none'; errEl.textContent=''; }
        var norm = normalizeCode(raw);
        if (!norm) { if (errEl){ errEl.textContent='Invalid voucher code'; errEl.style.display='inline'; } showToast('Invalid voucher code', 'error'); return; }
        var vouchers = null; try { vouchers = await fetchVouchers(); } catch(e){ showToast('Could not verify voucher', 'error'); return; }
        var amount = computeAmountFor(norm.type, vouchers);
        if (!amount || amount <= 0) { if (errEl){ errEl.textContent='Voucher not eligible'; errEl.style.display='inline'; } showToast('Voucher not eligible', 'warning'); return; }
        try { localStorage.setItem(VOUCHER_KEY_CODE, norm.code); localStorage.setItem(VOUCHER_KEY_AMOUNT, String(amount)); } catch(e){}
        updateSummary(price, amount);
        showToast('Voucher applied: ' + norm.code + ' (-' + formatPHP(amount) + ')', 'success');
      }

      async function applyBest(){
        if (errEl) { errEl.style.display='none'; errEl.textContent=''; }
        var vouchers = null; try { vouchers = await fetchVouchers(); } catch(e){ showToast('Could not load vouchers', 'error'); return; }
        var candidates = [
          { type:'welcome', amount: computeAmountFor('welcome', vouchers), code:'WELCOME-50' },
          { type:'loyalty', amount: computeAmountFor('loyalty', vouchers), code:'LOYALTY-50' },
          { type:'referral', amount: computeAmountFor('referral', vouchers), code:'REFERRAL-10' }
        ];
        candidates.sort(function(a,b){ return Number(b.amount||0) - Number(a.amount||0); });
        var best = candidates[0];
        if (!best || !best.amount) { showToast('No eligible voucher available', 'info'); return; }
        try { localStorage.setItem(VOUCHER_KEY_CODE, best.code); localStorage.setItem(VOUCHER_KEY_AMOUNT, String(best.amount)); } catch(e){}
        if (codeInput) codeInput.value = best.code;
        updateSummary(price, best.amount);
        showToast('Best voucher applied: ' + best.code + ' (-' + formatPHP(best.amount) + ')', 'success');
      }

      function removeVoucher(){
        try { localStorage.removeItem(VOUCHER_KEY_CODE); localStorage.removeItem(VOUCHER_KEY_AMOUNT); } catch(e){}
        if (codeInput) codeInput.value = '';
        updateSummary(price, 0);
        showToast('Voucher removed', 'info');
      }

      // Toggle form visibility
      if (toggleBtn && voucherForm) {
        toggleBtn.addEventListener('click', function(){
          var show = voucherForm.style.display === 'none';
          voucherForm.style.display = show ? 'block' : 'none';
          toggleBtn.textContent = show ? 'v' : '>';
        });
      }
      if (applyBtn && codeInput) { applyBtn.addEventListener('click', function(){ applyCode(codeInput.value); }); }
      if (bestBtn) { bestBtn.addEventListener('click', applyBest); }
      if (removeBtn) { removeVoucher(); removeBtn.addEventListener('click', removeVoucher); }

      // Restore previously selected voucher
      try {
        var savedCode = localStorage.getItem(VOUCHER_KEY_CODE);
        var savedAmt = Number(localStorage.getItem(VOUCHER_KEY_AMOUNT)||'0');
        if (savedCode && savedAmt>0) {
          if (codeInput) codeInput.value = savedCode;
          updateSummary(price, savedAmt);
        }
      } catch(e){}
    })();
  </script>
</body>
</html>

