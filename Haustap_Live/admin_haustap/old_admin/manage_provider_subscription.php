<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Providers - Subscription</title>
  <link rel="stylesheet" href="css/manage_provider_subscription.css">
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
        <button>Voucher</button>
        <button class="active">Subscription</button>
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
      </div>
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


    const modal = document.getElementById("detailsModal");
    const modalBody = document.getElementById("modal-body");
    const closeBtn = document.querySelector(".close");
    const buttons = document.querySelectorAll(".details-btn");

    const templates = {
      active: `
        <h3>Subscription Details</h3>
        <p><strong>Subscription ID:</strong> 0123</p>
        <p><strong>Plan Name:</strong> Haustap Partner Plan</p>
        <p><strong>Plan Price:</strong> ₱499</p>
        <p><strong>Duration:</strong> 30 Days</p>
        <p><strong>Start Date:</strong> October 1, 2025</p>
        <p><strong>Expiration Date:</strong> October 31, 2025</p>
        <p><strong>Status:</strong> <span class="status active">Active</span></p>

        <h3>Payment Information</h3>
        <p><strong>Payment Method:</strong> GCash</p>
        <p><strong>GCash Reference No.:</strong> 100294837650</p>
        <p><strong>Date of Payment:</strong> October 1, 2025 – 10:42 AM</p>
        <p><strong>Payment Status:</strong> Completed</p>

        <h3>Billing Summary</h3>
        <p><strong>Plan Price:</strong> ₱499</p>
        <p><strong>Voucher Applied:</strong> ₱50</p>
        <p><strong>Next Payment Amount:</strong> ₱449</p>
      `,
      expired: `
        <h3>Subscription Details</h3>
        <p><strong>Subscription ID:</strong> 0123</p>
        <p><strong>Plan Name:</strong> <a href="#">Haustap Partner Plan</a></p>
        <p><strong>Plan Price:</strong> ₱499</p>
        <p><strong>Duration:</strong> 30 Days</p>
        <p><strong>Start Date:</strong> October 1, 2025</p>
        <p><strong>Expiration Date:</strong> October 31, 2025</p>
        <p><strong>Status:</strong> <span class="status expired">Expired</span></p>

        <h3>Payment Information</h3>
        <p><strong>Payment Method:</strong> GCash</p>
        <p><strong>GCash Reference No.:</strong> 100294837650</p>
        <p><strong>Date of Payment:</strong> October 1, 2025 – 10:42 AM</p>
        <p><strong>Payment Status:</strong> Completed</p>

        <h3>Billing Summary</h3>
        <p><strong>Plan Price:</strong> ₱499</p>
        <p><strong>Voucher Applied:</strong> ₱50</p>
        <p><strong>Next Payment Amount:</strong> ₱449</p>
      `,
      inactive: `
        <h3>Subscription Details</h3>
        <p><strong>Subscription ID:</strong> 0123</p>
        <p><strong>Plan Name:</strong> Haustap Partner Plan</p>
        <p><strong>Plan Price:</strong> ₱499</p>
        <p><strong>Duration:</strong> 30 Days</p>
        <p><strong>Start Date:</strong> —</p>
        <p><strong>Expiration Date:</strong> —</p>
        <p><strong>Status:</strong> <span class="status inactive">Inactive</span></p>

        <h3>Payment Information</h3>
        <p><strong>Payment Method:</strong> —</p>
        <p><strong>GCash Reference No.:</strong> —</p>
        <p><strong>Date of Payment:</strong> —</p>
        <p><strong>Payment Status:</strong> —</p>
      `
    };

    buttons.forEach(btn => {
      btn.addEventListener("click", () => {
        const type = btn.dataset.type;
        modalBody.innerHTML = templates[type];
        modal.style.display = "block";
      });
    });

    closeBtn.onclick = () => modal.style.display = "none";
    window.onclick = (e) => { if (e.target === modal) modal.style.display = "none"; };
  </script>
</body>
</html>


