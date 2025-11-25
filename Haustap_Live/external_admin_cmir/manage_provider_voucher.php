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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Providers - Voucher</title>
  <link rel="stylesheet" href="css/manage_provider_voucher.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
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
        <button data-target="manage_provider_activity.php?id=<?php echo $pid; ?>&status=<?php echo $pstatus; ?>">Activity</button>
        <button class="active" data-target="manage_provider_voucher.php?id=<?php echo $pid; ?>&status=<?php echo $pstatus; ?>">Voucher</button>
        <button data-target="manage_provider_subscription.php?id=<?php echo $pid; ?>&status=<?php echo $pstatus; ?>">Subscription</button>
      </div>

      <section class="content">
        <div class="voucher-table">
          <table>
            <thead>
              <tr>
                <th>Voucher Code</th>
                <th>Description</th>
                <th>Discount</th>
                <th>Valid Until</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>PROV-50OFF</td>
                <td>₱50 off subscription</td>
                <td>₱50</td>
                <td>2025-12-31</td>
                <td><span class="status active">Active</span></td>
              </tr>
              <tr>
                <td>PROV-EXPIRED25</td>
                <td>₱25 off any service</td>
                <td>₱25</td>
                <td>2025-06-30</td>
                <td><span class="status expired">Expired</span></td>
              </tr>
            </tbody>
          </table>
          <div class="pagination">
            <span>◄ Prev</span>
            <span>Showing 1–10 of 24</span>
            <span>Next ►</span>
          </div>
        </div>
      </section>
    </main>
  </div>

  <script>
    (function(){
      const dropdownBtn = document.getElementById('userDropdownBtn');
      const dropdown = document.getElementById('userDropdown');
      if (dropdownBtn && dropdown) {
        dropdownBtn.addEventListener('click', function(e){ e.stopPropagation(); dropdown.classList.toggle('show'); });
        window.addEventListener('click', function(e){ if (!dropdown.contains(e.target)) dropdown.classList.remove('show'); });
      }
      const tabs = document.querySelector('.tabs');
      if (tabs) {
        tabs.querySelectorAll('button').forEach(function(btn){
          btn.addEventListener('click', function(){
            const target = btn.getAttribute('data-target');
            if (target) window.location.href = target;
          });
        });
      }
    })();
  </script>
</body>
</html>


