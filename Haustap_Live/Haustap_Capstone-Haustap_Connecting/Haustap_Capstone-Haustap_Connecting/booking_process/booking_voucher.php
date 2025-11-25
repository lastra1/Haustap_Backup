<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Booking Vouchers</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/booking_voucher.css" />
  <link rel="stylesheet" href="/client/css/homepage.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>
  <!-- HEADER -->
  <?php include dirname(__DIR__) . "/client/includes/header.php"; ?>

  <main class="voucher-container">
    <div class="voucher-box">
      <button class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back</button>
      <h1>Vouchers</h1>

      <!-- Voucher 1 -->
      <label class="voucher-option">
        <input type="radio" name="voucher" hidden>
        <div class="voucher-content">
          <span class="voucher-title">Welcome Voucher</span>
          <b>₱50 OFF for First-Time Users</b>
          <p><b>New here? Book your first service today and enjoy ₱50 voucher as our welcome gift.</b></p>
          <small>Condition: First time users only</small>
        </div>
      </label>

      <!-- Voucher 2 -->
      <label class="voucher-option">
        <input type="radio" name="voucher" hidden>
        <div class="voucher-content">
          <span class="voucher-title">Loyalty Bonus</span>
          <b>₱50 Voucher after 10 Completed Bookings</b>
          <p><b>Loyal customers deserve the best. After 10 completed bookings, enjoy a ₱50 voucher as a thank you for staying with HausTap.</p>
          <small>Condition: Must be completed within 3 months</small>
        </div>
      </label>

      <!-- Voucher 3 -->
      <label class="voucher-option">
        <input type="radio" name="voucher" hidden>
        <div class="voucher-content">
          <span class="voucher-title">Referral Bonus</span>
          <b>Earn ₱10 Voucher for every Successful Referral</b>
          <p><b>Share HAUSTAP with friends! Once your friend completes their first booking, you earn a ₱10 voucher.</b></p>
          <small>Condition: Voucher will be credited after your friend’s first completed booking</small>
        </div>
      </label>
    </div>
  </main>

  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
</body>
</html>