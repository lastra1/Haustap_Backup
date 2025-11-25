<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Set Your Password | HausTap</title>
  <link rel="stylesheet" href="/css/global.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
  />
  <link rel="stylesheet" href="/login_sign%20up/css/set-password.css" />
</head>
<body>
  <!-- HEADER -->
<?php include dirname(__DIR__) . "/client/includes/header.php"; ?>

  <!-- MAIN CONTENT -->
  <main>
    <div class="password-box">
      <div class="back-arrow">
        <i class="fas fa-arrow-left"></i>
      </div>
      <h3>Set your password</h3>
      <h4>Create a new password</h4>
      <form>
        <div class="password-input">
          <input type="password" placeholder="Password" required />
          <span class="toggle-password">
            <i class="fas fa-eye-slash"></i>
          </span>
        </div>
        <ul class="password-rules">
          <li>At least one lowercase character</li>
          <li>At least one uppercase character</li>
            <li>8&ndash;16 characters</li>
          <li>Only letters and numbers can be used</li>
        </ul>
        <button type="submit" class="next-btn">Next</button>
      </form>
    </div>
  </main>

  <!-- FOOTER -->
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
</body>
</html>


