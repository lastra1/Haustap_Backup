<?php require_once __DIR__ . '/includes/auth.php';

// Load provider data from storage by id (basic server-side connect)
$provider = null;
$providerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$storePath = realpath(__DIR__ . '/../../storage/data/providers.json');
if ($storePath && is_file($storePath)) {
  $raw = @file_get_contents($storePath);
  $items = json_decode($raw ?: '[]', true);
  if (is_array($items)) {
    foreach ($items as $it) {
      if (isset($it['id']) && (int)$it['id'] === $providerId) { $provider = $it; break; }
    }
  }
}

// Fallback default when not found
if (!$provider) {
  $provider = [
    'id' => $providerId ?: 0,
    'name' => 'Unknown',
    'skills' => '—',
    'rating' => '—',
    'hired_at' => '',
    'status' => isset($_GET['status']) ? $_GET['status'] : 'active',
    'email' => '',
    'phone' => ''
  ];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Clients | Profile</title>
<link rel="stylesheet" href="css/manage_provider_profile.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="js/lazy-images.js" defer></script></head>
<body>
<div class="dashboard-container">
    <!-- Sidebar -->
    <?php $active = 'providers'; include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Topbar -->
      <header class="topbar">
        <h3>Manage Providers </h3>
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
        <?php $pid = (int)($provider['id'] ?? 0); $pstatus = urlencode($provider['status'] ?? ''); ?>
        <button class="active" data-target="manage_provider_profile.php?id=<?php echo $pid; ?>&status=<?php echo $pstatus; ?>">Profile</button>
        <button data-target="manage_provider_jobs.php?id=<?php echo $pid; ?>&status=<?php echo $pstatus; ?>">Jobs</button>
        <button data-target="manage_provider_activity.php?id=<?php echo $pid; ?>&status=<?php echo $pstatus; ?>">Activity</button>
        <button data-target="manage_provider_voucher.php?id=<?php echo $pid; ?>&status=<?php echo $pstatus; ?>">Voucher</button>
        <button data-target="manage_provider_subscription.php?id=<?php echo $pid; ?>&status=<?php echo $pstatus; ?>">Subscription</button>
      </div>

    <!-- Profile Content -->
    <div class="profile-box">

      <div class="left-info">
        <div class="profile-img">
          <i class="fa-solid fa-user"></i>
        </div>
        <p class="register-date">Registered on:<br><strong><?php echo !empty($provider['hired_at']) ? htmlspecialchars(date('F d, Y', strtotime($provider['hired_at']))) : '—'; ?></strong></p>
      </div>

      <div class="right-info">
        <p><strong>Status:</strong> <?php echo htmlspecialchars(ucfirst($provider['status'] ?? '')); ?></p>
        <p><strong>ID:</strong> <?php echo htmlspecialchars($provider['id']); ?></p>
        <p><strong>Full name:</strong> <?php echo htmlspecialchars($provider['name']); ?></p>
        <p><strong>Skills:</strong> <?php echo htmlspecialchars($provider['skills'] ?? '—'); ?></p>
        <p><strong>Rating:</strong> <?php echo htmlspecialchars($provider['rating'] ?? '—'); ?></p>
        <p><strong>Email:</strong> <?php if (!empty($provider['email'])): ?><a href="mailto:<?php echo htmlspecialchars($provider['email']); ?>"><?php echo htmlspecialchars($provider['email']); ?></a><?php else: ?>—<?php endif; ?></p>
        <p><strong>Mobile number:</strong> <?php echo htmlspecialchars($provider['phone'] ?? '—'); ?></p>
        <p><strong>Date of Birth:</strong> —</p>
        <p><strong>Gender:</strong> —</p>
        <p><strong>Address:</strong> —</p>
      </div>

      

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
    (function(){
      const dropdownBtn = document.getElementById("userDropdownBtn");
      const dropdown = document.getElementById("userDropdown");
      if (!dropdownBtn || !dropdown) return;
      dropdownBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        dropdown.classList.toggle("show");
      });
      window.addEventListener("click", (e) => {
        if (!dropdown.contains(e.target)) dropdown.classList.remove("show");
      });
    })();
  </script>
  <script>
    // Tabs navigation: navigate to pages specified in data-target (includes id & status)
    (function(){
      const tabsContainer = document.querySelector('.tabs');
      if (!tabsContainer) return;
      const buttons = Array.from(tabsContainer.querySelectorAll('button'));
      buttons.forEach(btn => {
        btn.addEventListener('click', function(e){
          // If a data-target is present, navigate to it (full-page navigation)
          const target = btn.getAttribute('data-target');
          if (target) {
            // keep normal navigation behavior for same-page Profile target
            try { window.location.href = target; } catch (err) { console.error('Navigation failed', err); }
            return;
          }
          // Otherwise, toggle active state for client-side-only tabs
          buttons.forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
        });
      });
    })();
  </script>
  <script>
    // Account Actions: Suspend, Banned, Delete
    (function(){
      const providerId = <?php echo json_encode($provider['id'] ?? 0); ?>;
      if (!providerId) return;

      const suspendBtn = document.querySelector('.btn.suspend');
      const bannedBtn = document.querySelector('.btn.banned');
      const deleteBtn = document.querySelector('.btn.delete');

      function handleAction(action, buttonEl) {
        const confirmed = confirm(`Are you sure you want to ${action} this provider?`);
        if (!confirmed) return;

        const formData = new FormData();
        formData.append('id', providerId);
        formData.append('action', action);

        const apiUrl = 'api/update_provider_status.php';

        fetch(apiUrl, {
          method: 'POST',
          body: formData,
          credentials: 'same-origin'
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert(`Provider ${action === 'delete' ? 'deleted' : 'status updated to ' + action} successfully!`);
            if (action === 'delete') {
              // Redirect back to provider list
              window.location.href = 'manage_provider.php';
            } else {
              // Reload to show updated status
              window.location.reload();
            }
          } else {
            alert('Error: ' + (data.error || 'Unknown error'));
          }
        })
        .catch(err => {
          console.error('Action failed:', err);
          alert('Failed to perform action. Check console for details.');
        });
      }

      if (suspendBtn) {
        suspendBtn.addEventListener('click', () => handleAction('suspended', suspendBtn));
      }
      if (bannedBtn) {
        bannedBtn.addEventListener('click', () => handleAction('banned', bannedBtn));
      }
      if (deleteBtn) {
        deleteBtn.addEventListener('click', () => handleAction('delete', deleteBtn));
      }
    })();
  </script>

</body>
</html>



