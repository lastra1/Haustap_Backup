// HausTap Notification Bell UI
// Leverages window.HausTapNotify to receive push events and renders a dropdown
(function () {
  const bellBtn = document.getElementById('notifBellBtn');
  const dropdown = document.getElementById('notifDropdown');
  const countEl = document.getElementById('notifCount');
  const listEl = document.getElementById('notifList');
  const markAllBtn = document.getElementById('notifMarkAll');

  if (!bellBtn || !dropdown || !countEl || !listEl) {
    return; // Header not present on this page
  }

  // Ensure dropdown starts closed on every page load
  try {
    if (!dropdown.classList.contains('hidden')) {
      dropdown.classList.add('hidden');
    }
    dropdown.style.display = 'none';
    bellBtn.setAttribute('aria-expanded', 'false');
  } catch (e) {}

  const STORAGE_KEY = 'haus:notifications';
  const MAX_ITEMS = 50;
  let items = [];

  // --- Helpers for server sync (mock-api) ---
  function getUser(){
    try { return JSON.parse(localStorage.getItem('haustap_user') || 'null'); } catch (e) { return null; }
  }
  function getUserId(u){
    if (!u) return null;
    return u.id || u.user_id || u.client_id || null;
  }
  function currentUserId(){ return getUserId(getUser()); }

  function persistNotificationServer(localItem){
    var uid = currentUserId();
    if (!uid) return; // no user context
    try {
      var payload = {
        user_id: uid,
        notification: {
          id: (typeof localItem.id === 'string') ? localItem.id : undefined,
          type: (localItem.payload && localItem.payload.type) || 'notification',
          title: localItem.title || 'Notification',
          body: localItem.body || '',
          ts: localItem.ts || Date.now(),
          booking_id: localItem.bookingId || null,
          read: !!localItem.read,
        }
      };
      fetch('/mock-api/notifications/create', {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload), credentials: 'same-origin'
      }).then(function(r){ return r.json(); }).then(function(resp){
        if (resp && resp.notification && resp.notification.id) {
          // Update local item id to the server id for future syncs
          items = items.map(function(x){
            if (x === localItem) { return Object.assign({}, x, { id: resp.notification.id }); }
            return x;
          });
          save();
        }
      }).catch(function(){});
    } catch (e) {}
  }

  function serverMarkAllRead(){
    var uid = currentUserId(); if (!uid) return;
    fetch('/mock-api/notifications/mark-read', {
      method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ user_id: uid, mark_all: true }), credentials: 'same-origin'
    }).catch(function(){});
  }

  function serverMarkReadByIds(ids){
    var uid = currentUserId(); if (!uid || !ids || !ids.length) return;
    fetch('/mock-api/notifications/mark-read', {
      method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ user_id: uid, ids: ids }), credentials: 'same-origin'
    }).catch(function(){});
  }

  function hydrateFromServer(){
    var uid = currentUserId(); if (!uid) return;
    fetch('/mock-api/notifications/list?user_id=' + encodeURIComponent(uid), { credentials: 'same-origin' })
      .then(function(r){ return r.json(); })
      .then(function(data){
        var arr = (data && data.notifications) || [];
        if (!Array.isArray(arr) || !arr.length) return;
        var existingServerIds = new Set();
        items.forEach(function(x){ if (typeof x.id === 'string') existingServerIds.add(x.id); });
        arr.forEach(function(n){
          var sid = String(n.id || ''); if (!sid) return;
          if (existingServerIds.has(sid)) return;
          items.push({
            id: sid,
            title: n.title || 'Notification',
            body: n.body || '',
            bookingId: n.booking_id || null,
            read: !!n.read,
            ts: n.ts || Date.now(),
            payload: { type: n.type || 'notification' }
          });
        });
        save();
        updateBadge();
        render();
      }).catch(function(){});
  }

  function load() {
    try {
      const raw = localStorage.getItem(STORAGE_KEY);
      items = raw ? JSON.parse(raw) : [];
      if (!Array.isArray(items)) items = [];
    } catch (e) {
      items = [];
    }
  }

  function save() {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(items.slice(-MAX_ITEMS)));
    } catch (e) {}
  }

  function unreadCount() {
    return items.reduce((acc, it) => acc + (it.read ? 0 : 1), 0);
  }

  function updateBadge() {
    const n = unreadCount();
    if (n > 0) {
      countEl.textContent = String(n);
      countEl.style.display = 'inline-block';
    } else {
      countEl.style.display = 'none';
    }
  }

  function render() {
    listEl.innerHTML = '';
    if (!items.length) {
      const li = document.createElement('li');
      li.textContent = 'No notifications yet';
      li.style.cssText = 'padding:16px; color:#6b7280;';
      listEl.appendChild(li);
      return;
    }
    const ordered = items.slice().reverse();
    ordered.forEach((it) => {
      const li = document.createElement('li');
      li.className = 'notif-item';
      li.style.cssText = 'padding:10px 12px; border-bottom:1px solid #f1f5f9; display:flex; gap:8px; align-items:flex-start;';
      if (!it.read) li.style.background = '#f8fbfc';

      const dot = document.createElement('span');
      dot.style.cssText = 'width:8px;height:8px;border-radius:50%;margin-top:6px;flex:0 0 8px;';
      dot.style.background = it.read ? '#e5e7eb' : '#3dbfc3';

      const body = document.createElement('div');
      body.style.cssText = 'flex:1;';

      const title = document.createElement('div');
      title.textContent = it.title || 'Notification';
      title.style.cssText = 'font-weight:600; color:#111827; margin-bottom:4px;';

      const desc = document.createElement('div');
      desc.textContent = it.body || '';
      desc.style.cssText = 'color:#374151; font-size:14px;';

      const meta = document.createElement('div');
      const d = new Date(it.ts || Date.now());
      meta.textContent = d.toLocaleString();
      meta.style.cssText = 'color:#6b7280; font-size:12px; margin-top:6px;';

      const actions = document.createElement('div');
      actions.style.cssText = 'display:flex; gap:8px; margin-top:8px;';

      const readBtn = document.createElement('button');
      readBtn.textContent = it.read ? 'Read' : 'Mark read';
      readBtn.disabled = !!it.read;
      readBtn.style.cssText = 'background:transparent; border:1px solid #e5e7eb; color:#6b7280; border-radius:6px; padding:3px 8px; cursor:pointer;';
      readBtn.addEventListener('click', function () {
        items = items.map((x) => (x.id === it.id ? { ...x, read: true } : x));
        save();
        updateBadge();
        render();
        if (typeof it.id === 'string') {
          serverMarkReadByIds([it.id]);
        }
      });

      actions.appendChild(readBtn);

      if (it.bookingId) {
        const viewBtn = document.createElement('button');
        viewBtn.textContent = 'View booking';
        viewBtn.style.cssText = 'background:#3dbfc3; border:none; color:#fff; border-radius:6px; padding:4px 10px; cursor:pointer;';
        viewBtn.addEventListener('click', function () {
          try { localStorage.setItem('last_booking_id', String(it.bookingId)); } catch (e) {}
          window.location.href = '/bookings/booking.php';
        });
        actions.appendChild(viewBtn);
      }

      body.appendChild(title);
      body.appendChild(desc);
      body.appendChild(meta);
      body.appendChild(actions);

      li.appendChild(dot);
      li.appendChild(body);
      listEl.appendChild(li);
    });
  }

  function toTitle(str) {
    return String(str || '')
      .replace(/_/g, ' ')
      .replace(/\b\w/g, function (m) { return m.toUpperCase(); });
  }

  function addNotification(payload) {
    const title = payload.title || toTitle(payload.type || 'Notification');
    const body = payload.message || payload.body || payload.description || '';
    const bookingId = payload.booking_id || payload.bookingId || payload.id || null;
    const localItem = {
      id: Date.now() + Math.random(),
      title,
      body,
      bookingId,
      read: false,
      ts: Date.now(),
      payload,
    };
    items.push(localItem);
    save();
    updateBadge();
    render();
    // Lightweight pulse on the bell to draw attention
    try {
      bellBtn.classList.add('pulse');
      setTimeout(() => bellBtn.classList.remove('pulse'), 800);
    } catch (e) {}
    // Toast popup
    try {
      if (window.HausTapToast && typeof window.HausTapToast.show === 'function') {
        window.HausTapToast.show({ title: title, message: body, type: 'info', timeout: 4000 });
      }
    } catch (e) {}
    // Persist to backend (file-based mock API)
    persistNotificationServer(localItem);
  }

  // Toggle dropdown (ensure display style also reflects hidden state)
  bellBtn.addEventListener('click', function (e) {
    e.preventDefault();
    var willShow = dropdown.classList.contains('hidden');
    dropdown.classList.toggle('hidden');
    try { dropdown.style.display = willShow ? 'block' : 'none'; } catch (err) {}
    try { bellBtn.setAttribute('aria-expanded', willShow ? 'true' : 'false'); } catch (err) {}
  });

  // Click away to close
  document.addEventListener('click', function (e) {
    if (dropdown.classList.contains('hidden')) return;
    const t = e.target;
    if (!dropdown.contains(t) && !bellBtn.contains(t)) {
      dropdown.classList.add('hidden');
      try { dropdown.style.display = 'none'; } catch (err) {}
      try { bellBtn.setAttribute('aria-expanded', 'false'); } catch (err) {}
    }
  });

  // Close on Escape key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && !dropdown.classList.contains('hidden')) {
      dropdown.classList.add('hidden');
      try { dropdown.style.display = 'none'; } catch (err) {}
      try { bellBtn.setAttribute('aria-expanded', 'false'); } catch (err) {}
    }
  });

  // Mark all read
  if (markAllBtn) {
    markAllBtn.addEventListener('click', function () {
      items = items.map((x) => ({ ...x, read: true }));
      save();
      updateBadge();
      render();
      serverMarkAllRead();
    });
  }

  // Init
  load();
  // Server-side hydration (if a logged-in user is present)
  hydrateFromServer();
  updateBadge();
  render();

  // Subscribe to push events
  if (window.HausTapNotify && typeof window.HausTapNotify.subscribe === 'function') {
    window.HausTapNotify.subscribe(addNotification);
  } else {
    // Fallback: listen to a custom event in case notify.js hasn't been loaded yet
    document.addEventListener('HausTapNotifyEvent', function (e) {
      if (e && e.detail) addNotification(e.detail);
    });
  }
})();
