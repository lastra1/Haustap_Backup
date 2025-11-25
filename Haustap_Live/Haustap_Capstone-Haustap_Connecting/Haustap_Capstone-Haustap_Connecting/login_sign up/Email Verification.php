<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Email Verification | HausTap</title>
<link rel="stylesheet" href="/login_sign%20up/css/email%20verfication.css">
  <link rel="icon" href="image/logo.png">
<link rel="stylesheet" href="/client/css/homepage.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head>
<body>

  <!-- Header Logo -->
<?php include dirname(__DIR__) . "/client/includes/header.php"; ?>

  <!-- Tablet Container -->
  <section class="tablet-section">
    <div class="tablet">
      <div class="tablet-screen">
        <h2>Email Verification</h2>
        <p>We have sent a One-Time Password (OTP) to your registered email address. Please enter the code below to verify your email.</p>

        <form class="verify-form">
          <label>Enter Email</label>
          <input type="email" placeholder="Enter your email" required>

          <label>Enter OTP</label>
          <input type="text" placeholder="Enter OTP" required>

          <a href="#" class="resend">Resend OTP</a>

          <button type="submit" class="verify-btn">Verify</button>
        </form>
      </div>
    </div>
  </section>

  <!-- Pagination -->
  <div class="pagination">
    <a href="#">&lt;</a>
    <a href="#" class="active">1</a>
    <a href="#">2</a>
    <a href="#">3</a>
    <a href="#">4</a>
    <a href="#">&gt;</a>
  </div>

  <!-- FOOTER -->
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
</body>
</html>

