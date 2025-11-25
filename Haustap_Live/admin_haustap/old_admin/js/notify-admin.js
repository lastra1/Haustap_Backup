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
      footer.style.cssText = 'padding:8px 12px; border-top:1px solid #e5e7eb; display:flex; justify-content:flex-end; gap:12px;';
      var link = document.createElement('a');
      link.href = 'notifications.php';
      link.id = 'notifViewMore';
      link.setAttribute('aria-label','View all notifications');
      link.textContent = 'View all notifications';
      link.style.cssText = 'color:#3dbfc3; text-decoration:none; font-weight:500;';
      footer.appendChild(link);
      
      dropdown.appendChild(header);
      dropdown.appendChild(list);
      dropdown.appendChild(footer);
      userBox.appendChild(dropdown);
    }
  }

  function bindInteractions(){
    var userBox = document.querySelector('.topbar .user');
    var bellBtn = document.getElementById('notifBellBtn') || (userBox && userBox.querySelector('.notif-btn'));
    var dropdown = document.getElementById('notifDropdown');
    var count = document.getElementById('notifCount');
    var list = document.getElementById('notifList');
    var markAll = document.getElementById('notifMarkAll');
    var viewMore = document.getElementById('notifViewMore');

    if (!userBox || !bellBtn || !dropdown) return;
    if (bellBtn.dataset && bellBtn.dataset.htNotifBound === '1') return; // avoid double-binding

    var open = false;
    function showDropdown(){
      dropdown.classList.remove('hidden');
      open = true;
      try {
        bellBtn.classList.add('pulse');
        setTimeout(function(){ bellBtn.classList.remove('pulse'); }, 300);
      } catch(e){}
      try { bellBtn.setAttribute('aria-expanded','true'); } catch(e){}
    }

    function hideDropdown(){
      dropdown.classList.add('hidden');
      open = false;
      try { bellBtn.setAttribute('aria-expanded','false'); } catch(e){}
    }

    // Toggle on bell click
    bellBtn.addEventListener('click', function(e){
      e.preventDefault();
      e.stopPropagation();
      if (open) hideDropdown(); else showDropdown();
    });

    // Close when clicking outside user box
    document.addEventListener('click', function(e){
      if (!open) return;
      try {
        if (!userBox.contains(e.target)) hideDropdown();
      } catch(err) {
        hideDropdown();
      }
    });

    // Keyboard accessibility
    try {
      bellBtn.setAttribute('aria-controls','notifDropdown');
      bellBtn.setAttribute('aria-expanded','false');
    } catch(e){}
    bellBtn.addEventListener('keydown', function(e){
      if (e.key === 'Escape') hideDropdown();
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        showDropdown();
      }
    });

    // Mark all read handler
    if (markAll){
      markAll.addEventListener('click', function(e){
        e.preventDefault();
        try { if (count){ count.textContent = ''; count.style.display = 'none'; } } catch(e){}
        try {
          if (list){
            var items = list.querySelectorAll('li');
            for (var i=0;i<items.length;i++){
              items[i].classList.remove('unread');
            }
          }
        } catch(e){}
      });
    }

    // View all notifications: navigate to notifications page
    if (viewMore){
      viewMore.addEventListener('click', function(e){
        // allow default anchor navigation, or explicitly set location
        // e.preventDefault();
        try { window.location.href = 'notifications.php'; } catch(err){}
      });
    }

    // Seed with sample items if empty (prevents empty dropdown confusion)
    try {
      if (list && list.children.length === 0){
        // Helper to format relative time and keep it fresh
        function formatRelative(ts){
          try {
            var diff = Math.max(0, Date.now() - ts);
            var s = Math.floor(diff/1000);
            if (s < 60) return s + 's ago';
            var m = Math.floor(s/60);
            if (m < 60) return m + 'm ago';
            var h = Math.floor(m/60);
            if (h < 24) return h + 'h ago';
            var d = Math.floor(h/24);
            return d + 'd ago';
          } catch(e){ return 'now'; }
        }

        var now = Date.now();
        var samples = [
          { text: 'New booking placed by a client', ts: now - 2*60*1000 },
          { text: 'Provider updated availability schedule', ts: now - 60*60*1000 },
          { text: 'Voucher redeemed in checkout', ts: now - 3*60*60*1000 }
        ];
        for (var s=0; s<samples.length; s++){
          var li = document.createElement('li');
          li.style.cssText = 'padding:10px 12px; border-bottom:1px solid #f0f2f4;';
          var p = document.createElement('p'); p.textContent = samples[s].text; p.style.margin = '0 0 4px';
          var small = document.createElement('small');
          small.textContent = formatRelative(samples[s].ts);
          small.style.color = '#64748b';
          try { small.setAttribute('data-ts', String(samples[s].ts)); } catch(e){}
          li.appendChild(p); li.appendChild(small);
          list.appendChild(li);
        }
        if (count){ count.textContent = samples.length; count.style.display = 'inline-block'; }

        // Refresh relative times periodically so they adjust as clock advances
        function updateRelativeTimes(){
          try {
            var nodes = list.querySelectorAll('small[data-ts]');
            for (var i=0; i<nodes.length; i++){
              var el = nodes[i];
              var ts = parseInt(el.getAttribute('data-ts') || '0', 10);
              if (ts) { el.textContent = formatRelative(ts); }
            }
          } catch(e){}
        }
        updateRelativeTimes();
        try { window.__HT_NOTIF_REL_TIMER && clearInterval(window.__HT_NOTIF_REL_TIMER); } catch(e){}
        window.__HT_NOTIF_REL_TIMER = setInterval(updateRelativeTimes, 60000);
      }
    } catch(e){}

    // mark bound
    try { bellBtn.dataset.htNotifBound = '1'; } catch(e){}
  }

  if (document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', function(){ injectStyles(); ensureUi(); bindInteractions(); startRealtime(); });
  } else {
    injectStyles(); ensureUi(); bindInteractions(); startRealtime();
  }

  // --- Realtime updates: SSE with polling fallback ---
  function startRealtime(){
    try {
      var countEl = document.getElementById('notifCount');
      var es = null; var timer = null;

      function updateCount(n){
        try {
          if (!countEl) return;
          n = (typeof n === 'number') ? n : parseInt(n || '0', 10);
          if (isNaN(n) || n <= 0) { countEl.textContent = ''; countEl.style.display = 'none'; }
          else { countEl.textContent = String(n); countEl.style.display = 'inline-block'; }
        } catch(e){}
      }

      function fetchUnread(){
        fetch('/api/admin/notifications/unread_count').then(function(res){
          if (!res.ok) throw new Error('bad status');
          return res.json();
        }).then(function(data){
          var n = (data && (data.total ?? (data.count ?? 0))) || 0;
          updateCount(n);
        }).catch(function(){ /* silent */ });
      }

      function startPolling(){
        try { if (timer) clearInterval(timer); } catch(e){}
        fetchUnread();
        timer = setInterval(fetchUnread, 5000);
        window.__HT_NOTIF_TIMER = timer;
      }

      function startSSE(){
        try { if (es) es.close(); } catch(e){}
        es = new EventSource('/api/admin/notifications/stream');
        window.__HT_NOTIF_ES = es;
        es.onmessage = function(ev){
          try {
            var data = {};
            try { data = JSON.parse(ev.data || '{}'); } catch(e){}
            var n = (data && (data.total ?? (data.count ?? 0))) || 0;
            updateCount(n);
          } catch(err){}
        };
        es.onerror = function(){
          try { es.close(); } catch(e){}
          startPolling();
        };
      }

      // prefer SSE, fallback to polling
      if ('EventSource' in window) { startSSE(); } else { startPolling(); }

      // cleanup on navigation
      window.addEventListener('beforeunload', function(){
        try { if (es) es.close(); } catch(e){}
        try { if (timer) clearInterval(timer); } catch(e){}
      });
    } catch(err){}
  }
})();

