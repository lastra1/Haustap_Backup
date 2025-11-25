<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HausTap Bookings</title>
  <link rel="stylesheet" href="/css/global.css">
  <link rel="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/booking.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<link rel="stylesheet" href="/client/css/homepage.css"></head>
<body><?php include dirname(__DIR__) . "/client/includes/header.php"; ?>
  <script>
    // Require login for Bookings page: redirect guests to /login
    (function(){
      try {
        var t = localStorage.getItem('haustap_token');
        if (!t) { window.location.href = '/login'; return; }
      } catch(e) { window.location.href = '/login'; return; }
    })();
  </script>

  <!-- Skeleton Overlay -->
  <div id="skeleton-overlay" class="skeleton-overlay">
    <div class="skeleton-grid">
      <div class="skeleton-row">
        <div class="skeleton-block skeleton-line shimmer" style="width:50%"></div>
      </div>
      <div class="skeleton-line shimmer" style="width:90%"></div>
      <div class="skeleton-line shimmer" style="width:85%"></div>
      <div class="skeleton-row">
        <div class="skeleton-circle"></div>
        <div class="skeleton-block skeleton-line shimmer" style="width:30%"></div>
        <div class="skeleton-block skeleton-line shimmer" style="width:15%"></div>
      </div>
      <div class="skeleton-row" style="margin-top:8px">
        <div class="spinner" aria-hidden="true"></div>
        <span style="font-size:14px;color:#555">Loading bookings…</span>
      </div>
    </div>
  </div>

  <script>
    (function(){
      var overlay = document.getElementById('skeleton-overlay');
      function show(){ document.body.classList.add('loading'); overlay.style.display='block'; }
      function hide(){ document.body.classList.remove('loading'); overlay.style.display='none'; }
      window.HausTapLoading = { show: show, hide: hide };
      show();
      window.addEventListener('DOMContentLoaded', function(){ setTimeout(hide, 800); });
    })();
  </script>

  <div class="page-content">

  <!-- tabs -->
  <div class="mytabs">
    <input type="radio" id="tabpending" name="mytabs" checked="checked">
      <label for="tabpending">Pending</label>
      <div class="tab">
        <div class="booking-list" data-status="pending"></div>
        <!-- Static Pending demo removed; dynamic bookings render into .booking-list -->

      </div>
      

      <input type="radio" id="tabongoing" name="mytabs">
      <label for="tabongoing">Ongoing</label>
      <div class="tab">
        <div class="booking-list" data-status="ongoing"></div>
      </div>
      

      <input type="radio" id="tabcompleted" name="mytabs">
      <label for="tabcompleted">Completed</label>
      <div class="tab">
        <div class="booking-list" data-status="completed"></div>
      </div>
      

      <input type="radio" id="tabcancelled" name="mytabs">
      <label for="tabcancelled">Cancelled</label>
      <div class="tab">
        <div class="booking-list" data-status="cancelled"></div>
      </div>

      <!-- Return tab -->
      <input type="radio" id="tabreturn" name="mytabs">
      <label for="tabreturn">Return</label>
      <div class="tab">
        <div class="booking-returns" data-type="returns"></div>
        <p class="return-reminder">Reminder: You may request a return for free within 24 hours after the service. <br> After 24 hours, a 300 return fee will be charged.</p>
      </div>
    </div>

  
  
    <!-- FOOTER -->
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
  </div>
  <script>
    window.API_BASE_OVERRIDE = (window.location.origin || '') + '/api/firebase';
  </script>
  <script src="/login_sign up/js/api.js"></script>
  <script src="/client/js/booking-api.js"></script>
  <script src="/client/js/ht-confirm.js"></script>
  <script src="/client/js/cancel-reason-modal.js"></script>
  <script src="/client/js/booking-api.js"></script>
  <script>
    (function(){
      var focusId = (function(){
        try {
          var p = new URLSearchParams(window.location.search);
          return p.get('focus') || localStorage.getItem('last_booking_id') || null;
        } catch(e) { return null; }
      })();

      function fmtDate(d){ try { var dt = new Date(d); return dt.toLocaleDateString(undefined, { year:'numeric', month:'short', day:'2-digit' }); } catch(e){ return d || ''; } }
      function fmtTime(t){ try {
        var parts = String(t||'').split(':');
        if (parts.length>=2){ var h = Number(parts[0]); var m = parts[1]; var ampm = h>=12 ? 'PM' : 'AM'; h = h%12; if (h===0) h=12; return h+':'+m+' '+ampm; }
        return t || '';
      } catch(e){ return t || ''; } }

      function normalizeStatus(s){
        s = String(s||'').toLowerCase();
        if (!s) return 'pending';
        if (s === 'accepted' || s === 'in_progress' || s === 'inprogress' || s === 'started' || s === 'ongoing') return 'ongoing';
        if (s === 'done' || s === 'finished' || s === 'complete' || s === 'completed') return 'completed';
        if (s === 'canceled' || s === 'cancelled') return 'cancelled';
        return s;
      }

      function el(tag, cls){ var e = document.createElement(tag); if (cls) e.className = cls; return e; }
      function renderCard(b){
        var wrap = el('div', 'booking_tables dynamic');
        var header = el('div', 'booking-header');
        var left = el('div', '');
        var h2 = el('h2'); h2.textContent = b.service_name || 'Service';
        var p = el('p'); p.textContent = (b.provider && b.provider.name) ? b.provider.name : (b.address || '').slice(0,80);
        left.appendChild(h2); left.appendChild(p);
        var right = el('div', 'booking-status');
        var idSpan = el('span'); idSpan.textContent = 'Booking ID';
        var pipe = el('span'); pipe.textContent = '|';
        var statusNorm = normalizeStatus(b.status);
        var hasReturn = !!(b.last_return_id);
        var stClass = hasReturn ? 'return' : statusNorm;
        var st = el('span', 'status-pill ' + stClass);
        st.textContent = hasReturn ? 'RETURN' : String(statusNorm||'').toUpperCase();
        right.appendChild(idSpan); right.appendChild(pipe); right.appendChild(st);
        header.appendChild(left); header.appendChild(right);
        wrap.appendChild(header);

        var details = el('div', 'booking-details');
        var d1 = el('div', 'detail'); d1.appendChild(el('strong')).textContent='Date'; var dp=el('p'); dp.textContent = fmtDate(b.scheduled_date); d1.appendChild(dp);
        var d2 = el('div', 'detail'); d2.appendChild(el('strong')).textContent='Time'; var tp=el('p'); tp.textContent = fmtTime(b.scheduled_time); d2.appendChild(tp);
        var d3 = el('div', 'detail address'); d3.appendChild(el('strong')).textContent='Address'; var ap=el('p'); ap.textContent = b.address || '—'; d3.appendChild(ap);
        details.appendChild(d1); details.appendChild(d2); details.appendChild(d3);
        wrap.appendChild(details);

        var totals = el('div', 'booking-details no-border');
        var tline = el('div', 'detail full-width'); var tlabel = el('span'); tlabel.textContent='Total:'; var tval = el('span'); tval.textContent = (b.price!=null) ? String(b.price) : '—'; tline.appendChild(tlabel); tline.appendChild(tval); totals.appendChild(tline);
        wrap.appendChild(totals);

        // Client-side cancel for pending bookings
        if (statusNorm === 'pending') {
          var footer = el('div', 'booking-footer');
          var actions = el('div', 'actions');
          var cancelBtn = el('button', 'btn btn-outline-dark');
          cancelBtn.textContent = 'Cancel';
          cancelBtn.addEventListener('click', function(){
            if (!window.HausTapBookingAPI || !b.id) return;
            // First, collect a valid cancellation reason
            window.htCancelReason()
              .then(function(reason){
                if (!reason) return; // user dismissed
                // Confirm final action
                return window.htConfirm('Once cancelled, the service provider will be notified and this action cannot be undone.', { title:'Confirm Cancellation', okText:'Cancel Booking', cancelText:'Keep Booking' })
                  .then(function(go){
                    if (!go) return;
                    HausTapLoading && HausTapLoading.show && HausTapLoading.show();
                    try { localStorage.setItem('cancel_reason:'+String(b.id), String(reason)); } catch(e){}
                    return window.HausTapBookingAPI.cancelBooking(b.id, { reason: reason })
                      .then(function(){ refresh(); })
                      .catch(function(err){ console.error('Cancel failed', err); alert('Unable to cancel right now.'); })
                      .finally(function(){ HausTapLoading && HausTapLoading.hide && HausTapLoading.hide(); });
                  });
              })
              .catch(function(err){ console.error('Reason modal error', err); });
          });
          actions.appendChild(cancelBtn);
          footer.appendChild(actions);
          wrap.appendChild(footer);
        }

        // Add Chat button for ongoing or accepted bookings
        if (statusNorm === 'ongoing') {
          var footer2 = el('div', 'booking-footer');
          var actions2 = el('div', 'actions');
          var chatBtn = el('button', 'btn-ht btn-ht-outline');
          chatBtn.textContent = 'Chat';
          chatBtn.addEventListener('click', function(){
            try {
              var url = '/client/contact_client.php?booking_id=' + encodeURIComponent(b.id);
              window.location.href = url;
            } catch(e) { console.error(e); }
          });
          actions2.appendChild(chatBtn);
          footer2.appendChild(actions2);
          wrap.appendChild(footer2);
        }

        // Actions for completed bookings: Rate, Request Return, View Details
        if (statusNorm === 'completed') {
          var footer3 = el('div', 'booking-footer');
          // Provider summary (if available)
          var sp = el('div', 'service-provider');
          var spTop = el('div', 'service-provider-top');
          spTop.appendChild(el('i', 'fa-solid fa-user account-icon'));
          var spName = el('span', 'name'); spName.textContent = (b.provider && (b.provider.company_name || b.provider.name)) ? (b.provider.company_name || b.provider.name) : 'Service Provider';
          spTop.appendChild(spName);
          spTop.appendChild(el('i', 'fa-solid fa-comment message-icon'));
          sp.appendChild(spTop);
          footer3.appendChild(sp);

          var actions3 = el('div', 'actions');
          // Rate button
          var rateBtn = el('button', 'btn-ht btn-ht-primary');
          rateBtn.textContent = 'Rate';
          rateBtn.addEventListener('click', function(){
            try {
              if (!window.HausTapBookingAPI || !b.id) {
                // Fallback: open static rating page
                window.location.href = '/bookings/rate_sp.php';
                return;
              }
              var input = prompt('Rate the service (1-5):', '5');
              var rating = input ? Number(input) : null;
              if (!rating || rating < 1 || rating > 5) { alert('Please enter a number from 1 to 5.'); return; }
              HausTapLoading && HausTapLoading.show && HausTapLoading.show();
              window.HausTapBookingAPI.rateBooking(b.id, rating)
                .then(function(){ alert('Thanks for your rating!'); refresh(); })
                .catch(function(err){ console.error('Rating failed', err); alert('Unable to submit rating right now.'); })
                .finally(function(){ HausTapLoading && HausTapLoading.hide && HausTapLoading.hide(); });
            } catch(e) { console.error(e); }
          });
          actions3.appendChild(rateBtn);

          // Actions dropdown with Request Return and Report
          var retWrap = el('div', 'dropdown');
          var toggle = el('button', 'btn-ht btn-ht-outline dropdown-toggle');
          toggle.type = 'button';
          toggle.setAttribute('data-bs-toggle', 'dropdown');
          toggle.textContent = 'More';
          retWrap.appendChild(toggle);

          var menu = el('ul', 'dropdown-menu');
          // Request Return (disabled if already requested)
          var liReturn = el('li', '');
          var linkReturn = el('a', 'dropdown-item');
          if (hasReturn) {
            linkReturn.textContent = 'Return Requested';
            linkReturn.classList.add('disabled');
            linkReturn.setAttribute('aria-disabled', 'true');
          } else {
            linkReturn.textContent = 'Request for Return';
            linkReturn.href = '/bookings/request_return.php' + (b.id ? ('?id=' + encodeURIComponent(b.id)) : '');
          }
          liReturn.appendChild(linkReturn);
          menu.appendChild(liReturn);

          // Report
          var liReport = el('li', '');
          var linkReport = el('a', 'dropdown-item');
          linkReport.textContent = 'Report';
          linkReport.href = '/client/contact_client.php' + (b.id ? ('?booking_id=' + encodeURIComponent(b.id)) : '');
          liReport.appendChild(linkReport);
          menu.appendChild(liReport);

          retWrap.appendChild(menu);
          actions3.appendChild(retWrap);

          // View Details
          var detailsBtn = el('a', 'btn-ht btn-ht-outline');
          detailsBtn.textContent = 'View Details';
          detailsBtn.href = '/booking_process/booking_overview.php' + (b.id ? ('?id=' + encodeURIComponent(b.id)) : '');
          actions3.appendChild(detailsBtn);

          footer3.appendChild(actions3);
          wrap.appendChild(footer3);
        }

        // Removed focus highlight (green outline/box-shadow) to avoid visual distraction
        // Previously applied when the card ID matched `focusId`
        return wrap;
      }

      function placeCard(b){
        var status = normalizeStatus(b.status||'pending');
        var container = document.querySelector('.booking-list[data-status="'+status+'"]');
        if (!container) return;
        container.appendChild(renderCard(b));
        // Hide static demo blocks once we have dynamic data
        var tab = container.closest('.tab');
        if (tab) {
          // Hide static demo blocks once we have dynamic data,
          // but keep footers visible to allow static Cancel button fallback.
          var demos = tab.querySelectorAll('.booking_tables:not(.dynamic), .after-bookings, .return-reminder, .cancellation_details');
          demos.forEach(function(x){ x.style.display='none'; });
        }
      }

      function clearDynamic(){
        var lists = document.querySelectorAll('.booking-list, .booking-returns');
        lists.forEach(function(list){ list.innerHTML = ''; });
      }

      function listAndRender(){
        try {
          if (typeof HausTapBookingAPI === 'undefined') return;
          HausTapLoading && HausTapLoading.show && HausTapLoading.show();
          HausTapBookingAPI.listBookings().then(function(resp){
            var arr = [];
            if (resp && resp.data && resp.data.data && Array.isArray(resp.data.data)) arr = resp.data.data;
            else if (resp && resp.data && Array.isArray(resp.data)) arr = resp.data;
            else if (Array.isArray(resp)) arr = resp;
            // Show all bookings, including pending, to reflect full workflow
            clearDynamic();
            arr.forEach(placeCard);
            // Fallback: if Pending is still empty but we know a recent booking ID,
            // fetch that booking directly and render it so users always see their new booking.
            try {
              var pendingContainer = document.querySelector('.booking-list[data-status="pending"]');
              var hasPending = pendingContainer && pendingContainer.children && pendingContainer.children.length > 0;
              var recentId = String(focusId||'') || (localStorage.getItem('last_booking_id') || '');
              if (!hasPending && recentId && typeof HausTapBookingAPI.getBooking === 'function') {
                HausTapBookingAPI.getBooking(recentId).then(function(r){
                  var b = (r && r.data) ? r.data : r;
                  if (b && b.id) { placeCard(b); }
                }).catch(function(){ /* ignore */ });
              }
            } catch(e) { /* ignore */ }
          }).then(function(){
            if (typeof HausTapBookingAPI.listReturns !== 'function') return;
            return HausTapBookingAPI.listReturns().then(function(r){
              var list = [];
              if (r && r.data && Array.isArray(r.data)) list = r.data;
              var container = document.querySelector('.booking-returns');
              if (!container) return;
              list.forEach(function(rec){
                var bsum = rec.booking || {};
                var wrap = el('div', 'booking_tables dynamic return');

                // Header: Title + status pill like in screenshot
                var header = el('div', 'booking-header');
                var left = el('div', '');
                var h2 = el('h2'); h2.textContent = bsum.service_name || 'Service';
                var p = el('p'); p.textContent = (bsum.service_sub || '') || (bsum.address || '');
                left.appendChild(h2); left.appendChild(p);
                var right = el('div', 'booking-status');
                var idSpan = el('span'); idSpan.textContent = 'Booking ID';
                var pipe = el('span'); pipe.textContent = '|';
                var st = el('span', 'status-pill return');
                st.textContent = 'RETURN';
                right.appendChild(idSpan); right.appendChild(pipe); right.appendChild(st);
                header.appendChild(left); header.appendChild(right);
                wrap.appendChild(header);

                // Details: Date, Time, Address
                var details = el('div', 'booking-details');
                var d1 = el('div', 'detail'); d1.appendChild(el('strong')).textContent='Date'; var dp=el('p'); dp.textContent = fmtDate(bsum.scheduled_date); d1.appendChild(dp);
                var d2 = el('div', 'detail'); d2.appendChild(el('strong')).textContent='Time'; var tp=el('p'); tp.textContent = fmtTime(bsum.scheduled_time); d2.appendChild(tp);
                var d3 = el('div', 'detail address'); d3.appendChild(el('strong')).textContent='Address'; var ap=el('p'); ap.textContent = bsum.address || '—'; d3.appendChild(ap);
                details.appendChild(d1); details.appendChild(d2); details.appendChild(d3);
                wrap.appendChild(details);

                // Total line
                var totals = el('div', 'booking-details no-border');
                var tline = el('div', 'detail full-width total-line');
                var tlabel = el('span', 'label'); tlabel.textContent='TOTAL';
                var tval = el('span', 'price');
                var price = (bsum.price!=null) ? Number(bsum.price) : null;
                tval.textContent = price!=null ? ('₱'+price.toLocaleString(undefined,{minimumFractionDigits:2, maximumFractionDigits:2})) : '—';
                tline.appendChild(tlabel); tline.appendChild(tval); totals.appendChild(tline);
                wrap.appendChild(totals);

                // Footer: Service provider summary (if known)
                var footer = el('div', 'booking-footer');
                var sp = el('div', 'service-provider');
                var spTop = el('div', 'service-provider-top');
                spTop.appendChild(el('i', 'fa-solid fa-user account-icon'));
                var spName = el('span', 'name'); spName.textContent = 'Service Provider';
                spTop.appendChild(spName);
                spTop.appendChild(el('i', 'fa-solid fa-comment message-icon'));
                sp.appendChild(spTop);
                footer.appendChild(sp);

                // Actions: View Details
                var actions = el('div', 'actions');
                var view = el('a', 'btn-ht btn-ht-outline');
                var bid = bsum.id || rec.booking_id;
                view.textContent = 'View Full Details';
                view.href = '/booking_process/booking_overview.php' + (bid ? ('?id='+encodeURIComponent(bid)) : '');
                actions.appendChild(view);
                footer.appendChild(actions);
                wrap.appendChild(footer);

                container.appendChild(wrap);
              });
            });
          }).catch(function(err){
            console.error('Booking list failed:', err);
            // Fallback path: even if the list endpoint fails, try to render the most
            // recently created booking so the user sees their pending item.
            try {
              var pendingContainer = document.querySelector('.booking-list[data-status="pending"]');
              var hasPending = pendingContainer && pendingContainer.children && pendingContainer.children.length > 0;
              var recentId = String(focusId||'') || (localStorage.getItem('last_booking_id') || '');
              if (!hasPending && recentId && typeof HausTapBookingAPI !== 'undefined' && typeof HausTapBookingAPI.getBooking === 'function') {
                HausTapBookingAPI.getBooking(recentId).then(function(r){
                  var b = (r && r.data) ? r.data : r;
                  if (b && b.id) { placeCard(b); }
                }).catch(function(){ /* ignore */ });
              }
            } catch(e) { /* ignore */ }
          }).finally(function(){ HausTapLoading && HausTapLoading.hide && HausTapLoading.hide(); });
        } catch(e) { console.error(e); }
      }

      function refresh(){ listAndRender(); }

      // Attempt to render bookings; if unauthorized or unavailable, static demo remains
      listAndRender();
      // Poll for updates every 30 seconds to keep statuses in sync
      setInterval(function(){ try { listAndRender(); } catch(e){} }, 30000);

      // Fallback: enable Cancel on static Pending card when API is available
      try {
        var staticCancel = document.querySelector('.cancel-pending-static');
        if (staticCancel) {
          staticCancel.addEventListener('click', function(){
            var bid = null;
            try { bid = localStorage.getItem('last_booking_id'); } catch(e){}
            if (!bid) { alert('No booking selected to cancel yet.'); return; }
            if (typeof HausTapBookingAPI === 'undefined') { alert('Cancellation not available in preview mode.'); return; }
            window.htConfirm('Cancel this booking?', { title:'Cancel Booking', okText:'Cancel Booking', cancelText:'Keep Booking' })
              .then(function(go){ if (!go) return; HausTapLoading && HausTapLoading.show && HausTapLoading.show();
                HausTapBookingAPI.cancelBooking(bid)
                  .then(function(){ refresh(); })
                  .catch(function(err){ console.error('Cancel failed', err); alert('Unable to cancel right now.'); })
                  .finally(function(){ HausTapLoading && HausTapLoading.hide && HausTapLoading.hide(); });
              });
          });
        }
      } catch(e) { /* ignore */ }
    })();
  </script>
</body>
</html>
