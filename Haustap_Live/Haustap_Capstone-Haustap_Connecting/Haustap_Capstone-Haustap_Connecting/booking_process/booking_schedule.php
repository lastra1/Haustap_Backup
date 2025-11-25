<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Booking Schedule</title>
  <link rel="stylesheet" href="/css/global.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/booking_process/css/booking_schedule.css" />
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

<main class="booking-schedule">
    <h1 class="page-title">Booking Schedule</h1>
    <button class="subcategory-btn"><b>Bungalow</b></button>

    <section class="schedule-box">
      <h2 class="section-title">DATE</h2>
      <div class="date-grid"><!-- Dates are generated dynamically via JS --></div>

      <h2 class="section-title">TIME</h2>
      <select class="time-select">
        <option selected disabled>Select Time</option>
        <option value="08:00">8:00 AM</option>
        <option value="09:00">9:00 AM</option>
        <option value="10:00">10:00 AM</option>
        <option value="11:00">11:00 AM</option>
        <option value="12:00">12:00 PM</option>
        <option value="13:00">1:00 PM</option>
        <option value="14:00">2:00 PM</option>
        <option value="15:00">3:00 PM</option>
        <option value="16:00">4:00 PM</option>
      </select>
    </section>

    <div class="pagination">
      <button class="back-btn">&lt;</button>
      
      
      
      
      
      <button class="next-btn">&gt;</button>
    </div>
  </main>

  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
  <script>
    (function(){
      var selectedDate = null;
      var selectedTime = null;
      // Dynamically generate date boxes for the next 14 days
      var dateGrid = document.querySelector('.date-grid');
      var DAYS_TO_SHOW = 7; // Show next 7 days
      var today = new Date();
      var savedDate = null;
      try { savedDate = localStorage.getItem('selected_date') || null; } catch(e){}

      // Helpers to format dates in Philippines timezone (Asia/Manila)
      var TZ = 'Asia/Manila';
      function pad2(n){ return String(n).padStart(2,'0'); }
      function fmtParts(ts){
        var parts = new Intl.DateTimeFormat('en-US', {
          timeZone: TZ,
          year: 'numeric',
          month: '2-digit',
          day: '2-digit',
          weekday: 'long'
        }).formatToParts(new Date(ts));
        var out = {};
        parts.forEach(function(p){ out[p.type] = p.value; });
        return out; // {year, month, day, weekday}
      }
      function monthShort(ts){
        return new Intl.DateTimeFormat('en-US', { timeZone: TZ, month: 'short' }).format(new Date(ts));
      }

      for (var i = 0; i < DAYS_TO_SHOW; i++) {
        var ts = today.getTime() + i * 86400000; // add i days in ms
        var p = fmtParts(ts);
        var iso = p.year + '-' + p.month + '-' + p.day; // YYYY-MM-DD in Manila
        var label = document.createElement('label');
        label.className = 'date-box' + (i === 0 ? ' today' : '');
        label.setAttribute('data-date', iso);
        var input = document.createElement('input');
        input.type = 'radio';
        input.name = 'date';
        var div = document.createElement('div');
        div.className = 'date-text';
        var dateLine = '<strong>' + monthShort(ts) + ' ' + pad2(Number(p.day)) + ', ' + p.year + '</strong>';
        var dayLine = (i === 0) ? (p.weekday + ' - Today') : p.weekday;
        div.innerHTML = dateLine + '<br>' + dayLine;
        label.appendChild(input);
        label.appendChild(div);
        dateGrid && dateGrid.appendChild(label);
        if (savedDate && savedDate === iso) {
          input.checked = true;
          selectedDate = iso;
          label.style.outline = '2px solid #009999';
        }
      }
      // Read stored service label and display without overwriting
      var subcat = document.querySelector('.subcategory-btn');
      var storedServiceName = '';
      try { storedServiceName = localStorage.getItem('selected_service_name') || ''; } catch(e){}
      if (subcat && storedServiceName) {
        subcat.innerHTML = '<b>' + storedServiceName + '</b>';
      }

      var labels = Array.prototype.slice.call(document.querySelectorAll('.date-box'));
      labels.forEach(function(label){
        var input = label.querySelector('input[type="radio"]');
        var iso = label.getAttribute('data-date');
        if (input) {
          input.addEventListener('change', function(){
            selectedDate = iso || null;
            labels.forEach(function(l){ l.style.outline = 'none'; });
            label.style.outline = '2px solid #009999';
          });
        }
        // Also allow clicking the label itself
        label.addEventListener('click', function(){
          selectedDate = iso || null;
          labels.forEach(function(l){ l.style.outline = 'none'; });
          label.style.outline = '2px solid #009999';
          var inp = label.querySelector('input[type="radio"]');
          if (inp) inp.checked = true;
        });
      });

      var timeSelect = document.querySelector('.time-select');
      if (timeSelect) {
        // Restore previously saved time if available
        try {
          var savedTime = localStorage.getItem('selected_time');
          if (savedTime) {
            timeSelect.value = savedTime;
            selectedTime = savedTime;
          } else {
            selectedTime = timeSelect.value && timeSelect.value !== 'Select Time' ? timeSelect.value : null;
          }
        } catch(e) {
          selectedTime = timeSelect.value && timeSelect.value !== 'Select Time' ? timeSelect.value : null;
        }
        timeSelect.addEventListener('change', function(){
          selectedTime = timeSelect.value && timeSelect.value !== 'Select Time' ? timeSelect.value : null;
        });
      }

      var backBtn = document.querySelector('.back-btn');
      if (backBtn) backBtn.addEventListener('click', function(){ window.location.href = '/booking/choose-sp'; });

      var nextBtn = document.querySelector('.next-btn');
      if (nextBtn) nextBtn.addEventListener('click', function(){
        // Default to first labeled date if none explicitly selected
        if (!selectedDate) {
          var first = document.querySelector('.date-box');
          selectedDate = first ? first.getAttribute('data-date') : null;
        }
        try {
          if (selectedDate) localStorage.setItem('selected_date', selectedDate);
          if (selectedTime) localStorage.setItem('selected_time', selectedTime);
        } catch {}
        window.location.href = '/booking/overview';
      });
    })();
  </script>
</body>
</html>

