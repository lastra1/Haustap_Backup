<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/data_gateway.php';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$per = 10; $page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1); $offset = ($page - 1) * $per;
$providers = search_providers($q, null, $per, $offset);
$totalProviders = count_providers($q);
$endIndex = min($page * $per, $totalProviders);
$startIndex = $totalProviders === 0 ? 0 : ($offset + 1);
$totals = provider_status_counts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Providers</title>
  <link rel="stylesheet" href="css/manage_provider.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<script src="js/lazy-images.js" defer></script></head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <?php $active = 'providers'; include 'includes/sidebar.php'; ?>

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

      <section class="content-area">
        <!-- Search and Filter -->
        <div class="search-filter">
          <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="providerSearch" placeholder="Search Provider" value="<?= htmlspecialchars($q) ?>" />
          </div>

          <!-- Filter Dropdown -->
          <div class="filter-dropdown">
            <button class="filter-btn" id="filterBtn"><i class="fa-solid fa-sliders"></i> Filter</button>

            <div class="dropdown-content" id="filterDropdown">
              <div class="filter-section">
                <label>Filter by Status</label>
                <div class="checkbox-group">
                  <label><input type="checkbox" value="active" checked> Active</label>
                  <label><input type="checkbox" value="inactive" checked> Inactive</label>
                  <label><input type="checkbox" value="suspend" checked> Suspended</label>
                </div>
              </div>

              <button class="apply-btn">Apply</button>
            </div>
          </div>
        </div>

        <!-- Table -->
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Skills</th>
                <th>Rating</th>
                <th>Date Hired</th>
                <th>Status</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($providers)): ?>
                <?php foreach ($providers as $p): ?>
                <tr>
                  <td><?= htmlspecialchars($p['id']) ?></td>
                  <td><?= htmlspecialchars($p['name'] ?? 'Unknown') ?></td>
                  <td><?= htmlspecialchars($p['skills'] ?? '—') ?></td>
                  <td><?= htmlspecialchars($p['rating_fmt'] ?? '—') ?></td>
                  <td><?= htmlspecialchars($p['date_hired'] ?? '') ?></td>
                  <td>
                    <?php $st = strtolower($p['status'] ?? 'active'); ?>
                    <span class="status <?= $st ?>"><?= ucfirst($st) ?></span>
                  </td>
                  <td><span class="arrow">&gt;</span></td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="7" style="text-align:center;color:#888;">No providers found</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination and Summary -->
        <div class="table-footer">
          <div class="pagination">
            <span class="prev" data-page="<?= max(1, $page - 1) ?>">[ ◄ Prev ]</span>
            <span>Showing <?= (int)$startIndex ?>–<?= (int)$endIndex ?> of <?= (int)$totalProviders ?></span>
            <span class="next" data-page="<?= ($endIndex < $totalProviders) ? ($page + 1) : $page ?>">[ Next ► ]</span>
          </div>
          <div class="summary">
            <span>Total Providers: <?= (int)$totals['total'] ?></span>
            <span>Active: <?= (int)$totals['active'] ?></span>
            <span>Inactive: <?= (int)$totals['inactive'] ?></span>
            <span>Suspend: <?= (int)$totals['suspend'] ?></span>
          </div>
        </div>
      </section>
    </main>
  </div>

  <script>
    // User dropdown
    const dropdownBtn = document.getElementById("userDropdownBtn");
    const dropdown = document.getElementById("userDropdown");
    dropdownBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      dropdown.classList.toggle("show");
    });
    window.addEventListener("click", (e) => {
      if (!dropdown.contains(e.target)) dropdown.classList.remove("show");
    });

    // Filter dropdown
    const filterBtn = document.getElementById("filterBtn");
    const filterDropdown = document.getElementById("filterDropdown");
    filterBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      filterDropdown.classList.toggle("show");
    });
    window.addEventListener("click", (e) => {
      if (!filterDropdown.contains(e.target)) filterDropdown.classList.remove("show");
    });

    // Providers filter: show rows matching selected statuses
    (function(){
      const checkboxes = filterDropdown.querySelectorAll('input[type="checkbox"]');
      const applyBtn = filterDropdown.querySelector('.apply-btn');

      function applyProviderFilter(){
        const selected = new Set(Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value));
        const rows = document.querySelectorAll('.table-container tbody tr');
        rows.forEach(row => {
          const badge = row.querySelector('.status');
          let status = '';
          if (badge) {
            if (badge.classList.contains('active')) status = 'active';
            else if (badge.classList.contains('inactive')) status = 'inactive';
            else if (badge.classList.contains('suspend')) status = 'suspend';
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
        applyProviderFilter();
      }));
      if (applyBtn) applyBtn.addEventListener('click', (e) => { e.preventDefault(); applyProviderFilter(); });
    })();

    // Hook search box to navigate with query param
    (function(){
      const input = document.getElementById('providerSearch');
      if (!input) return;
      input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          const q = input.value.trim();
          const url = new URL(window.location.href);
          if (q) url.searchParams.set('q', q); else url.searchParams.delete('q');
          url.searchParams.delete('page');
          window.location.href = url.toString();
        }
      });
    })();

    // Hook pagination spans
    (function(){
      const prev = document.querySelector('.pagination .prev');
      const next = document.querySelector('.pagination .next');
      function goto(el){
        if (!el) return;
        el.addEventListener('click', () => {
          const page = el.getAttribute('data-page');
          const url = new URL(window.location.href);
          url.searchParams.set('page', page);
          window.location.href = url.toString();
        });
      }
      goto(prev); goto(next);
    })();

    // Row navigation to provider profile without changing UI
    (function(){
      const rows = document.querySelectorAll('.table-container tbody tr');
      rows.forEach(row => {
        const idCell = row.querySelector('td');
        if (!idCell) return;
        const id = idCell.textContent.trim();
        row.style.cursor = 'pointer';
        row.addEventListener('click', () => {
          window.location.href = 'manage_provider_profile.php?id=' + encodeURIComponent(id);
        });
      });
    })();
  </script>
</body>
</html>


