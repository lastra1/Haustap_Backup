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
  /* Make the logo/title text normal weight (remove bold) */
  .sidebar .logo span { font-weight: normal; }
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
      <li class="<?= $active === 'reports' ? 'active' : '' ?>"><a href="reports_violations.php">Reports</a></li>
      <li class="<?= $active === 'subscription' ? 'active' : '' ?>"><a href="subscription_management.php">Subscription Management</a></li>
      <li class="<?= $active === 'feedback' ? 'active' : '' ?>"><a href="feedback_reviews.php">Feedback & Reviews</a></li>
      <li class="<?= $active === 'settings' ? 'active' : '' ?>"><a href="system_settings.php">System Settings</a></li>
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
  // Defensive: ensure sidebar links always navigate even if another script prevents default.
  // This attaches a click handler that forces window.location to the anchor href.
  (function(){
    try {
      document.addEventListener('DOMContentLoaded', function(){
        var links = document.querySelectorAll('.sidebar nav a');
        links.forEach(function(a){
          // Skip links that look like in-page anchors or have explicit JS handlers
          if (!a.href || a.getAttribute('href').startsWith('#')) return;
          a.addEventListener('click', function(e){
            // If some other handler already called preventDefault and stopped propagation,
            // this still runs because it's added at the end; use location.assign to navigate.
            try {
              // Allow Ctrl/Cmd+click, middle-click to open in new tab
              if (e.ctrlKey || e.metaKey || e.button === 1) return;
            } catch(_) {}
            e.preventDefault();
            window.location.assign(a.href);
          });
        });
      });
    } catch (err) { console.warn('sidebar nav helper failed', err); }
  })();
</script>
<script>
  // Ensure Activity Logs links always resolve correctly from any page.
  (function(){
    try {
      var adminPath = '/admin_haustap/admin_haustap/activity_logs.php';
      document.addEventListener('DOMContentLoaded', function(){
        var anchors = document.querySelectorAll('a[href$="activity_logs.php"]');
        anchors.forEach(function(a){
          // If href already absolute to adminPath, skip
          try {
            var href = a.getAttribute('href');
            if (href === adminPath) return;
            // Update to absolute admin path so navigation works from any nested route
            a.setAttribute('href', adminPath);
          } catch(e){}
        });
      });
    } catch (err) { console.warn('activity logs link normalizer failed', err); }
  })();
</script>
<script>
  // Normalize Change Password links to be relative to the admin folder so they work
  // regardless of server base path. Also capture clicks to guarantee navigation.
  (function(){
    try {
      document.addEventListener('DOMContentLoaded', function(){
        var anchors = document.querySelectorAll('a[href*="change_password.php"]');
        anchors.forEach(function(a){
          try {
            // Use a relative link so the browser resolves it against the current admin folder
            a.setAttribute('href', 'change_password.php');
          } catch(e){}
        });
      });

      // Capture clicks and force navigation to the relative change_password.php when relevant
      document.addEventListener('click', function(e){
        try {
          var a = e.target.closest && e.target.closest('a');
          if (!a) return;
          var href = a.getAttribute('href') || '';
          if (href.indexOf('change_password.php') !== -1) {
            e.preventDefault();
            // Preserve modifier keys behavior
            if (e.ctrlKey || e.metaKey || e.button === 1) {
              window.open('change_password.php', '_blank');
              return;
            }
            window.location.assign('change_password.php');
          }
        } catch(_){}
      }, true);
    } catch (err) { console.warn('change password normalizer failed', err); }
  })();
</script>
<script>
  // Capture clicks on any Activity Logs anchors and force navigation to admin path.
  // This runs in the capture phase so it beats other handlers that may call preventDefault.
  (function(){
    try {
      var adminPath = '/admin_haustap/admin_haustap/activity_logs.php';
      document.addEventListener('click', function(e){
        try {
          var a = e.target.closest && e.target.closest('a');
          if (!a) return;
          var href = a.getAttribute('href');
          if (!href) return;
          if (href.indexOf('activity_logs.php') !== -1) {
            e.preventDefault();
            // Use location.replace to avoid adding an extra history entry if desired; using assign for normal behavior
            window.location.assign(adminPath);
          }
        } catch(_){}
      }, true); // capture
    } catch (err) { console.warn('activity logs click-capture failed', err); }
  })();
</script>
