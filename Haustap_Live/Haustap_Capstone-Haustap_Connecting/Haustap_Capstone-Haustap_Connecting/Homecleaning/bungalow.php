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
    <button class="cleaning-type-btn">Bungalow</button>
    <div class="cleaning-cards-container">
      <div class="cleaning-cards-row">
        <div class="cleaning-card">
          <input type="radio" name="cleaning" class="cleaning-radio" id="basic-cleaning">
          <label for="basic-cleaning" class="radio-label"></label>
          <div class="cleaning-title">Basic Cleaning &ndash; 1 Cleaner</div>
          <div class="cleaning-price">₱1,000</div>
          <div class="cleaning-inclusions-title">Inclusions:</div>
          <ul class="cleaning-inclusions">
            <li>Living Room: walls, mop, dusting furniture, trash removal</li>
            <li>Bedroom: bed making, sweeping, dusting, trash removal</li>
            <li>Hallways: mop & sweep, remove cobwebs</li>
            <li>Windows & Mirrors: quick wipe</li>
          </ul>
        </div>
        <div class="cleaning-card">
          <input type="radio" name="cleaning" class="cleaning-radio" id="standard-cleaning">
          <label for="standard-cleaning" class="radio-label"></label>
        <div class="cleaning-title">Standard Cleaning &ndash; 2 Cleaners</div>
          <div class="cleaning-price">₱2,000</div>
          <div class="cleaning-inclusions-title">Inclusions:</div>
          <ul class="cleaning-inclusions">
            <li>All Basic Cleaning tasks plus:</li>
            <li>Kitchen: wipe countertops, sink cleaning, stove top degrease, trash removal</li>
            <li>Bathrooms: scrub toilet, sink, shower, floor disinfecting</li>
            <li>Furniture: cleaning under/behind furniture</li>
            <li>Windows & Mirrors: full wipe & polish</li>
          </ul>
        </div>
      </div>
      <div class="cleaning-cards-row">
        <div class="cleaning-card wide">
          <input type="radio" name="cleaning" class="cleaning-radio" id="deep-cleaning">
          <label for="deep-cleaning" class="radio-label"></label>
        <div class="cleaning-title">Deep Cleaning &ndash; 3 Cleaners</div>
          <div class="cleaning-price">₱3,000</div>
          <div class="cleaning-inclusions-title">Inclusions:</div>
          <ul class="cleaning-inclusions">
            <li>All Standard Cleaning tasks plus:</li>
            <li>Flooring: scrubbing tiles/grout, polishing if applicable</li>
            <li>Appliances: behind refrigerator, oven, washing machine</li>
            <li>Carpets/Rugs: vacuum or shampoo</li>
            <li>Disinfection: doorknobs, switches, high-touch surfaces</li>
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
      function hasToken(){
        try { return !!localStorage.getItem('haustap_token'); } catch(e){ return false; }
      }
      const nextLink = document.querySelector('nav.pagination ul li:last-child a');
      const radios = document.querySelectorAll('input.cleaning-radio');
      const cards = document.querySelectorAll('.cleaning-card');
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

      function getSelectedPrice() {
        const checked = getSelectedRadio();
        const card = checked ? checked.closest('.cleaning-card') : null;
        const priceEl = card ? card.querySelector('.cleaning-price') : null;
        if (!priceEl) return 0;
        const num = Number(String(priceEl.textContent||'').replace(/[^0-9.]/g,''));
        return isNaN(num) ? 0 : num;
      }

      function persistLabel() {
        const house = typeBtn ? (typeBtn.textContent || '').trim() : '';
        const title = getSelectedTitle();
        const label = house && title ? `${house} - ${title}` : house || title;
        try {
          localStorage.setItem('selected_service_name', label);
          localStorage.setItem('selected_services_names', JSON.stringify([label]));
        } catch(e){}
        try { localStorage.setItem('selected_house_slug', getHouseSlug()); } catch(e){}
        const id = getSelectedRadio()?.id || null;
        try { localStorage.setItem('selected_cleaning_type', normalizeCleaning(id) || ''); } catch(e){}
      }

      radios.forEach(radio => {
        radio.addEventListener('change', () => {
          const id = getSelectedRadio()?.id || null;
          const type = normalizeCleaning(id);
          const house = getHouseSlug();
          const href = (type && house)
            ? `/booking_process/booking_location.php?house=${encodeURIComponent(house)}&cleaning=${encodeURIComponent(type)}`
            : '#';
          if (nextLink) nextLink.setAttribute('href', href);
        persistLabel();
        try { localStorage.setItem('selected_service_price', String(getSelectedPrice())); } catch(e){}
      });
      });

      if (nextLink) {
        nextLink.addEventListener('click', function (e) {
          const id = getSelectedRadio()?.id || null;
          const type = normalizeCleaning(id);
          const house = getHouseSlug();
          if (!type) {
            e.preventDefault();
            alert('Please select a cleaning type first.');
            return;
          }
          persistLabel();
          try { localStorage.setItem('selected_service_price', String(getSelectedPrice())); } catch(e){}
          e.preventDefault();
          window.location.href = `/booking_process/booking_location.php?house=${encodeURIComponent(house)}&cleaning=${encodeURIComponent(type)}`;
        });
      }

      var floatProceed = document.getElementById('ht-float-proceed');
      if (floatProceed) {
        floatProceed.addEventListener('click', function(){
          const id = getSelectedRadio()?.id || null;
          const type = normalizeCleaning(id);
          const house = getHouseSlug();
          if (!type) { alert('Please select a cleaning type first.'); return; }
          persistLabel();
          try { localStorage.setItem('selected_service_price', String(getSelectedPrice())); } catch(e){}
          window.location.href = `/booking_process/booking_location.php?house=${encodeURIComponent(house)}&cleaning=${encodeURIComponent(type)}`;
        });
      }

      // Toggle selection when clicking anywhere on the cleaning card
      cards.forEach(function(card){
        card.addEventListener('click', function(ev){
          var input = card.querySelector('input.cleaning-radio');
          if (!input) return;
          if (ev.target === input) { return; }
          input.checked = true;
          var id = getSelectedRadio()?.id || null;
          var type = normalizeCleaning(id);
          var house = getHouseSlug();
          var href = (type && house) ? `/booking_process/booking_location.php?house=${encodeURIComponent(house)}&cleaning=${encodeURIComponent(type)}` : '#';
          if (nextLink) nextLink.setAttribute('href', href);
          persistLabel();
          try { localStorage.setItem('selected_service_price', String(getSelectedPrice())); } catch(e){}
        }, true);
      });
    });
  </script>
</body>
</html>



