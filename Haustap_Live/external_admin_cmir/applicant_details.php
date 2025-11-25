<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin | Applicant Details</title>
  <link rel="stylesheet" href="css/applicant_details.css" />
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

      <!-- Page Title -->
      <div class="page-header">
        <h3>Manage of Applicants > Juan Dela Cruz</h3>
      </div>

      <!-- Application Form -->
      <div class="application-section">
        <div class="status-container">
          <label>Current Status:</label>
          <select class="status-dropdown">
            <option selected>Scheduled</option>
            <option>Pending Review</option>
            <option>Hired</option>
            <option>Rejected</option>
          </select>
        </div>

        <div class="application-card">
          <h4>Application Form</h4>
          <hr />
          <div class="form-section">
            <p><strong>Choose account Type:</strong></p>

            <p><strong>Basic Information</strong></p>
            <p>Last Name: <span>Dela Cruz</span></p>
            <p>First Name: <span>Juan</span></p>
            <p>Email:</p>
            <p>Mobile number:</p>
            <p>Birthdate:</p>

            <p><strong>Full Address</strong></p>
            <p>House no. & Street Name:</p>
            <p>Barangay:</p>
            <p>Municipal:</p>
            <p>Province:</p>

            <p><strong>Service/s offer:</strong></p>
          </div>
        </div>

        <div class="update-container">
          <button class="update-btn">Update Status</button>
        </div>
      </div>
    </main>
  </div>

  <script>
    // Dropdown logic
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


