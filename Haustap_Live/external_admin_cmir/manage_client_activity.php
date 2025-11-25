<?php require_once __DIR__ . '/includes/auth.php'; ?>
<?php
  $client = null;
  $clientId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
  $storePath = realpath(__DIR__ . '/../../storage/data/clients.json');
  if ($storePath && is_file($storePath)) {
    $raw = @file_get_contents($storePath);
    $items = json_decode($raw ?: '[]', true);
    if (is_array($items)) {
      foreach ($items as $it) { if (isset($it['id']) && (int)$it['id'] === $clientId) { $client = $it; break; } }
    }
  }
  if (!$client) { $client = ['id' => $clientId ?: 0, 'status' => isset($_GET['status']) ? $_GET['status'] : 'active']; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Activity</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="css/manage_client_activity.css">
<script src="js/lazy-images.js" defer></script>
</head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <?php $active = 'clients'; include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Topbar -->
      <header class="topbar">
        <h3>Manage of Clients</h3>
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
        <?php $cid = (int)($client['id'] ?? 0); $cstatus = urlencode($client['status'] ?? ''); ?>
        <button data-target="manage_client_profile.php?id=<?php echo $cid; ?>&status=<?php echo $cstatus; ?>">Profile</button>
        <button data-target="manage_client_booking.php?id=<?php echo $cid; ?>&status=<?php echo $cstatus; ?>">Bookings</button>
        <button class="active" data-target="manage_client_activity.php?id=<?php echo $cid; ?>&status=<?php echo $cstatus; ?>">Activity</button>
        <button data-target="manage_client_voucher.php?id=<?php echo $cid; ?>&status=<?php echo $cstatus; ?>">Voucher</button>
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


      <!-- Activity Table -->
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
  <script>
    (function(){
      var input = document.querySelector('.search-filter input[type="text"]');
      var tbody = document.querySelector('.table-container tbody');
      var fromInput = document.getElementById('from-date');
      var toInput = document.getElementById('to-date');
      var applyBtn = document.querySelector('.apply-btn');
      if (!input || !tbody) return;
      var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));
      function norm(s){ return (s||'').toString().replace(/\s+/g,' ').trim().toLowerCase(); }
      function parseRowDate(text){
        if (!text) return null;
        var t = text.replace(/\s*-\s*/g,'-').replace(/\s+/g,' ').trim();
        var parts = t.split(' ');
        var datePart = parts[0] || '';
        var timePart = parts[1] || '';
        var iso = datePart + 'T' + (timePart ? timePart + ':00' : '00:00:00');
        var d = new Date(iso);
        if (isNaN(d.getTime())) return null;
        return d;
      }
      function getFrom(){
        var v = fromInput && fromInput.value || '';
        return v ? new Date(v + 'T00:00:00') : null;
      }
      function getTo(){
        var v = toInput && toInput.value || '';
        return v ? new Date(v + 'T23:59:59') : null;
      }
      function apply(){
        var q = norm(input.value);
        var from = getFrom();
        var to = getTo();
        rows.forEach(function(row){
          var text = norm(row.textContent);
          var dateCell = row.querySelector('td:first-child');
          var d = parseRowDate(dateCell ? dateCell.textContent : '');
          var searchOk = !q || text.indexOf(q) !== -1;
          var dateOk = true;
          if (d) {
            if (from && d < from) dateOk = false;
            if (to && d > to) dateOk = false;
          }
          row.style.display = (searchOk && dateOk) ? '' : 'none';
        });
      }
      input.addEventListener('input', apply);
      if (applyBtn) applyBtn.addEventListener('click', function(e){ e.preventDefault(); apply(); });
      if (fromInput) fromInput.addEventListener('change', apply);
      if (toInput) toInput.addEventListener('change', apply);
    })();
  </script>
</body>
</html>


