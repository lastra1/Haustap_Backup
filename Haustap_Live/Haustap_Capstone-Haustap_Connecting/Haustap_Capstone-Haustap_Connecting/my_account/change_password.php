<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Change Password</title>
  <link rel="stylesheet" href="../client/css/homepage.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/change_password.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
  <?php include __DIR__ . '/../client/includes/header.php'; ?>


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
</aside>

<div class="change-password-page">
  <div class="change-password-box">
    <div class="change-password-header">
      <button class="back-btn"><i class="fas fa-arrow-left"></i></button>
      <h2>Change Password</h2>
    </div>

    <form class="password-form" method="POST" action="#">
      <div class="form-group">
        <label for="current-password">Current Password</label>
        <input type="password" id="current-password" name="current_password" placeholder="Enter current password">
      </div>

      <div class="form-group password-toggle">
        <label for="new-password">New Password</label>
        <div class="input-wrapper">
          <input type="password" id="new-password" name="new_password" placeholder="Enter new password">
          <i class="fas fa-eye toggle-icon"></i>
        </div>
      </div>

      <div class="form-group password-toggle">
        <label for="confirm-password">Confirm New Password</label>
        <div class="input-wrapper">
          <input type="password" id="confirm-password" name="confirm_password" placeholder="Re-enter new password">
          <i class="fas fa-eye toggle-icon"></i>
        </div>
      </div>

      <button type="submit" class="submit-btn">Save</button>
    </form>
  </div>
  </div>
</main>
<!-- FOOTER -->
  <?php include __DIR__ . '/../client/includes/footer.php'; ?>
  <script src="/client/js/toast.js"></script>
  <script>
    (function() {
      // Back button navigates to My Account
      const backBtn = document.querySelector('.back-btn');
      if (backBtn) backBtn.addEventListener('click', () => { window.location.href = '/account'; });

      // Toggle visibility for password fields
      document.querySelectorAll('.password-toggle .toggle-icon').forEach(function(icon){
        icon.addEventListener('click', function() {
          const input = this.closest('.input-wrapper')?.querySelector('input');
          if (!input) return;
          const isPwd = input.type === 'password';
          input.type = isPwd ? 'text' : 'password';
          this.classList.toggle('fa-eye-slash', isPwd);
        });
      });

      const form = document.querySelector('.password-form');
      if (form) {
        form.addEventListener('submit', async function(evt) {
          evt.preventDefault();
          const current = document.getElementById('current-password')?.value?.trim() || '';
          const next = document.getElementById('new-password')?.value?.trim() || '';
          const confirm = document.getElementById('confirm-password')?.value?.trim() || '';

          if (!current || !next || !confirm) {
            window.htToast?.error ? window.htToast.error('Please fill out all fields.', { title: 'Required' }) : alert('Please fill out all fields.');
            return;
          }
          if (next.length < 8) {
            window.htToast?.error ? window.htToast.error('Password must be at least 8 characters.', { title: 'Too Short' }) : alert('Password must be at least 8 characters.');
            return;
          }
          if (next !== confirm) {
            window.htToast?.error ? window.htToast.error('New passwords do not match.', { title: 'Mismatch' }) : alert('New passwords do not match.');
            return;
          }

          // Simulate server-side password update (to be wired to backend later)
          window.htToast?.success ? window.htToast.success('Password updated. A verification code was sent.', { title: 'Password Saved' }) : null;

          // Send OTP to user email and navigate to verification page
          try {
            const user = JSON.parse(localStorage.getItem('haustap_user')||'null');
            const email = user?.email || '';
            if (!email) throw new Error('Missing email.');
            const res = await fetch(`${API_BASE}/auth/otp/send`, {
              method: 'POST', headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ email })
            });
            const data = await res.json().catch(async()=>{ const t = await res.text(); try { return JSON.parse(t); } catch { return { message: t }; } });
            if (!res.ok) { throw new Error(data?.message || 'Failed to send OTP'); }
            sessionStorage.setItem('haustap_otp', JSON.stringify({ otpId: data.otpId || data.id, devCode: data.devCode || data.code, expiresAt: data.expiresAt }));
          } catch (err) {
            console.warn('OTP send error:', err);
          }
          window.location.href = '/my_account/verification_code.php';
        });
      }

      // Logout behavior consistent with My Account page
      const logoutBtn = document.querySelector('.logout-btn');
      if (logoutBtn) logoutBtn.addEventListener('click', function(){
        localStorage.removeItem('haustap_token');
        localStorage.removeItem('haustap_user');
        window.location.href = '/login';
      });
    })();
  </script>
  
</body>
</html>
