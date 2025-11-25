<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Indoor Services | Electrical | Homi</title>
  <link rel="stylesheet" href="css/indoor-services.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/client/css/homepage.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head>
<body>
<?php include dirname(__DIR__) . "/client/includes/header.php"; ?>
  <main>
    <h1 class="main-title">Indoor Services</h1>
    <button class="subcategory-btn">SUBCATEGORY</button>
    <div class="tabs">
      <a class="tab" href="/Indoor_services/Handyman.php">Handyman</a>
      <a class="tab" href="/Indoor_services/Plumbing.php">Plumbing</a>
      <a class="tab active" href="/Indoor_services/Electrical.php">Electrical</a>
      <a class="tab" href="/Indoor_services/Appliances%20repair.php">Appliance Repair</a>
      <a class="tab" href="/Indoor_services/Pest%20Control.php">Pest Control</a>
    </div>
    <div class="services-container">
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="inspection-fee">
        <label for="inspection-fee" class="radio-label"></label>
        <div class="service-title">Inspection Fee</div>
        <div class="service-price">₱300 <span class="note">Applies if install not processed</span></div>
        <div class="service-details">
          <strong>Inclusions:</strong><br>
          On-site visit by electrician<br>
          Assessment of wiring/electrical issue<br>
          Basic recommendations / cost estimate<br>
          Repair not included
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="outlet-install">
        <label for="outlet-install" class="radio-label"></label>
        <div class="service-title">Outlet installation / repair</div>
        <div class="service-price">₱400 per outlet</div>
        <div class="service-details">
          <strong>Inclusions:</strong><br>
          Mounting of new outlet or repair of damaged one<br>
          Electrical connection and safety check<br>
          Test using appliance or tester
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="light-switch-repair">
        <label for="light-switch-repair" class="radio-label"></label>
        <div class="service-title">Light Switch repair</div>
        <div class="service-price">₱400 per repair</div>
        <div class="service-details">
          <strong>Inclusions:</strong><br>
          Removal of damaged socket (if any)<br>
          Installation of new socket<br>
          Connection to existing wiring<br>
          Functionality and safety test
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="light-install">
        <label for="light-install" class="radio-label"></label>
        <div class="service-title">Light installation</div>
        <div class="service-price">₱300 per install</div>
        <div class="service-details">
          <strong>Inclusions:</strong><br>
          Switch installation<br>
          Installation of bulb socket / fixture<br>
          Power-on functionality test
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="light-switch-replacement">
        <label for="light-switch-replacement" class="radio-label"></label>
        <div class="service-title">Light switch replacement</div>
        <div class="service-price">₱350 per replacement</div>
        <div class="service-details">
          <strong>Inclusions:</strong><br>
          Replacement of switch<br>
          Reject for repair as needed<br>
          Test switch and connected light
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="circuit-breaker">
        <label for="circuit-breaker" class="radio-label"></label>
        <div class="service-title">Circuit breaker installation / replacement</div>
        <div class="service-price">₱500 per install/replacement</div>
        <div class="service-details">
          <strong>Inclusions:</strong><br>
          Installation of breaker (if applicable)<br>
          Removal of old breaker<br>
          Proper wiring connection<br>
          Functionality and safety test
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="ceiling-fan">
        <label for="ceiling-fan" class="radio-label"></label>
        <div class="service-title">Ceiling fan installation</div>
        <div class="service-price">₱500 per install</div>
        <div class="service-details">
          <strong>Inclusions:</strong><br>
          Mount fan on ceiling<br>
          Proper electrical connection<br>
          Balance and functionality test
        </div>
      </div>
    </div>
  </main>
   <!-- FOOTER -->
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      var activeTab = document.querySelector('.tabs .tab.active');
      function norm(t){ return String(t||'').replace(/\s+/g,' ').trim(); }
      document.addEventListener('change', function(e){
        var t = e.target;
        if (!t || !t.matches('input.service-radio')) return;
        var card = t.closest('.service-card');
        var titleEl = card ? card.querySelector('.service-title') : null;
        var subcat = activeTab ? norm(activeTab.textContent) : 'Electrical';
        var serviceTitle = titleEl ? norm(titleEl.textContent) : '';
        var label = subcat + ' - ' + serviceTitle;
        try { localStorage.setItem('selected_service_name', label); } catch(e){}
      }, true);
    });
  </script>
</body>
</html>


