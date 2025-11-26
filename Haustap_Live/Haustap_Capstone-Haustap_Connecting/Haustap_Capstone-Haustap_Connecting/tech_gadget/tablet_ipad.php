<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tablet & Ipad</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/tablet_ipad.css" />
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
      <li><a href="/tech_gadget/mobile_phone.php">Mobile Phone</a></li>
      <li><a href="/tech_gadget/laptop_desktop.php">Laptop & Desktop PC</a></li>
      <li class="active"><a href="/tech_gadget/tablet_ipad.php">Tablet & iPad</a></li>
      <li><a href="/tech_gadget/game_console.php">Game & Console</a></li>
    </ul>
  </nav>

  <div class="services-container">
    <section class="service-section">
      <div class="service-grid">

        <!-- Screen Replacement - iPad -->
        <label class="service-card">
          <input type="checkbox" class="service-check" />
          <div class="service-content">
            <h3>Screen Replacement (iPad)</h3>
            <p class="price">â‚±800 per unit</p>
            <p><strong>Inclusions:</strong><br>
              Removal of cracked or damaged screen<br>
              Installation of replacement screen (customer-provided or available stock)<br>
              Touch and display quality test<br>
              Device assembly and sealing check
            </p>
          </div>
        </label>

        <!-- Screen Replacement - Android Tablet -->
        <label class="service-card">
          <input type="checkbox" class="service-check" />
          <div class="service-content">
            <h3>Screen Replacement (Android Tablet)</h3>
            <p class="price">â‚±600 per unit</p>
            <p><strong>Inclusions:</strong><br>
              Removal of cracked or damaged screen<br>
              Installation of replacement screen (customer-provided or available stock)<br>
              Touch and display quality test<br>
              Device assembly and sealing check
            </p>
          </div>
        </label>

        <!-- Battery Replacement - iPad -->
        <label class="service-card">
          <input type="checkbox" class="service-check" />
          <div class="service-content">
            <h3>Battery Replacement (iPad)</h3>
            <p class="price">â‚±600 per unit</p>
            <p><strong>Inclusions:</strong><br>
              Removal of defective or swollen battery<br>
              Installation of new battery (customer-provided or available stock)<br>
              Charging and power functionality test<br>
              Safe disposal of old battery
            </p>
          </div>
        </label>

        <!-- Battery Replacement - Android Tablet -->
        <label class="service-card">
          <input type="checkbox" class="service-check" />
          <div class="service-content">
            <h3>Battery Replacement (Android Tablet)</h3>
            <p class="price">â‚±400 per unit</p>
            <p><strong>Inclusions:</strong><br>
              Removal of defective or swollen battery<br>
              Installation of new battery (customer-provided or available stock)<br>
              Charging and power functionality test<br>
              Safe disposal of old battery
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
        var names = []; var prices = [];
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





