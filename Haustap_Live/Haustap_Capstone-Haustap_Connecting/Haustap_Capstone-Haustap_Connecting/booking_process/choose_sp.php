<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Choose Service Provider</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/booking_process/css/choose_sp.css" />
  <link rel="stylesheet" href="/booking_process/css/show_sp_details.css" />
  <link rel="stylesheet" href="/client/css/homepage.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <script src="/login_sign up/js/api.js"></script>
  <script src="/client/js/booking-api.js"></script>
</head>

<body>
  <!-- HEADER -->
  <?php include dirname(__DIR__) . "/client/includes/header.php"; ?>
  <main class="choose-provider-page">
  <h1 class="page-title">Choose Provider</h1>

  <section class="providers-container"></section>

  <!-- Filter Box -->
  <section class="filter-box">
    <div class="filter-header">
      <p>Show service provider within:</p>
    </div>
    <div class="filter-options">
      <label><input type="radio" name="distance" /> 5 km</label>
      <label><input type="radio" name="distance" /> 10 km</label>
    </div>
  </section>

  <!-- Buttons -->
  <div class="action-buttons">
    <button class="cancel-btn">Cancel</button>
    <button class="book-btn">Book</button>
  </div>
</main>

  <!-- Provider Details Overlay -->
  <div id="sp-details-overlay" class="sp-overlay" aria-hidden="true">
    <div class="sp-overlay-backdrop"></div>
    <div class="sp-overlay-content">
      <button class="sp-overlay-close" aria-label="Close details">×</button>
      <!-- fetched .sp-details-box will be injected here -->
    </div>
  </div>

  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>

  <script>
    (function(){
      // Persist selected provider in localStorage for the confirm step
      var selected = null;
      var container = document.querySelector('.providers-container');
      var overlay = document.getElementById('sp-details-overlay');
      var overlayContent = overlay ? overlay.querySelector('.sp-overlay-content') : null;
      var overlayBackdrop = overlay ? overlay.querySelector('.sp-overlay-backdrop') : null;
      var overlayClose = overlay ? overlay.querySelector('.sp-overlay-close') : null;

      function openDetailsPopup() {
        if (!overlay || !overlayContent) return;
        fetch('/booking_process/show_sp_details.php', { credentials: 'include' })
          .then(function(r){ return r.text(); })
          .then(function(html){
            var temp = document.createElement('div');
            temp.innerHTML = html;
            var box = temp.querySelector('.sp-details-box');
            overlayContent.querySelectorAll('.sp-details-box').forEach(function(n){ n.remove(); });
            if (box) {
              overlayContent.appendChild(box);
            } else {
              var fallback = document.createElement('div');
              fallback.textContent = 'Provider details unavailable.';
              overlayContent.appendChild(fallback);
            }
            overlay.classList.add('show');
            overlay.setAttribute('aria-hidden', 'false');
          })
          .catch(function(){
            overlay.classList.add('show');
            overlay.setAttribute('aria-hidden', 'false');
          });
      }

      function closeDetailsPopup() {
        if (!overlay) return;
        overlay.classList.remove('show');
        overlay.setAttribute('aria-hidden', 'true');
      }

      if (overlayBackdrop) overlayBackdrop.addEventListener('click', closeDetailsPopup);
      if (overlayClose) overlayClose.addEventListener('click', closeDetailsPopup);
      document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeDetailsPopup(); });
      function renderProviders(list){
        if (!container) return;
        container.innerHTML = '';
        list.forEach(function(p){
          var div = document.createElement('div');
          div.className = 'provider-box';
          div.setAttribute('data-provider-id', String(p.id || ''));
          div.setAttribute('data-provider-name', p.name || '');
          var icon = document.createElement('div');
          icon.className = 'profile-icon';
          icon.innerHTML = '<i class="fa-solid fa-user"></i>';
          var info = document.createElement('div');
          info.className = 'provider-info';
          var h3 = document.createElement('h3');
          h3.textContent = p.name || 'Provider';
          var rate = document.createElement('p');
          rate.innerHTML = '<i class="fa-solid fa-star"></i> Rate: ' + (p.rating || 0);
          var dist = document.createElement('p');
          var km = (typeof p.distanceKm === 'number') ? p.distanceKm : parseFloat(p.distanceKm || '0');
          dist.innerHTML = '<i class="fa-solid fa-location-dot"></i> ' + (isNaN(km) ? '0' : km.toFixed(1)) + ' km away';
          info.appendChild(h3);
          info.appendChild(rate);
          info.appendChild(dist);
          div.appendChild(icon);
          div.appendChild(info);
          div.addEventListener('click', function(){
            selected = { id: parseInt(String(p.id || '0'), 10) || 0, name: p.name || '' };
            var boxes = container.querySelectorAll('.provider-box');
            boxes.forEach(function(b){ b.style.outline = 'none'; });
            div.style.outline = '2px solid #009999';
            openDetailsPopup();
          });
          container.appendChild(div);
        });
      }
      function loadProviders(){
        fetch('/api/firebase/providers').then(function(r){ return r.json(); }).then(function(d){
          if (d && d.success && Array.isArray(d.providers)) { renderProviders(d.providers); }
        }).catch(function(){
          renderProviders([
            { id: 1, name: 'Ana Santos', rating: 4.5, distanceKm: 2.5 },
            { id: 2, name: 'Maria Lopez', rating: 4.5, distanceKm: 2.6 },
            { id: 3, name: 'Lisa Deleon', rating: 4.5, distanceKm: 2.8 }
          ]);
        });
      }
      loadProviders();

      var cancelBtn = document.querySelector('.cancel-btn');
      if (cancelBtn) {
        cancelBtn.addEventListener('click', function(){ window.location.href = '/booking/overview'; });
      }

      var bookBtn = document.querySelector('.book-btn');
      if (bookBtn) {
        bookBtn.addEventListener('click', function(){
          if (!selected) {
            // Default to first provider if none explicitly selected
            var first = document.querySelector('.provider-box');
            if (first) {
              selected = {
                id: parseInt(first.getAttribute('data-provider-id') || '1', 10),
                name: first.getAttribute('data-provider-name') || 'Provider'
              };
            }
          }
          try {
            localStorage.setItem('selected_provider_id', String(selected.id));
            localStorage.setItem('selected_provider_name', selected.name);
          } catch {}
          window.location.href = '/booking_process/confirm_booking.php';
        });
      }
    })();
  </script>
</body>
</html>
