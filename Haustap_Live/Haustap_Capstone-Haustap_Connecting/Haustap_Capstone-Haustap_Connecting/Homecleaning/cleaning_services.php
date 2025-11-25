<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Cleaning Services | Homi</title>
  <link rel="stylesheet" href="/Homecleaning/css/indoor-cleaning.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/client/css/homepage.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head>
<body>
  <?php include dirname(__DIR__) . "/guest/includes/header.php"; ?>
  <main>
    <h1 class="main-title">Cleaning Services</h1>
    <button class="subcategory-btn">SUBCATEGORY</button>
    <div class="centered-section">
        <div class="breadcrumbs">
      <a href="/services/cleaning" aria-current="page">Home cleaning</a>
      <span> | </span>
      <a href="/services/cleaning/ac">AC cleaning</a>
      <span> | </span>
      <a href="/services/cleaning/ac-deep">AC Deep Cleaning (Chemical Cleaning)</a>
    </div>
      <div class="section-title">Type of House</div>
      <section class="house-group">
        <h2>Bungalow</h2>
        <div class="house-cards">
          <div class="house-card">
            <input type="checkbox" name="house" class="house-radio" id="bungalow">
            <label for="bungalow" class="radio-label"></label>
            <div class="house-title">Bungalow</div>
            <div class="house-desc">80-120 sqm<br>Single-story with wider living spaces, ideal for families.</div>
          </div>
        </div>
        <h2>Condominium</h2>
        <div class="house-cards">
          <div class="house-card">
            <input type="checkbox" name="house" class="house-radio" id="condo-studio">
            <label for="condo-studio" class="radio-label"></label>
            <div class="house-title">Condominium Studio / 1BR</div>
            <div class="house-desc">20-50 sqm<br>For singles, couples, or small families.</div>
          </div>
          <div class="house-card">
            <input type="checkbox" name="house" class="house-radio" id="condo-2br">
            <label for="condo-2br" class="radio-label"></label>
            <div class="house-title">Condominium 2BR</div>
            <div class="house-desc">60-100 sqm<br>2-bedroom units for small families, can add maid's room.</div>
          </div>
          <div class="house-card">
            <input type="checkbox" name="house" class="house-radio" id="condo-penthouse">
            <label for="condo-penthouse" class="radio-label"></label>
            <div class="house-title">Condominium Penthouse</div>
            <div class="house-desc">&gt;150 sqm<br>Luxury units at the top floor with variable, roomy unit sizes.</div>
          </div>
        </div>
        <h2>Duplex</h2>
        <div class="house-cards">
          <div class="house-card">
            <input type="checkbox" name="house" class="house-radio" id="duplex-sm">
            <label for="duplex-sm" class="radio-label"></label>
            <div class="house-title">Duplex</div>
            <div class="house-desc">Elevated 60-100 sqm<br>Shared wall, two separate entrances under one roof.</div>
          </div>
          <div class="house-card">
            <input type="checkbox" name="house" class="house-radio" id="duplex-lg">
            <label for="duplex-lg" class="radio-label"></label>
            <div class="house-title">Duplex</div>
            <div class="house-desc">Larger 120-200 sqm<br>2-storey duplex units with more functional areas.</div>
          </div>
        </div>
        <h2>Hotel</h2>
        <div class="house-cards">
          <div class="house-card">
            <input type="checkbox" name="house" class="house-radio" id="hotel-sm">
            <label for="hotel-sm" class="radio-label"></label>
            <div class="house-title">Hotel</div>
            <div class="house-desc">Suite (15-50 sqm)<br>Hotel rooms or suites with all guest areas.</div>
          </div>
          <div class="house-card">
            <input type="checkbox" name="house" class="house-radio" id="hotel-lg">
            <label for="hotel-lg" class="radio-label"></label>
            <div class="house-title">Hotel</div>
            <div class="house-desc">Suite (50-150 sqm)<br>Hotel suites with more entertaining and guest areas.</div>
          </div>
        </div>
        <h2>Motel</h2>
        <div class="house-cards">
          <div class="house-card">
            <input type="checkbox" name="house" class="house-radio" id="motel-sm">
            <label for="motel-sm" class="radio-label"></label>
            <div class="house-title">Motel</div>
            <div class="house-desc">Standard Room (15-30 sqm)<br>Basic lodging. Usually single room and bath.</div>
          </div>
          <div class="house-card">
            <input type="checkbox" name="house" class="house-radio" id="motel-lg">
            <label for="motel-lg" class="radio-label"></label>
            <div class="house-title">Motel</div>
            <div class="house-desc">Larger Room (30-60 sqm)<br>A lodging larger room with more unit amenities.</div>
          </div>
        </div>
        <h2>Container House</h2>
        <div class="house-cards">
          <div class="house-card">
            <input type="checkbox" name="house" class="house-radio" id="container-sm">
            <label for="container-sm" class="radio-label"></label>
            <div class="house-title">Container House</div>
            <div class="house-desc">Single (20-40 sqm)<br>Single modular prefab, suitable urban starter type.</div>
          </div>
          <div class="house-card">
            <input type="checkbox" name="house" class="house-radio" id="container-lg">
            <label for="container-lg" class="radio-label"></label>
            <div class="house-title">Container House</div>
            <div class="house-desc">Multiple (40-120 sqm)<br>Modular designs for combined families with more amenities.</div>
          </div>
        </div>
        <h2>Stilt House</h2>
        <div class="house-cards">
          <div class="house-card">
            <input type="checkbox" name="house" class="house-radio" id="stilt-sm">
            <label for="stilt-sm" class="radio-label"></label>
            <div class="house-title">Stilt House</div>
            <div class="house-desc">Small (30-60 sqm)<br>Raised structure, usually for shore or coastal areas.</div>
          </div>
          <div class="house-card">
            <input type="checkbox" name="house" class="house-radio" id="stilt-lg">
            <label for="stilt-lg" class="radio-label"></label>
            <div class="house-title">Stilt House</div>
            <div class="house-desc">Large (100-200 sqm)<br>Houses with more living and bathing areas.</div>
          </div>
        </div>
        <h2>Mansion</h2>
        <div class="house-cards">
          <div class="house-card">
            <input type="checkbox" name="house" class="house-radio" id="mansion-sm">
            <label for="mansion-sm" class="radio-label"></label>
            <div class="house-title">Mansion</div>
            <div class="house-desc">Small (150-300 sqm)<br>Luxury house usually with luxury features and staff rooms.</div>
          </div>
          <div class="house-card">
            <input type="checkbox" name="house" class="house-radio" id="mansion-lg">
            <label for="mansion-lg" class="radio-label"></label>
            <div class="house-title">Mansion</div>
            <div class="house-desc">Larger (300-1000 sqm)<br>Luxury mansion with more entertaining, staff and bath areas.</div>
          </div>
        </div>
        <h2>Villa</h2>
        <div class="house-cards">
          <div class="house-card">
            <input type="checkbox" name="house" class="house-radio" id="villa-sm">
            <label for="villa-sm" class="radio-label"></label>
            <div class="house-title">Villa</div>
            <div class="house-desc">Villa (100-250 sqm)<br>Standalone house with wider garden or garden + pool.</div>
          </div>
          <div class="house-card">
            <input type="checkbox" name="house" class="house-radio" id="villa-lg">
            <label for="villa-lg" class="radio-label"></label>
            <div class="house-title">Villa</div>
            <div class="house-desc">Larger (250-1000 sqm)<br>Luxury villa with more entertaining, bath and staff rooms.</div>
          </div>
        </div>
      </section>
    </div>
    <div class="action-bar" style="display:flex;align-items:center;justify-content:space-between;background:#F5F5F5;border-radius:10px;padding:12px;margin:20px auto;max-width:960px">
      <div class="total-box" style="font-weight:700;color:#00ADB5">Selected: None</div>
      <button class="proceed-btn" style="background:#00ADB5;color:#fff;border:none;border-radius:8px;padding:10px 16px;font-weight:700">Proceed</button>
    </div>
    <div id="previewFloating" style="position:fixed;right:20px;bottom:20px;z-index:999;background:#fff;border:1px solid #e0e0e0;border-radius:12px;box-shadow:0 4px 16px rgba(0,0,0,.08);padding:12px 14px;min-width:220px;display:none">
      <div style="font-size:14px;color:#666;margin-bottom:6px">Selected</div>
      <div id="previewFloatingText" style="font-size:16px;font-weight:700;color:#00ADB5">None</div>
    </div>
    <div id="chooserOverlay" style="display:none;position:fixed;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.35);z-index:1000"></div>
    <div id="chooserModal" style="display:none;position:fixed;left:50%;top:50%;transform:translate(-50%,-50%);background:#fff;border:2px solid #00ADB5;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,0.2);padding:16px;max-width:520px;width:92vw;z-index:1001">
      <div style="font-weight:700;color:#00ADB5;margin-bottom:8px">Choose one to continue</div>
      <div id="chooserList" style="max-height:40vh;overflow:auto;margin-bottom:12px"></div>
      <div style="display:flex;gap:10px;justify-content:flex-end">
        <button id="chooserProceedAll" style="background:#fff;color:#00ADB5;border:2px solid #00ADB5;border-radius:8px;padding:8px 12px;font-weight:700">Proceed All</button>
        <button id="chooserContinue" style="background:#00ADB5;color:#fff;border:none;border-radius:8px;padding:8px 12px;font-weight:700">Continue</button>
      </div>
    </div>
    <nav class="pagination">
      <ul>
        <li><a href="#" aria-label="Previous">&laquo;</a></li>
        
        
        
        
        
        <li><a href="#" aria-label="Next">&raquo;</a></li>
      </ul>
    </nav>
  </main>
  <!-- FOOTER -->
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
  <script>
  // Route Next arrow to the selected house type page
  document.addEventListener('DOMContentLoaded', function () {
    const nextLink = document.querySelector('nav.pagination a[aria-label="Next"]');
    const radios = document.querySelectorAll('input.house-radio');
    const cards = document.querySelectorAll('.house-card');
    const proceedBtn = document.querySelector('.action-bar .proceed-btn');
    const totalBox = document.querySelector('.action-bar .total-box');

    // Map radio IDs to their house-specific pages for cleaning type selection
    const routeMap = {
      'bungalow': '/Homecleaning/bungalow.php',
      'condo-studio': '/Homecleaning/condo_studio.php',
      'condo-2br': '/Homecleaning/condo_2br.php',
      'condo-penthouse': '/Homecleaning/penthouse.php',
      'duplex-sm': '/Homecleaning/duplex_smaller.php',
      'duplex-lg': '/Homecleaning/duplex_larger.php',
      'hotel-sm': '/Homecleaning/hotel_standard.php',
      'hotel-lg': '/Homecleaning/hotel_suite.php',
      'motel-sm': '/Homecleaning/motel_standard.php',
      'motel-lg': '/Homecleaning/motel_large.php',
      'container-sm': '/Homecleaning/house_container_single.php',
      'container-lg': '/Homecleaning/house_container_multiple.php',
      'stilt-sm': '/Homecleaning/Stilt_house_small.php',
      'stilt-lg': '/Homecleaning/Stilt_house_large.php',
      'mansion-sm': '/Homecleaning/Mansion_small.php',
      'mansion-lg': '/Homecleaning/mansion_larger.php',
      'villa-sm': '/Homecleaning/Villa_smaller.php',
      'villa-lg': '/Homecleaning/villa_larger.php',
    };

    function getSelectedRoute() {
      const checkedAll = Array.prototype.slice.call(document.querySelectorAll('input.house-radio:checked'));
      const first = checkedAll.length ? checkedAll[0] : null;
      return first ? (routeMap[first.id] || null) : null;
    }

    function getSelectedLabels() {
      const checkedAll = Array.prototype.slice.call(document.querySelectorAll('input.house-radio:checked'));
      return checkedAll.map(function(ch){
        const card = ch.closest('.house-card');
        const t = card ? card.querySelector('.house-title') : null;
        return t ? (t.textContent || '').trim() : '';
      }).filter(Boolean);
    }

    function getSelectedOptions(){
      const checkedAll = Array.prototype.slice.call(document.querySelectorAll('input.house-radio:checked'));
      return checkedAll.map(function(ch){
        const card = ch.closest('.house-card');
        const t = card ? card.querySelector('.house-title') : null;
        const label = t ? (t.textContent||'').trim() : '';
        return { id: ch.id, label: label, route: routeMap[ch.id] || null };
      }).filter(function(o){ return !!o.label; });
    }

    function hasToken(){
      try { return !!localStorage.getItem('haustap_token'); } catch(e){ return false; }
    }

    function updatePreview(){
      const names = getSelectedLabels();
      const labelText = names.length ? names.join(', ') : 'None';
      if (totalBox) totalBox.textContent = 'Selected: ' + labelText;
      try {
        localStorage.setItem('selected_services_names', JSON.stringify(names));
        localStorage.setItem('selected_service_name', names[0] || '');
        localStorage.removeItem('selected_services_prices'); // no prices at this step
        localStorage.setItem('selected_service_price', '0');
        const opts = getSelectedOptions();
        if (opts.length === 1) {
          localStorage.setItem('selected_house_slug', opts[0].id);
          localStorage.removeItem('selected_cleaning_type');
        }
      } catch (e) {}
      const floating = document.getElementById('previewFloating');
      const floatingText = document.getElementById('previewFloatingText');
      if (floating && floatingText) {
        if (names.length) {
          floatingText.innerHTML = names.map(function(n){ return '<div>'+n+'</div>'; }).join('');
          floating.style.display = '';
        } else {
          floating.style.display = 'none';
        }
      }
    }

    const floatProceed = document.getElementById('ht-float-proceed');
    if (floatProceed) {
      floatProceed.addEventListener('click', function(){
        const options = getSelectedOptions();
        if (!options.length) { alert('Please select a house type first.'); return; }
        updatePreview();
        if (options.length === 1) {
          window.location.href = options[0].route || '#';
        } else {
          openChooser();
        }
      });
    }

    function openChooser(){
      const options = getSelectedOptions();
      const overlay = document.getElementById('chooserOverlay');
      const modal = document.getElementById('chooserModal');
      const list = document.getElementById('chooserList');
      const proceedAll = document.getElementById('chooserProceedAll');
      const cont = document.getElementById('chooserContinue');
      if (!overlay || !modal || !list || !proceedAll || !cont) return;
      list.innerHTML = options.length ? options.map(function(o, idx){
        const rId = 'choose_'+idx;
        return '<label style="display:flex;align-items:center;gap:10px;margin:6px 0"><input type="radio" name="chooseOne" id="'+rId+'" data-idx="'+idx+'"> <span>'+o.label+'</span></label>';
      }).join('') : '<i>No selections</i>';
      overlay.style.display = 'block';
      modal.style.display = 'block';
      function close(){ overlay.style.display='none'; modal.style.display='none'; }
      overlay.onclick = close;
      proceedAll.onclick = function(){
        try {
          localStorage.setItem('selected_service_name', 'Multiple House Types');
          localStorage.setItem('selected_services_names', JSON.stringify(options.map(function(o){ return o.label; })));
          localStorage.setItem('selected_service_price', '0');
        } catch(e){}
        window.location.href = '/booking_process/booking_location.php?service=' + encodeURIComponent('Multiple House Types');
      };
      cont.onclick = function(){
        const picked = (function(){
          const r = document.querySelector('input[type="radio"][name="chooseOne"]:checked');
          if (!r) return options[0] || null;
          const idx = Number(r.getAttribute('data-idx')||'0');
          return options[idx] || options[0] || null;
        })();
        if (!picked || !picked.route){ close(); alert('Please select an item to continue.'); return; }
        try { localStorage.setItem('selected_service_name', picked.label); } catch(e){}
        window.location.href = picked.route;
      };
    }

    function selectRadio(el){
      // multi-select: toggle current and keep others
      el.checked = !el.checked;
      const route = getSelectedRoute();
      if (nextLink) nextLink.setAttribute('href', route || '#');
      updatePreview();
    }

    // Keep href refreshed when selection changes
    radios.forEach(r => {
      r.addEventListener('change', () => {
        // when checkbox changed directly, just refresh preview and next link
        const route = getSelectedRoute();
        if (nextLink) nextLink.setAttribute('href', route || '#');
        updatePreview();
      });
    });

    cards.forEach(c => {
      c.addEventListener('click', (e) => {
        const cb = c.querySelector('input.house-radio');
        if (!cb) return;
        if (!(e.target instanceof Element) || !e.target.closest('input.house-radio')) {
          // toggle selection on card click
          cb.checked = !cb.checked;
          const route = getSelectedRoute();
          if (nextLink) nextLink.setAttribute('href', route || '#');
          updatePreview();
        }
      });
    });

    nextLink.addEventListener('click', function (e) {
      const options = getSelectedOptions();
      if (!options.length) {
        e.preventDefault();
        alert('Please select a house type first.');
        return;
      }
      updatePreview();
      e.preventDefault();
      if (options.length === 1) {
        window.location.href = options[0].route || '#';
      } else {
        openChooser();
      }
    });

    if (proceedBtn) {
      proceedBtn.addEventListener('click', function(){
        const options = getSelectedOptions();
        if (!options.length) { alert('Please select a house type first.'); return; }
        updatePreview();
        if (options.length === 1) {
          window.location.href = options[0].route || '#';
        } else {
          openChooser();
        }
      });
    }

    // Initialize from stored selection
    try {
      const prev = (localStorage.getItem('selected_service_name') || '').trim();
      if (prev) {
        // match by title text
        document.querySelectorAll('.house-card .house-title').forEach(function(t){
          if ((t.textContent || '').trim() === prev) {
            const cb = t.closest('.house-card').querySelector('input.house-radio');
            if (cb) selectRadio(cb);
          }
        });
      }
    } catch(e) {}
    updatePreview();
  });
  </script>
</body>
</html>


