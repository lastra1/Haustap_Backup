<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Enter Verification Code</title>
  <link rel="stylesheet" href="../client/css/homepage.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/my_account/css/verification_code.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <script src="/login_sign up/js/api.js"></script>
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
              <li><a href="#" class="active">Profile</a></li>
              <li><a href="#">Addresses</a></li>
              <li><a href="#">Privacy Settings</a></li>
            </ul>
          </div>
          <ul class="sidebar-secondary">
            <li><i class="fa-solid fa-user-group"></i> Referral</li>
            <li><a href="/account/voucher" class="account-link"><i class="fa-solid fa-ticket"></i> My Vouchers</a></li>
            <li><i class="fa-solid fa-link"></i> Connect Haustap</li>
            <li><i class="fa-solid fa-file-contract"></i> Terms and Conditions</li>
            <li><i class="fa-solid fa-star"></i> Rate HOMI</li>
            <li><i class="fa-solid fa-circle-info"></i> About us</li>
          </ul>

          <button class="logout-btn">Log out</button>
        </nav>
      </aside>

      <!-- RIGHT -->
      <div class="change-password-page">
        <div class="change-password-box">
          <div class="change-password-header">
            <button class="back-btn"><i class="fas fa-arrow-left"></i></button>
            <h2>Enter Verification Code</h2>
          </div>

          <p class="instruction-text">Please enter the verification code sent to your email address.</p>

          <div class="otp-inputs" aria-label="Verification Code">
            <input type="text" inputmode="numeric" maxlength="1" class="otp-digit" autocomplete="one-time-code" />
            <input type="text" inputmode="numeric" maxlength="1" class="otp-digit" />
            <input type="text" inputmode="numeric" maxlength="1" class="otp-digit" />
            <input type="text" inputmode="numeric" maxlength="1" class="otp-digit" />
            <input type="text" inputmode="numeric" maxlength="1" class="otp-digit" />
            <input type="text" inputmode="numeric" maxlength="1" class="otp-digit" />
          </div>

          <div class="otp-actions">
            <p class="resend-row">
              Didn’t receive the code? <a href="#" class="resend-link">Resend Code</a>
            </p>
            <p class="expiry-row">This code will expire in <span class="countdown">01:00</span> minute</p>
            <p class="dev-hint" style="color:#888;font-size:12px"></p>
          </div>

          <button class="submit-btn next-btn">Next</button>
        </div>
      </div>
    </div>
  </main>
  <!-- FOOTER -->
  <?php include __DIR__ . '/../client/includes/footer.php'; ?>

  <script src="/client/js/toast.js"></script>
  <script>
    (function(){
      const API_BASE = window.API_BASE || ((window.location.origin ? window.location.origin : '') + '/mock-api');

      function storedUser(){ try { return JSON.parse(localStorage.getItem('haustap_user')||'null'); } catch { return null; } }
      function getOtpSession(){ try { return JSON.parse(sessionStorage.getItem('haustap_otp')||'null'); } catch { return null; } }
      function setOtpSession(obj){ try { sessionStorage.setItem('haustap_otp', JSON.stringify(obj)); } catch {}
      }

      const backBtn = document.querySelector('.back-btn');
      if (backBtn) backBtn.addEventListener('click', function(){ window.location.href = '/my_account/change_password.php'; });

      const digits = Array.from(document.querySelectorAll('.otp-digit'));
      const submitBtn = document.querySelector('.next-btn');
      const resendLink = document.querySelector('.resend-link');
      const countdownEl = document.querySelector('.countdown');
      const devHint = document.querySelector('.dev-hint');

      // Auto-advance and restrict to digits
      digits.forEach((el, idx) => {
        el.addEventListener('input', function(e){
          this.value = this.value.replace(/\D/g, '').slice(0, 1);
          if (this.value && idx < digits.length - 1) digits[idx+1].focus();
        });
        el.addEventListener('keydown', function(e){
          if (e.key === 'Backspace' && !this.value && idx > 0) digits[idx-1].focus();
        });
        el.addEventListener('paste', function(e){
          const text = (e.clipboardData?.getData('text') || '').replace(/\D/g, '').slice(0, digits.length);
          if (!text) return;
          e.preventDefault();
          for (let i=0; i<digits.length; i++){ digits[i].value = text[i]||''; }
          digits[digits.length-1].focus();
        });
      });

      // Timer (01:00 visual, independent of server 5-min expiry)
      let remain = 60; // seconds
      let timer = null;
      function startTimer(){
        clearInterval(timer);
        remain = 60;
        updateCountdown();
        timer = setInterval(() => { remain--; updateCountdown(); if (remain <= 0) { clearInterval(timer); } }, 1000);
      }
      function updateCountdown(){
        const mm = String(Math.floor(remain/60)).padStart(2,'0');
        const ss = String(remain%60).padStart(2,'0');
        if (countdownEl) countdownEl.textContent = mm+':'+ss;
      }

      // Initialize from session
      const session = getOtpSession();
      if (session?.devCode) devHint.textContent = `(For testing, use ${session.devCode})`;
      startTimer();

      async function sendOtp(){
        const user = storedUser();
        const email = (user && user.email) || '';
        if (!email) { window.htToast?.error ? window.htToast.error('Missing email.', { title: 'Error' }) : alert('Missing email.'); return; }
        try {
          const res = await fetch(`${API_BASE}/auth/otp/send`, {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email })
          });
          const data = await res.json().catch(async ()=>{ const t = await res.text(); try { return JSON.parse(t); } catch { return { message: t }; } });
          if (!res.ok) { throw new Error(data?.message || 'Failed to send OTP'); }
          setOtpSession({ otpId: data.otpId || data.id, devCode: data.devCode || data.code, expiresAt: data.expiresAt });
          devHint.textContent = data.devCode ? `(For testing, use ${data.devCode})` : (data.code ? `(For testing, use ${data.code})` : '');
          startTimer();
          window.htToast?.success ? window.htToast.success('Code resent.', { title: 'OTP Sent' }) : null;
        } catch (err) {
          console.error('Resend OTP error:', err);
          window.htToast?.error ? window.htToast.error('Network error. Please try again.', { title: 'Error' }) : alert('Network error. Please try again.');
        }
      }

      resendLink?.addEventListener('click', function(e){ e.preventDefault(); sendOtp(); });

      async function verifyOtp(){
        const code = digits.map(d => (d.value||'').trim()).join('');
        if (code.length !== 6) { window.htToast?.error ? window.htToast.error('Enter the 6-digit code.', { title: 'Invalid' }) : alert('Enter the 6-digit code.'); return; }
        const sess = getOtpSession();
        const otpId = sess?.otpId || sess?.id;
        if (!otpId) { window.htToast?.error ? window.htToast.error('No OTP session.', { title: 'Error' }) : alert('No OTP session.'); return; }
        try {
          const vRes = await fetch(`${API_BASE}/auth/otp/verify`, {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ otpId, code })
          });
          const vData = await vRes.json().catch(async ()=>{ const t = await vRes.text(); try { return JSON.parse(t); } catch { return { message: t }; } });
          if (!vRes.ok || !vData?.success) { throw new Error(vData?.message || 'Invalid OTP'); }
          window.htToast?.success ? window.htToast.success('OTP verified.', { title: 'Success' }) : null;
          window.location.href = '/my_account/password_saved.php';
        } catch (err) {
          console.error('Verify OTP error:', err);
          window.htToast?.error ? window.htToast.error(err.message || 'Verification failed.', { title: 'Error' }) : alert(err.message || 'Verification failed.');
        }
      }

      submitBtn?.addEventListener('click', function(){ verifyOtp(); });

      // Logout behavior
      const logoutBtn = document.querySelector('.logout-btn');
      if (logoutBtn) logoutBtn.addEventListener('click', function(){
        localStorage.removeItem('haustap_token');
        localStorage.removeItem('haustap_user');
        sessionStorage.removeItem('haustap_otp');
        window.location.href = '/login';
      });
    })();
  </script>
</body>
</html>
