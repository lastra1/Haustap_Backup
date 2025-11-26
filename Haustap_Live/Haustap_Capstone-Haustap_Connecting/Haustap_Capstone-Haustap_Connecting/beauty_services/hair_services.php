<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hair Services | Beauty Salon</title>
  <link rel="stylesheet" href="/css/global.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/hair_services.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link rel="stylesheet" href="/client/css/homepage.css"></head>

<body>
  <!-- HEADER -->
    <?php include dirname(__DIR__) . "/client/includes/header.php"; ?>

  <!-- MAIN CONTENT -->
  <main>
    <h1 class="main-title">Hair Services</h1>
    <button class="subcategory-btn">SUBCATEGORY</button>
    <nav class="subcategory-nav">
      <ul>
        <li class="active">Hair Services</li>
        <li>Nail Care</li>
        <li>Make-up</li>
        <li>Lashes</li>
        <li>Packages</li>
      </ul>
    </nav>

    <div class="services-container">
      <!-- HAIRCUTS -->
      <section class="service-section">
        <h2>Haircuts</h2>
        <div class="service-grid">
          <label class="service-card">
            <input type="checkbox" class="service-check" />
            <div class="service-content">
              <h3>Basic Haircut (Men)</h3>
              <p class="price">Starts at â‚±200</p>
              <p><strong>Inclusions:</strong><br>
                Professional haircut & basic styling<br />
                Shampoo & rinse (upon request)<br />
                Blow-dry finish
              </p>
            </div>
          </label>

          <label class="service-card">
            <input type="checkbox" class="service-check" />
            <div class="service-content">
              <h3>Kiddie Haircut (Men)</h3>
              <p class="price">Starts at â‚±100</p>
              <p><strong>Inclusions:</strong><br>
                Gentle haircut suitable for kids<br />
                Quick comb & styling<br />
                Free child-friendly handling
              </p>
            </div>
          </label>

          <label class="service-card">
            <input type="checkbox" class="service-check" />
            <div class="service-content">
              <h3>Basic Haircut (Women)</h3>
              <p class="price">Starts at â‚±300</p>
              <p><strong>Inclusions:</strong><br>
                Professional haircut & styling<br />
                Shampoo & conditioning wash<br />
                Blow-dry or light iron finish
              </p>
            </div>
          </label>

          <label class="service-card">
            <input type="checkbox" class="service-check" />
            <div class="service-content">
              <h3>Kiddie Haircut (Women)</h3>
              <p class="price">Starts at â‚±200</p>
              <p><strong>Inclusions:</strong><br>
                Gentle haircut suitable for kids<br />
                Simple styling (ponytail, braids, or blow-dry)<br />
                Free child-friendly handling
              </p>
            </div>
          </label>
        </div>
      </section>

      <!-- HAIR COLORING -->
      <section class="service-section">
        <h2>Hair Coloring</h2>
        <div class="service-grid">
          <label class="service-card">
            <input type="checkbox" class="service-check" />
            <div class="service-content">
            <h3>L&rsquo;Oreal Full Hair Color (Short)</h3>
            <p class="price">Starts at â‚±2,000&ndash;â‚±2,500</p>
              <p><strong>Inclusions:</strong><br>
            L&rsquo;Oreal color application<br />
                Scalp & strand protection<br />
                Rinse and blow-dry<br />
                Basic styling
              </p>
            </div>
          </label>

          <label class="service-card">
            <input type="checkbox" class="service-check" />
            <div class="service-content">
              <h3>Highlights / Low Lights</h3>
            <p class="price">Starts at â‚±2,500&ndash;â‚±3,200</p>
              <p><strong>Inclusions:</strong><br>
                Foil or balayage technique<br />
                Protective cream/treatment<br />
                Shampoo, rinse & blow-dry
              </p>
            </div>
          </label>

          <label class="service-card">
            <input type="checkbox" class="service-check" />
            <div class="service-content">
            <h3>L&rsquo;Oreal Full Hair Color (Medium)</h3>
            <p class="price">Starts at â‚±2,500&ndash;â‚±3,000</p>
              <p><strong>Inclusions:</strong><br>
            L&rsquo;Oreal color application<br />
                Scalp & strand protection<br />
                Rinse & blow-dry<br />
                Basic styling
              </p>
            </div>
          </label>

          <label class="service-card">
            <input type="checkbox" class="service-check" />
            <div class="service-content">
              <h3>Balayage / Ombre</h3>
            <p class="price">Starts at â‚±3,000&ndash;â‚±3,800</p>
              <p><strong>Inclusions:</strong><br>
                Professional balayage technique<br />
                Toner (if needed)<br />
                Protective treatment<br />
                Rinse & blow-dry
              </p>
            </div>
          </label>

          <label class="service-card">
            <input type="checkbox" class="service-check" />
            <div class="service-content">
            <h3>L&rsquo;Oreal Full Hair Color (Long)</h3>
            <p class="price">Starts at â‚±3,000&ndash;â‚±3,500</p>
              <p><strong>Inclusions:</strong><br>
            L&rsquo;Oreal color application<br />
                Scalp & strand protection<br />
                Rinse and blow-dry<br />
                Basic styling
              </p>
            </div>
          </label>
        </div>
      </section>

      <!-- STRAIGHTENING -->
      <section class="service-section">
        <h2>Straightening</h2>
        <div class="service-grid">
          <label class="service-card">
            <input type="checkbox" class="service-check" />
            <div class="service-content">
            <h3>L&rsquo;Oreal Hair Straightening (Short)</h3>
            <p class="price">Starts at â‚±2,000&ndash;â‚±3,000</p>
              <p><strong>Inclusions:</strong><br>
                Professional straightening<br />
                Heat protection serum<br />
                Blow-dry & ironing<br />
                Basic styling
              </p>
            </div>
          </label>

          <label class="service-card">
            <input type="checkbox" class="service-check" />
            <div class="service-content">
            <h3>L&rsquo;Oreal Hair Straightening (Medium)</h3>
            <p class="price">Starts at â‚±3,000&ndash;â‚±4,000</p>
              <p><strong>Inclusions:</strong><br>
                Professional straightening<br />
                Heat protection serum<br />
                Blow-dry & ironing<br />
                Basic styling
              </p>
            </div>
          </label>

          <label class="service-card">
            <input type="checkbox" class="service-check" />
            <div class="service-content">
            <h3>L&rsquo;Oreal Hair Straightening (Long)</h3>
            <p class="price">Starts at â‚±4,000&ndash;â‚±5,500</p>
              <p><strong>Inclusions:</strong><br>
                Professional straightening<br />
                Heat protection serum<br />
                Blow-dry & ironing<br />
                Basic styling
              </p>
            </div>
          </label>
        </div>
      </section>

      <!-- HAIR STYLING -->
      <section class="service-section">
        <h2>Hair Styling</h2>
        <div class="service-grid">
          <label class="service-card">
            <input type="checkbox" class="service-check" />
            <div class="service-content">
              <h3>Blow-Dry (Straight)</h3>
              <p class="price">â‚±300 per head</p>
              <p><strong>Inclusions:</strong><br>
                Shampoo wash (optional)<br />
                Blow-dry with brush styling
              </p>
            </div>
          </label>

          <label class="service-card">
            <input type="checkbox" class="service-check" />
            <div class="service-content">
              <h3>Blow-Dry (Curls/Waves)</h3>
              <p class="price">â‚±400 per head</p>
              <p><strong>Inclusions:</strong><br>
                Shampoo wash (optional)<br />
                Blow-dry with curling/waving<br />
                Light hair spray finish
              </p>
            </div>
          </label>

          <label class="service-card">
            <input type="checkbox" class="service-check" />
            <div class="service-content">
              <h3>Event Hairstyling</h3>
              <p class="price">â‚±500 per head</p>
              <p><strong>Inclusions:</strong><br />
                Updo, curls, or themed look<br />
                Use of heat tools<br />
                Setting with hairspray
              </p>
            </div>
          </label>

          <label class="service-card">
            <input type="checkbox" class="service-check" />
            <div class="service-content">
              <h3>Hair Accessories Styling</h3>
              <p class="price">â‚±400 per head</p>
              <p><strong>Inclusions:</strong><br />
                Styling with accessories<br />
                Secure fitting & adjustment<br />
                Final touch-ups
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



