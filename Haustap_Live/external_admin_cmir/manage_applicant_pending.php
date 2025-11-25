<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage of Applicants</title>
  <link rel="stylesheet" href="css/manage_applicant_pending.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <script src="js/lazy-images.js" defer></script>
</head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <?php $active = 'applicants'; include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Topbar -->
      <header class="topbar">
        <h3>Manage  Applicants</h3>
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
        <button>All</button>
        <button class="active">Pending Review</button>
        <button>Scheduled</button>
        <button>Hired</button>
        <button>Rejected</button>
      </div>

      <!-- Search and Filter -->
      <div class="search-filter">
        <div class="search-box">
          <input type="text" placeholder="Search Applicant" />
        </div>
<div class="filter-btn"><i class="fa-solid fa-sliders"></i> Filter</div>
      </div>

      <!-- Table -->
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Id</th>
              <th>Name</th>
              <th>Date Applied</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>Juan Ewan Dela Cruz</td>
              <td>January 7, 2025</td>
              <td><span class="status pending">Pending Review</span></td>
              <td class="arrow">›</td>
            </tr>
            <tr>
              <td>2</td>
              <td>Ramon Ang</td>
              <td>January 24, 2025</td>
              <td><span class="status pending">Pending Review</span></td>
              <td class="arrow">›</td>
            </tr>
          </tbody>
        </table>

        <div class="pagination">
          <span>◄ Prev</span>
          <span>Showing 2–10 of 120</span>
          <span>Next ►</span>
        </div>
      </div>
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
 

