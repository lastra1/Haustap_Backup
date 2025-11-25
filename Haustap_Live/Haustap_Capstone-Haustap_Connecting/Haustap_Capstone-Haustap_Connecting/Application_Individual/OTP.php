<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Email Verification | Homi</title>
  <link rel="stylesheet" href="css/OTP.css">
<link rel="stylesheet" href="/client/css/homepage.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head>
<body><?php include dirname(__DIR__) . "/client/includes/header.php"; ?>
  <div class="container">
    <div class="logo">
      <img src="/Application_Individual/image/logo.png" alt="HausTap" class="
      logo-img">
    </div>
    <div class="verify-box">
      <h2>Email Verification</h2>
      <div class="verify-panel">
        <p>
          We have sent a One-Time Password (OTP) to your registered email address. Please enter the code below to verify your email.
        </p>
        <input type="text" placeholder="Enter Email">
        <input type="text" placeholder="Enter OTP">
        <div class="resend">Resend OTP</div>
        <button type="submit">Verify</button>
      </div>
    </div>
    <div class="pagination">
      <button>&lt;</button>
      
      
      
      
      <button>&gt;</button>
    </div>
</main>

  <!-- FOOTER -->
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
</body>
</html>



