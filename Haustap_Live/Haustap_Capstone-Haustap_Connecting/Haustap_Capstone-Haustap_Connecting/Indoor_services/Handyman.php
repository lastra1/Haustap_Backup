<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Indoor Services | Homi</title>
  <link rel="stylesheet" href="/Indoor_services/css/indoor-services.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/client/css/homepage.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head>
<body>
<?php include dirname(__DIR__) . "/client/includes/header.php"; ?>
  <main>
    <h1 class="main-title">Indoor Services</h1>
    <button class="subcategory-btn">SUBCATEGORY</button>
    <div class="tabs">
      <a class="tab active" href="/Indoor_services/Handyman.php">Handyman</a>
      <a class="tab" href="/Indoor_services/Plumbing.php">Plumbing</a>
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
          Evaluation by a handyman.<br>
          Includes on-site assessment of needs.<br>
          Recommendations for repair/solutions.<br>
          <strong>No actual repair or installation included</strong>
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="furniture-small">
        <label for="furniture-small" class="radio-label"></label>
        <div class="service-title">Furniture Assembly - Small Items</div>
        <div class="service-price">₱350 per unit</div>
        <div class="service-details">
          Includes assembly of stools, side tables, shelves<br>
          Assembling basic flat pack furniture<br>
          Minor adjustment per proper function
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="furniture-medium">
        <label for="furniture-medium" class="radio-label"></label>
        <div class="service-title">Furniture Assembly - Medium Items</div>
        <div class="service-price">₱500 per unit</div>
        <div class="service-details">
          Includes chairs, study table, cabinet<br>
          Assembly of medium scale furniture<br>
          Minor adjustment per proper function
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="furniture-large">
        <label for="furniture-large" class="radio-label"></label>
        <div class="service-title">Furniture Assembly - Large Items</div>
        <div class="service-price">₱800 per unit</div>
        <div class="service-details">
          Includes beds, sofa, cabinets<br>
          Assembly of large scale furniture<br>
          Minor adjustment per proper function
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="door-knob">
        <label for="door-knob" class="radio-label"></label>
        <div class="service-title">Door knob / Lock replacement</div>
        <div class="service-price">₱400 per unit</div>
        <div class="service-details">
          Installation of new door knob, lock, or hinge<br>
          Removal and disposal of old knob, lock, or hinge<br>
          Lubrication of moving parts if needed<br>
          Location of new door knob if needed
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="door-hinge">
        <label for="door-hinge" class="radio-label"></label>
        <div class="service-title">Door hinge replacement</div>
        <div class="service-price">₱300 per unit</div>
        <div class="service-details">
          Installation of new door hinge<br>
          Removal and disposal of old hinge<br>
          Lubrication of moving parts if needed<br>
          Functionality check after repair
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="sliding-door">
        <label for="sliding-door" class="radio-label"></label>
        <div class="service-title">Sliding door / Closet adjustment</div>
        <div class="service-price">₱350 per unit</div>
        <div class="service-details">
          Adjustment of hardware<br>
          Repair and replacement of parts, knobs, or hinges<br>
          Lubrication of moving parts if needed<br>
          Functionality check after repair
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="loose-chair">
        <label for="loose-chair" class="radio-label"></label>
        <div class="service-title">Loose chair / Desk repair</div>
        <div class="service-price">₱400 per unit</div>
        <div class="service-details">
          Repair/replacement of screws, bolts, or hinges<br>
          Refastening of parts, legs, arms, or doors<br>
          Tightening of loose components<br>
          Functionality check after repair
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="cabinet-align">
        <label for="cabinet-align" class="radio-label"></label>
        <div class="service-title">Cabinet alignment / Fix</div>
        <div class="service-price">₱400 per unit</div>
        <div class="service-details">
          Realignment of panels, drawers, or doors<br>
          Replacement of broken, bent, or stuck hinges<br>
          Lubrication of moving parts if needed<br>
          Functionality check after repair
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="minor-wood">
        <label for="minor-wood" class="radio-label"></label>
        <div class="service-title">Minor wooden repairs</div>
        <div class="service-price">₱400 per panel/part</div>
        <div class="service-details">
          Refitting of panels, drawers, or doors<br>
          Repairing of cracks, splits, or chips<br>
          Replacement of broken, bent, or stuck hinges<br>
          Lubrication of moving parts if needed<br>
          Functionality check after repair
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="curtain-blinds">
        <label for="curtain-blinds" class="radio-label"></label>
        <div class="service-title">Curtain rod / Blinds installations</div>
        <div class="service-price">₱300 per unit</div>
        <div class="service-details">
          Installation and securing proper placement<br>
          Drilling and screwing with safety screws/anchors<br>
          Clean-up of minor dust/debris after work
        </div>
      </div>
      <div class="service-card">
        <input type="checkbox" name="service" class="service-radio" id="mirror-install">
        <label for="mirror-install" class="radio-label"></label>
        <div class="service-title">Mirror installation</div>
        <div class="service-price">₱300 per unit</div>
        <div class="service-details">
          Measuring and securing proper placement<br>
          Drilling and screwing with safety screws/anchors<br>
          Clean-up of minor dust/debris after work
        </div>
      </div>
    </div>
  </main>
  <!-- FOOTER -->
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      var inputs = Array.prototype.slice.call(document.querySelectorAll('input.service-radio'));
      var activeTab = document.querySelector('.tabs .tab.active');
      function norm(t){ return String(t||'').replace(/\s+/g,' ').trim(); }
      document.addEventListener('change', function(e){
        var t = e.target;
        if (!t || !t.matches('input.service-radio')) return;
        var card = t.closest('.service-card');
        var titleEl = card ? card.querySelector('.service-title') : null;
        var subcat = activeTab ? norm(activeTab.textContent) : 'Handyman';
        var serviceTitle = titleEl ? norm(titleEl.textContent) : '';
        var label = subcat + ' - ' + serviceTitle;
        try { localStorage.setItem('selected_service_name', label); } catch(e){}
      }, true);
    });
  </script>
</body>
</html>


