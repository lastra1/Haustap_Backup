<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laptop & Desktop PC</title>
  <link rel="stylesheet" href="/css/global.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/laptop_desktop.css" />
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
      <li class="active">Laptop & Desktop PC</li>
      <li>Tablet & iPad</li>
      <li>Game & Console</li>
    </ul>
  </nav>

  <div class="services-container">
    <section class="service-section">
      <div class="service-grid">

        <label class="service-card">
          <input type="radio" name="tech" checked />
          <div class="service-content">
            <h3>Fan / Cooling Repair (Laptop)</h3>
            <p class="price">â‚±500 per unit</p>
            <p><strong>Inclusions:</strong><br>
              Diagnosis of cooling system (fan, vents, heat sink)<br>
              Cleaning of dust and minor obstructions<br>
              Fan repair or replacement (part provided separately if needed)<br>
              Thermal paste application (if required)<br>
              Overheating test after repair
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="radio" name="tech" />
          <div class="service-content">
            <h3>Keyboard Replacement (Laptop)</h3>
            <p class="price">â‚±500 per unit</p>
            <p><strong>Inclusions:</strong><br>
              Removal of defective keyboard<br>
              Installation of new keyboard (client-provided or stock)<br>
              Functional test of all keys<br>
              Proper assembly and casing check
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="radio" name="tech" />
          <div class="service-content">
            <h3>OS Reformat + Software Installation (Laptop)</h3>
            <p class="price">â‚±700 per unit</p>
            <p><strong>Inclusions:</strong><br>
              Full OS reinstallation (Windows/macOS/Linux as provided)<br>
              Installation of basic drivers (audio, display, network, etc.)<br>
              Installation of up to 3 client-provided software/applications<br>
              Basic system optimization and testing<br>
              Data backup not included
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="radio" name="tech" />
          <div class="service-content">
            <h3>Fan / Cooling Repair (Desktop PC)</h3>
            <p class="price">â‚±500 per unit</p>
            <p><strong>Inclusions:</strong><br>
              Diagnosis of cooling system (fan, vents, heat sink)<br>
              Cleaning of dust and minor obstructions<br>
              Fan replacement (part provided separately if needed)<br>
              Thermal paste application (if required)<br>
              Overheating test after repair
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="radio" name="tech" />
          <div class="service-content">
            <h3>Keyboard Replacement (Desktop PC)</h3>
            <p class="price">â‚±500 per unit</p>
            <p><strong>Inclusions:</strong><br>
              Replacement of external desktop keyboard<br>
              Installation of new keyboard (client-provided or stock)<br>
              Functional test of all keys
            </p>
          </div>
        </label>

        <label class="service-card">
          <input type="radio" name="tech" />
          <div class="service-content">
            <h3>OS Reformat + Software Installation (Desktop PC)</h3>
            <p class="price">â‚±700 per unit</p>
            <p><strong>Inclusions:</strong><br>
              Full OS reinstallation (Windows/Linux as provided)<br>
              Installation of basic drivers (audio, display, network, etc.)<br>
              Installation of up to 3 client-provided software/applications<br>
              Basic system optimization and testing<br>
              Data backup not included (can be requested separately)
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




