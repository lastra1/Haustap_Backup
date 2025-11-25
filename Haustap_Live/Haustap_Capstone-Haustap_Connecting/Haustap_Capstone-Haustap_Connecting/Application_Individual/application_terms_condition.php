<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>HausTap Terms & Conditions</title>
  <link rel="stylesheet" href="css/application terms and condition.css" />
<link rel="stylesheet" href="/client/css/homepage.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head>
<body>
<?php include dirname(__DIR__) . "/client/includes/header.php"; ?>

  <main>
    <div class="tablet">
      <div class="tablet-screen">
        <div class="terms">
          <h3>HausTap Service Provider Terms & Conditions</h3>
          <p>This Agreement is entered into by and between HausTap (&ldquo;The Platform&rdquo;) and the undersigned Service Provider (&ldquo;The Provider&rdquo;). By signing below, the Provider agrees to the following Terms &amp; Conditions:</p>

          <h4>1. Verification & Eligibility</h4>
          <ul>
            <li>The Provider affirms that all submitted information and documents are true and correct.</li>
            <li>HausTap reserves the right to approve, reject, or revoke applications at its sole discretion.</li>
          </ul>

          <h4>2. Subscription & Access</h4>
          <ul>
            <li>An active subscription is required to access bookings through HausTap.</li>
            <li>Non-renewal or failure to renew may result in suspension of account access.</li>
          </ul>

          <h4>3. Service Standards</h4>
          <ul>
            <li>The Provider shall render services with professionalism, integrity, and safety.</li>
            <li>Tools, equipment, and materials are the Providerâ€™s responsibility, unless otherwise agreed with the client.</li>
            <li>Client privacy and confidentiality must always be respected.</li>
          </ul>

          <h4>4. Payments</h4>
          <ul>
            <li>Clients shall pay Providers directly in cash upon completion of services.</li>
            <li>HausTap does not process or hold payments. Disputes must be settled between Provider and Client.</li>
          </ul>

          <h4>5. Ratings & Reviews</h4>
          <ul>
            <li>Clients may leave ratings and reviews based on performance.</li>
            <li>Inappropriate or fake feedback or misconduct may result in suspension or termination.</li>
          </ul>

          <h4>6. Termination</h4>
          <ul>
            <li>HausTap may suspend or terminate this Agreement for violation of terms, false information, or repeated negative feedback.</li>
          </ul>

          <h4>7. Limitation of Liability</h4>
          <ul>
            <li>HausTap serves solely as a booking platform and bears no liability for disputes, damages, or losses arising from services.</li>
            <li>The Provider assumes full responsibility for service quality and outcomes.</li>
          </ul>

          <h4>8. Amendment</h4>
          <ul>
            <li>HausTap may amend these Terms at any time. Continued use of the platform constitutes acceptance of such amendments.</li>
          </ul>

          <h3>HausTap Service Provider Privacy Policy</h3>
          <h4>1. Information We Collect</h4>
          <ul>
            <li>Name, contact details, government ID, certifications, and service history.</li>
          </ul>

          <h4>2. Use of Information</h4>
          <ul>
            <li>For verification, account management, subscription records, and booking facilitation.</li>
          </ul>

          <h4>3. Sharing of Information</h4>
          <ul>
            <li>Data will not be sold or disclosed to unauthorized third parties.</li>
          </ul>

          <h4>4. Data Security</h4>
          <ul>
            <li>HausTap employs reasonable safeguards, including OTP verification and restricted access.</li>
          </ul>

          <h4>5. Provider Rights</h4>
          <ul>
            <li>Providers may request updates, corrections, or deletion of their personal data, subject to legal and operational requirements.</li>
          </ul>

          <h4>6. Policy Updates</h4>
          <ul>
            <li>HausTap may revise this Privacy Policy from time to time. Continued use of the platform constitutes acceptance of updated terms.</li>
          </ul>

          <div class="agreement">
            <input type="checkbox" id="agree" />
            <label for="agree">
              I, the undersigned, confirm that I have read, understood, and agree to the Terms & Conditions and the Privacy Policy set forth by HausTap.
            </label>
          </div>
        </div>
      </div>
    </div>

    <div class="pagination">
      <span>&lt;</span>
      <span class="active">1</span>
      <span>2</span>
      <span>3</span>
      <span>4</span>
      <span>&gt;</span>
    </div>
  </main>

  <!-- FOOTER -->
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
  <script>
    (function(){
      // Attach Next click (last span in pagination) to send OTP and navigate
      var nextSpan = (function(){
        var p = document.querySelector('.pagination');
        if (!p) return null;
        var spans = p.querySelectorAll('span');
        return spans.length ? spans[spans.length - 1] : null;
      })();
      var agree = document.getElementById('agree');
      function getEmail(){
        try { return String(localStorage.getItem('ht.app.email') || '').trim(); }
        catch(e){ return ''; }
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
      if (nextSpan) {
        nextSpan.style.cursor = 'pointer';
        nextSpan.addEventListener('click', function(){
          var email = getEmail();
          if (!agree || !agree.checked) { alert('Please accept the Terms & Conditions to continue.'); return; }
          if (!email) { alert('Please enter your email on the Application Form first.'); window.location.href = 'application_form.php'; return; }
          sendOtp(email)
            .then(function(){ window.location.href = 'Email_Verification.php'; })
            .catch(function(){ alert('Unable to send OTP. Please try again.'); });
        });
      }
    })();
  </script>
</body>
</html>
