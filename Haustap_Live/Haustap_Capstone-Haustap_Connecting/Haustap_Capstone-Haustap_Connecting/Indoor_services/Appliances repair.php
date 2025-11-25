<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Indoor Services | Appliance Repair | Homi</title>
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
      <a class="tab" href="/Indoor_services/Electrical.php">Electrical</a>
      <a class="tab active" href="/Indoor_services/Appliances%20repair.php">Appliance Repair</a>
      <a class="tab" href="/Indoor_services/Pest%20Control.php">Pest Control</a>
    </div>
    <div class="services-container">
      <!-- 1st row -->
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="inspection-fee">
        <label for="inspection-fee" class="radio-label"></label>
        <div class="service-title">Inspection Fee</div>
        <div class="service-price">â‚±300</div>
        <div class="service-details">
          Individually by a technician.<br>
          On-site assessment of needs.<br>
          Recommendations for repair/solutions.<br>
          <strong>No actual repair or installation included</strong>
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="refrigerator-repair">
        <label for="refrigerator-repair" class="radio-label"></label>
        <div class="service-title">Refrigerator Repair</div>
        <div class="service-price">â‚±800 per unit</div>
        <div class="service-details">
          Cooling issue, replacement of thermostat, compressor<br>
          Cleaning of evaporator, minor troubleshooting<br>
          Excludes replacement parts
        </div>
      </div>
      <!-- 2nd row -->
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="commercial-freezer">
        <label for="commercial-freezer" class="radio-label"></label>
        <div class="service-title">Commercial Freezer Repair</div>
        <div class="service-price">â‚±1,000 per unit</div>
        <div class="service-details">
          Cooling issue, replacement of thermostat, compressor<br>
          Cleaning of evaporator, minor troubleshooting<br>
          Excludes replacement parts
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="tv-repair-32">
        <label for="tv-repair-32" class="radio-label"></label>
        <div class="service-title">TV Repair (up to 32")</div>
        <div class="service-price">â‚±600 per unit</div>
        <div class="service-details">
          Power/Display issues, sound issues, remote troubleshooting<br>
          Excludes replacement parts
        </div>
      </div>
      <!-- 3rd row -->
      <div class="service-card">
        <input type="radio" name="service" class="service-radio" id="tv-repair-33-50">
        <label for="tv-repair-33-50" class="radio-label"></label>
        <div class="service-title">TV Repair (33" to 50")</div>
        <div class="service-price">â‚±800 per unit</div>
        <div class="service-details">
          Power/Display issues, sound issues, remote troubleshooting<br>
          Excludes replacement parts
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="tv-repair-51-80">
        <label for="tv-repair-51-80" class="radio-label"></label>
        <div class="service-title">TV Repair (51" to 80")</div>
        <div class="service-price">â‚±1,000 per unit</div>
        <div class="service-details">
          Power/Display issues, sound issues, remote troubleshooting<br>
          Excludes replacement parts
        </div>
      </div>
      <!-- 4th row -->
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="tv-install">
        <label for="tv-install" class="radio-label"></label>
        <div class="service-title">TV Installation</div>
        <div class="service-price">â‚±500 per unit</div>
        <div class="service-details">
          Mounting of TV on wall, connection to power<br>
          Setup of cable, minor configuration<br>
          Excludes replacement parts
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="washing-machine-repair">
        <label for="washing-machine-repair" class="radio-label"></label>
        <div class="service-title">Washing Machine Repair</div>
        <div class="service-price">â‚±800 per unit</div>
        <div class="service-details">
          Power/Drain issues, cleaning, minor troubleshooting<br>
          Excludes replacement parts
        </div>
      </div>
      <!-- 5th row -->
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="washing-machine-top-load">
        <label for="washing-machine-top-load" class="radio-label"></label>
        <div class="service-title">Washing Machine Cleaning Top Load</div>
        <div class="service-price">â‚±500 per unit</div>
        <div class="service-details">
          Drum cleaning, minor troubleshooting<br>
          Excludes replacement parts
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="washing-machine-front-load">
        <label for="washing-machine-front-load" class="radio-label"></label>
        <div class="service-title">Washing Machine Cleaning Front Load</div>
        <div class="service-price">â‚±700 per unit</div>
        <div class="service-details">
          Drum cleaning, minor troubleshooting<br>
          Excludes replacement parts
        </div>
      </div>
      <!-- 6th row -->
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="stand-fan-repair">
        <label for="stand-fan-repair" class="radio-label"></label>
        <div class="service-title">Stand Fan Repair</div>
        <div class="service-price">â‚±300 per unit</div>
        <div class="service-details">
          Motor/Blade issues, minor troubleshooting<br>
          Excludes replacement parts
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="tower-fan-repair">
        <label for="tower-fan-repair" class="radio-label"></label>
        <div class="service-title">Tower Fan Repair</div>
        <div class="service-price">â‚±700 per unit</div>
        <div class="service-details">
          Motor/Blade issues, minor troubleshooting<br>
          Excludes replacement parts
        </div>
      </div>
      <!-- 7th row -->
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="range-hood-repair">
        <label for="range-hood-repair" class="radio-label"></label>
        <div class="service-title">Range Hood Repair</div>
        <div class="service-price">â‚±800 per unit</div>
        <div class="service-details">
          Motor/Filter issues, cleaning, minor troubleshooting<br>
          Excludes replacement parts
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="range-hood-install">
        <label for="range-hood-install" class="radio-label"></label>
        <div class="service-title">Range Hood Installation</div>
        <div class="service-price">â‚±600 per unit</div>
        <div class="service-details">
          Mounting and connection, minor troubleshooting<br>
          Excludes replacement parts
        </div>
      </div>
      <!-- 8th row -->
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="microwave-repair-small">
        <label for="microwave-repair-small" class="radio-label"></label>
        <div class="service-title">Microwave Repair - Small</div>
        <div class="service-price">â‚±600 per unit</div>
        <div class="service-details">
          Power/Heating issues<br>
          Excludes replacement parts
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="microwave-repair-medium">
        <label for="microwave-repair-medium" class="radio-label"></label>
        <div class="service-title">Microwave Repair - Medium</div>
        <div class="service-price">â‚±700 per unit</div>
        <div class="service-details">
          Power/Heating issues<br>
          Excludes replacement parts
        </div>
      </div>
      <!-- 9th row -->
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="microwave-repair-large">
        <label for="microwave-repair-large" class="radio-label"></label>
        <div class="service-title">Microwave Repair - Large</div>
        <div class="service-price">â‚±850 per unit</div>
        <div class="service-details">
          Power/Heating issues<br>
          Excludes replacement parts
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="oven-repair">
        <label for="oven-repair" class="radio-label"></label>
        <div class="service-title">Oven Repair</div>
        <div class="service-price">â‚±1200 per unit</div>
        <div class="service-details">
          Heating/Power issues, thermostat, wiring<br>
          Excludes replacement parts
        </div>
      </div>
      <!-- 10th row -->
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="rice-cooker-repair">
        <label for="rice-cooker-repair" class="radio-label"></label>
        <div class="service-title">Rice Cooker Repair</div>
        <div class="service-price">â‚±400 per unit</div>
        <div class="service-details">
          Power/Heating issues<br>
          Excludes replacement parts
        </div>
      </div>
    </div>
  </main>
   <!-- FOOTER -->
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
<script>
document.addEventListener('DOMContentLoaded', function(){
  var activeTab = document.querySelector('.tabs .tab.active');
  function norm(txt){ return String(txt||'').replace(/\s+/g,' ').trim(); }
  document.addEventListener('change', function(e){
    var t = e.target;
    if (t && t.matches('input.service-radio')) {
      var card = t.closest('.service-card');
      var titleEl = card ? card.querySelector('.service-title') : null;
      var subcat = activeTab ? norm(activeTab.textContent) : 'Appliance Repair';
      var serviceTitle = titleEl ? norm(titleEl.textContent) : (t.id || t.value || '').trim();
      var label = subcat + ' - ' + serviceTitle;
      try { localStorage.setItem('selected_service_name', label); } catch(err){}
    }
  }, true);
});
</script>
</body>
</html>



