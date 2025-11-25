<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Password Saved</title>
  <link rel="stylesheet" href="../client/css/homepage.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/my_account/css/password_saved.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

</head>
<body>
  <?php include __DIR__ . '/../client/includes/header.php'; ?>


  <main class="account-page">
  <div class="account-container">
    <!-- LEFT SIDEBAR -->
    <aside class="sidebar">
      <div class="profile-card">
  <div class="profile-header-side">
    <i class="fa-solid fa-user fa-2x"></i>
    <div class="profile-text">
      <p class="profile-name">Jenn Bornilla</p>
      <button class="edit-profile-btn">
        <i class="fa-solid fa-pen"></i> Edit Profile
      </button>
    </div>
  </div>
</div>

      <nav class="sidebar-nav">
  <div class="sidebar-nav-group">
    <h4><i class="fa-solid fa-user-circle"></i> My Account</h4>
    <ul>
      <li><a href="#" class="active">Profile</a></li>
      <li><a href="#">Addresses</a></li>
      <li><a href="#">Privacy Settings</a></li>
    </ul>
  </div>
  <ul class="sidebar-secondary">
    <li><a href="/account/referral" class="account-link"><i class="fa-solid fa-user-group"></i> Referral</a></li>
            <li><a href="/account/voucher" class="account-link"><i class="fa-solid fa-ticket"></i> My Vouchers</a></li>
    <li><a href="/account/connect" class="account-link"><i class="fa-solid fa-link"></i> Connect Haustap</a></li>
    <li><a href="/account/terms" class="account-link"><i class="fa-solid fa-file-contract"></i> Terms and Conditions</a></li>
    <li><a href="/client/homepage.php#testimonials" class="account-link"><i class="fa-solid fa-star"></i> Rate HOMI</a></li>
    <li><a href="/about" class="account-link"><i class="fa-solid fa-circle-info"></i> About us</a></li>
  </ul>

  <button class="logout-btn">Log out</button>
</aside>

<div class="change-password-page">
  <div class="change-password-box">
    <div class="change-password-header">
      
      <h2>Password reset successfully</h2>
    </div>
    <div class="success-circle">
        <i class="fa-solid fa-check"></i>
    </div>
    <div>
        <p class="instruction-text">You have successfully reset the password for<br /> this account.</p>
    </div>
    
      <button type="submit" class="submit-btn">Done</button>
    </form>
  </div>
  </div>
</main>
<!-- FOOTER -->
<?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
  
</body>
</html>
