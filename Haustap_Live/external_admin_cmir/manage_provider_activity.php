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
  <title>Manage Providers - Activity</title>
  <link rel="stylesheet" href="css/manage_client_activity.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <script src="js/lazy-images.js" defer></script>
</head>
<body>
  <div class="dashboard-container">
    <?php $active = 'providers'; include 'includes/sidebar.php'; ?>

    <main class="main-content">
      <header class="topbar">
        <h3>Manage Providers </h3>
        <div class="user">
          <button class="notif-btn">🔔</button>
          <div class="user-menu">
            <button id="userDropdownBtn" class="user-dropdown-btn">Mj Punzalan ▼</button>
            <div class="user-dropdown" id="userDropdown">
              <a href="admin_profile.php">View Profile</a>
              <a href="/admin_haustap/admin_haustap/change_password.php">Change Password</a>
              <a href="logout.php" class="logout">Log out</a>
            </div>
          </div>
        </div>
      </header>

      <div class="tabs">
        <?php $pid = (int)($provider['id'] ?? 0); $pstatus = urlencode($provider['status'] ?? ''); ?>
        <button data-target="manage_provider_profile.php?id=<?php echo $pid; ?>&status=<?php echo $pstatus; ?>">Profile</button>
        <button data-target="manage_provider_jobs.php?id=<?php echo $pid; ?>&status=<?php echo $pstatus; ?>">Jobs</button>
        <button class="active" data-target="manage_provider_activity.php?id=<?php echo $pid; ?>&status=<?php echo $pstatus; ?>">Activity</button>
        <button data-target="manage_provider_voucher.php?id=<?php echo $pid; ?>&status=<?php echo $pstatus; ?>">Voucher</button>
        <button data-target="manage_provider_subscription.php?id=<?php echo $pid; ?>&status=<?php echo $pstatus; ?>">Subscription</button>
      </div>

     <!-- Search and Filter -->
<div class="search-filter">
  <input type="text" placeholder="Search Activity">

  <div class="filter-dropdown">
<button class="filter-btn"><i class="fa-solid fa-sliders"></i> Filter ▼</button>
    <div class="dropdown-content">
      
      <!-- Filter by Date -->
      <div class="filter-date">
        <p class="filter-title">Filter by Date</p>
        <div class="date-row">
          <label for="from-date">From:</label>
          <input type="date" id="from-date" value="2025-10-01">
        </div>
        <div class="date-row">
          <label for="to-date">Return:</label>
          <input type="date" id="to-date" value="2025-10-31">
        </div>
      </div>

      <button class="apply-btn">Apply</button>
    </div>
  </div>
</div>

      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Date &amp; Time</th>
              <th>Activity Type</th>
              <th>Details</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>2025 - 06 - 07 14:25</td>
              <td>Booking</td>
              <td>Booked Bungalow–Deep Cleaning with Ana Santos</td>
              <td><span class="status completed">Completed</span></td>
            </tr>
            <tr>
              <td>2025 - 06 - 07 13:25</td>
              <td>Cancellation Booking</td>
              <td>Booked Bungalow–Deep Cleaning with Ana Santos</td>
              <td><span class="status approved">Approved</span></td>
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

  <!-- JavaScript -->
  <script>
    // User Dropdown
    const dropdownBtn = document.getElementById("userDropdownBtn");
    const dropdown = document.getElementById("userDropdown");

    dropdownBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      dropdown.classList.toggle("show");
    });

    // Close user dropdown when clicking outside
    window.addEventListener("click", (e) => {
      if (!dropdown.contains(e.target)) dropdown.classList.remove("show");
    });

    // Filter Dropdown
    const filterBtn = document.querySelector('.filter-btn');
    const dropdownContent = document.querySelector('.dropdown-content');

    filterBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      dropdownContent.classList.toggle('show');
      filterBtn.innerHTML = dropdownContent.classList.contains('show')
        ? '<i class="fa-solid fa-sliders"></i> Filter ▲'
        : '<i class="fa-solid fa-sliders"></i> Filter ▼';
    });

    window.addEventListener('click', () => {
      dropdownContent.classList.remove('show');
filterBtn.innerHTML = '<i class="fa-solid fa-sliders"></i> Filter ▼';
    });
  </script>
  <script>
    (function(){
      var dropdownContent = document.querySelector('.dropdown-content');
      if (!dropdownContent) return;
      var applyBtn = dropdownContent.querySelector('.apply-btn');
      var fromDate = document.getElementById('from-date');
      var toDate = document.getElementById('to-date');
      var searchInput = document.querySelector('.search-filter input[type="text"]');
      function norm(s){ return (s||'').toString().replace(/\s+/g,' ').trim().toLowerCase(); }
      function parseCellDate(txt){
        var s = (txt||'').trim().replace(/\s*-\s*/g,'-');
        var parts = s.split(' ');
        var iso = parts[0] ? parts[0] + (parts[1] ? 'T'+parts[1] : '') : '';
        var d = iso ? new Date(iso) : null;
        return d && !isNaN(d.getTime()) ? d : null;
      }
      function getFrom(){ return (fromDate && fromDate.value) ? new Date(fromDate.value + 'T00:00:00') : null; }
      function getTo(){ var v = toDate && toDate.value || ''; return v ? new Date(v + 'T23:59:59') : null; }
      function apply(){
        var q = norm(searchInput ? searchInput.value : '');
        var f = getFrom();
        var t = getTo();
        var rows = document.querySelectorAll('.table-container tbody tr');
        rows.forEach(function(row){
          var text = norm(row.textContent||'');
          var cell = row.querySelector('td:nth-child(1)');
          var d = parseCellDate(cell ? cell.textContent : '');
          var searchOk = (!q || text.indexOf(q) !== -1);
          var dateOk = true;
          if (d) {
            if (f && d < f) dateOk = false;
            if (t && d > t) dateOk = false;
          }
          row.style.display = (searchOk && dateOk) ? '' : 'none';
        });
      }
      if (applyBtn) applyBtn.addEventListener('click', function(e){ e.preventDefault(); apply(); dropdownContent.classList.remove('show'); var fb=document.querySelector('.filter-btn'); if (fb) fb.innerHTML='<i class="fa-solid fa-sliders"></i> Filter ▼'; });
      if (fromDate) fromDate.addEventListener('change', apply);
      if (toDate) toDate.addEventListener('change', apply);
      if (searchInput) searchInput.addEventListener('input', apply);
    })();
  </script>
  <script>
    (function(){
      var tabs = document.querySelector('.tabs');
      if (!tabs) return;
      tabs.querySelectorAll('button').forEach(function(btn){
        btn.addEventListener('click', function(){
          var target = btn.getAttribute('data-target');
          if (target) { try { window.location.href = target; } catch(err) { console.error('Navigation failed', err); } }
        });
      });
    })();
  </script>
</body>
</html>


