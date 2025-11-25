<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Service Provider Details</title>
  <link rel="stylesheet" href="css/show_sp_details.css">
  <link rel="stylesheet" href="/client/css/homepage.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include dirname(__DIR__) . "/client/includes/header.php"; ?>

  <main class="sp-details-page">
  <div class="sp-details-box">
    <!-- profile icon -->
    <div class="sp-icon">
      <i class="fa-solid fa-user"></i>
    </div>

    <!-- name -->
    <h2 class="sp-name">Ana Santos</h2>

    <!-- info grid: icon | label | detail -->
    <div class="sp-info-grid">
      <!-- Row 1 -->
      <div class="info-icon"><i class="fa-solid fa-star"></i></div>
      <div class="info-label">Rating:</div>
      <div class="info-detail">4.8/5.0</div>

      <!-- Row 2 -->
      <div class="info-icon"><i class="fa-solid fa-location-dot"></i></div>
      <div class="info-label">Location:</div>
      <div class="info-detail">
        City of San Pedro<br>
        Laguna, Philippines
      </div>

      <!-- Row 3 -->
      <div class="info-icon"><i class="fa-solid fa-briefcase"></i></div>
      <div class="info-label">Skills:</div>
      <div class="info-detail">Home Cleaning</div>
    </div>
  </div>
</main>
<?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>

</body>
</html>
