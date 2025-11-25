<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Indoor Services | Plumbing | Homi</title>
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
      <a class="tab active" href="/Indoor_services/Plumbing.php">Plumbing</a>
      <a class="tab" href="/Indoor_services/Electrical.php">Electrical</a>
      <a class="tab" href="/Indoor_services/Appliances%20repair.php">Appliance Repair</a>
      <a class="tab" href="/Indoor_services/Pest%20Control.php">Pest Control</a>
    </div>
    <div class="services-container">
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="inspection-fee">
        <label for="inspection-fee" class="radio-label"></label>
        <div class="service-title">Inspection Fee</div>
        <div class="service-price">₱300</div>
        <div class="service-details">
          Individually by a plumber.<br>
          On-site assessment of needs.<br>
          Recommendations for repair/solutions.<br>
          <strong>No actual repair or installation included</strong>
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="faucet-leak">
        <label for="faucet-leak" class="radio-label"></label>
        <div class="service-title">Faucet leak repair</div>
        <div class="service-price">₱350 per unit</div>
        <div class="service-details">
          Inspection of faucet leaks (washer, cartridge, or small issue).<br>
          Leak troubleshooting, washer replacement.<br>
          Replacement faucet not included.
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="pipe-leak">
        <label for="pipe-leak" class="radio-label"></label>
        <div class="service-title">Pipe leak repair</div>
        <div class="service-price">₱600 per unit</div>
        <div class="service-details">
          Location & assessment of pipe leak.<br>
          Basic pipe patch/short-term joint repair.<br>
          Pipe replacement/parts not included.
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="clogged-sink">
        <label for="clogged-sink" class="radio-label"></label>
        <div class="service-title">Clogged sink / Drain cleaning</div>
        <div class="service-price">₱500 per unit</div>
        <div class="service-details">
          Check and remove debris/blockage.<br>
          Use of manual tools/temporary fix if needed.<br>
          Water flow test after cleaning.
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="toilet-bowl">
        <label for="toilet-bowl" class="radio-label"></label>
        <div class="service-title">Toilet bowl clog removal</div>
        <div class="service-price">₱650 per unit</div>
        <div class="service-details">
          Verification of blockage.<br>
          Manual unclogging by use of auger/pump.<br>
          Flushing test to ensure flow.
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="toilet-flush">
        <label for="toilet-flush" class="radio-label"></label>
        <div class="service-title">Toilet flush repair/ replacement</div>
        <div class="service-price">₱700 per unit</div>
        <div class="service-details">
          Inspection, mechanism failure, handle, tank parts.<br>
          Repair and/or replacement of faulty parts.<br>
          Replacement parts not included.
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="shower-head">
        <label for="shower-head" class="radio-label"></label>
        <div class="service-title">Shower head installation / replacement</div>
        <div class="service-price">₱400 per unit</div>
        <div class="service-details">
          Installation of new shower head (if any).<br>
          Removal of old shower head.<br>
          Water pressure test to ensure function.
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="water-heater">
        <label for="water-heater" class="radio-label"></label>
        <div class="service-title">Water heater installation - Basic</div>
        <div class="service-price">₱1,500 per unit</div>
        <div class="service-details">
          Basic installation of heater unit.<br>
          Connection of water lines to water line.<br>
          Leak & functionality checks.<br>
          Electrical wiring not included (separate fee).
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="pipe-install">
        <label for="pipe-install" class="radio-label"></label>
        <div class="service-title">Pipe installation - New connection</div>
        <div class="service-price">₱200 per unit</div>
        <div class="service-details">
          Installation of short pipes (any kind, faucet connection).<br>
          Testing of water flow.<br>
          Water pressure test.
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="siphon-trap">
        <label for="siphon-trap" class="radio-label"></label>
        <div class="service-title">Siphon / Trap replacement</div>
        <div class="service-price">₱500 per panel/part</div>
        <div class="service-details">
          Installation of all siphon or trap (sink or toilet).<br>
          Leak test after replacement.
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
        var subcat = activeTab ? norm(activeTab.textContent) : 'Plumbing';
        var serviceTitle = titleEl ? norm(titleEl.textContent) : '';
        var label = subcat + ' - ' + serviceTitle;
        try { localStorage.setItem('selected_service_name', label); } catch(e){}
      }, true);
    });
  </script>
</body>
</html>


