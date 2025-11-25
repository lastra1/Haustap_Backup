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
          <input type="radio" name="makeup" checked>
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
          <input type="radio" name="makeup">
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
          <input type="radio" name="makeup">
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
          <input type="radio" name="makeup">
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
      var radios = Array.prototype.slice.call(document.querySelectorAll('.service-card input[type="radio"]'));
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




