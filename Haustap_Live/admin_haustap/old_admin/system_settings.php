<?php
  require_once __DIR__ . '/includes/db.php';
  // Load saved settings from MySQL (fallback to file-based defaults if empty)
  $defaults = [
    'system_name' => 'HausTap',
    'contact_email' => 'support@example.com',
    'renewal_cycle' => 0,
    'voucher_deduction' => 0,
    'send_email_notifications' => 0,
    'in_app_alerts' => 1,
  ];
  $settings = $defaults;
  try {
    $db = get_db();
    if (table_exists($db, 'system_settings')) {
      $row = $db->query('SELECT system_name, contact_email, renewal_cycle, voucher_deduction, send_email_notifications, in_app_alerts FROM system_settings ORDER BY id ASC LIMIT 1')->fetch();
      if ($row) {
        $settings = array_merge($defaults, $row);
      }
    }
  } catch (Throwable $e) {
    // Silently fall back to defaults
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
            <button id="userDropdownBtn" class="user-dropdown-btn"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?> ▼</button>
            <div class="user-dropdown" id="userDropdown">
              <a href="#">View Profile</a>
              <a href="#">Change Password</a>
              <a href="#">Activity Logs</a>
              <a href="#" class="logout">Log out</a>
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
<button class="upload-btn"><i class="fa-solid fa-upload"></i></button>
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
              <input type="checkbox" id="renewalCycle" <?= !empty($settings['renewal_cycle']) ? 'checked' : '' ?>>
              <span class="slider"></span>
            </label>
          </div>

          <div class="toggle-group">
            <label>Voucher Deduction</label>
            <label class="switch">
              <input type="checkbox" id="voucherDeduction" <?= !empty($settings['voucher_deduction']) ? 'checked' : '' ?>>
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
              <input type="checkbox" id="sendEmail" <?= !empty($settings['send_email_notifications']) ? 'checked' : '' ?>>
              <span class="slider"></span>
            </label>
          </div>

          <div class="toggle-group">
            <label>In-App Alerts</label>
            <label class="switch">
              <input type="checkbox" id="inAppAlerts" <?= !empty($settings['in_app_alerts']) ? 'checked' : '' ?>>
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
      <section class="settings-card">
        <h4>Preview</h4>
        <div class="info-row">
          <p><strong>Renewal Cycle:</strong> <span id="previewRenewal"><?= !empty($settings['renewal_cycle']) ? 'Enabled' : 'Disabled' ?></span></p>
        </div>
        <div class="info-row">
          <p><strong>Voucher Deduction:</strong> <span id="previewVoucher"><?= !empty($settings['voucher_deduction']) ? 'Enabled' : 'Disabled' ?></span></p>
        </div>
        <div class="info-row">
          <p><strong>Email Notifications:</strong> <span id="previewEmail"><?= !empty($settings['send_email_notifications']) ? 'Enabled' : 'Disabled' ?></span></p>
        </div>
        <div class="info-row">
          <p><strong>In-App Alerts:</strong> <span id="previewAlerts"><?= !empty($settings['in_app_alerts']) ? 'Enabled' : 'Disabled' ?></span></p>
        </div>
      </section>
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

    // Show popup when Save is clicked
    saveButtons.forEach(btn => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        popup.style.display = "flex";
      });
    });

    // Close popup
    cancelPopup.addEventListener("click", () => {
      popup.style.display = "none";
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
      const renewalEl = document.getElementById('renewalCycle');
      const voucherEl = document.getElementById('voucherDeduction');
      const sendEmailEl = document.getElementById('sendEmail');
      const alertsEl = document.getElementById('inAppAlerts');
      const name = (nameEl && nameEl.value || '').trim();
      const email = (emailEl && emailEl.value || '').trim();

      if (!name) { showToast('System name is required.', 'error'); return; }
      if (!email) { showToast('Contact email is required.', 'error'); return; }

      const fd = new FormData();
      fd.append('system_name', name);
      fd.append('contact_email', email);
      fd.append('renewal_cycle', renewalEl && renewalEl.checked ? '1' : '0');
      fd.append('voucher_deduction', voucherEl && voucherEl.checked ? '1' : '0');
      fd.append('send_email_notifications', sendEmailEl && sendEmailEl.checked ? '1' : '0');
      fd.append('in_app_alerts', alertsEl && alertsEl.checked ? '1' : '0');

      // If a unified backend is configured, try it first
      const backendBase = (function(){
        const raw = (window.BACKEND_BASE || window.API_BASE || '').toString().trim();
        return raw ? raw.replace(/\/+$/, '') : '';
      })();
      const candidates = [
        backendBase ? backendBase + '/admin/settings' : null,
        'api/save_settings.php',
        '/admin/api/save_settings.php',
        '/admin_haustap/admin_haustap/api/save_settings.php'
      ].filter(Boolean);

      let lastError = 'Save failed';
      for (const url of candidates) {
        try {
          const resp = await fetch(url, {
            method: 'POST',
            body: fd,
            credentials: 'same-origin'
          });
          const data = await resp.json().catch(() => ({}));
          if (resp.ok && data && (data.ok || data.success)) {
            showToast('Settings saved', 'success');
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

    function bindPreview() {
      const renewalEl = document.getElementById('renewalCycle');
      const voucherEl = document.getElementById('voucherDeduction');
      const sendEmailEl = document.getElementById('sendEmail');
      const alertsEl = document.getElementById('inAppAlerts');
      const prRenewal = document.getElementById('previewRenewal');
      const prVoucher = document.getElementById('previewVoucher');
      const prEmail = document.getElementById('previewEmail');
      const prAlerts = document.getElementById('previewAlerts');
      const setText = (el, on) => { if (el) el.textContent = on ? 'Enabled' : 'Disabled'; };
      const sync = () => {
        setText(prRenewal, renewalEl && renewalEl.checked);
        setText(prVoucher, voucherEl && voucherEl.checked);
        setText(prEmail, sendEmailEl && sendEmailEl.checked);
        setText(prAlerts, alertsEl && alertsEl.checked);
      };
      ['change', 'input'].forEach(evt => {
        if (renewalEl) renewalEl.addEventListener(evt, sync);
        if (voucherEl) voucherEl.addEventListener(evt, sync);
        if (sendEmailEl) sendEmailEl.addEventListener(evt, sync);
        if (alertsEl) alertsEl.addEventListener(evt, sync);
      });
      sync();
    }

    bindPreview();
      showToast(lastError, 'error');
    }

    // Verify password (development logic) then save
    verifyPopup.addEventListener("click", () => {
      const password = document.getElementById("adminPassword").value;
      const allowedPassword = "Admin123!"; // dev credential used in login.php
      if (password.trim() === allowedPassword) {
        popup.style.display = "none";
        saveSettings();
      } else {
        showToast("Incorrect password!", "error");
      }
    });
  </script>
</body>
</html>


