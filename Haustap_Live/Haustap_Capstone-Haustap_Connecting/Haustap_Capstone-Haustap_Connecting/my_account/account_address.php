<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Account Addresses</title>
  <link rel="stylesheet" href="/css/global.css" />
  <link rel="stylesheet" href="../client/css/homepage.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
  
  <link rel="stylesheet" href="/my_account/css/account_address.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <script src="/login_sign up/js/api.js"></script>
  <script src="/my_account/js/referral-modal.js" defer></script>
</head>

<body>
  <!-- HEADER -->
  <?php include __DIR__ . '/../client/includes/header.php'; ?>

  <!-- MAIN CONTENT -->
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
              <li><a href="/account">Profile</a></li>
              <li><a href="/account/address" class="active">Addresses</a></li>
              <li><a href="/account/privacy">Privacy Settings</a></li>
            </ul>
          </div>
          <ul class="sidebar-secondary">
            <li><a href="/account/referral" class="account-link"><i class="fa-solid fa-user-group"></i> Referral</a></li>
            <li><a href="/account/voucher" class="account-link"><i class="fa-solid fa-ticket"></i> My Vouchers</a></li>
            <li><i class="fa-solid fa-link"></i> Connect Haustap</li>
            <li><i class="fa-solid fa-file-contract"></i> Terms and Conditions</li>
            <li><i class="fa-solid fa-star"></i> Rate HOMI</li>
            <li><i class="fa-solid fa-circle-info"></i> About us</li>
          </ul>

          <button class="logout-btn">Log out</button>
        </nav>
      </aside>

      <!-- RIGHT SECTION: ADDRESSES -->
      <section class="profile-section">
  <div class="profile-box">
    <div class="address-header">
      <h2 class="profile-header">Addresses</h2>
<button class="add-address-btn"><i class="fa-solid fa-plus"></i> Add New Address</button>
    </div>
    <hr class="divider">
    <!-- Dynamic addresses injected here -->
    <div id="addresses-list"></div>
  </div>
</section>
    </div>
  </main>

  <script>
    (function(){
      function getStoredUser(){ try { return JSON.parse(localStorage.getItem('haustap_user')||'null'); } catch(e){ return null; } }
      function setStoredUser(u){ try { localStorage.setItem('haustap_user', JSON.stringify(u)); } catch(e){} }
      function hasToken(){ try { return !!localStorage.getItem('haustap_token'); } catch(e){ return false; } }

      async function fetchMe(){
        // Optional: attempt backend profile; fallback to local
        if (!hasToken()) return null;
        try {
          const token = localStorage.getItem('haustap_token');
          const res = await fetch(`${API_BASE}/auth/me`, { method:'GET', headers:{ 'Authorization': `Bearer ${token}` } });
          if (!res.ok) return null; const ct = res.headers.get('content-type')||''; return ct.includes('application/json') ? res.json() : null;
        } catch { return null; }
      }

      function computeDisplayName(u){
        if (!u) return '';
        const first=(u.firstName||'').trim();
        const last=(u.lastName||'').trim();
        const combined=`${first} ${last}`.trim();
        const name=(u.name||'').trim();
        // Prefer explicit account name; fall back to first+last. No email fallback.
        return name || combined;
      }

      function addrKey(user){ const k = (user?.email || 'guest').toLowerCase(); return 'haustap_addresses_' + k; }
      function getAddresses(user){ try { return JSON.parse(localStorage.getItem(addrKey(user))||'[]'); } catch(e){ return []; } }
      function setAddresses(user, list){ try { localStorage.setItem(addrKey(user), JSON.stringify(list||[])); } catch(e){} }

      function renderAddresses(user){
        const listEl = document.getElementById('addresses-list'); if (!listEl) return;
        const addrs = getAddresses(user);
        if (!Array.isArray(addrs) || addrs.length === 0) {
          listEl.innerHTML = '<p style="color:#555;">No saved addresses yet.</p>';
          return;
        }
        listEl.innerHTML = '';
        addrs.forEach((a, idx) => {
          const box = document.createElement('div'); box.className = 'address-box';
          box.innerHTML = `
            <div class="address-info">
              <p class="address-name">${a.name || computeDisplayName(user) || 'Unnamed'}</p>
              <p class="address-details">${a.details || ''}</p>
            </div>
            <div class="address-actions">
              <div class="action-top">
                <button class="edit-btn" data-idx="${idx}">Edit</button>
                <button class="delete-btn" data-idx="${idx}">Delete</button>
              </div>
              <button class="default-btn" data-idx="${idx}">${a.isDefault ? 'Default' : 'Set as Default'}</button>
            </div>`;
          listEl.appendChild(box);
        });

        // Wire actions
        listEl.querySelectorAll('.edit-btn').forEach(btn => {
          btn.addEventListener('click', () => {
            const i = Number(btn.getAttribute('data-idx')); const arr = getAddresses(user);
            const current = arr[i] || {}; const name = prompt('Recipient name:', current.name || computeDisplayName(user) || '');
            if (name === null) return; const details = prompt('Address details:', current.details || '');
            if (details === null) return; arr[i] = Object.assign({}, current, { name: (name||'').trim(), details: (details||'').trim() }); setAddresses(user, arr); renderAddresses(user);
          });
        });
        listEl.querySelectorAll('.delete-btn').forEach(btn => {
          btn.addEventListener('click', () => {
            const i = Number(btn.getAttribute('data-idx')); const arr = getAddresses(user);
            if (!confirm('Delete this address?')) return; const wasDefault = !!arr[i]?.isDefault; arr.splice(i,1);
            if (wasDefault && arr.length > 0) { arr[0].isDefault = true; }
            setAddresses(user, arr); renderAddresses(user);
          });
        });
        listEl.querySelectorAll('.default-btn').forEach(btn => {
          btn.addEventListener('click', () => {
            const i = Number(btn.getAttribute('data-idx')); const arr = getAddresses(user);
            arr.forEach((x, j) => { x.isDefault = (j === i); }); setAddresses(user, arr); renderAddresses(user);
          });
        });
      }

      function wireHeader(user){
        const fullName = computeDisplayName(user); const profileNameEl = document.querySelector('.profile-name'); if (profileNameEl && fullName) profileNameEl.textContent = fullName;
        const addBtn = document.querySelector('.add-address-btn');
        if (addBtn) {
          addBtn.addEventListener('click', () => {
            const arr = getAddresses(user);
            const name = prompt('Recipient name:', computeDisplayName(user) || ''); if (name === null) return;
            const details = prompt('Address details:', ''); if (details === null) return;
            arr.push({ id: Date.now(), name: (name||'').trim(), details: (details||'').trim(), isDefault: arr.length === 0 });
            setAddresses(user, arr); renderAddresses(user);
          });
        }

        const logoutBtn = document.querySelector('.logout-btn');
        if (logoutBtn) {
          logoutBtn.addEventListener('click', function(){
            localStorage.removeItem('haustap_token'); localStorage.removeItem('haustap_user');
            // Clear address cache for this user
            try { localStorage.removeItem(addrKey(user)); } catch(e){}
            window.location.href = '/login';
          });
        }
      }

      async function init(){
        const serverUser = await fetchMe(); let user = serverUser || getStoredUser() || null; if (serverUser) setStoredUser(serverUser);
        if (!user) { window.location.href = '/login'; return; }
        // Seed addresses if none (first-time UX)
        const arr = getAddresses(user);
        if (!arr || arr.length === 0) {
          const seed = [
            { id: Date.now(), name: computeDisplayName(user), details: '123 P. Burgos Street, Brgy. San Isidro, Quezon City, Metro Manila', isDefault: true },
          ];
          setAddresses(user, seed);
        }
        wireHeader(user); renderAddresses(user);
      }

      init();
    })();
  </script>

  <!-- FOOTER -->
<?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
</body>
</html>
