<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Lashes Services | Beauty Salon</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/lash_services.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="/client/css/homepage.css"></head>

<body>
  <!-- HEADER -->
    <?php include dirname(__DIR__) . "/client/includes/header.php"; ?>

  <!-- MAIN CONTENT -->
  <main>
    <h1 class="main-title">Lashes Services</h1>
    <button class="subcategory-btn">SUBCATEGORY</button>
    <nav class="subcategory-nav">
      <ul>
        <li>Hair Services</li>
        <li>Nail Care</li>
        <li>Make-up</li>
        <li class="active">Lashes</li>
        <li>Packages</li>
      </ul>
    </nav>

    <div class="services-container">
      <section class="service-section">
        <div class="service-grid">
          <label class="service-card">
            <input type="checkbox" class="service-check" />
            <div class="service-content">
              <h3>Classic Lash Extensions</h3>
              <p class="price">Starts at â‚±500</p>
              <p><strong>Inclusions:</strong><br>
                1:1 lash application for a natural look<br>
                Lightweight and comfortable for daily wear<br>
                Enhances length and curl without heavy volume
              </p>
            </div>
          </label>

          <label class="service-card">
            <input type="checkbox" class="service-check" />
            <div class="service-content">
              <h3>Hybrid Lash Extensions</h3>
              <p class="price">Starts at â‚±800</p>
              <p><strong>Inclusions:</strong><br>
                Combination of classic and volume lash techniques<br>
                Fuller and more textured effect<br>
                Balanced style for both natural and glam looks
              </p>
            </div>
          </label>

          <label class="service-card">
            <input type="checkbox" class="service-check" />
            <div class="service-content">
              <h3>Volume Lash Extensions</h3>
              <p class="price">Starts at â‚±1,000</p>
              <p><strong>Inclusions:</strong><br>
            3D&ndash;6D lash fans applied for dramatic volume<br>
                Creates a glamorous, eye-catching effect<br>
                Ideal for clients who prefer bold lashes
              </p>
            </div>
          </label>

          <label class="service-card">
            <input type="checkbox" class="service-check" />
            <div class="service-content">
              <h3>Mega Volume Lash Extensions</h3>
              <p class="price">Starts at â‚±1,500</p>
              <p><strong>Inclusions:</strong><br>
                Multiple ultra-fine lash fans for extra density<br>
                Intense, dramatic lash look<br>
                Best for special occasions or high-glam styles
              </p>
            </div>
          </label>

          <label class="service-card">
            <input type="checkbox" class="service-check" />
            <div class="service-content">
              <h3>Lash Lift + Tint</h3>
              <p class="price">Starts at â‚±500</p>
              <p><strong>Inclusions:</strong><br>
                Lifts and curls natural lashes from the root<br>
                Tint adds depth and mascara-like effect<br>
                Lasts several weeks with low maintenance
              </p>
            </div>
          </label>

          <label class="service-card">
            <input type="checkbox" class="service-check" />
            <div class="service-content">
              <h3>Lower Lash Extensions</h3>
              <p class="price">Starts at â‚±300</p>
              <p><strong>Inclusions:</strong><br>
                Extensions applied to bottom lashes<br>
                Enhances definition and balance to eye look<br>
                Complements upper lash extensions
              </p>
            </div>
          </label>

          <label class="service-card">
            <input type="checkbox" class="service-check" />
            <div class="service-content">
              <h3>Lash Removal</h3>
              <p class="price">Starts at â‚±500</p>
              <p><strong>Inclusions:</strong><br>
                Gentle and safe removal of extensions<br>
                Protects natural lashes from damage<br>
                Recommended for switching lash styles
              </p>
            </div>
          </label>

          <label class="service-card">
            <input type="checkbox" class="service-check" />
            <div class="service-content">
            <h3>Lash Retouch / Refill (2&ndash;3 weeks)</h3>
              <p class="price">Starts at â‚±800</p>
              <p><strong>Inclusions:</strong><br>
                Fills in gaps from natural lash shedding<br>
                Maintains fullness and shape of extensions<br>
                Keeps lashes looking fresh and even
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
      function proceed(card){
        var label = buildLabel(card);
        try { localStorage.setItem('selected_service_name', label); } catch(e){}
        window.location.href = '/booking_process/booking_location.php?service=' + encodeURIComponent(label);
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



