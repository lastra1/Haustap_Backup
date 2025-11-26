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
      <div class="bigcal-header">
        <button class="bigcal-nav bigcal-prev"><i class="fa-solid fa-chevron-left"></i></button>
        <span class="bigcal-title"></span>
        <button class="bigcal-nav bigcal-next"><i class="fa-solid fa-chevron-right"></i></button>
      </div>
      <div class="bigcal-days">
        <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
      </div>
      <div class="bigcal-grid"></div>
      

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
      var savedDate = null;
      function pad2(n){ return String(n).padStart(2,'0'); }
      try { savedDate = localStorage.getItem('selected_date') || null; } catch(e){}
      // Read stored service label and display without overwriting
      var subcat = document.querySelector('.subcategory-btn');
      var storedServiceName = '';
      try { storedServiceName = localStorage.getItem('selected_service_name') || ''; } catch(e){}
      if (subcat && storedServiceName) {
        subcat.innerHTML = '<b>' + storedServiceName + '</b>';
      }

      var labels = [];

      var timeSelect = document.querySelector('.time-select');
      var bigGrid = document.querySelector('.bigcal-grid');
      var bigTitle = document.querySelector('.bigcal-title');
      var prevBtn = document.querySelector('.bigcal-prev');
      var nextBtn = document.querySelector('.bigcal-next');
      var today = new Date(); today.setHours(0,0,0,0);
      var shownMonth = new Date(today.getFullYear(), today.getMonth(), 1);

      function ymd(date){ return date.getFullYear()+'-'+pad2(date.getMonth()+1)+'-'+pad2(date.getDate()); }
      function renderCalendar(){
        if (!bigGrid || !bigTitle) return;
        bigTitle.textContent = shownMonth.toLocaleString('en-US', { month: 'long', year: 'numeric' });
        bigGrid.innerHTML = '';
        var firstDow = new Date(shownMonth.getFullYear(), shownMonth.getMonth(), 1).getDay();
        var daysIn = new Date(shownMonth.getFullYear(), shownMonth.getMonth()+1, 0).getDate();
        for (var i=0;i<firstDow;i++){ var blank=document.createElement('div'); blank.className='bigcal-cell blank'; bigGrid.appendChild(blank); }
        for (var d=1; d<=daysIn; d++){
          var date = new Date(shownMonth.getFullYear(), shownMonth.getMonth(), d);
          var cell = document.createElement('div'); cell.className='bigcal-cell';
          var past = date < today;
          var dow = date.getDay();
          var available = dow !== 0 && dow !== 6;
          if (past || !available) cell.classList.add('disabled');
          var num=document.createElement('span'); num.className='bigcal-num'; num.textContent=String(d);
          cell.appendChild(num);
          if (!past && available){
            cell.addEventListener('click', function(dt){ return function(){
              var iso=ymd(dt);
              selectedDate = iso;
              Array.prototype.slice.call(bigGrid.querySelectorAll('.bigcal-cell.selected')).forEach(function(el){ el.classList.remove('selected'); });
              this.classList.add('selected');
              fetchAvailabilityAndTimes(iso);
            };}(date));
          }
          if (savedDate && savedDate === ymd(date)) { cell.classList.add('selected'); selectedDate = savedDate; }
          bigGrid.appendChild(cell);
        }
      }
      renderCalendar();
      if (prevBtn) prevBtn.addEventListener('click', function(){ shownMonth = new Date(shownMonth.getFullYear(), shownMonth.getMonth()-1, 1); renderCalendar(); });
      if (nextBtn) nextBtn.addEventListener('click', function(){ shownMonth = new Date(shownMonth.getFullYear(), shownMonth.getMonth()+1, 1); renderCalendar(); });
      function setTimeSlots(slots){
        if (!timeSelect) return;
        timeSelect.innerHTML = '';
        if (!slots || !slots.length){
          var opt = document.createElement('option'); opt.textContent = 'No available times'; opt.disabled = true; opt.selected = true; timeSelect.appendChild(opt); return;
        }
        var first = document.createElement('option'); first.textContent = 'Select Time'; first.disabled = true; first.selected = true; timeSelect.appendChild(first);
        slots.forEach(function(s){ var opt=document.createElement('option'); opt.value=s; opt.textContent=s; timeSelect.appendChild(opt); });
      }
      function fetchAvailabilityAndTimes(iso){
        if (!iso) return;
        try {
          fetch('/api/bookings/availability?date='+encodeURIComponent(iso))
            .then(function(r){ return r.ok ? r.json() : Promise.reject(); })
            .then(function(res){ if (res && res.success){ setTimeSlots(res.available ? res.slots: []); } })
            .catch(function(){ /* keep default slots */ });
        } catch(_){ }
      }
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
        if (!selectedDate) { alert('Please pick a date from the calendar.'); return; }
        try {
          if (selectedDate) localStorage.setItem('selected_date', selectedDate);
          if (selectedTime) localStorage.setItem('selected_time', selectedTime);
          var token = localStorage.getItem('haustap_token');
          var userKey = (function(){
            var c=[localStorage.getItem('haustap_user_id'),localStorage.getItem('user_id'),localStorage.getItem('haustap_uid'),localStorage.getItem('haustap_email'),localStorage.getItem('user_email'),localStorage.getItem('email'),localStorage.getItem('haustap_token')];
            for (var i=0;i<c.length;i++){ var v=c[i]; if (v && String(v).trim()!=='') return String(v).trim(); } return ''; })();
          if (token && userKey && selectedTime){
            var payload={ user_key:userKey, date:selectedDate, time:selectedTime, service: (localStorage.getItem('selected_service_name')||'') };
            fetch('/api/bookings/hold', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload) })
              .then(function(r){ return r.ok ? r.json() : Promise.reject(); })
              .then(function(res){ if (res && res.success && res.data && res.data.id) { localStorage.setItem('booking_hold_id', res.data.id); } })
              .catch(function(){});
          }
        } catch {}
        window.location.href = '/booking/overview';
      });
    })();
  </script>
</body>
</html>

