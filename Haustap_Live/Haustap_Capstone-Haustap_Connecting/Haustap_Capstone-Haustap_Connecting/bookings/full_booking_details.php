<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HausTap Bookings</title>
  <link rel="stylesheet" href="/css/global.css">
  <link rel="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/full_booking_details.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="/client/css/homepage.css"></head>
<body><?php include dirname(__DIR__) . "/client/includes/header.php"; ?>
  <script>
    // Require login for full booking details: redirect guests to /login
    (function(){
      try {
        var t = localStorage.getItem('haustap_token');
        if (!t) { window.location.href = '/login'; return; }
      } catch(e) { window.location.href = '/login'; return; }
    })();
  </script>

  <!-- Skeleton Overlay -->
  <div id="skeleton-overlay" class="skeleton-overlay">
    <div class="skeleton-grid">
      <div class="skeleton-row">
        <div class="skeleton-block skeleton-line shimmer" style="width:55%"></div>
      </div>
      <div class="skeleton-line shimmer" style="width:80%"></div>
      <div class="skeleton-line shimmer" style="width:70%"></div>
      <div class="skeleton-row">
        <div class="skeleton-circle"></div>
        <div class="skeleton-block skeleton-line shimmer" style="width:25%"></div>
        <div class="skeleton-block skeleton-line shimmer" style="width:20%"></div>
      </div>
      <div class="skeleton-row" style="margin-top:8px">
        <div class="spinner" aria-hidden="true"></div>
        <span style="font-size:14px;color:#555">Loading booking details…</span>
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

  <main class="booking-details-container">
    <div class="booking-box">
<button class="close-btn" aria-label="Close"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>

      <h1>Home Cleaning</h1>
      <span class="sub-text">Bungalow - Basic Cleaning</span>

      <hr class="divider-black">

      <div class="info-section">
        <p><strong>Date:</strong> May 20, 2025</p>
        <p><strong>Time:</strong> 1:00 PM</p>
        <p><strong>Address:</strong> B1 L50 Mango St. Phase 1 Saint Joseph Village 10<br>
          Barangay Langgam, City of San Pedro, Laguna 4023
        </p>
        <div class="note-section">
          <label><strong>Note:</strong></label>
          <textarea placeholder="Enter note here..."></textarea>
        </div>
      </div>

      <div class="voucher-box">
<span class="voucher-icon"><i class="fa-solid fa-ticket"></i> Add a Voucher</span>
<button class="toggle-btn" aria-label="Toggle"><i class="fa-solid fa-chevron-down" aria-hidden="true"></i></button>
      </div>

      <hr class="divider-gray">

    <div class="total-section">
  <p class="subtotal"><span><b>Sub Total:</b></span> ₱800.00</p>
  <p class="voucher"><span><b>Voucher Discount:</b></span> 0</p>
  <p class="total"><span><b>Total:</b></span> ₱800.00</p>
    </div>


      <p class="note-text">
        Full payment will be collected directly by the service provider upon completion of the service.
      </p>

      <hr class="divider-gray">
        <!-- booking-footer -->
        <div class="booking-footer">
          <div class="service-provider">
            <div class="service-provider-top">
<i class="fa-solid fa-user account-icon"></i>
              <span class="name">Ana Santos</span>
<i class="fa-solid fa-comment message-icon"></i>
            </div>
            <div class="rating">
<i class="fa-solid fa-star" aria-hidden="true"></i> (4.9)
            </div>
          </div>
    </div>
  </main>


<!-- FOOTER -->
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
  </div>
</body>
</html>
