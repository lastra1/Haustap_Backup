<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - Analytics & Report</title>
  <link rel="stylesheet" href="css/analytics_report.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/lazy-images.js" defer></script>
</head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <?php $active = 'analytics'; include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Topbar -->
      <header class="topbar">
        <h3>Job Status Monitor</h3>
        <div class="user">
          <button class="notif-btn">🔔</button>
          <div class="user-menu">
            <button id="userDropdownBtn" class="user-dropdown-btn"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?> ▼</button>
            <div class="user-dropdown" id="userDropdown">
              <a href="#">View Profile</a>
              <a href="#">Change Password</a>
              <a href="#">Activity Logs</a>
              <a href="#" class="logout">Log out</a>
            </div>
          </div>
        </div>
      </header>

      <section class="stats-cards">
        <div class="card"><h3>1,234</h3><p>Total Applicants</p></div>
        <div class="card"><h3>89</h3><p>Total Providers</p></div>
        <div class="card"><h3>567</h3><p>Total Clients</p></div>
        <div class="card"><h3>456</h3><p>Total Bookings</p></div>
      </section>

      <section class="analytics-section">
        <div class="chart-box">
          <h3>User Activity Report</h3>
          <canvas id="activityChart"></canvas>
        </div>
        <div class="chart-box">
          <h3>User Types</h3>
          <canvas id="userTypeChart"></canvas>
        </div>
        <div class="chart-box">
          <h3>Service Distribution</h3>
          <canvas id="serviceChart"></canvas>
        </div>
      </section>

      <section class="bottom-section">
        <div class="booking-stats">
          <h3>Booking & Job Statistics</h3>
          <h1>1,245</h1>
          <p>Total Bookings</p>
          <canvas id="bookingChart"></canvas>
        </div>

        <div class="rating-card">
          <h3>Average Rating</h3>
          <h1>4.5 ⭐⭐⭐⭐⭐</h1>
        </div>
      </section>

      <section class="bottom-section analytics2">
        <div class="box">
          <h3>Applicant Acceptance Rate</h3>
          <h1>56%</h1>
        </div>
        <div class="box">
          <h3>Top-rated Providers</h3>
          <h1>25</h1>
        </div>
      </section>
    </main>
  </div>

  <script>
    // User dropdown
    const dropdownBtn = document.getElementById("userDropdownBtn");
    const dropdown = document.getElementById("userDropdown");
    dropdownBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      dropdown.classList.toggle("show");
    });
    window.addEventListener("click", (e) => {
      if (!dropdown.contains(e.target)) dropdown.classList.remove("show");
    });
    
    // User Activity Chart
    new Chart(document.getElementById('activityChart'), {
      type: 'bar',
      data: {
        labels: ['Day', 'Week', 'Month'],
        datasets: [{
          label: 'Active Users',
          data: [20, 40, 60],
          backgroundColor: '#00bcd4'
        }]
      },
      options: { responsive: true, plugins: { legend: { display: false } } }
    });

    // User Type Chart
    new Chart(document.getElementById('userTypeChart'), {
      type: 'doughnut',
      data: {
        labels: ['Admin', 'Client', 'Provider', 'Applicant'],
        datasets: [{
          data: [10, 30, 40, 20],
          backgroundColor: ['#009688', '#03a9f4', '#ff9800', '#e91e63']
        }]
      },
      options: { responsive: true }
    });

    // Service Distribution
    new Chart(document.getElementById('serviceChart'), {
      type: 'pie',
      data: {
        labels: ['Cleaning', 'Indoor Services', 'Beauty Services', 'Wellness Services', 'Tech & Gadget Services'],
        datasets: [{
          data: [30, 25, 15, 10, 20],
          backgroundColor: ['#4caf50', '#03a9f4', '#e91e63', '#ff9800', '#9c27b0']
        }]
      },
      options: { responsive: true }
    });

    // Booking Chart
    new Chart(document.getElementById('bookingChart'), {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
          label: 'Bookings',
          data: [80, 100, 120, 130, 140, 160, 170, 190, 200, 210, 220, 250],
          fill: true,
          backgroundColor: 'rgba(0,188,212,0.2)',
          borderColor: '#00bcd4'
        }]
      },
      options: { responsive: true, plugins: { legend: { display: false } } }
    });
  </script>
</body>
</html>



