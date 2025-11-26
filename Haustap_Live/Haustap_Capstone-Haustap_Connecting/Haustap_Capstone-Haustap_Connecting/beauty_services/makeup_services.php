<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Make-Up Services</title>
  <link rel="stylesheet" href="/css/global.css">
  <link rel="stylesheet" href="css/makeup_services.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="/client/css/homepage.css"></head>
<body>
  <!-- HEADER -->
  <?php include dirname(__DIR__) . "/client/includes/header.php"; ?>

  <!-- MAIN CONTENT -->
  <main>
  <h1 class="main-title">Beauty Services</h1>
  <button class="subcategory-btn">SUBCATEGORY</button>

  <!-- Subcategory Navigation -->
  <nav class="subcategory-nav">
    <ul>
      <li>Hair Services</li>
      <li>Nail Care</li>
      <li class="active">Make-up</li>
      <li>Lashes</li>
      <li>Packages</li>
    </ul>
  </nav>

  <div class="services-container">
    <section class="service-section">
      <div class="service-grid">
        <!-- Card 1 -->
        <label class="service-card">
          <input type="checkbox" class="service-check">
          <div class="service-content">
            <h3>Basic/Day Make-Up</h3>
            <p class="price">â‚±1,000</p>
            <p><strong>Inclusions:</strong><br>
              Light foundation & concealer<br>
              Natural eyeshadow & brows<br>
              Mascara, blush & lip tint
            </p>
          </div>
        </label>

        <!-- Card 2 -->
        <label class="service-card">
          <input type="checkbox" class="service-check">
          <div class="service-content">
            <h3>Evening/Party Make-Up</h3>
            <p class="price">â‚±1,200</p>
            <p><strong>Inclusions:</strong><br>
              Full coverage base<br>
              Bold eyeshadow & eyeliner<br>
              False lashes (optional)<br>
              Contour, highlighter & bold lips
            </p>
          </div>
        </label>

        <!-- Card 3 -->
        <label class="service-card">
          <input type="checkbox" class="service-check">
          <div class="service-content">
            <h3>Bridal Make-Up (Trial + Wedding Day)</h3>
            <p class="price">â‚±5,000</p>
            <p><strong>Inclusions:</strong><br>
              Trial session + final bridal look<br>
              Long-lasting HD foundation<br>
              Elegant eyes & false lashes<br>
              Contour, highlight & bridal lips<br>
              Setting spray for all-day hold
            </p>
          </div>
        </label>

        <!-- Card 4 -->
        <label class="service-card">
          <input type="checkbox" class="service-check">
          <div class="service-content">
            <h3>Debut/Prom Make-Up</h3>
            <p class="price">â‚±1,000</p>
            <p><strong>Inclusions:</strong><br>
              Medium coverage base<br>
              Fresh glam eyes & youthful brows<br>
              Blush & glossy/natural lips
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
      var checks = Array.prototype.slice.call(document.querySelectorAll('.service-card input.service-check'));
      var activeSubcat = document.querySelector('.subcategory-nav li.active');
      function normalizeLabel(txt){ return String(txt||'').replace(/\s+/g,' ').trim(); }
      function buildLabel(card){
        var titleEl = card ? card.querySelector('.service-content h3') : null;
        var subcat = activeSubcat ? normalizeLabel(activeSubcat.textContent) : 'Beauty Services';
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
        var priceEl = card ? card.querySelector('.price') : null;
        var price = priceEl ? parsePriceText(priceEl.textContent) : null;
        try {
          localStorage.setItem('selected_service_name', label);
          if (price != null && !isNaN(price)) {
            localStorage.setItem('selected_service_price', String(price));
          }
        } catch(e){}
        var nextUrl = '/booking_process/booking_location.php?service=' + encodeURIComponent(label);
        if (price != null && !isNaN(price)) { nextUrl += '&price=' + encodeURIComponent(String(price)); }
        window.location.href = nextUrl;
      }
      checks.forEach(function(c){
        c.addEventListener('click', function(){
          var card = c.closest('.service-card');
          checks.forEach(function(x){ if (x !== c) x.checked = false; });
          if (c.checked) proceed(card);
        });
      });
      var items = Array.prototype.slice.call(document.querySelectorAll('.subcategory-nav li'));
      var map = {
        'Hair Services': '/beauty_services/hair_services.php',
        'Nail Care': '/beauty_services/nail_services.php',
        'Make-up': '/beauty_services/makeup_services.php',
        'Lashes': '/beauty_services/lash_services.php',
        'Packages': '/beauty_services/packages_services.php'
      };
      items.forEach(function(li){ li.addEventListener('click', function(){ var t = String(li.textContent||'').replace(/\s+/g,' ').trim(); var href = map[t] || ''; if (href) window.location.href = href; }); });
    });
  </script>
</body>
</html>




