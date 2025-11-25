<?php require_once __DIR__ . '/includes/auth.php';

// Load client data from storage by id (basic server-side connect)
$client = null;
$clientId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$storePath = realpath(__DIR__ . '/../../storage/data/clients.json');
if ($storePath && is_file($storePath)) {
  $raw = @file_get_contents($storePath);
  $items = json_decode($raw ?: '[]', true);
  if (is_array($items)) {
    foreach ($items as $it) {
      if (isset($it['id']) && (int)$it['id'] === $clientId) { $client = $it; break; }
    }
  }
}

// Fallback default when not found
if (!$client) {
  $client = [
    'id' => $clientId ?: 0,
    'name' => 'Unknown',
    'email' => '',
    'phone' => '',
    'joined_at' => '',
    'status' => isset($_GET['status']) ? $_GET['status'] : 'active'
  ];
}

?>
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
        <h3>Manage Clients</h3>
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
        <?php $cid = (int)($client['id'] ?? 0); $cstatus = urlencode($client['status'] ?? ''); ?>
        <button class="active" data-target="manage_client_profile.php?id=<?php echo $cid; ?>&status=<?php echo $cstatus; ?>">Profile</button>
        <button data-target="manage_client_booking.php?id=<?php echo $cid; ?>&status=<?php echo $cstatus; ?>">Bookings</button>
        <button data-target="manage_client_activity.php?id=<?php echo $cid; ?>&status=<?php echo $cstatus; ?>">Activity</button>
        <button data-target="manage_client_voucher.php?id=<?php echo $cid; ?>&status=<?php echo $cstatus; ?>">Voucher</button>
      </div>

        <!-- Profile Content -->
        <div class="profile-box">

      <div class="left-info">
        <div class="profile-img">
          <i class="fa-solid fa-user"></i>
        </div>
        <p class="register-date">Registered on:<br><strong><?php echo !empty($client['joined_at']) ? htmlspecialchars(date('F d, Y', strtotime($client['joined_at']))) : '—'; ?></strong></p>
      </div>

      <div class="right-info">
        <p id="clientStatus"><strong>Status:</strong> <?php echo htmlspecialchars(ucfirst($client['status'] ?? '')); ?></p>
        <p><strong>ID:</strong> <?php echo htmlspecialchars($client['id']); ?></p>
        <p><strong>Full name:</strong> <?php echo htmlspecialchars($client['name']); ?></p>
        <p><strong>Email:</strong> <?php if (!empty($client['email'])): ?><a href="mailto:<?php echo htmlspecialchars($client['email']); ?>"><?php echo htmlspecialchars($client['email']); ?></a><?php else: ?>—<?php endif; ?></p>
        <p><strong>Mobile number:</strong> <?php echo htmlspecialchars($client['phone'] ?? '—'); ?></p>
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
      // User dropdown (defensive)
      const dropdownBtn = document.getElementById("userDropdownBtn");
      const dropdown = document.getElementById("userDropdown");
      if (dropdownBtn && dropdown) {
        dropdownBtn.addEventListener("click", (e) => {
          e.stopPropagation();
          dropdown.classList.toggle("show");
        });
        window.addEventListener("click", (e) => {
          if (!dropdown.contains(e.target)) dropdown.classList.remove("show");
        });
      }

      // Tabs navigation: navigate to pages specified in data-target (includes id & status)
      const tabsContainer = document.querySelector('.tabs');
      if (tabsContainer) {
        const buttons = Array.from(tabsContainer.querySelectorAll('button'));
        buttons.forEach(btn => {
          btn.addEventListener('click', function(e){
            const target = btn.getAttribute('data-target');
            if (target) {
              try { window.location.href = target; } catch (err) { console.error('Navigation failed', err); }
              return;
            }
            // Toggle active state for client-side-only tabs
            buttons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
          });
        });
      }

      // Account actions: suspend / banned / delete
      (function(){
        const id = '<?php echo (int)($client['id'] ?? 0); ?>';
        const suspendBtn = document.querySelector('.btn.suspend');
        const bannedBtn = document.querySelector('.btn.banned');
        const deleteBtn = document.querySelector('.btn.delete');
        const statusEl = document.getElementById('clientStatus');

        function doAction(action){
          if (!id) { alert('Invalid client id'); return; }
          const ok = confirm('Are you sure you want to ' + action + ' this account?');
          if (!ok) return;
          // disable buttons while request runs
          [suspendBtn, bannedBtn, deleteBtn].forEach(b => b && (b.disabled = true));

          fetch('api/update_client_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + encodeURIComponent(id) + '&action=' + encodeURIComponent(action)
          }).then(r => r.json()).then(data => {
            if (!data || !data.success) throw new Error((data && data.error) ? data.error : 'Server error');
            if (action === 'delete') {
              // navigate back to client list
              window.location.href = 'manage_client.php';
              return;
            }
            // update status in UI
            if (statusEl) statusEl.innerHTML = '<strong>Status:</strong> ' + (data.status ? (data.status.charAt(0).toUpperCase()+data.status.slice(1)) : '');
            alert('Action completed: ' + action);
          }).catch(err => {
            console.error(err);
            alert('Failed to ' + action + ': ' + (err.message || err));
          }).finally(() => {
            [suspendBtn, bannedBtn, deleteBtn].forEach(b => b && (b.disabled = false));
          });
        }

        suspendBtn && suspendBtn.addEventListener('click', () => doAction('suspended'));
        bannedBtn && bannedBtn.addEventListener('click', () => doAction('banned'));
        deleteBtn && deleteBtn.addEventListener('click', () => doAction('delete'));
      })();
    })();
  </script>

</body>
</html>


