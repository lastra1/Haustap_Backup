<?php require_once __DIR__ . '/includes/auth.php'; require_once __DIR__ . '/includes/db.php'; require_once __DIR__ . '/includes/data_gateway.php';
$totalBookings = count_bookings();
$pendingJobs = count_pending_jobs();
$verifiedProviders = count_verified_providers();
$totalClients = count_clients();
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
              <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?> ▼
            </button>
            <div class="dropdown" id="userDropdown">
              <a href="#">View Profile</a>
              <a href="#">Change Password</a>
              <a href="#">Activity Logs</a>
              <a href="#" class="logout">Log out</a>
            </div>
          </div>
        </div>
      </header>

      <!-- Summary Cards -->
      <section class="cards">
        <div class="card">
          <h3 id="totalBookings"><?= (int)$totalBookings ?></h3>
          <p>Total Bookings</p>
        </div>
        <div class="card">
          <h3 id="pendingJobs"><?= (int)$pendingJobs ?></h3>
          <p>Pending Jobs</p>
        </div>
        <div class="card">
          <h3 id="verifiedProviders"><?= (int)$verifiedProviders ?></h3>
          <p>Verified Service Providers</p>
        </div>
        <div class="card">
          <h3 id="totalClients"><?= (int)$totalClients ?></h3>
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
          <tr>
            <td>Juan Dela Cruz</td>
            <td>January 7, 2025</td>
            <td><a href="#">View Documents</a></td>
          </tr>
          <tr>
            <td>Ramon Ang</td>
            <td>January 24, 2025</td>
            <td><a href="#">View Documents</a></td>
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
    // Dropdown toggle
    const dropdownBtn = document.getElementById("userDropdownBtn");
    const dropdown = document.getElementById("userDropdown");

    dropdownBtn.addEventListener("click", () => {
      dropdown.classList.toggle("show");
    });

    // Close dropdown when clicking outside
    window.addEventListener("click", (e) => {
      if (!dropdownBtn.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.remove("show");
      }
    });
  </script>
</body>
</html>


