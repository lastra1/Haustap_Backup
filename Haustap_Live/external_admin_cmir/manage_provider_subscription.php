<?php require_once __DIR__ . '/includes/auth.php'; ?>
<?php
// Load provider data from storage by id
$provider = null;
$providerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$storePath = realpath(__DIR__ . '/../../storage/data/providers.json');
if ($storePath && is_file($storePath)) {
  $raw = @file_get_contents($storePath);
  $items = json_decode($raw ?: '[]', true);
  if (is_array($items)) {
    foreach ($items as $it) {
      if (isset($it['id']) && (int)$it['id'] === $providerId) { $provider = $it; break; }
    }
  }
}
if (!$provider) {
  $provider = [
    'id' => $providerId ?: 0,
    'name' => 'Unknown',
    'status' => isset($_GET['status']) ? $_GET['status'] : 'active'
  ];
}
?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Providers - Subscription</title>
  <link rel="stylesheet" href="css/manage_provider_subscription.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="js/lazy-images.js" defer></script>
</head>
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
              <a href="admin_profile.php">View Profile</a>
              <a href="/admin_haustap/admin_haustap/change_password.php">Change Password</a>
              <a href="logout.php" class="logout">Log out</a>
            </div>
          </div>
        </div>
      </header>

      <!-- Tabs -->
      <div class="tabs">
        <?php $pid = (int)($provider['id'] ?? 0); $pstatus = urlencode($provider['status'] ?? ''); ?>
        <button data-target="manage_provider_profile.php?id=<?php echo $pid; ?>&status=<?php echo $pstatus; ?>">Profile</button>
        <button data-target="manage_provider_jobs.php?id=<?php echo $pid; ?>&status=<?php echo $pstatus; ?>">Jobs</button>
        <button data-target="manage_provider_activity.php?id=<?php echo $pid; ?>&status=<?php echo $pstatus; ?>">Activity</button>
        <button data-target="manage_provider_voucher.php?id=<?php echo $pid; ?>&status=<?php echo $pstatus; ?>">Voucher</button>
        <button class="active" data-target="manage_provider_subscription.php?id=<?php echo $pid; ?>&status=<?php echo $pstatus; ?>">Subscription</button>
      </div>

      <!-- Search and Filter -->
      <div class="search-filter">
        <input id="subscriptionSearch" type="text" placeholder="Search Subscriptions">
        <div class="filter-dropdown">
          <button class="filter-btn"><i class="fa-solid fa-sliders"></i> Filter ▼</button>
          <div class="dropdown-content">
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

      <!-- Subscription Table -->
      <table class="subscription-table">
        <thead>
          <tr>
            <th>Plan Name</th>
            <th>Price</th>
            <th>Start Date</th>
            <th>Expiration Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Haustap Standard Access</td>
            <td>₱499</td>
            <td>2025-10-01</td>
            <td>2025-10-31</td>
            <td>
              <span class="status active">Active</span>
              <button class="details-btn" data-type="active">></button>
            </td>
          </tr>
          <tr>
            <td>Haustap Standard Access</td>
            <td>₱499</td>
            <td>2025-09-01</td>
            <td>2025-09-30</td>
            <td>
              <span class="status expired">Expired</span>
              <button class="details-btn" data-type="expired">></button>
            </td>
          </tr>
          <tr>
            <td>Haustap Standard Access</td>
            <td>₱499</td>
            <td>2025-08-01</td>
            <td>2025-08-31</td>
            <td>
              <span class="status inactive">Inactive</span>
              <button class="details-btn" data-type="inactive">></button>
            </td>
          </tr>
        </tbody>
      </table>
    </main>
  </div>

  <!-- Popup Modal -->
  <div class="modal" id="detailsModal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <div id="modal-body"></div>
    </div>
  </div>

  <script>
    (function(){
      // User dropdown
      const dropdownBtn = document.getElementById("userDropdownBtn");
      const dropdown = document.getElementById("userDropdown");
      if (dropdownBtn && dropdown) {
        dropdownBtn.addEventListener("click", (e) => {
          e.stopPropagation();
          dropdown.classList.toggle("show");
        });
        window.addEventListener("click", (e) => {
          if (!dropdown.contains(e.target)) dropdown.classList.remove("show");
        });
      }

      // Filter dropdown
      const filterBtn = document.querySelector('.filter-btn');
      const dropdownContent = document.querySelector('.dropdown-content');
      if (filterBtn && dropdownContent) {
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

        // Subscription search + date filter integration
        (function(){
          const searchInput = document.getElementById('subscriptionSearch');
          const applyBtn = dropdownContent.querySelector('.apply-btn');
          const fromDate = document.getElementById('from-date');
          const toDate = document.getElementById('to-date');

          function updateRowVisibility(){
            const rows = document.querySelectorAll('.subscription-table tbody tr');
            rows.forEach(row => {
              const fHidden = row.dataset.filterHidden === 'true';
              const sHidden = row.dataset.searchHidden === 'true';
              row.style.display = (fHidden || sHidden) ? 'none' : '';
            });
          }

          function applyDateFilter(){
            const f = fromDate && fromDate.value ? new Date(fromDate.value) : null;
            const t = toDate && toDate.value ? new Date(toDate.value) : null;
            const rows = document.querySelectorAll('.subscription-table tbody tr');
            rows.forEach(row => {
              const expiryTd = row.querySelector('td:nth-child(4)');
              const txt = expiryTd ? expiryTd.textContent.trim() : '';
              const d = txt ? new Date(txt) : null;
              let hide = false;
              if (d && f && d < f) hide = true;
              if (d && t) { t.setHours(23,59,59,999); if (d > t) hide = true; }
              row.dataset.filterHidden = hide ? 'true' : '';
            });
            updateRowVisibility();
          }

          function debounce(fn, wait){ let t; return (...args) => { clearTimeout(t); t = setTimeout(()=>fn(...args), wait); }; }

          function applySearch(q){
            const ql = (q||'').trim().toLowerCase();
            const rows = document.querySelectorAll('.subscription-table tbody tr');
            rows.forEach(row => {
              const plan = (row.querySelector('td:nth-child(1)')||{textContent:''}).textContent.toLowerCase();
              const price = (row.querySelector('td:nth-child(2)')||{textContent:''}).textContent.toLowerCase();
              const start = (row.querySelector('td:nth-child(3)')||{textContent:''}).textContent.toLowerCase();
              const expiry = (row.querySelector('td:nth-child(4)')||{textContent:''}).textContent.toLowerCase();
              const status = (row.querySelector('td:nth-child(5)')||{textContent:''}).textContent.toLowerCase();
              const matches = ql === '' || plan.includes(ql) || price.includes(ql) || start.includes(ql) || expiry.includes(ql) || status.includes(ql);
              row.dataset.searchHidden = matches ? '' : 'true';
            });
            updateRowVisibility();
          }

          if (searchInput) searchInput.addEventListener('input', debounce((e)=>applySearch(e.target.value), 200));
          if (applyBtn) applyBtn.addEventListener('click', (e)=>{ e.preventDefault(); applyDateFilter(); dropdownContent.classList.remove('show'); });
        })();
      }

      // Tabs
      const tabs = document.querySelector('.tabs');
      if (tabs) {
        const btns = Array.from(tabs.querySelectorAll('button'));
        btns.forEach(btn => btn.addEventListener('click', () => {
          const target = btn.getAttribute('data-target');
          if (target) window.location.href = target;
        }));
      }

      // Modal
      const modal = document.getElementById("detailsModal");
      const modalBody = document.getElementById("modal-body");
      const closeBtn = document.querySelector(".close");
      const buttons = document.querySelectorAll(".details-btn");

      const templates = {
        active: `
          <h3>Subscription Details</h3>
          <p><b>Subscription ID:</b> 0123</p>
          <p><b>Plan Name:</b> Haustap Partner Plan</p>
          <p><b>Plan Price:</b> ₱499</p>
          <p><b>Duration:</b> 30 Days</p>
          <p><b>Start Date:</b> October 1, 2025</p>
          <p><b>Expiration Date:</b> October 31, 2025</p>
          <p><b>Status:</b> <span class="status active">Active</span></p>

          <h3>Payment Information</h3>
          <p><b>Payment Method:</b> GCash</p>
          <p><b>GCash Reference No.:</b> 100294837560</p>
          <p><b>Date of Payment:</b> October 1, 2025 – 10:42 AM</p>
          <p><b>Payment Status:</b> Paid & Verified</p>

          <h3>Billing Summary</h3>
          <p><b>Plan Price:</b> ₱499</p>
          <p><b>Voucher Applied:</b> ₱50</p>
          <p><b>Next Payment Amount:</b> ₱449</p>
          <p><b>Voucher Note:</b> Your ₱50 voucher was successfully applied to this subscription.</p>

          <h3>Subscriber Information</h3>
          <p><b>Service Provider Name:</b> Juan Dela Cruz</p>
          <p><b>Email:</b> juan@haustap.com</p>
          <p><b>Service Category:</b> Plumbing</p>
          <p><b>Account Status:</b> Verified</p>
        `,
        expired: `
          <h3>Subscription Details</h3>
          <p><b>Subscription ID:</b> 0123</p>
          <p><b>Plan Name:</b> Haustap Partner Plan</p>
          <p><b>Plan Price:</b> ₱499</p>
          <p><b>Duration:</b> 30 Days</p>
          <p><b>Start Date:</b> October 1, 2025</p>
          <p><b>Expiration Date:</b> October 31, 2025</p>
          <p><b>Status:</b> <span class="status expired">Expired</span></p>

          <h3>Payment Information</h3>
          <p><b>Payment Method:</b> GCash</p>
          <p><b>GCash Reference No.:</b> 100294837560</p>
          <p><b>Date of Payment:</b> October 1, 2025 – 10:42 AM</p>
          <p><b>Payment Status:</b> Completed</p>

          <h3>Billing Summary</h3>
          <p><b>Plan Price:</b> ₱499</p>
          <p><b>Voucher Applied:</b> ₱50</p>
          <p><b>Next Payment Amount:</b> ₱449</p>

          <h3>Subscriber Information</h3>
          <p><b>Service Provider Name:</b> Juan Dela Cruz</p>
          <p><b>Email:</b> juan@haustap.com</p>
          <p><b>Service Category:</b> Plumbing</p>
          <p><b>Account Status:</b> Verified</p>
        `,
        inactive: `
          <h3>Subscription Details</h3>
          <p><b>Subscription ID:</b> 0123</p>
          <p><b>Plan Name:</b> Haustap Partner Plan</p>
          <p><b>Plan Price:</b> ₱499</p>
          <p><b>Duration:</b> 30 Days</p>
          <p><b>Start Date:</b> —</p>
          <p><b>Expiration Date:</b> —</p>
          <p><b>Status:</b> <span class="status inactive">Inactive</span></p>

          <h3>Payment Information</h3>
          <p><b>Payment Method:</b> —</p>
          <p><b>GCash Reference No.:</b> —</p>
          <p><b>Date of Payment:</b> —</p>
          <p><b>Payment Status:</b> —</p>
        `
      };

      if (buttons && modal && modalBody && closeBtn) {
        buttons.forEach(btn => {
          btn.addEventListener("click", () => {
            const type = btn.dataset.type;
            modalBody.innerHTML = templates[type] || '';
            modal.style.display = "block";
          });
        });
        closeBtn.onclick = () => modal.style.display = "none";
        window.addEventListener('click', (e) => { if (e.target === modal) modal.style.display = "none"; });
      }
    })();
  </script>
</body>
</html>
