<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cleaning Services - Homi</title>
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/cleaning.css">
    <link rel="stylesheet" href="css/responsive.css">
<link rel="stylesheet" href="/client/css/homepage.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head>
<body>
    <!-- Header -->
    <?php include dirname(__DIR__) . "/client/includes/header.php"; ?>

    <!-- Main Content -->
    <main class="cleaning-container">
        <section class="type-selector">
            <h2>Type of Cleaning</h2>
            <select>
                <option value="bungalow">Bungalow</option>
                <option value="condo">Condominium</option>
                <option value="duplex">Duplex</option>
                <option value="hotel">Hotel</option>
                <option value="Motel">Motel</option>
                <option value="Container House">Container House</option>
                <option value="Stilt">Stilt House</option>
                <option value="Mansion">Mansion</option>
                <option value="Villa">Villa</option>
            </select>
        </section>

        <!-- Cleaning Packages -->
        <section class="cleaning-packages">
            <!-- Basic Cleaning -->
            <div class="cleaning-package">
                <div class="package-header">
                    <h3>
                        <span class="package-title">Basic Cleaning - 1 Cleaner</span>
                        <input type="checkbox" value="basic" class="package-checkbox">
                    </h3>
                    <span class="price">₱1,000</span>
                </div>
                <div class="package-details">
                    <p>Inclusions:</p>
                    <ul>
                        <li>Living Room: walls, mop, dusting furniture, trash removal</li>
                        <li>Bedroom: bed making, sweeping, dusting, trash removal</li>
                        <li>Hallways: mop & sweep, remove cobwebs</li>
                        <li>Windows & Mirrors: quick wipe</li>
                    </ul>
                </div>
            </div>

            <!-- Standard Cleaning -->
            <div class="cleaning-package">
                <div class="package-header">
                    <h3>
                        <span class="package-title">Standard Cleaning - 2 Cleaners</span>
                        <input type="checkbox" value="standard" class="package-checkbox">
                    </h3>
                    <span class="price">₱2,000</span>
                </div>
                <div class="package-details">
                    <p>Inclusions:</p>
                    <ul>
                        <li>All Basic Cleaning tasks plus:</li>
                        <li>Kitchen: wipe countertops, sink cleaning, stove top degrease, trash removal</li>
                        <li>Bathroom: cleaning all surfaces, shower, floor disinfecting</li>
                        <li>Furniture: dusting under/behind furniture</li>
                        <li>Windows & Mirrors: full wipe & polish</li>
                    </ul>
                </div>
            </div>

            <!-- Deep Cleaning -->
            <div class="cleaning-package">
                <div class="package-header">
                    <h3>
                        <span class="package-title">Deep Cleaning - 3 Cleaners</span>
                        <input type="checkbox" value="deep" class="package-checkbox">
                    </h3>
                    <span class="price">₱3,000</span>
                </div>
                <div class="package-details">
                    <p>Inclusions:</p>
                    <ul>
                        <li>All Standard Cleaning tasks plus:</li>
                        <li>Flooring: scrubbing stains/grout, polishing if applicable</li>
                        <li>Appliances: defrost refrigerator, oven, washing machine</li>
                        <li>Cabinet tops: dusting and cleaning</li>
                        <li>Disinfection: doorknobs, switches, high-touch surfaces</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Pagination -->
        <div class="pagination">
            <button>&lt;</button>
            <button class="active">1</button>
            <button>2</button>
            <button>3</button>
            <button>4</button>
            <button>5</button>
            <button>&gt;</button>
        </div>

        <p class="cleaning-note">Cleaning materials are provided by the client</p>
    </main>

     <!-- FOOTER -->
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
  <div class="action-bar"><button class="proceed-btn">Proceed</button><div class="total-box">Total: ₱0.00</div></div>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      function hasToken(){ try { return !!localStorage.getItem('haustap_token'); } catch(e){ return false; } }
      const houseSelect = document.querySelector('.type-selector select');
      const checks = Array.prototype.slice.call(document.querySelectorAll('input.package-checkbox'));
      const nextBtn = document.querySelector('.pagination button:last-child');
      const proceedBtn = document.querySelector('.action-bar .proceed-btn');
      const totalBox = document.querySelector('.action-bar .total-box');
      function parsePriceText(txt){ const cleaned = String(txt||'').replace(/,/g,''); const m = cleaned.match(/(\d+(?:\.\d+)?)/); return m ? Number(m[1]) : 0; }
      function labelFor(inp){ const pkg = inp.closest('.cleaning-package'); const titleEl = pkg ? pkg.querySelector('.package-title') : null; const houseText = houseSelect ? (houseSelect.options[houseSelect.selectedIndex]?.text || '').trim() : ''; const t = titleEl ? (titleEl.textContent||'').trim() : ''; return houseText && t ? (houseText + ' - ' + t) : (t || houseText); }
      function compute(){ var names=[], prices=[]; checks.forEach(function(c){ if(c.checked){ var pkg=c.closest('.cleaning-package'); var pEl=pkg?pkg.querySelector('.price'):null; names.push(labelFor(c)); prices.push(parsePriceText(pEl?pEl.textContent:'')); }}); var sum=prices.reduce(function(a,b){return a+(Number(b)||0);},0); try{ localStorage.setItem('selected_services_names', JSON.stringify(names)); localStorage.setItem('selected_services_prices', JSON.stringify(prices)); localStorage.setItem('selected_service_name', names[0]||''); localStorage.setItem('selected_service_price', String(sum)); }catch(e){} if(totalBox) totalBox.textContent='Total: ₱'+Number(sum).toFixed(2); }
      checks.forEach(function(c){ c.addEventListener('change', compute); });
      if (houseSelect) { houseSelect.addEventListener('change', compute); }
      function go(){ compute(); var house = houseSelect ? (houseSelect.value||'').toLowerCase().replace(/\s+/g,'-') : ''; if(!hasToken()){ window.location.href='/login'; return; } window.location.href='/booking_process/booking_location.php?house='+encodeURIComponent(house); }
      if (proceedBtn) proceedBtn.addEventListener('click', go);
      if (nextBtn) nextBtn.addEventListener('click', function(e){ e.preventDefault(); go(); });
      compute();
    });
  </script>
</body>
</html>


