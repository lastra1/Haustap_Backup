<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

// If already logged in, go straight to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    $loginSuccess = false;
    $userId = null;
    $userName = 'Admin';

    try {
        $user = find_admin_user_by_email($email);
        if ($user && isset($user['password'])) {
            if (password_verify($password, $user['password'])) {
                $loginSuccess = true;
                $userId = (int)$user['id'];
                $userName = $user['name'] ?? $userName;
            }
        } else {
            // Development fallback: allow static credentials and seed admin into DB if used
            $allowedEmail = 'admin@haustap.local';
            $allowedPassword = 'Admin123!';
            if ($email === $allowedEmail && $password === $allowedPassword) {
                $seed = upsert_admin_user($email, $password, $userName);
                $loginSuccess = true;
                $userId = (int)$seed['id'];
                $userName = $seed['name'] ?? $userName;
            }
        }
    } catch (Throwable $e) {
        // On DB errors, allow static credentials to unblock local admin access
        $allowedEmail = 'admin@haustap.local';
        $allowedPassword = 'Admin123!';
        if ($email === $allowedEmail && $password === $allowedPassword) {
            $loginSuccess = true;
            $userId = null;
            $userName = $userName;
        }
    }

    // Record login event regardless of outcome
    record_login_event($userId, $email, $loginSuccess);

    if ($loginSuccess) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_email'] = $email;
        $_SESSION['admin_name'] = $userName;
        if ($userId) {
            $_SESSION['admin_user_id'] = $userId;
        }
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
  <link rel="stylesheet" href="css/global.css" />
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
      <a href="verification_code.php" class="forgot">Forgot Password?</a>
      <button type="submit" id="loginBtn">Log In</button>
    </form>
  </div>
  <script src="js/login.js" defer></script>
</body>
</html>
