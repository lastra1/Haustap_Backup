<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HausTap Bookings</title>
  <link rel="stylesheet" href="/css/global.css">
  <link rel="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/cancellation_booking.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="/client/css/homepage.css"></head>
<body><?php include dirname(__DIR__) . "/client/includes/header.php"; ?>

  <!-- Skeleton Overlay -->
  <div id="skeleton-overlay" class="skeleton-overlay">
    <div class="skeleton-grid">
      <div class="skeleton-row">
        <div class="skeleton-block skeleton-line shimmer" style="width:40%"></div>
      </div>
      <div class="skeleton-line shimmer" style="width:85%"></div>
      <div class="skeleton-row">
        <div class="skeleton-circle"></div>
        <div class="skeleton-block skeleton-line shimmer" style="width:25%"></div>
        <div class="skeleton-block skeleton-line shimmer" style="width:15%"></div>
      </div>
      <div class="skeleton-row" style="margin-top:8px">
        <div class="spinner" aria-hidden="true"></div>
        <span style="font-size:14px;color:#555">Loading cancellation details…</span>
      </div>
    </div>
  </div>

  <script>
    (function(){
      var overlay = document.getElementById('skeleton-overlay');
      function show(){ document.body.classList.add('loading'); overlay.style.display='block'; }
      function hide(){ document.body.classList.remove('loading'); overlay.style.display='none'; }
      window.HausTapLoading = window.HausTapLoading || { show: show, hide: hide };
      show();
      window.addEventListener('DOMContentLoaded', function(){ setTimeout(hide, 800); });
    })();
  </script>

  <div class="page-content">

  <div class="cancellation-box">
  <div class="cancel-header">
<button class="close-btn" aria-label="Close"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
  </div>

  <div class="cancel-status">
    <p class="status-title">Cancellation Completed</p>
    <p class="status-date">on (DATE & TIME)</p>
  </div>

  <div class="cancel-details">
    <h3>Home Cleaning</h3>
    <p class="sub-detail">Bungalow - Basic Cleaning</p>
  </div>

  <hr class="divider">

  <div class="cancel-total">
    <span class="total-label">TOTAL</span>
    <span class="total-price">₱2,700.00</span>
  </div>

  <hr class="divider">

  <div class="cancel-reason">
    <p><strong>Reason:</strong> Incorrect Booking Details</p>
  </div>
</div>


<!-- FOOTER -->
<?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
  </div>
</body>
</html>
