<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up | HausTap</title>
  <link rel="stylesheet" href="../css/global.css">
  <link rel="stylesheet" href="css/sign up.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Minimal styles for OTP overlay */
    .otp-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 9999; }
    .otp-modal { background: #fff; width: 100%; max-width: 420px; border-radius: 8px; padding: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
    .otp-modal h3 { margin: 0 0 8px; }
    .otp-modal p { margin: 0 0 12px; color: #444; }
    .otp-input { letter-spacing: 6px; font-size: 20px; text-align: center; padding: 10px; width: 100%; box-sizing: border-box; }
    .otp-actions { margin-top: 14px; display: flex; gap: 8px; }
    .otp-actions button { flex: 1; padding: 10px; border: none; border-radius: 6px; cursor: pointer; }
    .btn-primary { background: #1db7a6; color: #fff; }
    .btn-secondary { background: #eee; }
    .otp-hint { font-size: 12px; color: #888; margin-top: 6px; }
    .consent { margin-top: 12px; background:#f8f8f8; border:1px solid #e3e3e3; padding:10px; border-radius:8px }
    .consent-item { display:flex; align-items:flex-start; gap:8px; margin:6px 0 }
    .consent-item input[type="checkbox"] { margin-top:2px }
    .consent small { display:block; color:#666 }
    .doc-frame { width: 100%; height: 70vh; border: 1px solid #e3e3e3; border-radius:6px }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo">
      <img src="image/logo.png" alt="HausTap Logo" width="120" height="100">
    </div>
    <form class="signup-form">
      <h2>Sign Up</h2>
      <div class="row">
        <div>
          <label for="firstName">First Name <span class="required">*</span></label>
          <input type="text" id="firstName" name="firstName" required maxlength="60" pattern="^[A-Za-z][A-Za-z\s'-]{1,}$">
        </div>
        <div>
          <label for="lastName">Last Name <span class="required">*</span></label>
          <input type="text" id="lastName" name="lastName" required maxlength="60" pattern="^[A-Za-z][A-Za-z\s'-]{1,}$">
        </div>
      </div>
      <label>Birthdate <span class="required">*</span></label>
      <div class="inline-triplet">
        <div class="small">
          <label for="birthMonth">MM</label>
          <input type="number" id="birthMonth" name="birthMonth" min="1" max="12" required inputmode="numeric">
        </div>
        <div class="small">
          <label for="birthDay">DD</label>
          <input type="number" id="birthDay" name="birthDay" min="1" max="31" required inputmode="numeric">
        </div>
        <div class="small">
          <label for="birthYear">YYYY</label>
          <input type="number" id="birthYear" name="birthYear" min="1900" max="2100" required inputmode="numeric">
        </div>
      </div>
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required maxlength="120">
      <label for="mobile">Mobile number <span class="required">*</span></label>
      <input type="text" id="mobile" name="mobile" required inputmode="tel" maxlength="16" placeholder="e.g. +639XXXXXXXXX or 09XXXXXXXXX">
      <label for="password">Password <span class="required">*</span></label>
      <div class="input-with-icon">
        <input type="password" id="password" name="password" required maxlength="64">
        <span class="toggle" data-target="password"><i class="fa-solid fa-eye"></i></span>
      </div>
      <label for="confirmPassword">Confirm Password <span class="required">*</span></label>
      <div class="input-with-icon">
        <input type="password" id="confirmPassword" name="confirmPassword" required maxlength="64">
        <span class="toggle" data-target="confirmPassword"><i class="fa-solid fa-eye"></i></span>
      </div>
      <div class="consent" aria-label="User consent">
        <div class="consent-item">
          <input type="checkbox" id="agreeAll" aria-required="true" disabled>
          <label for="agreeAll">I have read and agree to the <a href="/client/terms.php" rel="noopener" id="openTerms">Terms &amp; Conditions</a> and <a href="/client/terms.php#privacy" rel="noopener" id="openPrivacy">Privacy Policy</a>.</label>
        </div>
        <small>To enable the checkbox, open and review both documents.</small>
      </div>
      <button type="submit">sign up</button>
      <div class="login-link">
        Already have an account? <a href="/login">Login</a>
      </div>
      <button type="button" class="partner-btn">Become a HausTap Partner</button>
    </form>
    <!-- OTP Modal -->
    <div class="otp-overlay" id="otpOverlay" role="dialog" aria-modal="true" aria-labelledby="otpTitle">
      <div class="otp-modal">
        <h3 id="otpTitle">Verify your account</h3>
        <p id="otpDesc">Enter the 6-digit code we sent to your contact.</p>
        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="6" class="otp-input" id="otpCode" placeholder="••••••" aria-label="OTP code">
        <div class="otp-actions">
          <button class="btn-secondary" type="button" id="otpResend">Resend Code</button>
          <button class="btn-primary" type="button" id="otpVerify">Verify</button>
        </div>
        <div class="otp-hint" id="otpHint"></div>
      </div>
    </div>
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsTitle" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="termsTitle">Terms &amp; Conditions</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeTerms"></button>
          </div>
          <div class="modal-body">
            <iframe class="doc-frame" src="/client/terms.php?embed=1"></iframe>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="agreeInModalTerms">I Agree</button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyTitle" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="privacyTitle">Privacy Policy</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closePrivacy"></button>
          </div>
          <div class="modal-body">
            <iframe class="doc-frame" src="/client/terms.php?embed=1#privacy"></iframe>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="agreeInModalPrivacy">I Agree</button>
          </div>
        </div>
      </div>
    </div>
  </div>
   <!-- FOOTER -->
<?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-auth-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-firestore-compat.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    (function() {
      const form = document.querySelector('.signup-form');
      if (!form) return;
      var urlNext = (function(){ try { return new URLSearchParams(window.location.search).get('next') || ''; } catch(e){ return ''; } })();
      try { if (urlNext) localStorage.setItem('post_signup_next', urlNext); } catch(e){}

      // OTP elements
      const otpOverlay = document.getElementById('otpOverlay');
      const otpCodeEl = document.getElementById('otpCode');
      const otpResendBtn = document.getElementById('otpResend');
      const otpVerifyBtn = document.getElementById('otpVerify');
      const otpDesc = document.getElementById('otpDesc');
      const otpHint = document.getElementById('otpHint');
      let currentOtpId = null;
      let pendingUser = null;
      let pendingToken = null;
      let apiBaseUsed = window.API_BASE;
      let isMock = typeof apiBaseUsed === 'string' && apiBaseUsed.indexOf('/mock-api') !== -1;
      window.__termsAgreed = !!window.__termsAgreed;
      window.__privacyAgreed = !!window.__privacyAgreed;

      // Navigate to Application Form when "Become a HausTap Partner" is clicked
      const partnerBtn = document.querySelector('.partner-btn');
      if (partnerBtn) {
        partnerBtn.addEventListener('click', function() {
          // Route to the web application form that includes Individual and Team
          window.location.href = '/Application_Individual/application_form.php';
        });
      }

      form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const firstName = document.getElementById('firstName').value.trim();
        const lastName = document.getElementById('lastName').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        const mobile = document.getElementById('mobile').value.trim();

        const birthMonth = document.getElementById('birthMonth').value;
        const birthDay = document.getElementById('birthDay').value;
        const birthYear = document.getElementById('birthYear').value;

        const name = `${firstName} ${lastName}`.trim();
        // Helpers for inline error UI
        function setErr(inputElId, errId, msg){
          var inputEl = document.getElementById(inputElId);
          var errEl = document.getElementById(errId);
          if (inputEl) inputEl.classList.add('invalid');
          if (errEl){ errEl.textContent = msg; errEl.style.display = 'block'; }
        }
        function clrErr(inputElId, errId){
          var inputEl = document.getElementById(inputElId);
          var errEl = document.getElementById(errId);
          if (inputEl) inputEl.classList.remove('invalid');
          if (errEl){ errEl.textContent = ''; errEl.style.display = 'none'; }
        }
        const payload = {
          name,
          email,
          password,
          confirmPassword,
          // Keep extra fields for client-side use (ignored by backend)
          firstName,
          lastName,
          mobile,
          birthMonth,
          birthDay,
          birthYear
        };

        // Client-side validations before hitting backend/OTP
        const emailPattern = /^[^\s@]+@[^\s@]+\.[A-Za-z]{2,}$/;
        const mobileE164 = /^\+?[1-9][0-9]{9,14}$/;
        const mobilePH = /^09[0-9]{9}$/;
        const namePattern = /^[A-Za-z][A-Za-z\s'-]{1,}$/;
        const passwordPolicy = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
        const toInt = (v) => parseInt(v, 10);
        const m = toInt(birthMonth), d = toInt(birthDay), y = toInt(birthYear);
        const isValidDate = (yy, mm, dd) => {
          if (!yy || !mm || !dd) return false;
          const dt = new Date(yy, mm - 1, dd);
          return dt.getFullYear() === yy && (dt.getMonth() + 1) === mm && dt.getDate() === dd;
        };
        if (!firstName || !namePattern.test(firstName) || firstName.length < 2) { alert('Enter a valid first name (letters, spaces, apostrophes).'); return; }
        if (!lastName || !namePattern.test(lastName) || lastName.length < 2) { alert('Enter a valid last name (letters, spaces, apostrophes).'); return; }
        const emailLower = email.toLowerCase();
        if (!emailLower || !emailPattern.test(emailLower) || /\.{2,}/.test(emailLower) || emailLower.startsWith('.') || emailLower.endsWith('.')) {
          setErr('email','emailError','Enter a valid email (e.g., name@example.com).');
          return;
        } else { clrErr('email','emailError'); }
        if (!mobile || !(mobileE164.test(mobile) || mobilePH.test(mobile))) { alert('Enter a valid mobile: +E.164 or PH format 09XXXXXXXXX.'); return; }
        const pwd = (password || '').trim();
        if (!passwordPolicy.test(pwd)) { setErr('password','passwordError','Password must be 8+ chars and include upper, lower, and number.'); return; } else { clrErr('password','passwordError'); }
        if (password !== confirmPassword) { setErr('confirmPassword','confirmError','Passwords do not match.'); return; } else { clrErr('confirmPassword','confirmError'); }
        if (!isValidDate(y, m, d)) { alert('Please enter a valid birth date.'); return; }
        try {
          const dob = new Date(y, m - 1, d);
          const today = new Date();
          let age = today.getFullYear() - dob.getFullYear();
          const mo = today.getMonth() - dob.getMonth();
          if (mo < 0 || (mo === 0 && today.getDate() < dob.getDate())) age--;
          if (age < 18) { alert('You must be at least 18 years old to sign up.'); return; }
        } catch(e) { alert('Birth date is invalid.'); return; }
        if (!window.__termsAgreed) { alert('Please open and read the Terms & Conditions, then click I Agree.'); return; }
        if (!window.__privacyAgreed) { alert('Please open and read the Privacy Policy, then click I Agree.'); return; }
        var agreeAllEl = document.getElementById('agreeAll');
        if (!agreeAllEl?.checked) { alert('You must agree to the Terms & Conditions and Privacy Policy to sign up.'); return; }

        try {
          var configRes = await fetch('http://localhost:3001/api/firebase/firebase-config', { headers: { 'Accept': 'application/json' } });
          if (!configRes.ok) {
            var txt = await configRes.text().catch(function(){ return ''; });
            throw new Error('Auth config fetch failed: ' + (configRes.status || '') + ' ' + (txt || ''));
          }
          var cfgJson;
          try { cfgJson = await configRes.json(); }
          catch(e) { throw new Error('Auth config is not valid JSON'); }
          var cfg = (cfgJson && cfgJson.success) ? cfgJson.config : null;
          if (!cfg) { throw new Error('Auth config missing'); }
          try { firebase.initializeApp(cfg); } catch(e){}
          var auth = firebase.auth();
          var db = firebase.firestore();
          var cred = await auth.createUserWithEmailAndPassword(email, password);
          var user = cred.user;
          var token = await user.getIdToken();
          localStorage.setItem('haustap_token', token);
          var u = { uid: user.uid, email: user.email || email, name: `${firstName} ${lastName}`.trim() || (email.split('@')[0]) };
          localStorage.setItem('haustap_user', JSON.stringify(u));
          try {
            await db.collection('users').doc(user.uid).set({
              email: u.email,
              name: u.name,
              role: 'client',
              roles: ['client'],
              termsAcceptedAt: new Date().toISOString(),
              privacyAcceptedAt: new Date().toISOString(),
              termsVersion: 'v1'
            }, { merge: true });
          } catch(e) {}
          try { await user.sendEmailVerification(); } catch(e){}
          var next = '';
          try { next = localStorage.getItem('post_signup_next') || ''; } catch(e){}
          if (next) {
            try { localStorage.removeItem('post_signup_next'); } catch(e){}
            window.location.href = next;
          } else {
            window.location.href = '../my_account/my_account.php';
          }
        } catch (err) {
          try {
            var msg = (err && err.message) ? err.message : '';
            var code = (err && err.code) ? err.code : '';
            console.error('Firebase registration error:', err);
            alert('Registration failed' + (code ? (' ('+code+')') : '') + (msg ? (': '+msg) : '.'));
          } catch(e) {
            alert('Registration failed.');
          }
        }
      });

      // Resend OTP
      otpResendBtn?.addEventListener('click', async function() {
        if (!pendingUser) return;
        try {
          const sendRes = await fetch(`${apiBaseUsed}/auth/otp/send`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: pendingUser.email })
          });
          let sendData = await sendRes.json().catch(async () => { const t = await sendRes.text(); try { return JSON.parse(t); } catch { return { message: t }; } });
          if (!sendRes.ok || !sendData?.success) { alert(sendData?.message || 'Failed to resend OTP'); return; }
          currentOtpId = sendData.otpId || currentOtpId;
          const devCode = sendData.dev_code || sendData.devCode;
          otpHint.textContent = devCode ? `(For testing, use ${devCode})` : '';
          alert('OTP resent. Please check again.');
        } catch (err) {
          console.error('Resend OTP error:', err);
          alert('Network error. Please try again.');
        }
      });

      otpVerifyBtn?.addEventListener('click', function(){ otpOverlay.style.display = 'none'; });
      // Password toggles
      document.querySelectorAll('.input-with-icon .toggle').forEach(function(tog){
        tog.addEventListener('click', function(){
          var id = tog.getAttribute('data-target');
          var inp = document.getElementById(id);
          if (!inp) return;
          inp.type = inp.type === 'password' ? 'text' : 'password';
          tog.innerHTML = inp.type === 'password' ? '<i class="fa-solid fa-eye"></i>' : '<i class="fa-solid fa-eye-slash"></i>';
        });
      });
    })();
  </script>
</body>
</html>
<script>
  (function(){
    var agreeAllEl = document.getElementById('agreeAll');
    var openTermsLink = document.getElementById('openTerms');
    var openPrivacyLink = document.getElementById('openPrivacy');
    var termsModalEl = document.getElementById('termsModal');
    var privacyModalEl = document.getElementById('privacyModal');
    var termsModal = termsModalEl ? new bootstrap.Modal(termsModalEl) : null;
    var privacyModal = privacyModalEl ? new bootstrap.Modal(privacyModalEl) : null;
    function updateAgreeEnabled(){
      if (!agreeAllEl) return;
      var ready = !!(window.__termsAgreed && window.__privacyAgreed);
      agreeAllEl.disabled = !ready;
    }
    if (openTermsLink && termsModal) { openTermsLink.addEventListener('click', function(ev){ ev.preventDefault(); termsModal.show(); }); }
    if (openPrivacyLink && privacyModal) { openPrivacyLink.addEventListener('click', function(ev){ ev.preventDefault(); privacyModal.show(); }); }
    agreeAllEl?.addEventListener('click', function(ev){ if (agreeAllEl.disabled) { ev.preventDefault(); termsModal?.show(); } });
    document.getElementById('acceptTermsBox')?.addEventListener('change', function(){
      var btn = document.getElementById('agreeInModalTerms');
      if (btn) btn.disabled = !this.checked;
    });
    document.getElementById('acceptPrivacyBox')?.addEventListener('change', function(){
      var btn = document.getElementById('agreeInModalPrivacy');
      if (btn) btn.disabled = !this.checked;
    });
    document.getElementById('agreeInModalTerms')?.addEventListener('click', function(){ termsModal?.hide(); window.__termsAgreed = true; updateAgreeEnabled(); privacyModal?.show(); });
    document.getElementById('agreeInModalPrivacy')?.addEventListener('click', function(){ privacyModal?.hide(); window.__privacyAgreed = true; updateAgreeEnabled(); if (!agreeAllEl.disabled) { agreeAllEl.checked = true; } });
    updateAgreeEnabled();

    // Close modals when embed page asks to go back
    window.addEventListener('message', function(ev){
      try {
        var d = ev.data || {};
        if (d && d.type === 'terms.back') {
          termsModal?.hide();
          privacyModal?.hide();
        }
      } catch(e){}
    });
  })();
</script>
