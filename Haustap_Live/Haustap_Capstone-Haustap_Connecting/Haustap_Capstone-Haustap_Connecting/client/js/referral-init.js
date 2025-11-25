// Auto-generate a referral code for every logged-in client, globally.
// This runs on page load, is safe to include everywhere, and is idempotent.
(function(){
  if (window.__HT_REFERRAL_INIT_ATTACHED) return;
  window.__HT_REFERRAL_INIT_ATTACHED = true;

  function getUser(){
    try { return JSON.parse(localStorage.getItem('haustap_user') || 'null'); } catch(e){ return null; }
  }

  function ensureReferralCreated(email){
    if (!email) return;
    var key = 'haustap_referral_init_' + String(email).toLowerCase();
    try { if (localStorage.getItem(key) === 'done') return; } catch(e) {}

    var base = ((window.location && window.location.origin) || '') + '/mock-api';
    var url = base + '/referral?email=' + encodeURIComponent(String(email).toLowerCase());
    fetch(url, { method: 'GET' })
      .then(function(res){ return res.ok ? res.json() : Promise.reject(new Error('referral init failed')); })
      .then(function(){ try { localStorage.setItem(key, 'done'); } catch(e) {} })
      .catch(function(){ /* silent: non-blocking initializer */ });
  }

  function init(){
    var u = getUser();
    var email = (u && u.email) ? String(u.email).trim() : '';
    if (email) ensureReferralCreated(email);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();

