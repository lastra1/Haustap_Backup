<?php require_once __DIR__ . '/includes/auth.php'; ?>
<?php
// Load summary data (try DB first, fall back to JSON files)
$totalBookings = 0;
$pendingJobs = 0;
$verifiedProviders = 0;
$totalClients = 0;

// Try DB connection if DSN provided via env
$dbDsn = getenv('DB_DSN') ?: getenv('DATABASE_URL');
$dbUser = getenv('DB_USER') ?: getenv('DATABASE_USER');
$dbPass = getenv('DB_PASS') ?: getenv('DATABASE_PASS');
$usedDb = false;
if ($dbDsn) {
  try {
    $pdo = new PDO($dbDsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    // Total bookings
    try {
      $stmt = $pdo->query('SELECT COUNT(*) FROM bookings');
      $totalBookings = (int)$stmt->fetchColumn();
    } catch (Exception $e) { $totalBookings = 0; }

    // Pending jobs
    try {
      $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE LOWER(status) = 'pending'");
      $stmt->execute();
      $pendingJobs = (int)$stmt->fetchColumn();
    } catch (Exception $e) { $pendingJobs = 0; }

    // Verified providers (status = active)
    try {
      $stmt = $pdo->prepare("SELECT COUNT(*) FROM providers WHERE LOWER(status) = 'active'");
      $stmt->execute();
      $verifiedProviders = (int)$stmt->fetchColumn();
    } catch (Exception $e) { $verifiedProviders = 0; }

    // Total clients
    try {
      $stmt = $pdo->query('SELECT COUNT(*) FROM clients');
      $totalClients = (int)$stmt->fetchColumn();
    } catch (Exception $e) { $totalClients = 0; }

    $usedDb = true;
  } catch (Exception $e) {
    // DB connection failed; we'll fall back to JSON files below
    $usedDb = false;
  }
}

if (!$usedDb) {
  $bookings = [];
  $clients = [];
  $providers = [];

  $bookingsPath = realpath(__DIR__ . '/../../backend/api/storage/data/bookings.json');
  if ($bookingsPath && is_file($bookingsPath)) {
    $raw = @file_get_contents($bookingsPath);
    $bookings = json_decode($raw ?: '[]', true) ?: [];
  }

  $clientsPath = realpath(__DIR__ . '/../../storage/data/clients.json');
  if ($clientsPath && is_file($clientsPath)) {
    $raw = @file_get_contents($clientsPath);
    $clients = json_decode($raw ?: '[]', true) ?: [];
  }

  $providersPath = realpath(__DIR__ . '/../../storage/data/providers.json');
  if ($providersPath && is_file($providersPath)) {
    $raw = @file_get_contents($providersPath);
    $providers = json_decode($raw ?: '[]', true) ?: [];
  }

  // Compute summary values from JSON files
  $totalBookings = count($bookings);
  $pendingJobs = 0;
  foreach ($bookings as $b) {
    if (isset($b['status']) && strtolower($b['status']) === 'pending') $pendingJobs++;
  }

  $verifiedProviders = 0;
  foreach ($providers as $p) {
    if (isset($p['status']) && strtolower($p['status']) === 'active') $verifiedProviders++;
  }

  $totalClients = count($clients);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="css/dashboard.css" />
<script src="js/lazy-images.js" defer></script>
<script src="js/app.js" defer></script>
  <style>
    /* clickable applicant rows */
    .clickable-row { cursor: pointer; }
    .clickable-row:hover { background: #f6fbfb; }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <?php $active = 'dashboard'; include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
      <header class="topbar">
        <div class="user">
          <button class="notif-btn">🔔</button>
          <div class="user-menu">
            <button class="user-btn" id="userDropdownBtn">
              Mj Punzalan ▼
            </button>
            <div class="dropdown" id="userDropdown">
              <a href="admin_profile.php">View Profile</a>
              <a href="change_password.php">Change Password</a>
              <a href="logout.php" class="logout">Log out</a>
            </div>
          </div>
        </div>
      </header>


      <!-- Summary Cards -->
      <section class="cards">
        <div class="card">
          <h3 id="totalBookings"><?php echo number_format(intval($totalBookings)); ?></h3>
          <p>Total Bookings</p>
        </div>
        <div class="card">
          <h3 id="pendingJobs"><?php echo number_format(intval($pendingJobs)); ?></h3>
          <p>Pending Jobs</p>
        </div>
        <div class="card">
          <h3 id="verifiedProviders"><?php echo number_format(intval($verifiedProviders)); ?></h3>
          <p>Verified Service Providers</p>
        </div>
        <div class="card">
          <h3 id="totalClients"><?php echo number_format(intval($totalClients)); ?></h3>
          <p>Total Clients</p>
        </div>
      </section>

      <!-- Applicants List -->
      <section class="applicants">
        <h2>List of Applicants</h2>
        <table>
          <tr>
            <th>Name</th>
            <th>Date Applied</th>
            <th></th>
          </tr>
          <tr class="clickable-row" data-id="1">
            <td>Juan Dela Cruz</td>
            <td>January 7, 2025</td>
            <td></td>
          </tr>
          <tr class="clickable-row" data-id="2">
            <td>Ramon Ang</td>
            <td>January 24, 2025</td>
            <td></td>
          </tr>
        </table>
      </section>

      <!-- System Alert -->
      <section class="system-alert">
        <h2>System Alert</h2>
        <div class="alert">
          ⚠️ 3 new booking request pending
        </div>
        <div class="alert">
          ⚠️ Provider approval required for 2 new applicants
        </div>
      </section>
    </main>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function(){
      // Make applicant rows navigate to manage_applicant.php?id={id} when clicked
      (function(){
        const table = document.querySelector('.applicants table');
        if (!table) return;
        table.addEventListener('click', function(e){
          const tr = e.target.closest('tr.clickable-row');
          if (!tr) return;
          const id = tr.dataset.id;
          if (!id) return;
          // Respect modifier keys to allow opening in new tab
          const url = 'manage_applicant.php?id=' + encodeURIComponent(id);
          if (e.ctrlKey || e.metaKey || e.button === 1) {
            window.open(url, '_blank');
            return;
          }
          window.location.href = url;
        });
      })();

      // Dropdown toggle (user menu)
      (function(){
        const dropdownBtn = document.getElementById('userDropdownBtn');
        const dropdown = document.getElementById('userDropdown');
        if (!dropdownBtn || !dropdown) return;
        dropdownBtn.addEventListener('click', function(e){
          e.stopPropagation();
          dropdown.classList.toggle('show');
        });
        // Close when clicking outside
        document.addEventListener('click', function(e){
          if (!dropdown.contains(e.target) && !dropdownBtn.contains(e.target)) dropdown.classList.remove('show');
        });
      })();
    });
  </script>

  
</body>
</html>


