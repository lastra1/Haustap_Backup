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
      <li>Mobile Phone</li>
      <li>Laptop & Desktop PC</li>
      <li class="active">Tablet & iPad</li>
      <li>Game & Console</li>
    </ul>
  </nav>

  <div class="services-container">
    <section class="service-section">
      <div class="service-grid">

        <!-- Screen Replacement - iPad -->
        <label class="service-card">
          <input type="radio" name="tech" checked />
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
          <input type="radio" name="tech" />
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
          <input type="radio" name="tech" />
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
          <input type="radio" name="tech" />
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
      function proceed(card){
        var label = buildLabel(card);
        try { localStorage.setItem('selected_service_name', label); } catch(e){}
        window.location.href = '/booking_process/booking_location.php?service=' + encodeURIComponent(label);
      }
      radios.forEach(function(r){
        r.addEventListener('change', function(){ proceed(r.closest('.service-card')); });
        r.addEventListener('click', function(){ if (r.checked) proceed(r.closest('.service-card')); });
      });
    });
  </script>
</body>
</html>





