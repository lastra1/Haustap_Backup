<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Manage Clients</title>
  <link rel="stylesheet" href="css/manage_provider_voucher.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="js/lazy-images.js" defer></script></head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <?php $active = 'providers'; include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Topbar -->
      <header class="topbar">
        <h3>Manage of Provider > name </h3>
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
        <button>Activity</button>
        <button class="active">Voucher</button>
        <button>Subscription</button>
      </div>

      <!-- Search and Filter -->
<div class="search-filter">
  <input type="text" placeholder="Search">

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

        <!-- Table -->
        <div class="voucher-table">
          <table>
            <thead>
              <tr>
                <th>Code</th>
                <th>Discount</th>
                <th>Expiry Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>WELCOME VOUCHER</td>
                <td>₱50 OFF</td>
                <td>2025-10-01</td>
                <td><span class="status active">Active</span></td>
              </tr>
              <tr>
                <td>LOYALTY BONUS</td>
                <td>₱50 OFF</td>
                <td>2025-10-01</td>
                <td><span class="status expired">Expired</span></td>
              </tr>
            </tbody>
          </table>

          <!-- Pagination -->
          <div class="pagination">
            <button class="prev">◀ Prev</button>
            <p>Showing 2–10 of 120</p>
            <button class="next">Next ▶</button>
          </div>
        </div>
      </section>

    </main>
  </div>

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


