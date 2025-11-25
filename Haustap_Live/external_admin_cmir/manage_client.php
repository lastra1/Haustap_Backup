<?php require_once __DIR__ . '/includes/auth.php'; ?>
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

        <!-- Search + Filter -->
        <div class="search-filter">
            <div class="search-box">
              <i class="fa-solid fa-search" aria-hidden="true"></i>
              <input type="text" class="search-input" placeholder="Search Name" aria-label="Search Name">
            </div>

<button class="filter-btn"><i class="fa-solid fa-sliders"></i> Filter</button>

            <!-- ✅ Filter Dropdown -->
            <div class="filter-dropdown dropdown-content" id="filterBox">
                <p>Filter by Status</p>
                <label><input type="checkbox" value="active"> Active</label>
                <label><input type="checkbox" value="inactive"> Inactive</label>
                <label><input type="checkbox" value="suspended"> Suspended</label>
                <label><input type="checkbox" value="banned"> Banned</label>
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
                    <tr>
                        <td>1</td><td>Jenn Bornilla</td><td>January 7, 2024</td>
                        <td><span class="status active">Active</span></td><td>></td>
                    </tr>
                    <tr>
                        <td>2</td><td>Pagod na</td><td>January 24, 2024</td>
                        <td><span class="status inactive">Inactive</span></td><td>></td>
                    </tr>
                    <tr>
                        <td>3</td><td>Pagod na</td><td>January 24, 2024</td>
                        <td><span class="status suspended">Suspend</span></td><td>></td>
                    </tr>
                    <tr>
                        <td>4</td><td>Jenn Bornilla</td><td>January 7, 2024</td>
                        <td><span class="status active">Active</span></td><td>></td>
                    </tr>
                    <tr>
                        <td>5</td><td>Pagod na</td><td>January 24, 2024</td>
                        <td><span class="status inactive">Inactive</span></td><td>></td>
                    </tr>
                </tbody>
            </table>

            <div class="pagination">
                <span>◄ Prev</span>
                <span>Showing 1–10 of 120</span>
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

document.querySelector('.filter-btn').addEventListener('click', (e) => {
    e.stopPropagation();
    const box = document.getElementById('filterBox');
    if (box) {
      box.classList.toggle('show');
      const btn = document.querySelector('.filter-btn');
      if (btn) btn.setAttribute('aria-expanded', box.classList.contains('show'));
    }
});

document.addEventListener('click', (event) => {
    const filterBox = document.getElementById('filterBox');
    const filterBtn = document.querySelector('.filter-btn');
    if (filterBox && !filterBox.contains(event.target) && !filterBtn.contains(event.target)) {
        filterBox.classList.remove('show');
        if (filterBtn) filterBtn.setAttribute('aria-expanded', 'false');
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

  // Let users select one or more statuses, then click Apply to filter.
  // (checkbox changes no longer auto-apply)
  checkboxes.forEach(cb => cb.addEventListener('change', () => {
    // noop: wait for user to press Apply to run the filter
  }));

  if (applyBtn) applyBtn.addEventListener('click', (e) => {
    e.preventDefault();
    applyClientFilter();
    // close dropdown after applying
    const box = document.getElementById('filterBox');
    if (box) box.classList.remove('show');
    const btn = document.querySelector('.filter-btn');
    if (btn) btn.setAttribute('aria-expanded', 'false');
    // update counts if available
    if (window.updateClientCounts) window.updateClientCounts();
  });
})();

// Update footer counts to match current visible list
(function(){
  const totalEl = document.getElementById('countTotal');
  const activeEl = document.getElementById('countActive');
  const inactiveEl = document.getElementById('countInactive');
  const suspendedEl = document.getElementById('countSuspended');

  function updateCounts(){
    const rows = Array.from(document.querySelectorAll('.table-wrapper tbody tr'));
    const visible = rows.filter(r => r.style.display !== 'none');
    const counts = { total: visible.length, active:0, inactive:0, suspended:0 };
    visible.forEach(r => {
      const badge = r.querySelector('.status');
      if (!badge) return;
      if (badge.classList.contains('active')) counts.active++;
      else if (badge.classList.contains('inactive')) counts.inactive++;
      else if (badge.classList.contains('suspended')) counts.suspended++;
    });
    if (totalEl) totalEl.textContent = 'Total Clients: ' + counts.total;
    if (activeEl) activeEl.textContent = 'Active: ' + counts.active;
    if (inactiveEl) inactiveEl.textContent = 'Inactive: ' + counts.inactive;
    if (suspendedEl) suspendedEl.textContent = 'Suspend: ' + counts.suspended;
  }

  // expose for other scripts to call if needed
  window.updateClientCounts = updateCounts;

  // run on load
  setTimeout(updateCounts, 30);

  // re-run when filters change: observe mutations of tbody or listen for clicks on apply/checkboxes
  const observer = new MutationObserver(() => updateCounts());
  const tbody = document.querySelector('.table-wrapper tbody');
  if (tbody) observer.observe(tbody, { attributes: true, childList: true, subtree: true, characterData: true });

  // also update when filter controls are used
  const filterBox = document.getElementById('filterBox');
  if (filterBox){
    filterBox.addEventListener('change', updateCounts);
    const applyBtn = filterBox.querySelector('.apply-btn');
    if (applyBtn) applyBtn.addEventListener('click', updateCounts);
  }
})();

// Arrow cell: navigate to client profile when last cell (>) is clicked
(function(){
  const tbody = document.querySelector('.table-wrapper tbody');
  if (!tbody) return;
  tbody.addEventListener('click', function(e){
    // allow clicking the text '>' or the last td in a row
    const td = e.target.closest('td');
    if (!td) return;
    const tr = td.closest('tr');
    if (!tr) return;
    const cells = Array.from(tr.children);
    const isArrowCell = (td === cells[cells.length - 1]) || td.classList.contains('arrow') || (td.textContent && td.textContent.trim() === '>');
    if (!isArrowCell) return;
    const idCell = tr.querySelector('td:first-child');
    const id = idCell ? idCell.textContent.trim() : '';
    const badge = tr.querySelector('.status');
    let status = '';
    if (badge) {
      if (badge.classList.contains('active')) status = 'active';
      else if (badge.classList.contains('inactive')) status = 'inactive';
      else if (badge.classList.contains('suspended')) status = 'suspended';
    }
    const url = `manage_client_profile.php?id=${encodeURIComponent(id)}&status=${encodeURIComponent(status)}`;
    try { window.location.href = url; } catch(err) { console.error('Navigation failed', err); }
  });
})();

// ----------------------
// Search input: live filter for clients
// ----------------------
(function(){
  const input = document.querySelector('.search-input');
  const tbody = document.querySelector('.table-wrapper tbody');
  if (!input || !tbody) return;

  const rows = Array.from(tbody.querySelectorAll('tr'));

  function normalize(s){ return (s||'').toString().replace(/\s+/g,' ').trim().toLowerCase(); }

  function applySearch(q){
    const text = normalize(q);
    // If empty query, show all rows
    if (!text){
      rows.forEach(r => r.style.display = '');
      window.updateClientCounts && window.updateClientCounts();
      return;
    }

    // Match only against the first column (ID / Booking ID)
    rows.forEach(r => {
      const id = normalize(r.querySelector('td:first-child')?.textContent);
      r.style.display = id.indexOf(text) !== -1 ? '' : 'none';
    });
    window.updateClientCounts && window.updateClientCounts();
  }

  let timer = null;
  input.addEventListener('input', function(e){
    clearTimeout(timer);
    timer = setTimeout(() => applySearch(e.target.value), 150);
  });

  // support clearing with Esc
  input.addEventListener('keydown', function(e){ if (e.key === 'Escape'){ input.value = ''; applySearch(''); } });
})();
</script>

</body>
</html>


