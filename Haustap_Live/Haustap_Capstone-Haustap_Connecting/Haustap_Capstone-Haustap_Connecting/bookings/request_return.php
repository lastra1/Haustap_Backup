<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HausTap Bookings</title>
  <link rel="stylesheet" href="/css/global.css">
  <link rel="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/request_return.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="/client/css/homepage.css">
<script>
  // Ensure the page uses local mock API during preview
  // so it works without the backend running.
  window.API_BASE_OVERRIDE = (window.location.origin || '') + '/mock-api';
</script>
<script src="/login_sign up/js/api.js"></script>
<script src="/client/js/booking-api.js"></script>
</head>
<body><?php include dirname(__DIR__) . "/client/includes/header.php"; ?>

<!-- REQUEST RETURN PAGE -->
<section class="request-return-page">

  <!-- Header outside the boxes -->
  <h2 class="request-return-title">Request Return</h2>

  <!-- Box 1 -->
  <div class="request-box">
    <div class="help-section">
      <h3>How Can We Help?</h3>
      <div class="help-right">
        <span class="booking-ref">—</span>
        <span class="free-return">FREE RETURN</span>
      </div>
    </div>
  </div>

  <!-- Box 2 -->
  <div class="request-box">
    <div class="product-section">
      <h3>Product You Want to Return</h3>
      <p class="product-title">Home Cleaning</p>
      <p class="product-sub">Bungalow - Basic Cleaning</p>
    </div>
  </div>

  <!-- Box 3 -->
  <div class="request-box">
    <div class="reason-section">
      <h3>Why Do You Want to Return?</h3>

      <div class="reason-row">
        <label for="reason">Reason:</label>
        <select id="reason" name="reason" required>
        <option value="" disabled selected hidden>Select a reason</option>
          <option>Service not delivered (no-show)</option>
          <option>Incomplete service (not finished)</option>
          <option>Wrong service provided</option>
          <option>Poor service quality</option>
          <option>Tools/Equipment issue</option>
          <option>Property damaged during service</option>
          <option>Miscommunication in booking details</option>
          <option>Safety concern</option>
          <option>Promised inclusions not delivered</option>
          <option>Double booking issue</option>
          <option>Changed mind after service result</option>
          <option>Others (please specify)</option>
        </select>
      </div>

      <div class="description-row">
        <label for="description">Description:</label>
        <textarea id="description" placeholder="Leave your comments here"></textarea>
      </div>

      <div class="button-row">
        <button class="cancel-btn" id="btnCancel">Cancel</button>
        <button class="submit-btn" id="btnSubmit">Submit</button>
      </div>
    </div>
  </div>

</section>

<script>
  (function(){
    function qs(name){ try { return new URLSearchParams(window.location.search).get(name); } catch(e){ return null; } }
    function parseDateTime(dateStr, timeStr){
      try {
        if (!dateStr) return null;
        var t = (timeStr || '12:00 PM');
        var d = new Date(dateStr + ' ' + t);
        return isNaN(d.getTime()) ? null : d;
      } catch(e){ return null; }
    }

    var bookingId = qs('id');
    var refEl = document.querySelector('.booking-ref');
    var freeEl = document.querySelector('.free-return');
    var titleEl = document.querySelector('.product-title');
    var subEl = document.querySelector('.product-sub');
    var reasonEl = document.getElementById('reason');
    var descEl = document.getElementById('description');
    var btnCancel = document.getElementById('btnCancel');
    var btnSubmit = document.getElementById('btnSubmit');

    function markFreeOrFee(b){
      // Prefer completed_at for accurate fee logic; fallback to scheduled date/time.
      var now = new Date();
      var comp = null;
      try { comp = b.completed_at ? new Date(b.completed_at) : null; } catch(e) { comp = null; }
      var dt = comp && !isNaN(comp.getTime()) ? comp : parseDateTime(b.scheduled_date, b.scheduled_time);
      var free = dt ? ((now - dt) <= 24*3600*1000) : true;
      freeEl.textContent = free ? 'FREE RETURN' : '₱300 RETURN FEE';
    }

    function hydrate(){
      if (!window.HausTapBookingAPI || !bookingId) {
        // Static preview
        refEl.textContent = String(bookingId || '—');
        return;
      }
      window.HausTapBookingAPI.listBookings()
        .then(function(resp){
          var arr = [];
          if (resp && resp.data && resp.data.data && Array.isArray(resp.data.data)) arr = resp.data.data;
          else if (resp && resp.data && Array.isArray(resp.data)) arr = resp.data;
          else if (Array.isArray(resp)) arr = resp;
          var found = arr.find(function(x){ return String(x.id||'') === String(bookingId||''); });
          if (!found) return;
          titleEl.textContent = found.service_name || 'Service';
          subEl.textContent = found.service_sub || (found.service_type || '');
          refEl.textContent = String(found.id||'—');
          markFreeOrFee(found);
        }).catch(function(){
          refEl.textContent = String(bookingId || '—');
        });
    }

    function submit(){
      if (!bookingId || !window.HausTapBookingAPI) { alert('Return not available in preview mode.'); return; }
      var reason = reasonEl.value || '';
      if (!reason) { alert('Please select a reason.'); return; }
      var payload = { issues: [reason], notes: descEl.value || '' };
      btnSubmit.disabled = true; btnSubmit.textContent = 'Submitting…';
      window.HausTapBookingAPI.requestReturn(bookingId, payload)
        .then(function(resp){
          var fee = (resp && resp.data && typeof resp.data.fee !== 'undefined') ? Number(resp.data.fee) : null;
          var msg = 'Return requested successfully.';
          if (fee !== null) { msg += ' Fee: ₱' + fee; }
          alert(msg);
          // Redirect to bookings page Return tab
          window.location.href = '/bookings/booking.php#return';
        }).catch(function(err){
          console.error('Return request failed', err);
          alert((err && err.message) ? err.message : 'Unable to submit return request right now.');
        }).finally(function(){
          btnSubmit.disabled = false; btnSubmit.textContent = 'Submit';
        });
    }

    document.addEventListener('DOMContentLoaded', function(){
      hydrate();
      if (btnCancel) btnCancel.addEventListener('click', function(){ window.location.href = '/bookings/booking.php'; });
      if (btnSubmit) btnSubmit.addEventListener('click', submit);
    });
  })();
</script>

 <!-- FOOTER -->
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
</body>
</html>
