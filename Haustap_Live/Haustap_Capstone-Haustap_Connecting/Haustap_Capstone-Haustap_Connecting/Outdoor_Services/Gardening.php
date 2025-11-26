<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Type of Gardening - Small Garden</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/Gardening.css">
<link rel="stylesheet" href="/client/css/homepage.css"></head>
<body>
<?php include dirname(__DIR__) . "/client/includes/header.php"; ?>

    <main>
        <h1 class="section-title">Type of Gardening</h1>
        
        <div class="garden-type">
            <span class="garden-type-badge">Small Garden</span>
        </div>

        <h2 class="subtitle">Gardening & Landscaping</h2>

        <div class="services-grid">
            <label class="service-card">
                <div class="service-header">
                    <div>
                        <h3 class="service-title">Inspection Fee</h3>
                        <p class="service-price">₱300</p>
                        <p class="service-note">Additional fee per gardener for inspection</p>
                    </div>
                    <div class="service-icon">
                        <input type="checkbox" class="service-check" value="inspection">
                    </div>
                </div>
                <div class="inclusions">
                    <p class="service-description">Inspection for gardening and landscaping involves assessing the site's soil quality, drainage, and overall layout to ensure it meets healthy plant growth. It also helps identify any preparations or adjustments needed before starting the project for optimal results.</p>
                </div>
            </label>

            <label class="service-card">
                <div class="service-header">
                    <div>
        <h3 class="service-title">Basic &ndash; 1 gardener</h3>
                        <p class="service-price">₱500</p>
                    </div>
                    <div class="service-icon">
                        <input type="checkbox" class="service-check" value="basic">
                    </div>
                </div>
                <div class="inclusions">
                    <p class="inclusions-title">Inclusions:</p>
                    <ul>
                        <li>Grass cutting / trimming</li>
                        <li>Removing wilted dried leaves</li>
                        <li>Basic plant watering</li>
                    </ul>
                </div>
            </label>

            <label class="service-card">
                <div class="service-header">
                    <div>
        <h3 class="service-title">Standard &ndash; 2 gardeners</h3>
                        <p class="service-price">₱1,000</p>
                    </div>
                    <div class="service-icon">
                        <input type="checkbox" class="service-check" value="standard">
                    </div>
                </div>
                <div class="inclusions">
                    <p class="inclusions-title">Inclusions:</p>
                    <ul>
                        <li>All Basic tasks</li>
                        <li>Plant trimming & hedge shaping</li>
                        <li>Soil tilling & weeding</li>
                        <li>Fertilizer application (provided by client)</li>
                    </ul>
                </div>
            </label>

            <label class="service-card">
                <div class="service-header">
                    <div>
        <h3 class="service-title">Deep &ndash; 3 gardeners</h3>
                        <p class="service-price">₱1,800</p>
                    </div>
                    <div class="service-icon">
                        <input type="checkbox" class="service-check" value="deep">
                    </div>
                </div>
                <div class="inclusions">
                    <p class="inclusions-title">Inclusions:</p>
                    <ul>
                        <li>All Standard tasks</li>
                        <li>Lawn mowing & edging</li>
                        <li>Tree pruning (small trees)</li>
                        <li>Garden waste disposal</li>
                        <li>Decorative plant arrangement</li>
                    </ul>
                </div>
            </label>
        </div>

        <p class="materials-note">*Materials are provided by the client</p>
    </main>

   <!-- FOOTER -->
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
<div class="action-bar"><button class="proceed-btn">Proceed</button><div class="total-box">Total: ₱0.00</div></div>
<script>
(function () {
  function getText(el, sel) { var node = el ? el.querySelector(sel) : null; return node ? (node.textContent || '').trim() : ''; }
  function parsePriceText(txt){ var cleaned = String(txt||'').replace(/,/g,''); var m = cleaned.match(/(\d+(?:\.\d+)?)/); return m ? Number(m[1]) : 0; }
  function formatPHP(v){ return '₱' + Number(v||0).toFixed(2); }
  document.addEventListener('DOMContentLoaded', function(){
    var checks = Array.prototype.slice.call(document.querySelectorAll('.service-card input.service-check'));
    var subEl = document.querySelector('.garden-type-badge');
    var proceedBtn = document.querySelector('.action-bar .proceed-btn');
    var totalBox = document.querySelector('.action-bar .total-box');
    function compute(){ var names=[], prices=[]; checks.forEach(function(c){ if(c.checked){ var card=c.closest('.service-card'); var title=getText(card,'.service-title')||String(c.value||'').trim(); var subcat=subEl ? (subEl.textContent||'').trim() : 'Gardening & Landscaping'; var label=subcat+' - '+title; names.push(label); var pEl=card?card.querySelector('.service-price'):null; prices.push(parsePriceText(pEl?pEl.textContent:'')); } }); var sum=prices.reduce(function(a,b){return a+(Number(b)||0);},0); try{ localStorage.setItem('selected_services_names', JSON.stringify(names)); localStorage.setItem('selected_services_prices', JSON.stringify(prices)); localStorage.setItem('selected_service_name', names[0]||''); localStorage.setItem('selected_service_price', String(sum)); }catch(e){} if(totalBox) totalBox.textContent='Total: '+formatPHP(sum); }
    checks.forEach(function(c){ c.addEventListener('change', compute); });
    if (proceedBtn){ proceedBtn.addEventListener('click', function(){ compute(); window.location.href='/booking_process/booking_location.php'; }); }
    compute();
  });
})();
</script>
</body>
</html>



