<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Cleaning Services | Homi</title>
  <link rel="stylesheet" href="/Homecleaning/css/aircon-deep-clean.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/client/css/homepage.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head>
<body>
<?php include dirname(__DIR__) . "/client/includes/header.php"; ?>
  <main>
    <h1 class="main-title">Cleaning Services</h1>
    <button class="subcategory-btn">SUBCATEGORY</button>
    <div class="centered-section">
      <div class="breadcrumbs">
        <a href="/services/cleaning">Home cleaning</a>
        <span> | </span>
        <a href="/services/cleaning/ac">AC cleaning</a>
        <span> | </span>
        <a href="/services/cleaning/ac-deep" aria-current="page">AC Deep Cleaning (Chemical Cleaning)</a>
      </div>
    </div>
    <!-- tabs removed per request; breadcrumbs provide navigation -->
    <div class="services-container">
      <div class="service-card">
        <input type="radio" name="service" class="service-radio" id="window-1">
        <label for="window-1" class="radio-label"></label>
        <div class="service-title">Window Type (0.5 HP &ndash; 1.5 HP)</div>
        <div class="service-price">₱1,500/unit</div>
        <ul>
          <li>Full disassembly of unit</li>
          <li>Cleaning of evaporator coil & condenser</li>
          <li>Cleaning of blower, drain pan, and drain pipe</li>
          <li>Flushing of drain line to avoid heavy dirt/clogs</li>
          <li>Change of parts if necessary</li>
          <li>Thorough safety assessment</li>
          <li>Test run and performance check</li>
        </ul>
      </div>
      <div class="service-card">
        <input type="radio" name="service" class="service-radio" id="split-1">
        <label for="split-1" class="radio-label"></label>
        <div class="service-title">Split Type (0.5 HP &ndash; 1.5 HP)</div>
        <div class="service-price">₱1,500/unit</div>
        <ul>
          <li>Full disassembly of unit</li>
          <li>Cleaning of evaporator coil & condenser</li>
          <li>Cleaning of blower, drain pan, and drain pipe</li>
          <li>Flushing of drain line to avoid heavy dirt/clogs</li>
          <li>Change of parts if necessary</li>
          <li>Thorough safety assessment</li>
          <li>Test run and performance check</li>
        </ul>
      </div>
      <div class="service-card">
        <input type="radio" name="service" class="service-radio" id="window-2">
        <label for="window-2" class="radio-label"></label>
        <div class="service-title">Window Type (2.0 HP &ndash; 2.5 HP)</div>
        <div class="service-price">₱1,800/unit</div>
        <ul>
          <li>Full disassembly of unit</li>
          <li>Cleaning of evaporator coil & condenser</li>
          <li>Cleaning of blower, drain pan, and drain pipe</li>
          <li>Flushing of drain line to avoid heavy dirt/clogs</li>
          <li>Change of parts if necessary</li>
          <li>Thorough safety assessment</li>
          <li>Test run and performance check</li>
        </ul>
      </div>
      <div class="service-card">
        <input type="radio" name="service" class="service-radio" id="split-2">
        <label for="split-2" class="radio-label"></label>
        <div class="service-title">Split Type (2.0 HP &ndash; 2.5 HP)</div>
        <div class="service-price">₱1,800/unit</div>
        <ul>
          <li>Full disassembly of unit</li>
          <li>Cleaning of evaporator coil & condenser</li>
          <li>Cleaning of blower, drain pan, and drain pipe</li>
          <li>Flushing of drain line to avoid heavy dirt/clogs</li>
          <li>Change of parts if necessary</li>
          <li>Thorough safety assessment</li>
          <li>Test run and performance check</li>
        </ul>
      </div>
      <div class="service-card wide">
        <input type="radio" name="service" class="service-radio" id="split-3">
        <label for="split-3" class="radio-label"></label>
        <div class="service-title">Split Type (3.0 HP and above)</div>
        <div class="service-price">₱2,000/unit</div>
        <ul>
          <li>Full disassembly of unit</li>
          <li>Cleaning of evaporator coil & condenser</li>
          <li>Cleaning of blower, drain pan, and drain pipe</li>
          <li>Flushing of drain line to avoid heavy dirt/clogs</li>
          <li>Change of parts if necessary</li>
          <li>Thorough safety assessment</li>
          <li>Test run and performance check</li>
        </ul>
      </div>
    </div>
  </main>
  <!-- FOOTER -->
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
  <script>
    // On AC deep clean selection, proceed to Booking Location with label
    document.addEventListener('DOMContentLoaded', function(){
      const radios = document.querySelectorAll('input.service-radio');
      function getLabel(radio){
        const card = radio.closest('.service-card');
        const title = card ? card.querySelector('.service-title') : null;
        return title ? (title.textContent || '').trim() : '';
      }
      function proceed(radio){
        const label = getLabel(radio);
        try { localStorage.setItem('selected_service_name', label); } catch(e){}
        window.location.href = '/booking_process/booking_location.php?service=' + encodeURIComponent(label);
      }
      radios.forEach(function(r){
        r.addEventListener('change', function(){ if (r.checked) proceed(r); });
        r.addEventListener('click', function(){ if (r.checked) proceed(r); });
      });
    });
  </script>
</body>
</html>


