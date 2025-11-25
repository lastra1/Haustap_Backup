<?php require_once __DIR__ . '/includes/auth.php'; ?>
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

      <!-- Change Password Box -->
      <section class="password-section">
        <div class="password-card">
          <h2>Enter Your Password</h2>
          <div class="password-input">
            <input type="password" id="password" placeholder="Enter Your Password">
            <span class="toggle" id="togglePassword"></span>
          </div>
          <button type="button" class="confirm-btn" id="confirmBtn">Confirm</button>
          <p id="cpError" style="color:#d9534f; display:none; margin-top:8px; font-size:13px"></p>
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
    (function(){
      const password = document.getElementById("password");
      const togglePassword = document.getElementById("togglePassword");
      const confirmBtn = document.getElementById('confirmBtn');
      const errEl = document.getElementById('cpError');
      console.debug('change_password:init', { passwordEl: !!password, toggleEl: !!togglePassword, confirmEl: !!confirmBtn, errEl: !!errEl });

      if (togglePassword && password) {
        togglePassword.addEventListener("click", () => {
          const type = password.getAttribute("type") === "password" ? "text" : "password";
          password.setAttribute("type", type);
          console.debug('change_password:toggle', { type });
        });
      }

      // Hide error when user types
      if (password && errEl) {
        password.addEventListener('input', () => { errEl.style.display = 'none'; errEl.textContent = ''; });
        // allow Enter to confirm
        password.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); confirmBtn && confirmBtn.click(); } });
      }

      // Confirm: verify current password via server then navigate to create_password.php
      if (confirmBtn) {
        confirmBtn.addEventListener('click', (e)=>{
          e.preventDefault();
          const val = (password && password.value || '').trim();
          console.debug('change_password:confirm-click', { valuePresent: !!val });
          if (!val) {
            if (errEl) { errEl.textContent = 'Please enter your current password.'; errEl.style.display = 'block'; }
            return;
          }

          // POST to API endpoint to verify password
          const form = new FormData();
          form.append('password', val);

          // Try multiple candidate URLs (robust against different base paths)
          const candidates = [
            'api/verify_password.php',
            '/admin/api/verify_password.php',
            '/admin_haustap/admin_haustap/api/verify_password.php'
          ];

          (async function tryUrls(urls){
            let lastErr = null;
            if (errEl) { errEl.style.display = 'block'; errEl.style.color = '#333'; errEl.textContent = 'Verifying...'; }
            for (const url of urls) {
              console.debug('change_password:trying-url', { url });
              try {
                const resp = await fetch(url, { method: 'POST', body: form, credentials: 'same-origin' });
                const text = await resp.text();
                let json = null;
                try { json = JSON.parse(text); } catch (e) { /* not JSON */ }
                console.debug('change_password:response', { url, status: resp.status, json });

                if (resp.ok && json && json.ok) {
                  console.debug('change_password:validated', { url });
                  if (errEl) { errEl.style.color = '#2b8a3e'; errEl.textContent = 'Verified — redirecting...'; }
                  // small delay so the user sees the success message
                  setTimeout(() => window.location.assign('create_password.php'), 350);
                  return;
                }

                // If server responded but validation failed, show error and stop trying
                if (resp.ok && json && json.ok === false) {
                  if (errEl) { errEl.style.color = '#d9534f'; errEl.textContent = 'Incorrect password.'; errEl.style.display = 'block'; }
                  return;
                }

                lastErr = { url, status: resp.status, json };
              } catch (err) {
                console.warn('change_password:fetch-failed', { url, err });
                lastErr = err;
              }
            }

            console.error('change_password:all-candidates-failed', lastErr);
            if (errEl) { errEl.style.color = '#d9534f'; errEl.textContent = 'Unable to verify password. Try again later.'; errEl.style.display = 'block'; }
          })(candidates);
        });
      }
    })();
  </script>
</body>
</html>


