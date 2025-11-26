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
          <input type="checkbox" class="service-check" />
          <div class="service-content">
            <h3>Full Body Massage (Swedish)</h3>
            <p class="price">â‚±800 / 60 mins</p>
            <p>
              Relaxing massage to relieve tension and improve circulation.
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="checkbox" class="service-check" />
          <div class="service-content">
            <h3>Full Body Massage (Deep Tissue)</h3>
            <p class="price">â‚±1,000 / 60 mins</p>
            <p>
              Firm pressure targeting deep muscle knots and chronic tension.
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="checkbox" class="service-check" />
          <div class="service-content">
            <h3>Aromatherapy Massage</h3>
            <p class="price">â‚±1,200 / 60 mins</p>
            <p>
              Relaxing massage with essential oils for stress relief.
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="checkbox" class="service-check" />
          <div class="service-content">
            <h3>Reflexology (Foot Massage)</h3>
            <p class="price">â‚±600 / 45 mins</p>
            <p>
              Focused massage on pressure points in the feet.
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="checkbox" class="service-check" />
          <div class="service-content">
            <h3>Scalp & Head Massage</h3>
            <p class="price">â‚±500 / 30 mins</p>
            <p>
              Relieves headaches and promotes blood circulation.
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="checkbox" class="service-check" />
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
  <script src="/client/js/multi-select-services.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      var items = Array.prototype.slice.call(document.querySelectorAll('.subcategory-nav li'));
      function normalizeLabel(txt){ return String(txt||'').replace(/\s+/g,' ').trim(); }
      items.forEach(function(li){
        li.addEventListener('click', function(){
          var name = normalizeLabel(li.textContent);
          var href = (name==='Massage')?'/wellness_services/massage_services.php':(name==='Packages'?'/wellness_services/packages.php':'');
          if (href) window.location.href = href;
        });
      });
    });
  </script>
</body>
</html>




