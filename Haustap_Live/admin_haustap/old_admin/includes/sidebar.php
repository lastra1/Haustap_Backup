<?php
require_once __DIR__ . '/auth.php';
$active = isset($active) ? $active : '';
?>
<style>
  .sidebar nav ul { list-style: none; padding: 0; margin: 0; }
  .sidebar nav ul li a {
    display: block;
    text-decoration: none;
    color: inherit;
  }
  .sidebar nav ul li a:visited { color: inherit; }
  .sidebar nav ul li a:hover { text-decoration: none; }
  .sidebar .logo a { text-decoration: none; }
</style>
<aside class="sidebar">
  <div class="logo">
    <a href="dashboard.php" title="Dashboard"><img src="images/logo.png" alt="logo" /></a>
    <span>Admin Dashboard</span>
  </div>
  <nav>
    <ul>
      <li class="<?= $active === 'dashboard' ? 'active' : '' ?>"><a href="dashboard.php">Dashboard Overview</a></li>
      <li class="<?= $active === 'applicants' ? 'active' : '' ?>"><a href="manage_applicant.php">Manage Applicants</a></li>
      <li class="<?= $active === 'clients' ? 'active' : '' ?>"><a href="manage_client.php">Manage Clients</a></li>
      <li class="<?= $active === 'providers' ? 'active' : '' ?>"><a href="manage_provider.php">Manage Providers</a></li>
      <li class="<?= $active === 'bookings' ? 'active' : '' ?>"><a href="manage_booking.php">Manage Bookings</a></li>
      <li class="<?= $active === 'job_status' ? 'active' : '' ?>"><a href="job_status_monitor.php">Job Status Monitor</a></li>
      <li class="<?= $active === 'analytics' ? 'active' : '' ?>"><a href="analytics_report.php">Analytics & Report</a></li>
      <li class="<?= $active === 'subscription' ? 'active' : '' ?>"><a href="subscription_management.php">Subscription Management</a></li>
      <li class="<?= $active === 'feedback' ? 'active' : '' ?>"><a href="feedback_reviews.php">Feedback & Reviews</a></li>
      <li class="<?= $active === 'settings' ? 'active' : '' ?>"><a href="system_settings.php">System Settings</a></li>
      <li class="<?= $active === 'backend_admin' ? 'active' : '' ?>"><a href="filament_redirect.php">Backend Admin</a></li>
    </ul>
  </nav>
</aside>
<script>
  // Default socket URL for notifications/chat if not set elsewhere
  window.SOCKET_URL = window.SOCKET_URL || 'http://localhost:3000';
</script>
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
<!-- Removed cross-root client script references to avoid 404s on admin dev server -->
<!-- Admin-specific UI injection to add dropdown markup and count bubble -->
<script src="js/notify-admin.js" defer></script>
<!-- UI binding that renders dropdown and handles unread count -->
<!-- If shared client UI binding is needed later, serve it under admin/js to keep paths consistent -->
<!-- Make the topbar Log out link functional -->
<script src="js/logout-admin.js" defer></script>
<script>
  // Normalize user dropdown links across pages to real endpoints
  document.addEventListener('DOMContentLoaded', function(){
    try {
      var dd = document.getElementById('userDropdown');
      if (!dd) return;
      var links = dd.querySelectorAll('a');
      links.forEach(function(link){
        var t = (link.textContent || '').trim().toLowerCase();
        if (t === 'view profile') link.setAttribute('href', 'admin_profile.php');
        else if (t === 'change password') link.setAttribute('href', 'change_password.php');
        else if (t === 'activity logs') link.setAttribute('href', 'activity_logs.php');
      });
    } catch(e){}
  });
</script>
