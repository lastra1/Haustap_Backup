// Shared client-side helper: reflect the signed-in client's name across pages.
(function() {
  function getUser() {
    try { return JSON.parse(localStorage.getItem('haustap_user') || 'null'); } catch { return null; }
  }

  function computeName(u) {
    if (!u) return '';
    const first = (u.firstName || '').trim();
    const last = (u.lastName || '').trim();
    const combined = `${first} ${last}`.trim();
    const nameField = (u.name || '').trim();
    const emailLocal = ((u.email || '').split('@')[0] || '').trim();
    return combined || nameField || emailLocal;
  }

  function updateProfileName(name) {
    if (!name) return;
    const els = document.querySelectorAll('.profile-name');
    els.forEach(el => { el.textContent = name; });
  }

  function updateHeaderAccount(name) {
    const link = document.querySelector('.account-link');
    if (!link) return;
    // Ensure the header shows a consistent label: "My Account"
    const icon = link.querySelector('.account-icon');
    const existing = link.querySelector('.account-name');
    if (existing) {
      existing.textContent = 'My Account';
      return;
    }
    // If structure differs, rebuild safely
    link.textContent = '';
    if (icon) link.appendChild(icon);
    const span = document.createElement('span');
    span.className = 'account-name';
    span.style.marginLeft = '6px';
    span.textContent = 'My Account';
    link.appendChild(span);
  }

  function init() {
    const user = getUser();
    const name = computeName(user);
    if (name) updateProfileName(name);
    // Always reflect "My Account" in the header, regardless of name
    updateHeaderAccount(name);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
