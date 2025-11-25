<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Clients | Profile</title>
<link rel="stylesheet" href="css/manage_client_profile.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="js/lazy-images.js" defer></script>
</head>
<body>
<div class="dashboard-container">
    <!-- Sidebar -->
    <?php $active = 'clients'; include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Topbar -->
      <header class="topbar">
        <h3>Manage of Clients</h3>
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
        <button class="active">Profile</button>
        <button>Bookings</button>
        <button>Activity</button>
        <button>Voucher</button>
      </div>

        <!-- Profile Content -->
        <div class="profile-box">

            <div class="left-info">
                <div class="profile-img">
<i class="fa-solid fa-user"></i>
                </div>
                <p class="register-date">Registered on:<br><strong>January 01, 2025</strong></p>
            </div>

            <div class="right-info">
                <p><strong>Status:</strong> Active</p>
                <p><strong>ID:</strong> 01</p>
                <p><strong>Full name:</strong> Jenn Bornilla</p>
                <p><strong>Email:</strong> <a href="#">jennbornilla@gmail.com</a></p>
                <p><strong>Mobile number:</strong> 09489129312</p>
                <p><strong>Date of Birth:</strong> May 04 2003</p>
                <p><strong>Gender:</strong> Female</p>
                <p><strong>Address:</strong> Blk 1 Lot 50 Mango St. Saint Joseph Village 10 Brgy Langgam City of San Pedro Laguna</p>
            </div>

            <button class="submit-btn">Submit</button>

        </div>

        <!-- Account Actions -->
        <div class="actions">
            <h4>Account Actions:</h4>
            <button class="btn suspend">Suspend</button>
            <button class="btn banned">Banned</button>
            <button class="btn delete">Delete</button>
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


