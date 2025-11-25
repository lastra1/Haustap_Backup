<?php include __DIR__ . '/includes/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - View Profile</title>
  <link rel="stylesheet" href="css/profile_admin.css">
  <script src="js/api-base.js"></script>
  <script src="js/user-mode.js"></script>
  <script src="js/lazy-images.js" defer></script>
  <style>
    .mode-toggle { margin-top: 12px; padding: 10px; background: #f8fafb; border-radius: 8px; }
    .mode-toggle label { margin-right: 12px; }
    .mode-hint { font-size: 12px; color: #6b7280; margin-top: 6px; }
  </style>
  </head>
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
              <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?> ▼
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
        <h3>Admin &gt; View Profile</h3>
      </section>

      <!-- Profile Section -->
      <section class="profile-section">
        <div class="profile-card">
          <div class="profile-left">
            <h2>Profile</h2>
            <form>
              <label>Name</label>
              <input type="text" placeholder="Enter name">

              <label>Email</label>
              <div class="email-field">
                <span>je********@gmail.com</span>
                <a href="#">Change</a>
              </div>

              <label>Role</label>
              <p>Superadmin</p>

              <label>Phone Number</label>
              <div class="phone-field">
                <span>********46</span>
                <a href="#">Change</a>
              </div>

              <label>Gender</label>
              <div class="gender">
                <label><input type="radio" name="gender"> Male</label>
                <label><input type="radio" name="gender"> Female</label>
              </div>

              <label>Date of Birth</label>
              <a href="#" class="add-link">Add</a>

              <label>Active Mode</label>
              <div class="mode-toggle">
                <label><input type="radio" name="user_mode" value="client" checked> Client (buying)</label>
                <label><input type="radio" name="user_mode" value="provider"> Service Provider (selling)</label>
                <div class="mode-hint">Switch your role for features and access. This syncs to backend.</div>
              </div>

              <div class="buttons">
                <button type="button" class="change-password">Change Password</button>
                <button type="submit" class="save-btn">Save</button>
              </div>
            </form>
          </div>

          <div class="divider"></div>

          <div class="profile-right">
            <div class="image-upload">
              <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
  <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
  <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
</svg>
              <button class="upload-btn">Select Image</button>
              <p class="file-info">File size: maximum 1 MB<br>File extension: .JPEG, .PNG</p>
            </div>
          </div>
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

    // Sync admin email into localStorage for backend mirroring
    (function() {
      var email = "<?php echo htmlspecialchars($_SESSION['admin_email'] ?? ''); ?>";
      if (email) {
        localStorage.setItem('HT_USER_EMAIL', email);
        window.currentUser = Object.assign({}, window.currentUser || {}, { email: email });
      }
    })();

    // Initialize and wire mode toggle to backend
    (async function() {
      try {
        var current = await (window.AdminUserMode && window.AdminUserMode.sync ? window.AdminUserMode.sync() : { mode: 'client' });
        var radios = document.querySelectorAll('input[name="user_mode"]');
        radios.forEach(function(r) { r.checked = (r.value === (current.mode || 'client')); });

        radios.forEach(function(radio) {
          radio.addEventListener('change', async function(e) {
            var val = e.target.value === 'provider' ? 'provider' : 'client';
            var res = await window.AdminUserMode.setMode(val);
            var ok = res && res.success !== false; // tolerate offline
            var msg = ok ? ('Mode set to ' + val) : ('Saved locally. Enter email to sync.');
            try { console.log('[Mode]', msg); } catch (_) {}
          });
        });
      } catch (err) { try { console.warn('Mode init failed', err); } catch (_) {} }
    })();
  </script>
</body>
</html>


