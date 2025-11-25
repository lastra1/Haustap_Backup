// Admin notification UI initializer
// Injects dropdown markup and count bubble so client notify-ui can bind
(function(){
  function injectStyles(){
    try {
      var style = document.createElement('style');
      style.textContent = [
        '.hidden{display:none!important;}',
        '.notif-count{position:absolute; top:-6px; right:-6px; background:#ff3b30; color:#fff; border-radius:12px; padding:0 6px; font-size:12px; line-height:18px; min-width:18px; text-align:center; display:none;}',
        '.pulse{animation:bellPulse .8s ease-in-out;}',
        '@keyframes bellPulse{0%{transform:scale(1)}50%{transform:scale(1.15)}100%{transform:scale(1)}}'
      ].join('\n');
      document.head.appendChild(style);
    } catch(e){}
  }

  function ensureUi(){
    var userBox = document.querySelector('.topbar .user');
    var bellBtn = userBox && userBox.querySelector('.notif-btn');
    if (!userBox || !bellBtn) return;

    // Prepare anchoring
    try { userBox.style.position = 'relative'; } catch(e){}

    // Tag bell with expected id and add count bubble
    bellBtn.id = 'notifBellBtn';
    try { bellBtn.style.position = 'relative'; } catch(e){}
    if (!document.getElementById('notifCount')){
      var count = document.createElement('span');
      count.id = 'notifCount';
      count.className = 'notif-count';
      bellBtn.appendChild(count);
    }

  // Build dropdown if missing
    if (!document.getElementById('notifDropdown')){
      var dropdown = document.createElement('div');
      dropdown.id = 'notifDropdown';
      dropdown.className = 'notif-dropdown hidden';
      dropdown.setAttribute('aria-label','Notifications');
      dropdown.style.cssText = 'position:absolute; right:0; top:36px; width:320px; background:#fff; border:1px solid #e5e7eb; box-shadow:0 8px 24px rgba(0,0,0,0.12); border-radius:10px; overflow:hidden; z-index:999;';

      var header = document.createElement('div');
      header.className = 'notif-header';
      header.style.cssText = 'display:flex; justify-content:space-between; align-items:center; padding:10px 12px; background:#f7f9fa; border-bottom:1px solid #e5e7eb;';
      var strong = document.createElement('strong'); strong.textContent = 'Notifications';
      var markAll = document.createElement('button');
      markAll.type = 'button'; markAll.id = 'notifMarkAll';
      markAll.textContent = 'Mark all read';
      markAll.style.cssText = 'background:transparent; border:none; color:#3dbfc3; cursor:pointer;';
      header.appendChild(strong); header.appendChild(markAll);

      var list = document.createElement('ul');
      list.id = 'notifList'; list.className = 'notif-list';
      list.style.cssText = 'list-style:none; margin:0; padding:0; max-height:360px; overflow:auto;';

      var footer = document.createElement('div');
      footer.className = 'notif-footer';
      footer.style.cssText = 'padding:8px 12px; border-top:1px solid #e5e7eb; text-align:right;';
      var link = document.createElement('a'); link.href = '/bookings/booking.php';
      link.textContent = 'View bookings'; link.style.cssText = 'color:#3dbfc3; text-decoration:none; font-weight:500;';
      footer.appendChild(link);

      dropdown.appendChild(header);
      dropdown.appendChild(list);
      dropdown.appendChild(footer);
      userBox.appendChild(dropdown);
    }
    // Attach interactive handlers
    try{
      const bell = document.getElementById('notifBellBtn');
      const dropdownEl = document.getElementById('notifDropdown');
      const countEl = document.getElementById('notifCount');
      const markAllBtn = document.getElementById('notifMarkAll');

      // toggle dropdown
      bell.addEventListener('click', async function(e){
        e.stopPropagation();
        dropdownEl.classList.toggle('hidden');
        // when opening, refresh list
        if (!dropdownEl.classList.contains('hidden')) await loadDropdown();
      });

      // hide on outside click
      document.addEventListener('click', function(ev){
        if (!dropdownEl.contains(ev.target) && !bell.contains(ev.target)) dropdownEl.classList.add('hidden');
      });

      // mark all read
      markAllBtn.addEventListener('click', async function(){
        try{ await markAllRead(); await loadDropdown(); await updateCount(); } catch(e){ console.error(e); }
      });

      // initial load
      updateCount();
      // poll for unread count periodically
      setInterval(updateCount, 25000);
    }catch(e){ console.warn('notif UI attach failed', e); }
  }

  if (document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', function(){ injectStyles(); ensureUi(); });
  } else {
    injectStyles(); ensureUi();
  }
})();

// === notification data helpers (global so tests can call) ===
async function fetchNotifications(params){
  const qs = params ? ('?'+new URLSearchParams(params).toString()) : '';
  const res = await fetch('api/notifications.php'+qs, { credentials: 'same-origin' });
  return res.json();
}

async function loadDropdown(limit){
  limit = limit || 20;
  try{
    const data = await fetchNotifications({ limit: limit });
    if (!data.ok) return;
    const list = data.data || [];
    const ul = document.getElementById('notifList');
    ul.innerHTML = '';
    if (list.length === 0){
      ul.innerHTML = '<li style="padding:12px;">No notifications</li>';
      return;
    }
    list.forEach(n => {
      const li = document.createElement('li');
      li.style.padding = '10px 12px';
      li.style.borderBottom = '1px solid #f0f2f4';
      li.style.display = 'flex';
      li.style.alignItems = 'center';
      const left = document.createElement('div'); left.style.flex='1';
      const msg = document.createElement('div'); msg.textContent = n.message || '';
      msg.style.cursor = n.href ? 'pointer' : 'default';
      msg.style.marginBottom = '4px';
      if (!n.read){ const dot = document.createElement('span'); dot.textContent='• '; dot.style.color='#3dbfc3'; msg.prepend(dot); }
      const small = document.createElement('small'); small.style.color='#64748b'; small.textContent = (new Date(n.created_at)).toLocaleString();
      left.appendChild(msg); left.appendChild(small);
      li.appendChild(left);
      const actions = document.createElement('div');
      if (!n.read){ const b = document.createElement('button'); b.textContent='Mark'; b.style.background='transparent'; b.style.border='0'; b.style.color='#3dbfc3'; b.style.cursor='pointer'; b.addEventListener('click', async (e)=>{ e.stopPropagation(); await markRead(n.id); await loadDropdown(limit); await updateCount(); }); actions.appendChild(b); }
      if (n.href){ const go = document.createElement('button'); go.textContent='Open'; go.style.marginLeft='8px'; go.style.background='transparent'; go.style.border='0'; go.style.color='#0b74da'; go.style.cursor='pointer'; go.addEventListener('click', ()=>{ window.location.href = n.href; }); actions.appendChild(go); }
      li.appendChild(actions);
      ul.appendChild(li);
      // clicking message navigates
      if (n.href) msg.addEventListener('click', ()=> window.location.href = n.href);
    });
  }catch(e){ console.error('loadDropdown failed', e); }
}

async function markRead(id){
  try{ const res = await fetch('api/notifications.php', { method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ action:'mark_read', id: id }) }); return res.json(); }catch(e){ console.error(e); }
}

async function markAllRead(){
  try{ const res = await fetch('api/notifications.php', { method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ action:'mark_all_read' }) }); return res.json(); }catch(e){ console.error(e); }
}

async function updateCount(){
  try{
    const data = await fetchNotifications({ unread: 1 });
    if (!data.ok) return;
    const unread = (data.data || []).length;
    const countEl = document.getElementById('notifCount');
    if (countEl){ countEl.textContent = unread>0?unread:''; countEl.style.display = unread>0?'inline-block':'none'; }
  }catch(e){ console.error(e); }
}

