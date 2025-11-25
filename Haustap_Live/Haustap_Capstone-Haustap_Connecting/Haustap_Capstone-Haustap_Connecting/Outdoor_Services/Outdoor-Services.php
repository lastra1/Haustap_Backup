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
                        <input type="radio" name="garden" value="small">
                    </div>
                </div>

                <div class="garden-card">
                    <div class="garden-info">
                        <h3>Medium Garden</h3>
                        <p class="size">50-150 sqm</p>
                        <p class="description">A moderate-sized outdoor area, common in family homes.</p>
                    </div>
                    <div class="radio-btn">
                        <input type="radio" name="garden" value="medium">
                    </div>
                </div>

                <div class="garden-card">
                    <div class="garden-info">
                        <h3>Large Garden</h3>
                        <p class="size">(150-250 sqm)</p>
                        <p class="description">Expansive outdoor areas, often for villas or mansions.</p>
                    </div>
                    <div class="radio-btn">
                        <input type="radio" name="garden" value="large">
                    </div>
                </div>
            </div>
        </div>
  </main>
  <!-- FOOTER -->
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
<script>
(function(){
  document.addEventListener('change', function(e){
    var t = e.target;
    if (t && t.matches('input[type=\"radio\"][name=\"garden\"]')) {
      var val = (t.value || '').toLowerCase();
      var map = {
        small: '/Outdoor_Services/Gardening.php',
        medium: '/Outdoor_Services/Gardening-medium.php',
        large: '/Outdoor_Services/Gardening-large.php'
      };
      var next = map[val];
      if (next) { window.location.href = next; }
    }
  });
})();
</script>
</body>
</html>


