<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Account</title>
  <link rel="stylesheet" href="/css/global.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/my_account/css/my_account.css" />
  <link rel="stylesheet" href="/client/css/homepage.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <script src="/login_sign up/js/api.js"></script>
  <script src="/my_account/js/referral-modal.js" defer></script>

</head>

<body>
  <!-- HEADER -->
<?php include dirname(__DIR__) . "/client/includes/header.php"; ?>

  <main class="account-page">
  <div class="account-container">
    <!-- LEFT SIDEBAR -->
    <aside class="sidebar">
      <div class="profile-card">
  <div class="profile-header-side">
    <i class="fa-solid fa-user fa-2x"></i>
    <div class="profile-text">
      <p class="profile-name">Jenn Bornilla</p>
      <button class="edit-profile-btn">
        <i class="fa-solid fa-pen"></i> Edit Profile
      </button>
    </div>
  </div>
</div>

      <nav class="sidebar-nav">
  <div class="sidebar-nav-group">
    <h4><i class="fa-solid fa-user-circle"></i> My Account</h4>
    <ul>
      <li><a href="/account" class="active">Profile</a></li>
      <li><a href="/account/address">Addresses</a></li>
      <li><a href="/account/privacy">Privacy Settings</a></li>
    </ul>
  </div>
  <ul class="sidebar-secondary">
    <li><a href="/account/referral" class="account-link"><i class="fa-solid fa-user-group"></i> Referral</a></li>
            <li><a href="/account/voucher" class="account-link"><i class="fa-solid fa-ticket"></i> My Vouchers</a></li>
    <li><a href="/account/connect" class="account-link"><i class="fa-solid fa-link"></i> Connect Haustap</a></li>
    <li><a href="/account/terms" class="account-link"><i class="fa-solid fa-file-contract"></i> Terms and Conditions</a></li>
    <li><a href="/client/homepage.php#testimonials" class="account-link"><i class="fa-solid fa-star"></i> Rate HOMI</a></li>
    <li><a href="/about" class="account-link"><i class="fa-solid fa-circle-info"></i> About us</a></li>
  </ul>

  <button class="logout-btn">Log out</button>
</nav>
    </aside>

    <!-- RIGHT MAIN CONTENT -->
    <section class="profile-section">
      <div class="profile-box">
        <h2 class="profile-header">Profile</h2>

        <div class="profile-content">
          <div class="profile-info">
            <div class="info-row">
              <label>Name:</label>
              <input type="text" id="name" placeholder="">
            </div>

            <div class="info-row">
              <label>Email:</label>
              <span>jen********@gmail.com <a href="#">Change</a></span>
            </div>

            <div class="info-row">
              <label>Phone Number:</label>
              <span>********89 <a href="#">Change</a></span>
            </div>

            <div class="info-row">
              <label>Gender:</label>
              <span>
                <label><input type="radio" name="gender"> Male</label>
                <label><input type="radio" name="gender"> Female</label>
              </span>
            </div>

            <div class="info-row">
              <label>Date of Birth:</label>
              <span><a href="#">Add</a></span>
            </div>

            <div class="btn-group">
              <button class="change-password">Change Password</button>
              <button class="save-btn">Save</button>
            </div>
          </div>

          <div class="profile-image">
            <i class="fa-solid fa-user fa-4x"></i>
            <button class="select-image">Select Image</button>
            <p class="file-note">File size: maximum 1MB<br>File extension: JPEG, PNG</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</main>

   <!-- FOOTER -->
<?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
  <script src="/client/js/toast.js"></script>
  <script>
    (function() {
      const API_BASE = (function() {
        const override = (window.API_BASE || '').replace(/\/+$/, '');
        if (override) return override;
        const origin = (window.location && window.location.origin) || '';
        return origin ? origin + '/mock-api' : '/mock-api';
      })();

      function getStoredUser() {
        try { return JSON.parse(localStorage.getItem('haustap_user') || 'null'); } catch { return null; }
      }
      function setStoredUser(u) {
        try { localStorage.setItem('haustap_user', JSON.stringify(u)); } catch {}
      }
      function hasToken() {
        try { return !!localStorage.getItem('haustap_token'); } catch { return false; }
      }

      async function fetchMe() {
        if (!hasToken()) return null;
        try {
          const token = localStorage.getItem('haustap_token');
          const res = await fetch(`${API_BASE}/auth/me`, {
            method: 'GET',
            headers: { 'Authorization': `Bearer ${token}` },
          });
          if (!res.ok) return null;
          const ct = res.headers.get('content-type') || '';
          if (ct.includes('application/json')) return res.json();
          return null;
        } catch { return null; }
      }

      const setRowValue = (labelText, value, withChangeLink = true) => {
        const rows = document.querySelectorAll('.info-row');
        for (const row of rows) {
          const label = row.querySelector('label');
          if (!label) continue;
          if ((label.textContent || '').trim().toLowerCase() === labelText.toLowerCase()) {
            let target = row.querySelector('span');
            if (!target) {
              target = document.createElement('span');
              row.appendChild(target);
            }
            if (withChangeLink) {
              target.innerHTML = `${value || ''} <a href="#" class="change-link">Change</a>`;
            } else {
              target.textContent = value || '';
            }
            break;
          }
        }
      };

      function computeDisplayName(u) {
        if (!u) return '';
        const first = (u.firstName || '').trim();
        const last = (u.lastName || '').trim();
        const combined = `${first} ${last}`.trim();
        const name = (u.name || '').trim();
        // Prefer explicit account name; fall back to first+last.
        // Do NOT fall back to email local-part.
        return name || combined;
      }

      async function hydrate() {
        // Try server first (backend); fallback to local cache
        const serverUser = await fetchMe();
        let user = serverUser || getStoredUser() || null;
        if (serverUser) setStoredUser(serverUser);

        // Redirect guests to login for profile page consistency (Figma behavior)
        if (!user) {
          window.location.href = '/login';
          return;
        }

        const fullName = computeDisplayName(user);
        const profileNameEl = document.querySelector('.profile-name');
        if (profileNameEl && fullName) profileNameEl.textContent = fullName;
        const nameInput = document.getElementById('name');
        if (nameInput && fullName) nameInput.value = fullName;

        setRowValue('Email:', user.email || '');
        setRowValue('Phone Number:', user.mobile || user.phone || '');

        if (user.gender) {
          const radios = document.querySelectorAll('input[type="radio"][name="gender"]');
          const g = (user.gender || '').toLowerCase();
          if (radios.length >= 2) {
            if (g === 'male') radios[0].checked = true;
            if (g === 'female') radios[1].checked = true;
          }
        }

        const dob = user.dob || (user.birthMonth && user.birthDay && user.birthYear ? `${user.birthMonth}/${user.birthDay}/${user.birthYear}` : '');
        if (dob) setRowValue('Date of Birth:', dob, false);
      }

      function wireActions() {
        // Save button: persist locally to reflect immediate changes (Figma: Save)
        const saveBtn = document.querySelector('.save-btn');
        if (saveBtn) {
          saveBtn.addEventListener('click', function() {
            const nameInput = document.getElementById('name');
            const u = getStoredUser() || {};
            if (nameInput) u.name = nameInput.value.trim();
            // Persist selected gender
            const radios = document.querySelectorAll('input[type="radio"][name="gender"]');
            if (radios && radios.length) {
              if (radios[0].checked) u.gender = 'male';
              else if (radios[1].checked) u.gender = 'female';
            }
            setStoredUser(u);
            if (window.htToast && typeof window.htToast.success === 'function') {
              window.htToast.success('Profile saved.', { title: 'Saved' });
            } else { alert('Profile saved.'); }
          });
        }

        // Change Password: navigate to local page that matches Figma
        const changePwdBtn = document.querySelector('.change-password');
        if (changePwdBtn) {
          changePwdBtn.addEventListener('click', function() {
            window.location.href = '/my_account/change_password.php';
          });
        }

        // Email/Phone Change links: simple prompt update without altering layout
        document.addEventListener('click', function(e) {
          const t = e.target;
          if (t && t.classList && t.classList.contains('change-link')) {
            e.preventDefault();
            const row = t.closest('.info-row');
            const label = row?.querySelector('label')?.textContent?.trim().toLowerCase();
            const u = getStoredUser() || {};
            if (label === 'email:') {
              const val = prompt('Enter new email address:', u.email || '');
              if (val) { u.email = val.trim(); setStoredUser(u); setRowValue('Email:', u.email || ''); }
            } else if (label === 'phone number:') {
              const val = prompt('Enter new phone number:', u.mobile || u.phone || '');
              if (val) { u.mobile = val.trim(); setStoredUser(u); setRowValue('Phone Number:', u.mobile || u.phone || ''); }
            }
          }
        });

        // Log out clears local data and navigates to login
        const logoutBtn = document.querySelector('.logout-btn');
        if (logoutBtn) {
          logoutBtn.addEventListener('click', function() {
            localStorage.removeItem('haustap_token');
            localStorage.removeItem('haustap_user');
            window.location.href = '/login';
          });
        }
      }

      // Initialize
      hydrate().then(wireActions);
    })();
  </script>
</body>
</html>
