<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Change Password</title>
  <link rel="stylesheet" href="css/change_password.css">
<script src="js/lazy-images.js" defer></script></head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <?php $active = 'dashboard'; include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
      <header class="topbar">
        <div class="user">
          <button class="notif-btn">🔔</button>
          <div class="user-menu">
            <button class="user-btn" id="userDropdownBtn">
              Mj Punzalan ▼
            </button>
            <div class="dropdown" id="userDropdown">
              <a href="#">View Profile</a>
              <a href="#">Change Password</a>
              <a href="#">Activity Logs</a>
              <a href="#" class="logout">Log out</a>
            </div>
          </div>
        </div>
      </header>

      <!-- Page Header -->
      <section class="page-header">
        <h3>Admin &gt; Change Password</h3>
      </section>

      <!-- Change Password Box -->
      <section class="password-section">
        <div class="password-card">
          <h2>Enter Your Password</h2>
          <div class="password-input">
            <input type="password" id="password" placeholder="Enter Your Password">
            <span class="toggle" id="togglePassword"></span>
          </div>
          <button class="confirm-btn">Confirm</button>
        </div>
      </section>
    </main>
  </div>

  <script>
    // Dropdown toggle
    const dropdownBtn = document.getElementById("userDropdownBtn");
    const dropdown = document.getElementById("userDropdown");
    dropdownBtn.addEventListener("click", () => {
      dropdown.classList.toggle("show");
    });
    window.addEventListener("click", (e) => {
      if (!dropdownBtn.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.remove("show");
      }
    });

    // Password visibility toggle
    const password = document.getElementById("password");
    const togglePassword = document.getElementById("togglePassword");

    togglePassword.addEventListener("click", () => {
      const type = password.getAttribute("type") === "password" ? "text" : "password";
      password.setAttribute("type", type);
    });

    // Confirm button: after entering password, go to OTP verification
    const confirmBtn = document.querySelector('.confirm-btn');
    if (confirmBtn) {
      confirmBtn.addEventListener('click', function(e){
        e.preventDefault();
        // In a real flow, you would validate the current password first.
        // For now, navigate to the Verification Code page (OTP step).
        window.location.href = 'verification_code.php';
      });
    }
  </script>
</body>
</html>


