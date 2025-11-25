<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Bookings - Return</title>
  <link rel="stylesheet" href="css/manage_booking_return.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
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
        <h3>Manage of Bookings</h3>
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

      <section class="content">
        <!-- Tabs -->
        <div class="tabs">
          <button>All</button>
          <button>Pending</button>
          <button>Ongoing</button>
          <button>Completed</button>
          <button>Cancelled</button>
          <button class="active">Return</button>
        </div>

        <!-- Search and Filter -->
<div class="search-filter">
  <input type="text" placeholder="Search">

  <div class="filter-dropdown">
<button class="filter-btn"><i class="fa-solid fa-sliders"></i> Filter ▼</button>
    <div class="dropdown-content">
            <p class="filter-title">Filter by Status</p>
            <label><input type="checkbox"> Approved</label>
            <label><input type="checkbox"> Pending</label>
            <label><input type="checkbox"> Declined</label>
            <button class="apply-btn">Apply</button>
          </div>
        </div>
      </div>

        <!-- Table -->
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
                <td>Juan Dela Cruz</td>
                <td>Ramon Ang</td>
                <td>Home Cleaning</td>
                <td>2025-06-07 14:30</td>
                <td><span class="status approved">Approved</span></td>
                <td>&gt;</td>
              </tr>
              <tr>
                <td>2</td>
                <td>Ramon Ang</td>
                <td>Juan Dela Cruz</td>
                <td>Home Cleaning</td>
                <td>2025-06-07 14:30</td>
                <td><span class="status pending">Pending</span></td>
                <td>&gt;</td>
              </tr>
              <tr>
                <td>3</td>
                <td>Cj Pogi</td>
                <td>Juan Dela Cruz</td>
                <td>Home Cleaning</td>
                <td>2025-06-07 14:30</td>
                <td><span class="status declined">Declined</span></td>
                <td>&gt;</td>
              </tr>
            </tbody>
          </table>

          <div class="pagination">
            <span>[◄ Prev]</span>
            <span>Showing 2–10 of 120 Clients</span>
            <span>[Next ►]</span>
          </div>
        </div>
      </section>
    </main>
  </div>
  <script>
    const dropdownBtn = document.getElementById("userDropdownBtn");
    const dropdown = document.getElementById("userDropdown");

    dropdownBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      dropdown.classList.toggle("show");
    });

    window.addEventListener("click", (e) => {
      if (!dropdown.contains(e.target)) dropdown.classList.remove("show");
    });
  </script>
</body>
</html>


