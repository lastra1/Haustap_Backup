<?php require_once __DIR__ . '/includes/auth.php'; ?>
<?php
  $provider = null;
  $providerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
  $storePath = realpath(__DIR__ . '/../../storage/data/providers.json');
  if ($storePath && is_file($storePath)) {
    $raw = @file_get_contents($storePath);
    $items = json_decode($raw ?: '[]', true);
    if (is_array($items)) {
      foreach ($items as $it) { if ((int)($it['id'] ?? 0) === $providerId) { $provider = $it; break; } }
    }
  }
  if (!$provider) { $provider = ['id' => $providerId ?: 0, 'status' => isset($_GET['status']) ? $_GET['status'] : 'active']; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Providers - Jobs</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="css/manage_provider_jobs.css">
  <script src="js/lazy-images.js" defer></script></head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <?php $active = 'providers'; include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Topbar -->
      <header class="topbar">
        <h3>Manage Providers </h3>
        <div class="user">
          <button class="notif-btn">🔔</button>
          <div class="user-menu">
            <button id="userDropdownBtn" class="user-dropdown-btn">Mj Punzalan ▼</button>
            <div class="user-dropdown" id="userDropdown">
              <a href="#">View Profile</a>
              <a href="#">Change Password</a>
              <a href="#" class="logout">Log out</a>
            </div>
          </div>
        </div>
      </header>
        <!-- Tabs -->
      <div class="tabs">
        <?php $pid = (int)($provider['id'] ?? 0); $pstatus = urlencode($provider['status'] ?? ''); ?>
        <button data-target="manage_provider_profile.php?id=<?php echo $pid; ?>&status=<?php echo $pstatus; ?>">Profile</button>
        <button class="active" data-target="manage_provider_jobs.php?id=<?php echo $pid; ?>&status=<?php echo $pstatus; ?>">Jobs</button>
        <button data-target="manage_provider_activity.php?id=<?php echo $pid; ?>&status=<?php echo $pstatus; ?>">Activity</button>
        <button data-target="manage_provider_voucher.php?id=<?php echo $pid; ?>&status=<?php echo $pstatus; ?>">Voucher</button>
        <button data-target="manage_provider_subscription.php?id=<?php echo $pid; ?>&status=<?php echo $pstatus; ?>">Subscription</button>
      </div>

      <!-- Search and Filter -->
      <div class="search-filter">
        <input type="text" placeholder="Search Services">

        <div class="filter-dropdown">
<div class="filter-btn"><i class="fa-solid fa-sliders"></i> Filter</div>
          <div class="dropdown-content">
            <p class="filter-title">Filter by Status</p>
            <label><input type="checkbox" value="pending"> Pending</label>
            <label><input type="checkbox" value="ongoing"> Ongoing</label>
            <label><input type="checkbox" value="completed"> Completed</label>
            <label><input type="checkbox" value="cancelled"> Cancelled</label>
            <label><input type="checkbox" value="return"> Return</label>
            <button class="apply-btn">Apply</button>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Booking Id</th>
              <th>Client name</th>
              <th>Services</th>
              <th>Date &amp; Time</th>
              <th>Total</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td><td>Ana Santos</td><td>Home Cleaning</td><td>2025 - 06 - 07 1:30</td><td>1,000</td><td><span class="status completed">Completed</span></td><td>›</td>
            </tr>
            <tr>
              <td>2</td><td>Ana Santos</td><td>Plumbing</td><td>2025 - 06 - 07 1:30</td><td>1,000</td><td><span class="status cancelled">Cancelled</span></td><td>›</td>
            </tr>
            <tr>
              <td>3</td><td>Ana Santos</td><td>Plumbing</td><td>2025 - 06 - 07 1:30</td><td>1,000</td><td><span class="status pending">Pending</span></td><td>›</td>
            </tr>
            <tr>
              <td>4</td><td>Ana Santos</td><td>Plumbing</td><td>2025 - 06 - 07 1:30</td><td>1,000</td><td><span class="status ongoing">Ongoing</span></td><td>›</td>
            </tr>
            <tr>
              <td>5</td><td>Ana Santos</td><td>Plumbing</td><td>2025 - 06 - 07 1:30</td><td>1,000</td><td><span class="status return">Return</span></td><td>›</td>
            </tr>
          </tbody>
        </table>

        <div class="pagination">
          <button class="prev">◀ Prev</button>
          <p>Showing 1–10 of 120</p>
          <button class="next">Next ▶</button>
        </div>
      </div>
    </main>
  </div>

    <script>
    const dropdownBtn = document.getElementById("userDropdownBtn");
    const dropdown = document.getElementById("userDropdown");

    dropdownBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      dropdown.classList.toggle("show");
    });

    window.addEventListener("click", (e) => {
      if (!dropdown.contains(e.target)) dropdown.classList.remove("show");
    });

    // Filter dropdown toggle (scoped to the button's parent for alignment)
    (function(){
      const filterBtn = document.querySelector('.filter-btn');
      if (!filterBtn) return;
      const dropdownContent = filterBtn.parentElement && filterBtn.parentElement.querySelector('.dropdown-content');
      if (!dropdownContent) return;
      filterBtn.setAttribute('aria-expanded','false');
      filterBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdownContent.classList.toggle('show');
        const open = dropdownContent.classList.contains('show');
        filterBtn.innerHTML = open ? '<i class="fa-solid fa-sliders"></i> Filter ▲' : '<i class="fa-solid fa-sliders"></i> Filter ▼';
        filterBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
      window.addEventListener('click', (e) => {
        if (!dropdownContent.contains(e.target) && !filterBtn.contains(e.target)) {
          dropdownContent.classList.remove('show');
          filterBtn.innerHTML = '<i class="fa-solid fa-sliders"></i> Filter ▼';
          filterBtn.setAttribute('aria-expanded','false');
        }
      });
    })();

    (function(){
      const dropdownContent = document.querySelector('.dropdown-content');
      if (!dropdownContent) return;
      const checkboxes = dropdownContent.querySelectorAll('input[type="checkbox"]');
      const applyBtn = dropdownContent.querySelector('.apply-btn');
      const searchInput = document.querySelector('.search-filter input[type="text"]');
      function norm(s){ return (s||'').toString().replace(/\s+/g,' ').trim().toLowerCase(); }
      function rowStatus(badge){
        if (!badge) return '';
        const cls = badge.classList;
        if (cls.contains('completed')) return 'completed';
        if (cls.contains('complete')) return 'complete';
        if (cls.contains('ongoing')) return 'ongoing';
        if (cls.contains('pending')) return 'pending';
        if (cls.contains('cancelled')) return 'cancelled';
        if (cls.contains('return')) return 'return';
        return '';
      }
      function applyFilters(){
        const q = norm(searchInput ? searchInput.value : '');
        const selected = new Set(Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value));
        const rows = document.querySelectorAll('.table-container tbody tr');
        rows.forEach(row => {
          const text = norm(row.textContent||'');
          const badge = row.querySelector('.status');
          const s = rowStatus(badge);
          const statusOk = (selected.size === 0 || selected.has(s));
          const searchOk = (!q || text.indexOf(q) !== -1);
          row.style.display = (statusOk && searchOk) ? '' : 'none';
        });
      }
      checkboxes.forEach(cb => cb.addEventListener('change', applyFilters));
      if (applyBtn) applyBtn.addEventListener('click', (e) => { e.preventDefault(); applyFilters(); const dc=document.querySelector('.dropdown-content'); if(dc) dc.classList.remove('show'); const fb=document.querySelector('.filter-btn'); if(fb) fb.innerHTML='<i class="fa-solid fa-sliders"></i> Filter ▼'; });
      if (searchInput) searchInput.addEventListener('input', applyFilters);
    })();
  </script>
  <script>
    (function(){
      const tabs = document.querySelector('.tabs');
      if (!tabs) return;
      tabs.querySelectorAll('button').forEach(function(btn){
        btn.addEventListener('click', function(){
          const target = btn.getAttribute('data-target');
          if (target) window.location.href = target;
        });
      });
    })();
  </script>
  <script>
    (function(){
      var tbody = document.querySelector('.table-container tbody');
      if (!tbody) return;
      var overlay = document.createElement('div');
      overlay.className = 'popup-overlay';
      overlay.style.position='fixed';
      overlay.style.left='0';
      overlay.style.top='0';
      overlay.style.right='0';
      overlay.style.bottom='0';
      overlay.style.background='rgba(0,0,0,0.4)';
      overlay.style.display='none';
      overlay.style.alignItems='center';
      overlay.style.justifyContent='center';
      overlay.style.zIndex='9999';
      var content = document.createElement('div');
      content.className='popup-content';
      content.style.background='#fff';
      content.style.width='720px';
      content.style.maxWidth='95%';
      content.style.borderRadius='8px';
      content.style.boxShadow='0 8px 24px rgba(0,0,0,0.2)';
      content.style.overflow='hidden';
      var header = document.createElement('div');
      header.className='popup-header';
      header.style.display='flex';
      header.style.alignItems='center';
      header.style.justifyContent='space-between';
      header.style.padding='12px 16px';
      var statusEl = document.createElement('span');
      statusEl.className='status';
      statusEl.style.padding='6px 10px';
      statusEl.style.borderRadius='16px';
      statusEl.style.color='#fff';
      statusEl.style.fontWeight='600';
      var close = document.createElement('button');
      close.textContent='×';
      close.style.border='0';
      close.style.background='transparent';
      close.style.fontSize='20px';
      close.style.cursor='pointer';
      close.addEventListener('click', function(){ overlay.style.display='none'; });
      header.appendChild(statusEl);
      header.appendChild(close);
      var body = document.createElement('div');
      body.style.padding='16px';
      content.appendChild(header);
      content.appendChild(body);
      overlay.appendChild(content);
      overlay.addEventListener('click', function(e){ if (e.target===overlay) overlay.style.display='none'; });
      document.body.appendChild(overlay);

      function cap(s){ s=(s||'').toString(); return s.charAt(0).toUpperCase()+s.slice(1); }
      function setStatusStyle(s){
        var c='#6c757d';
        if (s==='completed' || s==='complete') c='#28a745';
        else if (s==='ongoing') c='#17a2b8';
        else if (s==='pending') c='#6c757d';
        else if (s==='cancelled') c='#dc3545';
        else if (s==='return') c='#ffc107';
        statusEl.style.background=c;
        statusEl.textContent=cap(s);
      }
      function openPopup(row){
        var id = row.querySelector('td:nth-child(1)')?.textContent.trim()||'';
        var client = row.querySelector('td:nth-child(2)')?.textContent.trim()||'';
        var service = row.querySelector('td:nth-child(3)')?.textContent.trim()||'';
        var dt = row.querySelector('td:nth-child(4)')?.textContent.trim()||'';
        var total = row.querySelector('td:nth-child(5)')?.textContent.trim()||'';
        var badge = row.querySelector('.status');
        var s = badge ? (badge.classList.contains('completed')?'completed':badge.classList.contains('complete')?'complete':badge.classList.contains('ongoing')?'ongoing':badge.classList.contains('pending')?'pending':badge.classList.contains('cancelled')?'cancelled':badge.classList.contains('return')?'return':'') : '';
        setStatusStyle(s||'');
        var h = ''+
          '<div style="font-weight:600;margin-bottom:8px">Client: '+client+'</div>'+
          '<div style="margin-bottom:8px">Service: '+service+'</div>'+
          '<div style="display:flex;gap:16px;margin-bottom:8px"><div><div>Date</div><div>'+dt.split(' ')[0]+'</div></div><div><div>Time</div><div>'+(dt.split(' ')[1]||'')+'</div></div></div>'+
          '<div style="margin-bottom:8px">Sub Total: ₱'+total+'</div>'+
          '<div style="display:flex;align-items:center;gap:8px;border:1px solid #ddd;border-radius:8px;padding:10px;margin:12px 0"><i class="fa-solid fa-ticket"></i><span>No voucher added</span></div>';
        if (s==='cancelled') {
          h += '<hr><div style="font-weight:600;margin-bottom:6px">Cancellation Details</div>'+
               '<div style="display:flex;gap:16px;margin-bottom:8px"><div><div>Date</div><div>'+dt.split(' ')[0]+'</div></div><div><div>Time</div><div>'+(dt.split(' ')[1]||'')+'</div></div></div>'+
               '<div>Reason: —</div><div>Description: —</div>';
        }
        if (s==='return') {
          h += '<hr><div style="font-weight:600;margin-bottom:6px">Return Reason</div>'+
               '<div>Date: '+dt.split(' ')[0]+'</div><div>Time: '+(dt.split(' ')[1]||'')+'</div>'+
               '<div>Reason: —</div><div>Description: —</div>'+
               '<div style="margin-top:8px"><input type="file"></div>';
        }
        body.innerHTML = h;
        overlay.style.display='flex';
      }
      Array.prototype.slice.call(tbody.querySelectorAll('tr')).forEach(function(row){
        var arrow = row.querySelector('td:last-child');
        if (arrow) { arrow.style.cursor='pointer'; arrow.addEventListener('click', function(){ openPopup(row); }); }
      });
    })();
  </script>

</body>
</html>



