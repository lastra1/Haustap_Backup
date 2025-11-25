<?php require_once __DIR__ . '/includes/auth.php';
  // Load saved settings (file-based dev storage)
  // Prefer absolute base path to avoid issues under different routers
  if (defined('BASE_PATH')) {
    $settingsPath = BASE_PATH . DIRECTORY_SEPARATOR . 'admin_haustap' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'system-settings.json';
  } else {
    $settingsPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'system-settings.json';
  }

  $defaults = [
    'system_name' => 'Ana Santos',
    'contact_email' => 'haustap@gmail.com'
  ];
  $settings = $defaults;
  if (is_file($settingsPath)) {
    $loaded = json_decode(@file_get_contents($settingsPath), true);
    if (is_array($loaded)) {
      $settings = array_merge($defaults, $loaded);
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>System Settings | Admin Dashboard</title>
  <link rel="stylesheet" href="css/system_settings.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="js/lazy-images.js" defer></script></head>
<body>
  <div class="dashboard-container">

    <!-- Sidebar -->
    <?php $active = 'settings'; include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
      <header class="topbar">
        <h3>System Settings</h3>
        <div class="user">
          <button class="notif-btn">🔔</button>
          <div class="user-menu">
            <button id="userDropdownBtn" class="user-dropdown-btn">Mj Punzalan ▼</button>
            <div class="user-dropdown" id="userDropdown">
           <a href="admin_profile.php">View Profile</a>
           <a href="change_password.php">Change Password</a>
           <a href="logout.php" class="logout">Log out</a>
            </div>
          </div>
        </div>
      </header>

      <!-- Settings Sections -->
      <div class="settings-container">

        <!-- General Settings -->
        <section class="settings-card">
          <h4>General Settings</h4>
          <div class="form-group">
            <label>System Name:</label>
            <input type="text" id="systemName" name="system_name" value="<?= htmlspecialchars($settings['system_name'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label>System Logo:</label>
            <div class="logo-upload">
              <div class="logo-preview" id="logoPreview">
                <span class="placeholder-text">No logo</span>
              </div>
              <div class="logo-actions">
                <input type="file" id="systemLogoInput" accept="image/*" style="display:none">
                <button type="button" class="upload-btn" id="uploadBtn"><i class="fa-solid fa-upload"></i> Upload</button>
                <button type="button" class="remove-btn" id="removeLogoBtn"><i class="fa-solid fa-trash"></i> Remove</button>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Contact Email:</label>
            <input type="email" id="contactEmail" name="contact_email" value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label>Default Language:</label>
            <div class="radio-group">
              <label><input type="radio" name="language" checked> English</label>
              <label><input type="radio" name="language"> Tagalog</label>
            </div>
          </div>

          <button class="save-btn">Save</button>
        </section>

        <!-- Subscription Settings -->
        <section class="settings-card">
          <h4>Subscription Settings</h4>
          <div class="form-group">
            <label>Plan Type:</label>
            <input type="text" value="Haustap Standard Access">
          </div>

          <div class="toggle-group">
            <label>Renewal Cycle</label>
            <label class="switch">
              <input type="checkbox">
              <span class="slider"></span>
            </label>
          </div>

          <div class="toggle-group">
            <label>Voucher Deduction</label>
            <label class="switch">
              <input type="checkbox">
              <span class="slider"></span>
            </label>
          </div>

          <button class="save-btn">Save</button>
        </section>

        <!-- Notification Settings -->
        <section class="settings-card">
          <h4>Notification Settings</h4>
          <div class="toggle-group">
            <label>Send Email Notifications</label>
            <label class="switch">
              <input type="checkbox">
              <span class="slider"></span>
            </label>
          </div>

          <div class="toggle-group">
            <label>In-App Alerts</label>
            <label class="switch">
              <input type="checkbox" checked>
              <span class="slider"></span>
            </label>
          </div>

          <button class="save-btn">Save</button>
        </section>

        <!-- System Info -->
        <section class="settings-card system-info">
          <h4>System Info</h4>
          <div class="info-row">
            <p><strong>Version:</strong> Version 1.0</p>
          </div>
          <div class="info-row">
            <p><strong>Last Update:</strong> October 2025</p>
          </div>
        </section>

      </div>
    </main>
  </div>

  <!-- Confirm Admin Access Popup -->
  <div id="adminPopup" class="popup-overlay">
    <div class="popup-box">
      <h3>Confirm Admin Access</h3>
      <p>For security purposes, please re-enter your admin password to access System Settings</p>

      <div class="form-group">
        <label>Password:</label>
        <input type="password" id="adminPassword" placeholder="Enter admin password">
      </div>

      <div class="popup-buttons">
        <button id="cancelPopup" class="cancel-btn">Cancel</button>
        <button id="verifyPopup" class="verify-btn">Verify Access</button>
      </div>
    </div>
  </div>

  <!-- Themed Toast Notification -->
  <div id="toast" class="toast" role="status" aria-live="polite" aria-atomic="true"></div>

  <script>
    // User dropdown menu
    const dropdownBtn = document.getElementById("userDropdownBtn");
    const dropdown = document.getElementById("userDropdown");

    dropdownBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      dropdown.classList.toggle("show");
    });

    window.addEventListener("click", (e) => {
      if (!dropdown.contains(e.target)) dropdown.classList.remove("show");
    });

    // Admin password popup logic
    const popup = document.getElementById("adminPopup");
    const cancelPopup = document.getElementById("cancelPopup");
    const verifyPopup = document.getElementById("verifyPopup");
    const saveButtons = document.querySelectorAll(".save-btn");
    let isVerified = false;

    // Show popup on page load (when entering system settings)
    window.addEventListener('DOMContentLoaded', () => {
      popup.style.display = "flex";
    });

    // Close popup and prevent access
    cancelPopup.addEventListener("click", () => {
      // If not verified, redirect back
      if (!isVerified) {
        window.location.href = 'dashboard.php';
      } else {
        popup.style.display = "none";
      }
    });

    // Toast helper (HausTap themed)
    function showToast(message, type = 'success') {
      const toast = document.getElementById('toast');
      toast.textContent = message;
      toast.className = 'toast ' + type + ' show';
      // Auto hide after 2.5s
      setTimeout(() => {
        toast.className = 'toast';
        toast.textContent = '';
      }, 2500);
    }

    // Save settings to backend
    async function saveSettings() {
      const nameEl = document.getElementById('systemName');
      const emailEl = document.getElementById('contactEmail');
      const name = (nameEl && nameEl.value || '').trim();
      const email = (emailEl && emailEl.value || '').trim();

      if (!name) { showToast('System name is required.', 'error'); return; }
      if (!email) { showToast('Contact email is required.', 'error'); return; }

      const fd = new FormData();
      fd.append('system_name', name);
      fd.append('contact_email', email);

      const candidates = [
        'api/save_settings.php',
        '/admin/api/save_settings.php',
        '/admin_haustap/admin_haustap/api/save_settings.php'
      ];

      let lastError = 'Save failed';
      for (const url of candidates) {
        try {
          const resp = await fetch(url, {
            method: 'POST',
            body: fd,
            credentials: 'same-origin'
          });
          const data = await resp.json().catch(() => ({}));
          if (resp.ok && data && data.ok) {
            showToast('Settings saved', 'success');
            // Refresh to rehydrate inputs from persisted JSON
            setTimeout(() => { try { location.reload(); } catch (_) {} }, 600);
            return;
          } else {
            lastError = (data && data.error) || ('Save failed: ' + resp.status);
          }
        } catch (err) {
          lastError = 'Save error: ' + err.message;
        }
      }
      showToast(lastError, 'error');
    }

    // Verify password (development logic) then allow access
    verifyPopup.addEventListener("click", () => {
      const password = document.getElementById("adminPassword").value;
      const allowedPassword = "Admin123!"; // dev credential used in login.php
      if (password.trim() === allowedPassword) {
        isVerified = true;
        popup.style.display = "none";
        document.getElementById("adminPassword").value = '';
        showToast('Access verified', 'success');
      } else {
        showToast("Incorrect password!", "error");
      }
    });

    // Save buttons now just save without showing popup
    saveButtons.forEach(btn => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        if (isVerified) {
          saveSettings();
        } else {
          showToast('Please verify your password first', 'error');
        }
      });
    });

    // Allow Enter key in password field to verify
    document.getElementById('adminPassword').addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        verifyPopup.click();
      }
    });

    // Logo upload behavior: preview + remove
    (function(){
      const input = document.getElementById('systemLogoInput');
      const uploadBtn = document.getElementById('uploadBtn');
      const removeBtn = document.getElementById('removeLogoBtn');
      const preview = document.getElementById('logoPreview');

      function setPreview(src){
        preview.innerHTML = '';
        if (!src) {
          const span = document.createElement('span');
          span.className = 'placeholder-text';
          span.textContent = 'No logo';
          preview.appendChild(span);
          return;
        }
        const img = document.createElement('img');
        img.src = src;
        img.alt = 'System logo';
        preview.appendChild(img);
      }

      uploadBtn && uploadBtn.addEventListener('click', (e)=>{ e.preventDefault(); input && input.click(); });
      removeBtn && removeBtn.addEventListener('click', (e)=>{ e.preventDefault(); setPreview(null); input && (input.value=''); });

      if (input) {
        input.addEventListener('change', (e)=>{
          const f = e.target.files && e.target.files[0];
          if (!f) return setPreview(null);
          if (!f.type.startsWith('image/')) return showToast('Please select an image file', 'error');
          const url = URL.createObjectURL(f);
          setPreview(url);
        });
      }

      // init: no logo
      setPreview(null);
    })();
  </script>
</body>
</html>


