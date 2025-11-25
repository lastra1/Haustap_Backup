<?php
session_start();
// If already logged in, go straight to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

  // Prefer persistent admin credentials stored in storage/data/admins.json
  $dataDir = __DIR__ . '/../../storage/data';
  $adminsFile = $dataDir . '/admins.json';
  $authenticated = false;

  if (is_file($adminsFile)) {
    $contents = @file_get_contents($adminsFile);
    $decoded = json_decode($contents, true);
    if (is_array($decoded)) {
      foreach ($decoded as $a) {
        if (isset($a['email']) && $a['email'] === $email && isset($a['password'])) {
          if (password_verify($password, $a['password'])) {
            $authenticated = true;
            break;
          }
        }
      }
    }
  }

  // Fallback to development static credentials for first-time access
  if (!$authenticated) {
    $allowedEmail = 'admin@haustap.local';
    $allowedPassword = 'Admin123!';
    if ($email === $allowedEmail && $password === $allowedPassword) $authenticated = true;
  }

  if ($authenticated) {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_email'] = $email;
    $_SESSION['admin_name'] = 'Admin';
    header('Location: dashboard.php');
    exit;
  } else {
    $error = 'Invalid email or password.';
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login</title>
  <link rel="stylesheet" href="/css/global.css" />
  <link rel="stylesheet" href="css/login.css" />
  <style>
    /* Keep page consistent with client login styling */
    .accent-bar { display: none; }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo">
      <a href="dashboard.php" aria-label="Go to admin dashboard">
        <img src="images/logo.png" alt="HausTap Logo" width="120" height="100">
      </a>
    </div>
    <form class="login-form" method="post" action="login.php" novalidate>
      <h2>Log In</h2>
      <?php if ($error): ?>
        <div class="error" role="alert" aria-live="polite"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <label for="email">Email</label>
      <input type="email" id="email" name="email" autocomplete="username" required>
      <label for="password">Password</label>
      <input type="password" id="password" name="password" autocomplete="current-password" required>
      <button type="submit" id="loginBtn">Log In</button>
    </form>
  </div>
  <script src="js/login.js" defer></script>
</body>
</html>
