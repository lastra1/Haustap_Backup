<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mobile Phone</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/tech_gadget/css/mobile_phone.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="/client/css/homepage.css"></head>

<body>
  <!-- HEADER -->
    <?php include dirname(__DIR__) . "/client/includes/header.php"; ?>

  <!-- MAIN CONTENT -->
  <main>
  <h1 class="main-title">Tech & Gadget</h1>
  <button class="subcategory-btn">SUBCATEGORY</button>

  <!-- Subcategory Navigation -->
  <nav class="subcategory-nav">
    <ul>
      <li class="active"><a href="/tech_gadget/mobile_phone.php">Mobile Phone</a></li>
      <li><a href="/tech_gadget/laptop_desktop.php">Laptop & Desktop PC</a></li>
      <li><a href="/tech_gadget/tablet_ipad.php">Tablet & iPad</a></li>
      <li><a href="/tech_gadget/game_console.php">Game & Console</a></li>
    </ul>
  </nav>

  <div class="services-container">
    <section class="service-section">
      <div class="service-grid">
        <label class="service-card">
          <input type="checkbox" class="service-check" />
          <div class="service-content">
            <h3>Charging Port Repair - iOS</h3>
            <p class="price">â‚±600 per unit</p>
            <p><strong>Inclusions:</strong><br>
              Detailed check of charging dock/port<br>
              Cleaning or replacement of faulty port<br>
              Re-soldering and alignment adjustments<br>
              Charging and data transfer test
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="checkbox" class="service-check" />
          <div class="service-content">
            <h3>Screen Replacement - Android</h3>
            <p class="price">â‚±500 per unit</p>
            <p><strong>Inclusions:</strong><br>
              Removal of broken/damaged screen<br>
              Installation of replacement screen (customer-provided or stock)<br>
              Functionality test for touch and display<br>
              Basic cleaning of device exterior
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="checkbox" class="service-check" />
          <div class="service-content">
            <h3>Screen Replacement - iOS</h3>
            <p class="price">â‚±700 per unit</p>
            <p><strong>Inclusions:</strong><br>
              Safe removal of damaged screen<br>
              Installation of replacement screen<br>
              Touch responsiveness and display quality test<br>
              Device assembly and sealing check
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="checkbox" class="service-check" />
          <div class="service-content">
            <h3>Battery Replacement - Android</h3>
            <p class="price">â‚±300 per unit</p>
            <p><strong>Inclusions:</strong><br>
              Removal of old/non-working battery<br>
              Installation of new battery (customer-provided or stock)<br>
              Power-on and charging function test<br>
              Proper disposal of defective battery
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="checkbox" class="service-check" />
          <div class="service-content">
            <h3>Battery Replacement - iOS</h3>
            <p class="price">â‚±500 per unit</p>
            <p><strong>Inclusions:</strong><br>
              Safe removal of swollen/damaged battery<br>
              Installation of new battery<br>
              Power, charging, and battery health check<br>
              Device reassembly with secure fitting
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="checkbox" class="service-check" />
          <div class="service-content">
            <h3>Charging Port Repair - Android</h3>
            <p class="price">â‚±400 per unit</p>
            <p><strong>Inclusions:</strong><br>
              Diagnosis of charging port issue<br>
              Cleaning or replacement of charging port<br>
              Soldering work if required<br>
              Testing for charging stability and USB connection
            </p>
          </div>
        </label>
      </div>
    </section>
  </div>
</main>


  <!-- FOOTER -->
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
  <div class="action-bar">
    <button class="proceed-btn">Proceed</button>
    <div class="total-box">Total: ₱0.00</div>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      var checks = Array.prototype.slice.call(document.querySelectorAll('label.service-card input.service-check'));
      var activeSubcat = document.querySelector('.subcategory-nav li.active');
      var proceedBtn = document.querySelector('.action-bar .proceed-btn');
      var totalBox = document.querySelector('.action-bar .total-box');
      function norm(t){ return String(t||'').replace(/\s+/g,' ').trim(); }
      function priceNum(txt){ var cleaned = String(txt||'').replace(/,/g,''); var m = cleaned.match(/(\d+(?:\.\d+)?)/); return m ? Number(m[1]) : 0; }
      function labelFor(card){ var titleEl = card ? card.querySelector('.service-content h3') : null; var subcat = activeSubcat ? norm(activeSubcat.textContent) : 'Tech & Gadget'; var t = titleEl ? norm(titleEl.textContent) : ''; return subcat + ' - ' + t; }
      function formatPHP(v){ return '₱' + Number(v||0).toFixed(2); }
      function compute(){
        var names = [];
        var prices = [];
        checks.forEach(function(c){ if (c.checked){ var card = c.closest('.service-card'); var name = labelFor(card); var pEl = card ? card.querySelector('.price') : null; var p = priceNum(pEl ? pEl.textContent : ''); if (name) names.push(name); if (!isNaN(p)) prices.push(p); } });
        var sum = prices.reduce(function(a,b){ return a + (Number(b)||0); }, 0);
        try { localStorage.setItem('selected_services_names', JSON.stringify(names)); localStorage.setItem('selected_services_prices', JSON.stringify(prices)); localStorage.setItem('selected_service_name', names[0] || ''); localStorage.setItem('selected_service_price', String(sum)); } catch(e){}
        if (totalBox) totalBox.textContent = 'Total: ' + formatPHP(sum);
      }
      checks.forEach(function(c){ c.addEventListener('change', compute); });
      if (proceedBtn){ proceedBtn.addEventListener('click', function(){ compute(); window.location.href = '/booking_process/booking_location.php'; }); }
      compute();
    });
  </script>
</body>
</html>




