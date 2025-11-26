<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Outdoor Services - Homi</title>
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/Outdoor_Services/css/Outdoor-Services.css">
<link rel="stylesheet" href="/client/css/homepage.css"></head>
<body>
<?php include dirname(__DIR__) . "/client/includes/header.php"; ?>

    <main>
        <h1 class="section-title">Outdoor Services</h1>

        <div class="category-section">
            <h2 class="category-title">Gardening & Landscaping</h2>
            
            <h3 class="subsection-title">Type of Garden</h3>
            
            <div class="garden-options">
                <div class="garden-card">
                    <div class="garden-info">
                        <h3>Small Garden</h3>
                        <p class="size">up to 50 sqm</p>
                        <p class="description">A compact outdoor space for light maintenance.</p>
                    </div>
                    <div class="radio-btn">
                        <input type="checkbox" class="garden-check" value="small">
                    </div>
                </div>

                <div class="garden-card">
                    <div class="garden-info">
                        <h3>Medium Garden</h3>
                        <p class="size">50-150 sqm</p>
                        <p class="description">A moderate-sized outdoor area, common in family homes.</p>
                    </div>
                    <div class="radio-btn">
                        <input type="checkbox" class="garden-check" value="medium">
                    </div>
                </div>

                <div class="garden-card">
                    <div class="garden-info">
                        <h3>Large Garden</h3>
                        <p class="size">(150-250 sqm)</p>
                        <p class="description">Expansive outdoor areas, often for villas or mansions.</p>
                    </div>
                    <div class="radio-btn">
                        <input type="checkbox" class="garden-check" value="large">
                    </div>
                </div>
            </div>
        </div>
  </main>
  <!-- FOOTER -->
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
<script>
(function(){
  var map = {
    small: '/Outdoor_Services/Gardening.php',
    medium: '/Outdoor_Services/Gardening-medium.php',
    large: '/Outdoor_Services/Gardening-large.php'
  };
  function go(val){ var next = map[(val||'').toLowerCase()]; if (next) window.location.href = next; }
  document.addEventListener('click', function(e){
    var card = e.target.closest('.garden-card');
    if (!card) return;
    var input = card.querySelector('input.garden-check');
    if (!input) return;
    var checks = Array.prototype.slice.call(document.querySelectorAll('input.garden-check'));
    checks.forEach(function(c){ if (c !== input) c.checked = false; });
    if (e.target !== input) input.checked = !input.checked;
    if (input.checked) go(input.value);
  });
})();
</script>
</body>
</html>


