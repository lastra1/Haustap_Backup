<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notifications</title>
  <link rel="stylesheet" href="/css/global.css">
  <link rel="stylesheet" href="/client/css/homepage.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    .notif-page {
      max-width: 900px;
      margin: 40px auto;
      padding: 0 16px;
    }
    .notif-header-row { display:flex; justify-content:space-between; align-items:center; }
    .notif-title { font-size: 24px; font-weight: 700; }
    .notif-actions { display:flex; gap:10px; }
    .notif-actions button {
      background:#3dbfc3; border:none; color:#fff; border-radius:8px; padding:8px 12px; cursor:pointer;
    }
    .notif-list-page { list-style:none; margin:20px 0 0; padding:0; }
    .notif-item-page {
      padding:14px 16px; border:1px solid #e5e7eb; border-radius:10px; margin-bottom:12px; display:flex; gap:12px; align-items:flex-start; background:#fff;
    }
    .notif-item-page.unread { background:#f8fbfc; }
    .notif-dot { width:10px; height:10px; border-radius:50%; flex:0 0 10px; margin-top:6px; }
    .notif-body { flex:1; }
    .notif-title-text { font-weight:600; color:#111827; margin-bottom:4px; }
    .notif-desc { color:#374151; font-size:14px; }
    .notif-meta { color:#6b7280; font-size:12px; margin-top:6px; }
    .notif-item-actions { display:flex; gap:8px; margin-top:10px; }
    .notif-secondary-btn { background:transparent; border:1px solid #e5e7eb; color:#6b7280; border-radius:6px; padding:6px 10px; cursor:pointer; }
  </style>
</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>
  <script>
    // Require login for notifications page
    (function(){
      try { if (!localStorage.getItem('haustap_token')) { window.location.href = '/login'; return; } } catch(e){ window.location.href = '/login'; return; }
    })();
  </script>

  <main class="notif-page">
    <div class="notif-header-row">
      <div class="notif-title">Notifications</div>
      <div class="notif-actions">
        <button id="markAllBtn">Mark all read</button>
      </div>
    </div>

    <ul id="notifPageList" class="notif-list-page"></ul>
  </main>

  <?php $context='client'; include dirname(__DIR__) . '/includes/footer.shared.php'; ?>

  <script>
    (function(){
      const STORAGE_KEY = 'haus:notifications';
      let items = [];

      function getUser(){
        try { return JSON.parse(localStorage.getItem('haustap_user') || 'null'); } catch(e){ return null; }
      }
      function getUserId(u){ return u && (u.id || u.user_id || u.client_id) || null; }
      function currentUserId(){ return getUserId(getUser()); }

      function loadLocal(){
        try {
          const raw = localStorage.getItem(STORAGE_KEY);
          items = raw ? JSON.parse(raw) : [];
          if (!Array.isArray(items)) items = [];
        } catch(e){ items = []; }
      }

      function saveLocal(){
        try { localStorage.setItem(STORAGE_KEY, JSON.stringify(items)); } catch(e){}
      }

      function hydrateFromServer(){
        const uid = currentUserId(); if (!uid) { render(); return; }
        fetch(`/mock-api/notifications/list?user_id=${encodeURIComponent(uid)}&limit=200`, { credentials:'same-origin' })
          .then(r=>r.json()).then(data=>{
            const serverItems = (data && data.notifications) ? data.notifications : [];
            // Merge by id when available; otherwise keep local entry
            const byId = new Map();
            items.forEach(x=>{ if (x && typeof x.id === 'string') byId.set(x.id, x); });
            serverItems.forEach(s=>{ if (s && typeof s.id === 'string') byId.set(s.id, Object.assign({}, s, { ts: s.ts || Date.now() })); });
            // If both had content, rebuild items from map + any local without id
            const localsNoId = items.filter(x=>typeof x.id !== 'string');
            items = Array.from(byId.values()).concat(localsNoId);
            items.sort((a,b)=> (b.ts||0) - (a.ts||0));
            saveLocal();
            render();
          }).catch(()=>{ render(); });
      }

      function serverMarkAllRead(){
        const uid = currentUserId(); if (!uid) return;
        fetch('/mock-api/notifications/mark-read', {
          method:'POST', headers:{'Content-Type':'application/json'}, credentials:'same-origin',
          body: JSON.stringify({ user_id: uid, mark_all: true })
        }).catch(()=>{});
      }

      function serverMarkReadByIds(ids){
        const uid = currentUserId(); if (!uid || !ids || !ids.length) return;
        fetch('/mock-api/notifications/mark-read', {
          method:'POST', headers:{'Content-Type':'application/json'}, credentials:'same-origin',
          body: JSON.stringify({ user_id: uid, ids: ids })
        }).catch(()=>{});
      }

      function toTitle(str){ return String(str||'').replace(/_/g,' ').replace(/\b\w/g, m=>m.toUpperCase()); }

      function render(){
        const listEl = document.getElementById('notifPageList');
        listEl.innerHTML = '';
        if (!items.length){
          const li = document.createElement('li');
          li.textContent = 'No notifications yet';
          li.style.cssText = 'padding:16px; color:#6b7280;';
          listEl.appendChild(li);
          return;
        }
        items.slice().sort((a,b)=> (b.ts||0) - (a.ts||0)).forEach(it=>{
          const li = document.createElement('li');
          li.className = 'notif-item-page' + (it.read ? '' : ' unread');
          const dot = document.createElement('span'); dot.className = 'notif-dot'; dot.style.background = it.read ? '#e5e7eb' : '#3dbfc3';
          const body = document.createElement('div'); body.className = 'notif-body';
          const title = document.createElement('div'); title.className = 'notif-title-text'; title.textContent = it.title || toTitle(it.type||'Notification');
          const desc = document.createElement('div'); desc.className = 'notif-desc'; desc.textContent = it.body || '';
          const meta = document.createElement('div'); meta.className = 'notif-meta'; meta.textContent = new Date(it.ts||Date.now()).toLocaleString();
          const actions = document.createElement('div'); actions.className = 'notif-item-actions';
          const readBtn = document.createElement('button'); readBtn.className='notif-secondary-btn'; readBtn.textContent = it.read ? 'Read' : 'Mark read'; readBtn.disabled = !!it.read;
          readBtn.addEventListener('click', function(){
            items = items.map(x => (x.id === it.id ? Object.assign({}, x, { read: true }) : x));
            saveLocal(); render(); if (typeof it.id === 'string') serverMarkReadByIds([it.id]);
          });
          actions.appendChild(readBtn);
          if (it.bookingId){
            const viewBtn = document.createElement('button'); viewBtn.textContent = 'View booking'; viewBtn.addEventListener('click', function(){
              try { localStorage.setItem('last_booking_id', String(it.bookingId)); } catch(e){}
              window.location.href = '/bookings/booking.php';
            });
            actions.appendChild(viewBtn);
          }
          body.appendChild(title); body.appendChild(desc); body.appendChild(meta); body.appendChild(actions);
          li.appendChild(dot); li.appendChild(body); listEl.appendChild(li);
        });
      }

      document.getElementById('markAllBtn').addEventListener('click', function(){
        items = items.map(x => Object.assign({}, x, { read: true }));
        saveLocal(); render(); serverMarkAllRead();
      });

      loadLocal();
      hydrateFromServer();
      render();
    })();
  </script>
</body>
</html>

