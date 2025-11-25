<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Massage Services</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/massage_services.css" />
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
      <li class="active">Massage</li>
      <li>Packages</li>
    </ul>
  </nav>

  <div class="services-container">
    <section class="service-section">
      <div class="service-grid">
        <label class="service-card">
          <input type="radio" name="massage" checked />
          <div class="service-content">
            <h3>Full Body Massage (Swedish)</h3>
            <p class="price">â‚±800 / 60 mins</p>
            <p>
              Relaxing massage to relieve tension and improve circulation.
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="radio" name="massage" />
          <div class="service-content">
            <h3>Full Body Massage (Deep Tissue)</h3>
            <p class="price">â‚±1,000 / 60 mins</p>
            <p>
              Firm pressure targeting deep muscle knots and chronic tension.
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="radio" name="massage" />
          <div class="service-content">
            <h3>Aromatherapy Massage</h3>
            <p class="price">â‚±1,200 / 60 mins</p>
            <p>
              Relaxing massage with essential oils for stress relief.
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="radio" name="massage" />
          <div class="service-content">
            <h3>Reflexology (Foot Massage)</h3>
            <p class="price">â‚±600 / 45 mins</p>
            <p>
              Focused massage on pressure points in the feet.
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="radio" name="massage" />
          <div class="service-content">
            <h3>Scalp & Head Massage</h3>
            <p class="price">â‚±500 / 30 mins</p>
            <p>
              Relieves headaches and promotes blood circulation.
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="radio" name="massage" />
          <div class="service-content">
            <h3>Back & Shoulder Massage</h3>
            <p class="price">â‚±500 / 30 mins</p>
            <p>
              Quick relief for upper body tension.
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
        var subcat = activeSubcat ? normalizeLabel(activeSubcat.textContent) : 'Wellness Services';
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




