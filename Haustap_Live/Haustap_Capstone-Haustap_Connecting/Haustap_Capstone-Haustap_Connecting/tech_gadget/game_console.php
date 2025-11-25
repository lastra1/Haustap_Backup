<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Game & Console</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/game_console.css" />
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
      <li>Mobile Phone</li>
      <li>Laptop & Desktop PC</li>
      <li>Tablet & iPad</li>
      <li class="active">Game & Console</li>
    </ul>
  </nav>

  <div class="services-container">
    <section class="service-section">
      <div class="service-grid">
        <label class="service-card">
          <input type="radio" name="tech" checked />
          <div class="service-content">
            <h3>Controller repair</h3>
            <p class="price">â‚±300 per unit</p>
            <p><strong>Inclusions:</strong><br>
                Diagnosis of controller issues (buttons, joystick, triggers, or connectivity)<br>
                Cleaning and calibration of internal parts<br>
                Replacement of minor components/parts (if provided by client)<br>
                Functionality test and gameplay test after repair
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="radio" name="tech" />
          <div class="service-content">
            <h3>HDMI port repair</h3>
            <p class="price">â‚±700 per unit</p>
            <p><strong>Inclusions:</strong><br>
                Diagnosis of HDMI port issue (loose, bent, or damaged pins)<br>
                Removal of faulty HDMI port<br>
                Installation of new HDMI port (customer-provided or available stock)<br>
                Testing of video/audio output to monitor/TV<br>
                Device assembly and functionality check
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="radio" name="tech" />
          <div class="service-content">
            <h3>Disc Drive Repair / Replacement </h3>
            <p class="price">â‚±800 per unit</p>
            <p><strong>Inclusions:</strong><br>
                Diagnosis of disc reading/ejecting issues<br>
                Cleaning of optical lens<br>
                Adjustment or repair of disc tray mechanism<br>
                Replacement of faulty disc drive (if provided by client)<br>
                Playback test with game disc
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="radio" name="tech" />
          <div class="service-content">
            <h3>Power Supply Repair / Replacement </h3>
            <p class="price">â‚±900 per unit</p>
            <p><strong>Inclusions:</strong><br>
                Diagnosis of power-related issues (no power, sudden shutdowns)<br>
                Checking and repairing internal power supply<br>
                Replacement of power supply unit (if provided by client)<br>
                Functionality and safety test after repai
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="radio" name="tech" />
          <div class="service-content">
            <h3>Software Reinstallation / Update </h3>
            <p class="price">â‚±300 per unit</p>
            <p><strong>Inclusions:</strong><br>
                Diagnosis of controller issues (buttons, joystick, triggers, or connectivity)<br>
                Cleaning and calibration of internal parts<br>
                Replacement of minor components/parts (if provided by client)<br>
                Functionality test and gameplay test after repair
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="radio" name="tech" />
          <div class="service-content">
            <h3>HDMI port repair</h3>
            <p class="price">â‚±600 per unit</p>
            <p><strong>Inclusions:</strong><br>
                System software update/reinstallation (PlayStation, Xbox, Nintendo, etc.)<br>
                Installation of latest firmware version<br>
                Optimization of console performance<br>
                Testing of system functions and online connectivity
            </p>
          </div>
        </label>
      </div>
    </section>
  </div>
</main>


  <!-- FOOTER -->
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      var radios = Array.prototype.slice.call(document.querySelectorAll('input[type="radio"][name="tech"]'));
      var activeSubcat = document.querySelector('.subcategory-nav li.active');
      function normalizeLabel(txt){ return String(txt||'').replace(/\s+/g,' ').trim(); }
      function buildLabel(card){
        var titleEl = card ? card.querySelector('.service-content h3') : null;
        var subcat = activeSubcat ? normalizeLabel(activeSubcat.textContent) : 'Tech & Gadget';
        var serviceTitle = titleEl ? normalizeLabel(titleEl.textContent) : '';
        return subcat + ' - ' + serviceTitle;
      }
      function parsePriceText(txt){
        var cleaned = String(txt||'').replace(/,/g,'');
        var m = cleaned.match(/(\d+(?:\.\d+)?)/);
        return m ? Number(m[1]) : null;
      }
      function proceed(card){
        var label = buildLabel(card);
        try {
          localStorage.setItem('selected_service_name', label);
          var pEl = card ? card.querySelector('.service-price') : null;
          var price = pEl ? parsePriceText(pEl.textContent) : null;
          if (price != null && !isNaN(price)) {
            localStorage.setItem('selected_service_price', String(price));
          }
        } catch(e){}
        var nextUrl = '/booking_process/booking_location.php?service=' + encodeURIComponent(label);
        var pEl2 = card ? card.querySelector('.service-price') : null;
        var price2 = pEl2 ? parsePriceText(pEl2.textContent) : null;
        if (price2 != null && !isNaN(price2)) { nextUrl += '&price=' + encodeURIComponent(String(price2)); }
        window.location.href = nextUrl;
      }
      radios.forEach(function(r){
        r.addEventListener('change', function(){ proceed(r.closest('.service-card')); });
        r.addEventListener('click', function(){ if (r.checked) proceed(r.closest('.service-card')); });
      });
    });
  </script>
</body>
</html>




