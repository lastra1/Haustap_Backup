<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Packages</title>
  <link rel="stylesheet" href="/css/global.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/wellness_services/css/packages.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="/client/css/homepage.css"></head>

<body>
  <!-- HEADER -->
    <?php include dirname(__DIR__) . "/client/includes/header.php"; ?>

  <!-- MAIN CONTENT -->
  <main>
  <h1 class="main-title">Wellness Services</h1>
  <button class="subcategory-btn">SUBCATEGORY</button>

  <!-- Subcategory Navigation -->
  <nav class="subcategory-nav">
    <ul>
      <li>Massage</li>
      <li class="active">Packages</li>
    </ul>
  </nav>

  <div class="services-container">
    <section class="service-section">
      <div class="service-grid">
        <label class="service-card">
          <input type="radio" name="massage" checked />
          <div class="service-content">
            <h3>Total Relaxation Package</h3>
            <p class="price">â‚±800</p>
            <p>Relaxing massage to rSwedish Full Body Massage<br>
            (60 mins) + Reflexology (45 mins)elieve tension and improve<br>
            circulation.
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="radio" name="massage" />
          <div class="service-content">
            <h3>Stress Relief Duo</h3>
            <p class="price">â‚±900</p>
            <p>Back & Shoulder Massage (30 mins) + Scalp & Head<br>
            Massage (30 mins)
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
      var radios = Array.prototype.slice.call(document.querySelectorAll('.service-card input[type="radio"]'));
      var activeSubcat = document.querySelector('.subcategory-nav li.active');
      function normalizeLabel(txt){ return String(txt||'').replace(/\s+/g,' ').trim(); }
      function parsePriceText(txt){
        var cleaned = String(txt||'').replace(/,/g,'');
        var m = cleaned.match(/(\d+(?:\.\d+)?)/);
        return m ? Number(m[1]) : null;
      }
      function buildLabel(card){
        var titleEl = card ? card.querySelector('.service-content h3') : null;
        var subcat = activeSubcat ? normalizeLabel(activeSubcat.textContent) : 'Wellness Services';
        var serviceTitle = titleEl ? normalizeLabel(titleEl.textContent) : '';
        return subcat + ' - ' + serviceTitle;
      }
      function proceed(card){
        var label = buildLabel(card);
        var priceEl = card ? card.querySelector('.price') : null;
        var price = priceEl ? parsePriceText(priceEl.textContent) : null;
        try {
          localStorage.setItem('selected_service_name', label);
          if (price != null && !isNaN(price)) {
            localStorage.setItem('selected_service_price', String(price));
          }
        } catch(e){}
        var url = '/booking_process/booking_location.php?service=' + encodeURIComponent(label);
        if (price != null && !isNaN(price)) { url += '&price=' + encodeURIComponent(String(price)); }
        window.location.href = url;
      }
      radios.forEach(function(r){
        r.addEventListener('change', function(){ proceed(r.closest('.service-card')); });
        r.addEventListener('click', function(){ if (r.checked) proceed(r.closest('.service-card')); });
      });
    });
  </script>
</body>
</html>




