<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Account Referral</title>
  <link rel="stylesheet" href="/css/global.css" />
  <link rel="stylesheet" href="../client/css/homepage.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/my_account/css/account_referral.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
  <?php include __DIR__ . '/../client/includes/header.php'; ?>
  <main class="account-referral">
  <div class="referral-box">
    <h2>Referral</h2>
    <hr>

    <div class="referral-section">
      <p class="your-code"><strong>Your Code</strong></p>

      <div class="code-box">
        <div class="inner-box">
          <p class="referral-code">------</p>
        </div>
        <button class="copy-btn">Copy</button>
      </div>

      <div class="add-code-box">
        <p>Add the referral code you have received from your friend</p>
        <input id="friendCodeInput" type="text" placeholder="Enter code (e.g., 6AYI6F)">
        <button class="submit-btn">Submit</button>
      </div>
    </div>
  </div>
</main> 
<?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
</body>
<script>
(function(){
  const codeEl = document.querySelector('.referral-code');
  const copyBtn = document.querySelector('.copy-btn');
  const submitBtn = document.querySelector('.submit-btn');
  const inputEl = document.getElementById('friendCodeInput');

  function getUser(){
    try { return JSON.parse(localStorage.getItem('haustap_user')||'null'); } catch(e){ return null; }
  }
  function ensureLoggedIn(user){ if (!user || !user.email) { window.location.href = '/login'; return false; } return true; }

  async function fetchMyReferral(email){
    const url = `/mock-api/referral?email=${encodeURIComponent(email)}`;
    try {
      const res = await fetch(url, { method: 'GET' });
      const data = await res.json();
      if (data && data.success && data.data && data.data.code) {
        return data.data.code;
      }
    } catch(e){}
    return null;
  }

  async function applyReferral(email, code){
    try {
      const res = await fetch('/mock-api/referral/apply', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, code })
      });
      const data = await res.json();
      if (!res.ok || !data.success) {
        const msg = (data && data.message) ? data.message : 'Unable to apply referral code';
        throw new Error(msg);
      }
      return data;
    } catch(err){ throw err; }
  }

  async function init(){
    const user = getUser();
    if (!ensureLoggedIn(user)) return;
    const myCode = await fetchMyReferral(user.email);
    if (codeEl) { codeEl.textContent = myCode || '------'; }

    if (copyBtn) {
      copyBtn.addEventListener('click', async function(){
        const code = codeEl ? codeEl.textContent.trim() : '';
        if (!code || code === '------') { alert('No referral code available yet.'); return; }
        try { await navigator.clipboard.writeText(code); alert('Referral code copied!'); }
        catch(e){
          // Fallback copy
          const ta = document.createElement('textarea'); ta.value = code; document.body.appendChild(ta); ta.select(); try { document.execCommand('copy'); alert('Referral code copied!'); } finally { document.body.removeChild(ta); }
        }
      });
    }

    if (submitBtn) {
      submitBtn.addEventListener('click', async function(){
        const val = (inputEl && inputEl.value || '').trim().toUpperCase();
        if (!val) { alert('Please enter a referral code'); return; }
        try {
          await applyReferral(user.email, val);
          window.location.href = '/account/referral/success';
        } catch(err){
          alert(err.message || 'Failed to apply code');
        }
      });
    }
  }

  init();
})();
</script>
</html>
