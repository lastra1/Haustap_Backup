<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>HausTap Client Terms & Conditions</title>
  <link rel="stylesheet" href="/css/global.css" />
  <link rel="stylesheet" href="/client/css/terms.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
  <?php if (!isset($_GET['embed'])) { include __DIR__ . '/includes/header.php'; } ?>

  <main class="terms-page">
    <div class="terms-container">
      <h1>HausTap Client Terms &amp; Conditions</h1>
      <p class="last-updated">Last Updated: October 2025</p>

      <div class="terms-card">
        <p>These Terms and Conditions ("Terms") govern your access to and use of the HausTap mobile and web platform ("HausTap", "we", "our", or "the Platform"). By creating an account or booking services on HausTap, you agree to these Terms.</p>

        <ol class="terms-list">
          <li>
            <strong>Eligibility & Account Registration</strong>
            <p>You must be at least 18 years old to create an account and book services. You agree to provide accurate information and maintain the confidentiality of your login credentials.</p>
          </li>
          <li>
            <strong>Service Request & Booking</strong>
            <p>Bookings are requests made to service providers for home services such as cleaning, repairs, beauty, wellness, and more. Availability is subject to provider acceptance and scheduling.</p>
          </li>
          <li>
            <strong>Payment Terms</strong>
            <p>Payments may be processed via cash or online partners. Prices and fees may vary by service type and location. Vouchers and promotions are subject to their own terms.</p>
          </li>
          <li>
            <strong>Client Responsibilities</strong>
            <p>Ensure safe and reasonable conditions for service performance. Respect cancellation windows and provide accurate address and contact details.</p>
          </li>
          <li>
            <strong>Ratings & Reviews</strong>
            <p>Provide honest feedback. Reviews should be fair, accurate, and not defamatory. We may moderate content to maintain platform integrity.</p>
          </li>
          <li>
            <strong>Limitation of Liability</strong>
            <p>HausTap is a booking facilitator. Service providers are independent contractors responsible for the work performed. To the fullest extent allowed by law, HausTap is not liable for indirect damages.</p>
          </li>
          <li>
            <strong>Suspension or Termination</strong>
            <p>We may suspend or terminate accounts for violations, fraud, or misuse. You may request account deletion subject to applicable policies.</p>
          </li>
          <li>
            <strong>Communication Consent</strong>
            <p>By using the Platform, you consent to receive notifications related to bookings, schedule updates, and promotions. You can manage preferences in your account.</p>
          </li>
          <li>
            <strong>Amendments</strong>
            <p>We may update these Terms periodically. Continued use of the Platform constitutes acceptance of updates. The latest version will be posted here.</p>
          </li>
        </ol>

        
      </div>
      <div class="terms-card" id="privacy" style="margin-top:16px">
        <h2>HausTap Client Privacy Policy</h2>
        <p class="last-updated">Last Updated: October 2025</p>
        <ol class="terms-list">
          <li>
            <strong>Information We Collect</strong>
            <p>We collect information necessary for account creation, booking, and communication, including: full name, birthdate, email address (for OTP and notifications), mobile number, location/address (for service booking and provider matching), and booking history/feedback. We do not collect or store payment card details because all payments are done in cash directly to the service provider.</p>
          </li>
          <li>
            <strong>How We Use Your Information</strong>
            <p>Your data is used for registration and OTP identity verification, sending booking confirmations and service notifications, matching you with nearby verified providers, improving platform security and service experience, and providing support.</p>
          </li>
          <li>
            <strong>Sharing of Information</strong>
            <p>HausTap respects your privacy and will not sell or disclose your personal data to unauthorized third parties. Limited information may be shared with verified service providers only for booking/coordination and with legal authorities when required by law or for security purposes.</p>
          </li>
          <li>
            <strong>Data Security</strong>
            <p>We implement reasonable security measures, including OTP verification and secure login access, restricted access to sensitive data, and monitoring for suspicious or fraudulent activity. However, no digital system is 100% secure; HausTap shall not be held liable for breaches caused by hacking, third‑party intrusion, or situations beyond our control.</p>
          </li>
          <li>
            <strong>Your Rights as a User</strong>
            <p>You have the right to update or correct your information, request account deletion (subject to verification and pending transactions), and decline promotional messages (essential system alerts may still be sent). Requests may be sent through HausTap’s official support channels.</p>
          </li>
          <li>
            <strong>Data Retention</strong>
            <p>Your data will be stored only as long as necessary for account usage, legal compliance, or dispute resolution. Terminated accounts may be securely archived or permanently deleted as needed.</p>
          </li>
          <li>
            <strong>Policy Updates</strong>
            <p>HausTap may update this Privacy Policy at any time to improve safety and compliance. You will be notified of major changes. Continued use of the platform confirms your acceptance.</p>
          </li>
          <li>
            <strong>Contact Information</strong>
            <p>For questions, support, or privacy‑related concerns, you may reach us through our official communication channels: Email, Facebook, or Instagram.</p>
          </li>
        </ol>
        
      </div>
      <div class="terms-actions terms-bottom-actions" style="margin-top:20px; text-align:center;">
        <button type="button" class="back-btn">Back</button>
      </div>
    </div>
  </main>

  <script>
    (function(){
      var isEmbed = (location.search.indexOf('embed=1') !== -1);
      var btns = document.querySelectorAll('.back-btn');
      btns.forEach(function(b){
        b.addEventListener('click', function(e){
          e.preventDefault();
          if (isEmbed && window.parent) {
            try {
              window.parent.postMessage({ type: 'terms.back' }, '*');
              var t = window.parent.document.getElementById('termsModal');
              var p = window.parent.document.getElementById('privacyModal');
              try { t && t.querySelector('.btn-close') && t.querySelector('.btn-close').click(); } catch(e){}
              try { p && p.querySelector('.btn-close') && p.querySelector('.btn-close').click(); } catch(e){}
            } catch (err) {}
          } else {
            history.back();
          }
        });
      });
    })();
  </script>

  <?php if (!isset($_GET['embed'])) { include __DIR__ . '/includes/footer.php'; } ?>
</body>
</html>
