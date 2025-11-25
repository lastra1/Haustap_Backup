(() => {
  // Guard against double-including this script across pages
  if (window.__HT_REFERRAL_MODAL_ATTACHED) return;
  window.__HT_REFERRAL_MODAL_ATTACHED = true;
  function injectStyles() {
    if (document.getElementById('ht-referral-modal-styles')) return;
    const style = document.createElement('style');
    style.id = 'ht-referral-modal-styles';
    style.textContent = `
      /* Haustap themed referral modal */
      .ht-referral-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 10000; }
      .ht-referral-modal {
        /* theme tokens */
        --ht-primary: #3DC1C6;
        --ht-primary-600: #36b2b7;
        --ht-primary-700: #2ea3a8;
        --ht-text: #1e1e1e;
        --ht-muted: #666;
        --ht-border: #e6e6e6;
        --ht-shadow: 0 12px 40px rgba(0,0,0,0.18);

        background: #fff; width: 92%; max-width: 640px; border-radius: 12px; box-shadow: var(--ht-shadow);
        font-family: Georgia, 'Times New Roman', Times, serif; overflow: hidden;
      }
      .ht-referral-header { display:flex; align-items:center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid var(--ht-border); }
      .ht-referral-header h2 { margin: 0; font-size: 20px; color: var(--ht-text); font-weight: 700; }
      .ht-close-btn { background: transparent; border: none; font-size: 22px; line-height: 1; cursor: pointer; color: var(--ht-muted); padding: 6px; border-radius: 8px; }
      .ht-close-btn:hover { background: #f5f5f5; }
      .ht-referral-body { padding: 22px; }
      .your-code { margin: 0 0 10px; color: var(--ht-muted); font-size: 14px; }

      .code-box {
        background: linear-gradient(135deg, var(--ht-primary), var(--ht-primary-700));
        padding: 18px; border-radius: 12px; box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        display:flex; flex-direction: column; align-items:center; gap:12px; margin: 0 auto 18px; max-width: 420px;
      }
      .inner-box {
        background-color: #fff; border: 1px solid rgba(0,0,0,0.08);
        padding: 16px 48px; border-radius: 12px; text-align:center;
      }
      .referral-code { font-size: 30px; font-weight: 700; color: var(--ht-text); margin: 0; letter-spacing: 1px; }

      .copy-btn {
        background-color: #fff; color: var(--ht-text);
        border: 2px solid var(--ht-primary);
        font-weight: 700; padding: 9px 28px; font-size: 14px; border-radius: 10px; cursor: pointer;
        transition: background-color .15s ease, color .15s ease, transform .1s ease;
      }
      .copy-btn:hover { background-color: var(--ht-primary); color: #fff; }
      .copy-btn:active { transform: translateY(1px); }

      .add-code-box { background-color: #fff; padding: 16px; border-radius: 10px; border: 1px solid var(--ht-border); box-shadow: 0 4px 16px rgba(0,0,0,0.08); margin: 16px auto 0; max-width: 420px; }
      .add-code-box p { font-size: 14px; color: var(--ht-text); text-align:center; margin: 0 0 10px; }
      .add-code-box input { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 10px; font-size: 14px; margin-bottom: 12px; outline: none; transition: border-color .15s ease, box-shadow .15s ease; }
      .add-code-box input:focus { border-color: var(--ht-primary); box-shadow: 0 0 0 3px rgba(61, 193, 198, 0.15); }

      .submit-btn {
        background-color: var(--ht-primary); color: #fff;
        font-weight: 700; padding: 10px 18px; font-size: 14px; border-radius: 10px; cursor: pointer; display:block; margin: 0 auto;
        border: none; transition: filter .15s ease, transform .1s ease; box-shadow: 0 2px 6px rgba(0,0,0,0.12);
      }
      .submit-btn:hover { filter: brightness(1.05); }
      .submit-btn:active { transform: translateY(1px); }

      .ht-success { text-align:center; padding: 12px; color: #2FB576; font-weight: 600; background: rgba(47, 181, 118, 0.08); border-radius: 8px; margin-top: 12px; }

      @media (max-width: 480px) {
        .ht-referral-body { padding: 16px; }
        .inner-box { padding: 14px 36px; }
        .referral-code { font-size: 26px; }
      }
    `;
    document.head.appendChild(style);
  }

  function getUser() {
    try { return JSON.parse(localStorage.getItem('haustap_user')||'null'); } catch(e){ return null; }
  }
  function ensureLoggedIn(user){ if (!user || !user.email) { window.location.href = '/login'; return false; } return true; }

  async function fetchMyReferral(email){
    try {
      const res = await fetch(`/mock-api/referral?email=${encodeURIComponent(email)}`);
      const data = await res.json();
      if (data && data.success && data.data && data.data.code) return data.data.code;
    } catch(e){}
    return null;
  }

  async function applyReferral(email, code){
    const res = await fetch('/mock-api/referral/apply', {
      method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ email, code })
    });
    const data = await res.json();
    if (!res.ok || !data.success) throw new Error((data && data.message) || 'Unable to apply referral code');
    return data;
  }

  function buildModal() {
    injectStyles();
    const backdrop = document.createElement('div'); backdrop.className = 'ht-referral-backdrop';
    backdrop.innerHTML = `
      <div class="ht-referral-modal" role="dialog" aria-modal="true" aria-label="Referral">
        <div class="ht-referral-header">
          <h2>Referral</h2>
          <button class="ht-close-btn" aria-label="Close">&times;</button>
        </div>
        <div class="ht-referral-body">
          <p class="your-code"><strong>Your Code</strong></p>
          <div class="code-box">
            <div class="inner-box"><p class="referral-code">------</p></div>
            <button class="copy-btn">Copy</button>
          </div>
          <div class="add-code-box">
            <p>Add the referral code you have received from your friend</p>
            <input id="friendCodeInput" type="text" placeholder="Enter code (e.g., 6AYI6F)" />
            <button class="submit-btn">Submit</button>
          </div>
          <div class="ht-success" style="display:none;">Success! You’ll get your referral reward once your invite completes a booking.</div>
        </div>
      </div>
    `;
    return backdrop;
  }

  async function openReferralModal() {
    const user = getUser(); if (!ensureLoggedIn(user)) return;
    const modal = buildModal();
    document.body.appendChild(modal);
    const codeEl = modal.querySelector('.referral-code');
    const copyBtn = modal.querySelector('.copy-btn');
    const submitBtn = modal.querySelector('.submit-btn');
    const inputEl = modal.querySelector('#friendCodeInput');
    const closeBtn = modal.querySelector('.ht-close-btn');
    const successEl = modal.querySelector('.ht-success');

    // Load my code
    const myCode = await fetchMyReferral(user.email);
    if (codeEl) codeEl.textContent = myCode || '------';

    // Wire events
    closeBtn?.addEventListener('click', () => modal.remove());
    modal.addEventListener('click', (e) => { if (e.target === modal) modal.remove(); });
    document.addEventListener('keydown', function onEsc(ev){ if (ev.key === 'Escape') { modal.remove(); document.removeEventListener('keydown', onEsc); } });

    copyBtn?.addEventListener('click', async () => {
      const code = codeEl?.textContent?.trim() || '';
      if (!code || code === '------') { alert('No referral code available yet.'); return; }
      try { await navigator.clipboard.writeText(code); alert('Referral code copied!'); }
      catch(e){ const ta=document.createElement('textarea'); ta.value=code; document.body.appendChild(ta); ta.select(); try{ document.execCommand('copy'); alert('Referral code copied!'); } finally { document.body.removeChild(ta); } }
    });

    submitBtn?.addEventListener('click', async () => {
      const val = (inputEl?.value || '').trim().toUpperCase();
      if (!val) { alert('Please enter a referral code'); return; }
      try {
        await applyReferral(user.email, val);
        successEl.style.display = 'block';
      } catch(err){ alert(err.message || 'Failed to apply code'); }
    });
  }

  function setupInterceptor() {
    document.addEventListener('click', (e) => {
      const target = e.target.closest('a[href="/account/referral"]');
      if (target) {
        e.preventDefault();
        openReferralModal();
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupInterceptor);
  } else {
    setupInterceptor();
  }
})();
