<?php
require_once __DIR__ . '/includes/auth.php';

$success = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
    $confirm = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

    if ($new === '') $errors[] = 'New password is required.';
    if ($new !== $confirm) $errors[] = 'Passwords do not match.';
    if ($new !== '' && strlen($new) < 8) $errors[] = 'Password must be at least 8 characters.';

    if (empty($errors)) {
        $dataDir = __DIR__ . '/../../storage/data';
        if (!is_dir($dataDir)) @mkdir($dataDir, 0755, true);
        $adminsFile = $dataDir . '/admins.json';

        $admins = [];
        if (is_file($adminsFile)) {
            $decoded = json_decode(@file_get_contents($adminsFile), true);
            if (is_array($decoded)) $admins = $decoded;
        }

        $email = isset($_SESSION['admin_email']) ? $_SESSION['admin_email'] : 'admin@haustap.local';
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $found = false;
        foreach ($admins as &$a) {
            if (isset($a['email']) && $a['email'] === $email) {
                $a['password'] = $hash;
                $found = true;
                break;
            }
        }
        unset($a);

        if (!$found) {
            $admins[] = ['email' => $email, 'password' => $hash];
        }

        if (@file_put_contents($adminsFile, json_encode($admins, JSON_PRETTY_PRINT)) === false) {
            $errors[] = 'Failed to save the new password. Check permissions on storage/data.';
        } else {
            $success = 'Password updated successfully.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Change Password</title>
  <link rel="stylesheet" href="css/create_password.css">
  <script src="js/lazy-images.js" defer></script>
</head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <?php $active = 'dashboard'; include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Top Bar -->
      <header class="topbar">
        <div class="user">
          <button class="notif-btn">🔔</button>
          <div class="user-menu">
            <button class="user-btn" id="userDropdownBtn">Mj Punzalan ▼</button>
            <div class="dropdown" id="userDropdown">
              <a href="admin_profile.php">View Profile</a>
              <a href="change_password.php">Change Password</a>
              <a href="logout.php" class="logout">Log out</a>
            </div>
          </div>
        </div>
      </header>

      <!-- Page Header -->
      <section class="page-header">
        <h3>Admin &gt; Change Password</h3>
      </section>

      <!-- Change Password Form -->
      <section class="password-section">
        <div class="password-card">
          <?php if ($success): ?>
            <div class="success" style="color: #2b8a3e; margin-bottom:12px"><?= htmlspecialchars($success) ?></div>
          <?php endif; ?>
          <?php if (!empty($errors)): ?>
            <div class="error" style="color:#d9534f; margin-bottom:12px">
              <?php foreach ($errors as $err) echo '<div>' . htmlspecialchars($err) . '</div>'; ?>
            </div>
          <?php endif; ?>

          <form method="post" action="create_password.php">
            <div class="form-group">
              <label for="new-password">New Password</label>
              <div class="input-wrapper">
                <input type="password" id="new-password" name="new_password" placeholder="Enter New Password" required>
                <span class="toggle-password" onclick="togglePassword('new-password')"></span>
              </div>
            </div>

            <div class="form-group">
              <label for="confirm-password">Confirm Password</label>
              <div class="input-wrapper">
                <input type="password" id="confirm-password" name="confirm_password" placeholder="Re-Enter New Password" required>
                <span class="toggle-password" onclick="togglePassword('confirm-password')"></span>
              </div>
            </div>

            <button type="submit" class="save-btn">Save</button>
          </form>
        </div>
      </section>
    </main>
  </div>

  <script>
    // Toggle password visibility
    function togglePassword(id) {
      const input = document.getElementById(id);
      if (!input) return;
      input.type = input.type === "password" ? "text" : "password";
    }

    // Dropdown toggle
    const dropdownBtn = document.getElementById("userDropdownBtn");
    const dropdown = document.getElementById("userDropdown");
    if (dropdownBtn) dropdownBtn.addEventListener("click", () => dropdown.classList.toggle("show"));
    window.addEventListener("click", (e) => {
      if (!dropdownBtn || !dropdown) return;
      if (!dropdownBtn.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.remove("show");
      }
    });
  </script>
</body>
</html>


