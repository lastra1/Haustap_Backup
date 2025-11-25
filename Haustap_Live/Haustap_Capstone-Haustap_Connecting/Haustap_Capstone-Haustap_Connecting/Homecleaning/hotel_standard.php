<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Type of Cleaning | Homi</title>
  <link rel="stylesheet" href="/css/global.css">
  <link rel="stylesheet" href="css/indoor-cleaning.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/client/css/homepage.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head>
<body>
<?php include dirname(__DIR__) . "/client/includes/header.php"; ?>
  <main>
    <h1 class="main-title">Type of Cleaning</h1>
    <button class="cleaning-type-btn">Hotel-Standard</button>
    <div class="cleaning-cards-container">
      <div class="cleaning-cards-row">
        <div class="cleaning-card">
          <input type="radio" name="cleaning" class="cleaning-radio" id="basic-cleaning">
          <label for="basic-cleaning" class="radio-label"></label>
          <div class="cleaning-title">Basic Cleaning &ndash; 1 Cleaner</div>
          <div class="cleaning-price">₱600</div>
          <div class="cleaning-inclusions-title">Inclusions:</div>
          <ul class="cleaning-inclusions">
            <li>Bed area: tidy up, sweep, mop, dust</li>
            <li>Trash disposal</li>
            <li>Mirror/glass quick wipe</li>
          </ul>
        </div>
        <div class="cleaning-card">
          <input type="radio" name="cleaning" class="cleaning-radio" id="standard-cleaning">
          <label for="standard-cleaning" class="radio-label"></label>
          <div class="cleaning-title">Standard Cleaning &ndash; 1-2 Cleaners</div>
          <div class="cleaning-price">₱1,000</div>
          <div class="cleaning-inclusions-title">Inclusions:</div>
          <ul class="cleaning-inclusions">
            <li>All Basic tasks</li>
            <li>Bathroom scrub (toilet, sink, shower)</li>
            <li>Light kitchen/counter wipe (if any)</li>
          </ul>
        </div>
      </div>
      <div class="cleaning-cards-row">
        <div class="cleaning-card wide">
          <input type="radio" name="cleaning" class="cleaning-radio" id="deep-cleaning">
          <label for="deep-cleaning" class="radio-label"></label>
          <div class="cleaning-title">Deep Cleaning &ndash; 2 Cleaners</div>
          <div class="cleaning-price">₱1,800</div>
          <div class="cleaning-inclusions-title">Inclusions:</div>
          <ul class="cleaning-inclusions">
            <li>All Standard tasks</li>
            <li>Carpet shampoo/vacuum</li>
            <li>Disinfection of switches, doorknobs</li>
          </ul>
        </div>
      </div>
      <div class="cleaning-note">Cleaning materials are provided by the client</div>
      <nav class="pagination">
        <ul>
          <li><a href="#">&laquo;</a></li>
          
          
          
          
          
          <li><a href="#">&raquo;</a></li>
        </ul>
      </nav>
    </div>
  </main>
  <!-- FOOTER -->
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
  <script>
    // Booking flow: build label, persist, and route to booking_location with params
    document.addEventListener('DOMContentLoaded', function () {
      const nextLink = document.querySelector('nav.pagination ul li:last-child a');
      const radios = document.querySelectorAll('input.cleaning-radio');
      const typeBtn = document.querySelector('.cleaning-type-btn');

      function getSelectedRadio() {
        return document.querySelector('input.cleaning-radio:checked');
      }

      function normalizeCleaning(id) {
        return id ? id.replace('-cleaning', '') : null;
      }

      function getHouseSlug() {
        const txt = typeBtn ? (typeBtn.textContent || '').trim() : '';
        return txt.toLowerCase().replace(/\s+/g, '-');
      }

      function getSelectedTitle() {
        const checked = getSelectedRadio();
        const card = checked ? checked.closest('.cleaning-card') : null;
        const titleEl = card ? card.querySelector('.cleaning-title') : null;
        return titleEl ? titleEl.textContent.trim() : '';
      }

      function persistLabel() {
        const house = typeBtn ? (typeBtn.textContent || '').trim() : '';
        const title = getSelectedTitle();
        const label = house && title ? `${house} - ${title}` : house || title;
        try { localStorage.setItem('selected_service_name', label); } catch(e){}
      }

      radios.forEach(radio => {
        radio.addEventListener('change', () => {
          const id = getSelectedRadio()?.id || null;
          const type = normalizeCleaning(id);
          const house = getHouseSlug();
          const href = (type && house) ? `/booking_process/booking_location.php?house=${encodeURIComponent(house)}&cleaning=${encodeURIComponent(type)}` : '#';
          nextLink && nextLink.setAttribute('href', href);
          persistLabel();
          if (type && house) {
            window.location.href = href;
          }
});
      });

      nextLink && nextLink.addEventListener('click', function (e) {
        const id = getSelectedRadio()?.id || null;
        const type = normalizeCleaning(id);
        const house = getHouseSlug();
        if (!type) {
          e.preventDefault();
          alert('Please select a cleaning type first.');
          return;
        }
        persistLabel();
        e.preventDefault();
        window.location.href = `/booking_process/booking_location.php?house=${encodeURIComponent(house)}&cleaning=${encodeURIComponent(type)}`;
      });
    });
  </script>
</body>
</html>



