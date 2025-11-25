<?php require_once __DIR__ . '/includes/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Notifications</title>
  <link rel="stylesheet" href="css/dashboard.css" />
  <script src="js/lazy-images.js" defer></script>
  <script src="js/app.js" defer></script>
</head>
<body>
  <div class="dashboard-container">
    <?php $active = 'notifications'; include 'includes/sidebar.php'; ?>

    <main class="main-content">
      <header class="topbar">
        <div class="user">
          <button class="notif-btn" id="notifBtn">🔔</button>
          <!-- topbar notification dropdown -->
          <div id="notifDropdown" class="notif-dropdown" style="position:absolute; right:18px; top:58px; display:none; z-index:1500;">
            <div style="width:360px; background:#fff; border:1px solid #e5e7eb; border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,0.12); overflow:hidden;">
              <div style="padding:10px 12px; background:#f7f9fa; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center;">
                <strong>Notifications</strong>
                <button id="notifMarkAllTop" style="background:transparent;border:0;color:#3dbfc3;cursor:pointer;font-weight:500;">Mark all read</button>
              </div>
              <ul id="notifTopList" style="list-style:none;margin:0;padding:8px; max-height:320px; overflow:auto;"></ul>
              <div style="padding:8px 12px; border-top:1px solid #e5e7eb; text-align:center;">
                <a href="notifications.php" style="color:#3dbfc3; text-decoration:none; font-weight:500;">See all notifications</a>
              </div>
            </div>
          </div>
          <div class="user-menu">
            <button class="user-btn" id="userDropdownBtn">Admin ▼</button>
            <div class="dropdown" id="userDropdown">
              <a href="admin_profile.php">View Profile</a>
              <a href="/admin_haustap/admin_haustap/change_password.php">Change Password</a>
              <a href="logout.php" class="logout">Log out</a>
            </div>
          </div>
        </div>
      </header>

      <section class="cards">
        <div class="card">
          <h3>Notifications</h3>
          <p>Recent updates and alerts across bookings, providers, and system.</p>
        </div>
      </section>

      <section class="notifications" style="margin-top: 20px;">
        <h2>All Notifications</h2>
        <div id="notificationBox" style="background:#fff; border:1px solid #e5e7eb; border-radius:10px; overflow:hidden;">
          <div style="padding:10px 12px; background:#f7f9fa; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center;">
            <strong>Recent</strong>
            <div>
              <a href="system_settings.php" style="color:#3dbfc3; text-decoration:none; font-weight:500; margin-right:12px;">Notification Settings</a>
              <button id="markAllReadBtn" style="background:transparent;border:0;color:#3dbfc3;cursor:pointer;font-weight:500;">Mark all read</button>
            </div>
          </div>
          <ul id="notificationsList" style="list-style:none; margin:0; padding:0;">
            <li style="padding:10px 12px; border-bottom:1px solid #f0f2f4;">Loading…</li>
          </ul>
          <div style="padding:8px 12px; border-top:1px solid #e5e7eb; text-align:right;">
          </div>
        </div>
      </section>

      <script>
        // fetch & render notifications
        async function loadNotifications(limit = 20){
          try{
            const res = await fetch('api/notifications.php?limit='+encodeURIComponent(limit), { credentials: 'same-origin' });
            const data = await res.json();
            if (!data.ok) throw new Error(data.error || 'Failed');
            const list = data.data || [];
            const ul = document.getElementById('notificationsList');
            ul.innerHTML = '';
            if (list.length === 0) {
              ul.innerHTML = '<li style="padding:10px 12px; border-bottom:1px solid #f0f2f4;">No notifications</li>';
            } else {
              list.forEach(n => {
                const li = document.createElement('li');
                li.style.padding = '10px 12px';
                li.style.borderBottom = '1px solid #f0f2f4';
                li.dataset.id = n.id;
                const msg = document.createElement('p'); msg.style.margin='0 0 4px'; msg.textContent = n.message || '';
                const small = document.createElement('small'); small.style.color='#64748b'; small.textContent = timeAgo(n.created_at || '');
                if (n.href) {
                  msg.style.cursor = 'pointer';
                  msg.addEventListener('click', () => { window.location.href = n.href + (n.href.indexOf('?')===-1 ? '?':'&') + 'id='+encodeURIComponent(n.id); });
                }
                if (!n.read) {
                  const badge = document.createElement('span'); badge.textContent = '•'; badge.style.color='#3dbfc3'; badge.style.marginRight = '8px';
                  msg.prepend(badge);
                }
                li.appendChild(msg);
                li.appendChild(small);
                // add mark as read button
                if (!n.read) {
                  const btn = document.createElement('button'); btn.textContent = 'Mark read'; btn.style.float='right'; btn.style.background='transparent'; btn.style.border='0'; btn.style.color='#3dbfc3'; btn.style.cursor='pointer';
                  btn.addEventListener('click', async (e)=>{ e.stopPropagation(); await markRead(n.id); loadNotifications(limit); updateBellCount(); });
                  li.appendChild(btn);
                }
                ul.appendChild(li);
              });
            }
          }catch(err){ console.error(err); const ul = document.getElementById('notificationsList'); if(ul) ul.innerHTML = '<li style="padding:10px 12px; border-bottom:1px solid #f0f2f4;">Failed to load notifications</li>'; }
        }

        async function markRead(id){
          try{
            const res = await fetch('api/notifications.php', { method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ action:'mark_read', id: id }) });
            return await res.json();
          }catch(e){ console.error(e); }
        }

        async function markAllRead(){
          try{
            const res = await fetch('api/notifications.php', { method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ action:'mark_all_read' }) });
            return await res.json();
          }catch(e){ console.error(e); }
        }

        function timeAgo(iso){ try{ const d=new Date(iso); const s=Math.floor((Date.now()-d.getTime())/1000); if(s<60) return s+'s ago'; if(s<3600) return Math.floor(s/60)+'m ago'; if(s<86400) return Math.floor(s/3600)+'h ago'; return Math.floor(s/86400)+'d ago'; }catch(e){return '';} }

        async function updateBellCount(){
          try{
            const res = await fetch('api/notifications.php?unread=1', { credentials:'same-origin' });
            const data = await res.json();
            const unread = (data.data || []).length;
            const btn = document.querySelector('.notif-btn');
            if (btn){
              let badge = btn.querySelector('.notif-count');
              if (!badge){ badge = document.createElement('span'); badge.className='notif-count'; badge.style.background='#ef4444'; badge.style.color='#fff'; badge.style.padding='2px 6px'; badge.style.borderRadius='12px'; badge.style.marginLeft='8px'; badge.style.fontSize='12px'; btn.appendChild(badge); }
              badge.textContent = unread > 0 ? unread : '';
            }
          }catch(e){ console.error(e); }
        }

        document.getElementById('markAllReadBtn').addEventListener('click', async ()=>{ await markAllRead(); await loadNotifications(20); updateBellCount(); });

        // load on init
        updateBellCount(); loadNotifications(20);
      </script>

      <script>
        // Topbar notif dropdown behaviour
        const notifBtn = document.getElementById('notifBtn');
        const notifDropdown = document.getElementById('notifDropdown');
        const notifTopList = document.getElementById('notifTopList');
        const notifMarkAllTop = document.getElementById('notifMarkAllTop');

        async function loadTopNotifications(limit = 6){
          try{
            const res = await fetch('api/notifications.php?limit='+encodeURIComponent(limit), { credentials: 'same-origin' });
            const data = await res.json();
            const list = (data.ok ? (data.data||[]) : []);
            notifTopList.innerHTML = '';
            if (list.length === 0) {
              notifTopList.innerHTML = '<li style="padding:8px 6px;color:#64748b">No notifications</li>';
            } else {
              list.forEach(n => {
                const li = document.createElement('li');
                li.style.padding = '8px';
                li.style.borderBottom = '1px solid #f3f4f6';
                li.style.cursor = 'pointer';
                li.dataset.id = n.id;
                const row = document.createElement('div');
                row.style.display = 'flex';
                row.style.justifyContent = 'space-between';
                const left = document.createElement('div');
                const msg = document.createElement('div'); msg.textContent = n.message || ''; msg.style.marginBottom='6px';
                const meta = document.createElement('small'); meta.style.color='#94a3b8'; meta.textContent = timeAgo(n.created_at || '');
                left.appendChild(msg); left.appendChild(meta);
                const right = document.createElement('div');
                if (!n.read){ const dot = document.createElement('span'); dot.textContent='•'; dot.style.color='#ef4444'; dot.style.marginLeft='8px'; right.appendChild(dot); }
                row.appendChild(left); row.appendChild(right);
                li.appendChild(row);
                li.addEventListener('click', async (e)=>{
                  e.stopPropagation();
                  // mark read then navigate if href
                  await markRead(n.id);
                  updateBellCount();
                  // navigate
                  if (n.href) window.location.href = n.href + (n.href.indexOf('?')===-1 ? '?':'&') + 'id='+encodeURIComponent(n.id);
                });
                notifTopList.appendChild(li);
              });
            }
          }catch(err){ console.error('loadTopNotifications', err); notifTopList.innerHTML = '<li style="padding:8px;color:#ef4444">Failed to load</li>'; }
        }

        notifBtn && notifBtn.addEventListener('click', (e)=>{
          e.stopPropagation();
          if (notifDropdown.style.display === 'none' || !notifDropdown.style.display) {
            notifDropdown.style.display = 'block';
            loadTopNotifications(6);
          } else { notifDropdown.style.display = 'none'; }
        });

        // mark all read from topbar
        notifMarkAllTop && notifMarkAllTop.addEventListener('click', async (e)=>{ e.stopPropagation(); await markAllRead(); loadTopNotifications(6); updateBellCount(); });

        // close when clicking outside
        window.addEventListener('click', (e)=>{
          if (notifDropdown && !notifDropdown.contains(e.target) && e.target !== notifBtn) notifDropdown.style.display = 'none';
        });
      </script>

      <script>
        // Dropdown toggle for user menu
        const dropdownBtn = document.getElementById("userDropdownBtn");
        const dropdown = document.getElementById("userDropdown");
        dropdownBtn.addEventListener("click", () => { dropdown.classList.toggle("show"); });
        window.addEventListener("click", (e) => {
          if (!dropdownBtn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove("show");
          }
        });
      </script>
    </main>
  </div>
</body>
</html>
