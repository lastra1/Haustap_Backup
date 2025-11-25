<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/data_gateway.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$per = 10; $page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1); $offset = ($page - 1) * $per;
$clients = search_clients($q, null, $per, $offset);
$totalClients = count_clients($q);
$endIndex = min($page * $per, $totalClients);
$startIndex = $totalClients === 0 ? 0 : ($offset + 1);
$totals = client_status_counts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Clients | Admin Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="css/manage_client.css">
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

        <!-- Search + Filter -->
        <div class="search-filter">
            <div class="search-box">
                <i class="fa-solid fa-search"></i>
                <input type="text" id="clientSearch" placeholder="Search Client" value="<?= htmlspecialchars($q) ?>">
            </div>

<div class="filter-btn"><i class="fa-solid fa-sliders"></i> Filter</div>

            <!-- ✅ Filter Dropdown -->
            <div class="filter-dropdown" id="filterBox">
                <p>Filter by Status</p>
                <label><input type="checkbox" value="active"> Active</label>
                <label><input type="checkbox" value="inactive"> Inactive</label>
                <label><input type="checkbox" value="suspended"> Suspended</label>

                <button class="apply-btn">Apply</button>
            </div>
        </div>

        <!-- Table -->
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Date Joined</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                <?php if (!empty($clients)): ?>
                    <?php foreach ($clients as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['id']) ?></td>
                        <td><?= htmlspecialchars($c['name'] ?? $c['email'] ?? 'Unknown') ?></td>
                        <td><?= htmlspecialchars($c['date_joined'] ?? '') ?></td>
                        <td>
                          <?php $st = strtolower($c['status'] ?? 'active'); ?>
                          <span class="status <?= $st ?>"><?= ucfirst($st) ?></span>
                        </td>
                        <td>&gt;</td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center;color:#888;">No clients found</td></tr>
                <?php endif; ?>
                </tbody>
            </table>

            <div class="pagination">
                <span class="prev" data-page="<?= max(1, $page - 1) ?>">◄ Prev</span>
                <span>Showing <?= (int)$startIndex ?>–<?= (int)$endIndex ?> of <?= (int)$totalClients ?></span>
                <span class="next" data-page="<?= ($endIndex < $totalClients) ? ($page + 1) : $page ?>">Next ►</span>
            </div>
        </div>

        <div class="footer-count">
            Total Clients: <?= (int)$totals['total'] ?> | Active: <?= (int)$totals['active'] ?> | Inactive: <?= (int)$totals['inactive'] ?> | Suspend: <?= (int)$totals['suspend'] ?>
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

document.querySelector('.filter-btn').addEventListener('click', () => {
    const box = document.getElementById('filterBox');
    box.style.display = box.style.display === "block" ? "none" : "block";
});

document.addEventListener('click', (event) => {
    const filterBox = document.getElementById('filterBox');
    const filterBtn = document.querySelector('.filter-btn');
    if (!filterBox.contains(event.target) && !filterBtn.contains(event.target)) {
        filterBox.style.display = "none";
    }
});

// Clients filter: show rows matching selected statuses
(function(){
  const dropdown = document.getElementById('filterBox');
  if (!dropdown) return;
  const applyBtn = dropdown.querySelector('.apply-btn');
  const checkboxes = dropdown.querySelectorAll('input[type="checkbox"]');

  function applyClientFilter(){
    const selected = new Set(Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value));
    const rows = document.querySelectorAll('.table-wrapper tbody tr');
    rows.forEach(row => {
      const badge = row.querySelector('.status');
      let status = '';
      if (badge) {
        if (badge.classList.contains('active')) status = 'active';
        else if (badge.classList.contains('inactive')) status = 'inactive';
        else if (badge.classList.contains('suspended')) status = 'suspended';
      }
      row.style.display = (selected.size === 0 || selected.has(status)) ? '' : 'none';
    });
  }

  // Apply filter immediately and enforce single-select behavior
  // Clicking any status keeps only that status checked and filters rows
  checkboxes.forEach(cb => cb.addEventListener('change', (e) => {
    // Force the clicked checkbox to remain checked
    if (!cb.checked) cb.checked = true;
    // Uncheck all other statuses (single-select)
    checkboxes.forEach(other => { if (other !== cb) other.checked = false; });
    applyClientFilter();
  }));
  if (applyBtn) applyBtn.addEventListener('click', (e) => { e.preventDefault(); applyClientFilter(); });
})();

// Hook search box to navigate with query param without changing UI
(function(){
  const input = document.getElementById('clientSearch');
  if (!input) return;
  input.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      const q = input.value.trim();
      const url = new URL(window.location.href);
      if (q) url.searchParams.set('q', q); else url.searchParams.delete('q');
      url.searchParams.delete('page'); // reset pagination when searching
      window.location.href = url.toString();
    }
  });
})();

// Hook pagination spans to navigate pages
(function(){
  const prev = document.querySelector('.pagination .prev');
  const next = document.querySelector('.pagination .next');
  function goto(el){
    if (!el) return;
    el.addEventListener('click', (e) => {
      const page = el.getAttribute('data-page');
      const url = new URL(window.location.href);
      url.searchParams.set('page', page);
      window.location.href = url.toString();
    });
  }
  goto(prev); goto(next);
})();

// Make table rows clickable to view client profile without changing UI
(function(){
  const rows = document.querySelectorAll('.table-wrapper tbody tr');
  rows.forEach(row => {
    const idCell = row.querySelector('td');
    if (!idCell) return;
    const id = idCell.textContent.trim();
    row.style.cursor = 'pointer';
    row.addEventListener('click', () => {
      window.location.href = 'manage_client_profile.php?id=' + encodeURIComponent(id);
    });
  });
})();
</script>

</body>
</html>


