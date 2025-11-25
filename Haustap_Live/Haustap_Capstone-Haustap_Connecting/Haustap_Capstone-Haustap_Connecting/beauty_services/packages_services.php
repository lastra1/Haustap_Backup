<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Packages</title>
  <link rel="stylesheet" href="/css/global.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/beauty_services/css/packages_services.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="/client/css/homepage.css"></head>

<body>
  <!-- HEADER -->
    <?php include dirname(__DIR__) . "/client/includes/header.php"; ?>

  <!-- MAIN CONTENT -->
  <main>
  <h1 class="main-title">Packages</h1>
  <button class="subcategory-btn">SUBCATEGORY</button>

  <!-- Subcategory Navigation -->
  <nav class="subcategory-nav">
    <ul>
      <li>Hair Services</li>
      <li>Nail Care</li>
      <li>Make-up</li>
      <li>Lashes</li>
      <li class="active">Packages</li>
    </ul>
  </nav>

  <div class="services-container">
    <section class="service-section">
      <div class="service-grid">
        <label class="service-card">
          <input type="radio" name="packages" checked />
          <div class="service-content">
            <h3>Basic Care Package</h3>
            <p class="price">Starts at â‚±1,000</p>
            <p><strong>Inclusions:</strong><br>
              Haircut (Ladies or Men)<br>
              Manicure with trimming, shaping, and polish<br>
              Pedicure with foot soak, cleaning, and polish
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="radio" name="packages" />
          <div class="service-content">
            <h3>Glam Essentials Package</h3>
            <p class="price">Starts at â‚±2,200</p>
            <p><strong>Inclusions:</strong><br>
              Professional blow-dry and hairstyling<br>
              Gel manicure with long-lasting polish<br>
              Classic lash extensions for a natural glam look
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="radio" name="packages" />
          <div class="service-content">
            <h3>Event Ready Package</h3>
            <p class="price">Starts at â‚±3,500</p>
            <p><strong>Inclusions:</strong><br>
              Full make-up for special occasions<br>
              Hybrid lash extensions for fuller eyes<br>
              Simple hairstyling to complete the look
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="radio" name="packages" />
          <div class="service-content">
            <h3>Bridal Radiance Package</h3>
            <p class="price">Starts at â‚±8,000</p>
            <p><strong>Inclusions:</strong><br>
              Bridal make-up (trial + wedding day)<br>
              Volume lash extensions for elegant eyes<br>
              Bridal hairstyling for a timeless finish
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="radio" name="packages" />
          <div class="service-content">
            <h3>Mani + Pedi Combo</h3>
            <p class="price">Starts at â‚±500</p>
            <p><strong>Inclusions:</strong><br>
              Complete hand and foot nail cleaning<br>
              Nail shaping, cuticle care, and polish<br>
              Relaxing soak and light massage
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="radio" name="packages" />
          <div class="service-content">
            <h3>Gel Mani + Pedi Combo</h3>
            <p class="price">Starts at â‚±1,300</p>
            <p><strong>Inclusions:</strong><br>
              Gel manicure with long-lasting polish<br>
              Gel pedicure with durable glossy finish<br>
              Ideal for low-maintenance, polished nails
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
        var subcat = activeSubcat ? normalizeLabel(activeSubcat.textContent) : 'Beauty Services';
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




