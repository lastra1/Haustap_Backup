<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Activity</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="css/manage_provider_activity.css">
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
        <h3>Manage of Provider > Name </h3>
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

      <!-- Tabs -->
      <div class="tabs">
        <button>Profile</button>
        <button>Bookings</button>
        <button class="active">Activity</button>
        <button>Voucher</button>
        <button>Subscription</button>
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
</body>
</html>


