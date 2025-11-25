<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Bookings - Admin Dashboard</title>
  <link rel="stylesheet" href="css/manage_booking_ongoing.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="js/lazy-images.js" defer></script></head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <?php $active = 'bookings'; include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Topbar -->
      <header class="topbar">
        <h3>Manage of Bookings </h3>
        <div class="user">
          <button class="notif-btn">🔔</button>
          <div class="user-menu">
            <button id="userDropdownBtn" class="user-dropdown-btn">Mj Punzalan ▼</button>
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
        <button>All</button>
        <button>Pending</button>
        <button class="active">Ongoing</button>
        <button>Completed</button>
        <button>Cancelled</button>
        <button>Return</button>
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

        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Booking Id</th>
                <th>Client</th>
                <th>Provider</th>
                <th>Service</th>
                <th>Date & Time</th>
                <th>Status</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>Juan Ewan Dela Cruz</td>
                <td>Ramon Ang</td>
                <td>Home Cleaning</td>
                <td>2025-06-07 14:30</td>
                <td><span class="status ongoing">Ongoing</span></td>
                <td>&gt;</td>
              </tr>
              <tr>
                <td>2</td>
                <td>Ramon Ang</td>
                <td>Juan Dela Cruz</td>
                <td>Home Cleaning</td>
                <td>2025-06-07 14:30</td>
                <td><span class="status ongoing">Ongoing</span></td>
                <td>&gt;</td>
              </tr>
            </tbody>
          </table>
         <div class="pagination">
          <span>[ ◀ Prev ]</span>
          <span>Showing 2–10 of 120 Clients</span>
          <span>[ Next ▶ ]</span>
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

    // Date filter: show rows within selected date range
    (function(){
      const fromInput = document.getElementById('from-date');
      const toInput = document.getElementById('to-date');
      const applyBtn = document.querySelector('.apply-btn');

      function parseRowDate(text){
        if (!text) return null;
        const m = text.match(/(\d{4})\s*-\s*(\d{2})\s*-\s*(\d{2})/);
        if (!m) return null;
        const iso = `${m[1]}-${m[2]}-${m[3]}`;
        const d = new Date(iso);
        return isNaN(d.getTime()) ? null : d;
      }

      function applyDateFilter(){
        const fromVal = fromInput ? fromInput.value : '';
        const toVal = toInput ? toInput.value : '';
        const fromDate = fromVal ? new Date(fromVal) : null;
        const toDate = toVal ? new Date(toVal) : null;

        const rows = document.querySelectorAll('.table-container tbody tr');
        rows.forEach(row => {
          const dateCell = row.querySelector('td:nth-child(5)');
          const rowDate = parseRowDate(dateCell ? dateCell.textContent.trim() : '');
          if (!rowDate) {
            row.style.display = '';
            return;
          }
          const within = (!fromDate || rowDate >= fromDate) && (!toDate || rowDate <= toDate);
          row.style.display = within ? '' : 'none';
        });
      }

      if (fromInput) fromInput.addEventListener('change', applyDateFilter);
      if (toInput) toInput.addEventListener('change', applyDateFilter);
      if (applyBtn) applyBtn.addEventListener('click', (e) => { e.preventDefault(); applyDateFilter(); });
    })();
  </script>
</body>
</html>



