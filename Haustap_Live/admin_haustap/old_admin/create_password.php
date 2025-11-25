<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Change Password</title>
  <link rel="stylesheet" href="css/create_password.css">
<script src="js/lazy-images.js" defer></script></head>
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
            <button class="user-btn" id="userDropdownBtn"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?> ▼</button>
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

      <!-- Change Password Form -->
      <section class="password-section">
        <div class="password-card">
          <form>
            <div class="form-group">
              <label for="current-password">Current Password</label>
              <input type="password" id="current-password" placeholder="Enter Current Password">
            </div>

            <div class="form-group">
              <label for="new-password">New Password</label>
              <div class="input-wrapper">
                <input type="password" id="new-password" placeholder="Enter New Password">
                <span class="toggle-password" onclick="togglePassword('new-password')"></span>
              </div>
            </div>

            <div class="form-group">
              <label for="confirm-password">Confirm Password</label>
              <div class="input-wrapper">
                <input type="password" id="confirm-password" placeholder="Re-Enter New Password">
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
      input.type = input.type === "password" ? "text" : "password";
    }

    // Dropdown toggle
    const dropdownBtn = document.getElementById("userDropdownBtn");
    const dropdown = document.getElementById("userDropdown");
    dropdownBtn.addEventListener("click", () => dropdown.classList.toggle("show"));
    window.addEventListener("click", (e) => {
      if (!dropdownBtn.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.remove("show");
      }
    });

    // Save: after setting new password, return to profile page
    const saveBtn = document.querySelector('.save-btn');
    if (saveBtn) {
      saveBtn.addEventListener('click', function(e){
        e.preventDefault();
        // In a real flow, you would submit to an API and handle response.
        // For now, navigate to Admin Profile to complete the UX flow.
        window.location.href = 'admin_profile.php';
      });
    }
  </script>
</body>
</html>


