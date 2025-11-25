<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Email Verification | HausTap</title>
  <link rel="stylesheet" href="css/email verfication.css">
  <link rel="icon" href="image/logo.png">
<link rel="stylesheet" href="/client/css/homepage.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head>
<body>

  <!-- Header Logo -->
<?php include dirname(__DIR__) . "/client/includes/header.php"; ?>

  <!-- Tablet Container -->
  <section class="tablet-section">
    <div class="tablet">
      <div class="tablet-screen">
        <h2>Email Verification</h2>
        <p>We have sent a One-Time Password (OTP) to your registered email address. Please enter the code below to verify your email.</p>

        <form class="verify-form">
          <label>Enter Email</label>
          <input type="email" placeholder="Enter your email" required>

          <label>Enter OTP</label>
          <input type="text" placeholder="Enter OTP" required>

          <a href="#" class="resend">Resend OTP</a>

          <button type="submit" class="verify-btn">Verify</button>
        </form>
      </div>
    </div>
  </section>

  <div class="pagination">
      <button>&lt;</button>
      
      
      
      
      <button>&gt;</button>
    </div>
</main>

  <!-- FOOTER -->
<?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
<script>
  (function(){
    var form = document.querySelector('.verify-form');
    var emailInput = form ? form.querySelector('input[type="email"]') : null;
    var codeInput = form ? form.querySelector('input[type="text"]') : null;
    var resendLink = form ? form.querySelector('.resend') : null;
    function getEmail(){
      var v = '';
      try { v = String(localStorage.getItem('ht.app.email') || '').trim(); } catch(e) {}
      if (!v && emailInput) v = String(emailInput.value || '').trim();
      return v;
    }
    function getOtpId(){
      try { return String(localStorage.getItem('ht.app.otpId') || '').trim(); } catch(e) { return ''; }
    }
    function setEmailIfAvailable(){
      var v = getEmail();
      if (emailInput && v) emailInput.value = v;
    }
    async function sendOtp(email){
      var res = await fetch('/mock-api/auth/otp/send/index.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: email })
      });
      if (!res.ok) throw new Error('Failed to send OTP');
      var data = await res.json();
      if (!data || !data.success || !data.otpId) throw new Error('Unexpected response');
      try { localStorage.setItem('ht.app.otpId', String(data.otpId)); } catch(e) {}
    }
    async function verifyOtp(otpId, code){
      var res = await fetch('/mock-api/auth/otp/verify/index.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ otpId: otpId, code: code })
      });
      if (!res.ok) throw new Error('Failed to verify OTP');
      var data = await res.json();
      if (!data || !data.success) throw new Error('Invalid or expired OTP');
    }
    setEmailIfAvailable();
    if (resendLink) {
      resendLink.addEventListener('click', function(ev){
        ev.preventDefault();
        var email = getEmail();
        if (!email) { alert('Please enter your email first.'); return; }
        sendOtp(email).then(function(){ alert('OTP resent. Please check your email.'); })
          .catch(function(){ alert('Failed to resend OTP. Try again.'); });
      });
    }
    if (form) {
      form.addEventListener('submit', function(ev){
        ev.preventDefault();
        var email = getEmail();
        var code = codeInput ? String(codeInput.value || '').trim() : '';
        var otpId = getOtpId();
        if (!email) { alert('Email is required.'); return; }
        if (!code) { alert('Please enter the OTP code.'); return; }
        if (!otpId) { alert('No OTP found. Please resend and try again.'); return; }
        verifyOtp(otpId, code)
          .then(function(){ window.location.href = 'application_process.php'; })
          .catch(function(){ alert('Verification failed. Please check the code and try again.'); });
      });
    }
  })();
</script>
</body>
</html>



