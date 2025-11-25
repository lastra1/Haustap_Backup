<?php require_once __DIR__ . '/includes/auth.php'; ?>
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
        <h3>Manage Provider</h3>
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

      <section class="content-area">
        <!-- Search and Filter -->
        <div class="search-filter">
          <div class="search-box">
            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            <input type="text" class="search-input" placeholder="Search provider (id, name, skills, rating, date, status)" aria-label="Search providers" />
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
                  <label><input type="checkbox" value="banned"> Banned</label>
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
              <tr>
                <td>1</td><td>Jenn Bornilla</td><td>Home Cleaning</td><td><span class="provider-rating" data-rating="4.5">4.5/5</span></td><td>January 7, 2024</td>
                <td><span class="status active">Active</span></td><td><span class="arrow">&gt;</span></td>
              </tr>
              <tr>
                <td>2</td><td>Pagod na</td><td>Plumbing</td><td><span class="provider-rating" data-rating="4.5">4.5/5</span></td><td>January 24, 2024</td>
                <td><span class="status inactive">Inactive</span></td><td><span class="arrow">&gt;</span></td>
              </tr>
              <tr>
                <td>3</td><td>Pagod na</td><td>Electrical</td><td><span class="provider-rating" data-rating="4.5">4.5/5</span></td><td>January 24, 2024</td>
                <td><span class="status suspend">Suspend</span></td><td><span class="arrow">&gt;</span></td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="pagination-row">
                <td colspan="7">
                  <div class="pagination">
                    <span>[ ◄ Prev ]</span>
                    <span>Showing 1–10 of 10</span>
                    <span>[ Next ► ]</span>
                  </div>
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </section>
    </main>
  </div>

    <!-- Provider detail modal removed: no pop-up modal for provider details -->

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

    // Providers filter: show rows matching selected statuses and update summary counts
    (function(){
      const checkboxes = filterDropdown.querySelectorAll('input[type="checkbox"]');
      const applyBtn = filterDropdown.querySelector('.apply-btn');
      const ratingFilter = document.getElementById('ratingFilter');
      const selectedRatingInput = document.getElementById('selectedRating');

      // Helper to combine filter & search visibility flags per-row.
      // Rows will have data-filter-hidden and data-search-hidden set to 'true' when hidden by that mechanism.
      window.updateProviderRowVisibility = function(row){
        try {
          const filterHidden = row.dataset.filterHidden === 'true';
          const searchHidden = row.dataset.searchHidden === 'true';
          row.style.display = (filterHidden || searchHidden) ? 'none' : '';
        } catch(err) {
          // defensive: if anything fails, fallback to showing the row
          row.style.display = '';
        }
      };

      function getSelectedRating(){
        const v = selectedRatingInput ? selectedRatingInput.value : '';
        return v ? parseInt(v,10) : null;
      }

      function applyProviderFilter(){
        const selectedStatuses = new Set(Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value));
        const selRating = getSelectedRating();
        console.log('[manage_provider] applyProviderFilter', { selectedStatuses: Array.from(selectedStatuses), selRating });
        const rows = document.querySelectorAll('.table-container tbody tr');
          rows.forEach(row => {
          const badge = row.querySelector('.status');
          let status = '';
          if (badge) {
            if (badge.classList.contains('active')) status = 'active';
            else if (badge.classList.contains('inactive')) status = 'inactive';
            else if (badge.classList.contains('suspend')) status = 'suspend';
            else if (badge.classList.contains('banned')) status = 'banned';
          }

          // rating: prefer a numeric data attribute from .provider-rating; fallback to parsing text like '4.5/5'.
          const ratingSpan = row.querySelector('.provider-rating');
          let ratingVal = null;
          if (ratingSpan) {
            const v = ratingSpan.getAttribute('data-rating');
            if (v) ratingVal = parseFloat(v);
          } else {
            const ratingCell = row.querySelector('td:nth-child(4)');
            if (ratingCell) {
              const txt = ratingCell.textContent || '';
              const m = txt.match(/([0-9]+(\.[0-9]+)?)/);
              if (m) ratingVal = parseFloat(m[1]);
            }
          }

          // small helper for debugging in DevTools when rating filter is active
          if (selRating !== null) {
            const idCell = row.querySelector('td:first-child');
            const providerId = idCell ? idCell.textContent.trim() : '';
            console.debug('[manage_provider] evaluating provider', { providerId, status, ratingVal, selRating });
          }

          let visible = true;
          if (selectedStatuses.size > 0 && !selectedStatuses.has(status)) visible = false;
          if (visible && selRating !== null && ratingVal !== null) {
            // Minimum-rating behavior: selecting N shows providers with rating >= N
            // Example: selecting 4 will include 4.0 and 4.5 entries.
            const lower = Number(selRating);
            if (!(ratingVal >= lower)) visible = false;
          } else if (visible && selRating !== null && ratingVal === null) {
            // if user filtered by rating but row has no rating info, hide it
            visible = false;
          }

          // mark row as hidden/visible due to provider filters
          row.dataset.filterHidden = visible ? '' : 'true';
          // update final visibility combining search & filter
          if (typeof window.updateProviderRowVisibility === 'function') window.updateProviderRowVisibility(row);
        });
        updateSummaryCounts();
      }

      // Update the summary counts based on currently visible rows
      function updateSummaryCounts(){
        const rows = Array.from(document.querySelectorAll('.table-container tbody tr'));
        let total = 0, active = 0, inactive = 0, suspend = 0, banned = 0;
        rows.forEach(row => {
          if (row.style.display === 'none') return; // only count visible rows
          total += 1;
          const badge = row.querySelector('.status');
          if (badge) {
            if (badge.classList.contains('active')) active += 1;
            else if (badge.classList.contains('inactive')) inactive += 1;
            else if (badge.classList.contains('suspend')) suspend += 1;
            else if (badge.classList.contains('banned')) banned += 1;
          }
        });

        const elTotal = document.getElementById('totalClients');
        const elActive = document.getElementById('totalActive');
        const elInactive = document.getElementById('totalInactive');
        const elSuspend = document.getElementById('totalSuspend');
        const elBanned = document.getElementById('totalBanned');

        if (elTotal) elTotal.textContent = `Total Clients: ${total}`;
        if (elActive) elActive.textContent = `Active: ${active}`;
        if (elInactive) elInactive.textContent = `Inactive: ${inactive}`;
        if (elSuspend) elSuspend.textContent = `Suspend: ${suspend}`;
        if (elBanned) elBanned.textContent = `Banned: ${banned}`;
  }

  // expose the summary updater globally so other scripts (search) can call it
  window.updateProviderSummaryCounts = updateSummaryCounts;

      // Wire checkbox changes (allow multi-select)
      checkboxes.forEach(cb => cb.addEventListener('change', () => applyProviderFilter()));

      // Rating stars handling with accessibility (keyboard + ARIA)
      if (ratingFilter) {
        const stars = Array.from(ratingFilter.querySelectorAll('.star'));

        // compute whether stars are in ascending order (1..5) or descending (5..1)
        const starRatings = stars.map(s => parseInt(s.getAttribute('data-rating'), 10));
        const ascending = starRatings.length < 2 ? true : (starRatings[0] < starRatings[1]);
        console.debug('[manage_provider] starRatings', { starRatings, ascending });

        function setSelectedRatingUI(value) {
          // Mark stars selected by comparing their numeric data-rating to the selected value.
          // If stars are in ascending order (1..5) select those with rating <= value.
          // If descending (5..1) select those with rating >= value.
          if (value === null) {
            stars.forEach(s => {
              s.classList.remove('selected');
              s.setAttribute('aria-checked', 'false');
            });
            return;
          }

          const numVal = Number(value);
          stars.forEach((s) => {
            const r = parseInt(s.getAttribute('data-rating'), 10);
            const shouldSelect = ascending ? (r <= numVal) : (r >= numVal);
            if (shouldSelect) s.classList.add('selected'); else s.classList.remove('selected');
            // aria-checked true only for the exact selected rating value
            s.setAttribute('aria-checked', (r === numVal) ? 'true' : 'false');
          });
        }

        // click handler
        stars.forEach((st, idx) => {
          st.addEventListener('click', (e) => {
            const rating = parseInt(st.getAttribute('data-rating'),10);
            const current = getSelectedRating();
            const newSel = (current === rating) ? null : rating;
            if (selectedRatingInput) {
              selectedRatingInput.value = newSel ? String(newSel) : '';
              console.debug('[manage_provider] star click set selectedRatingInput', selectedRatingInput.value);
            }
            setSelectedRatingUI(newSel);
            console.debug('[manage_provider] star clicked', { clickedRating: rating, newSel });
            applyProviderFilter();
          });

          // keyboard support: Enter/Space selects, ArrowLeft/Right moves focus
          st.addEventListener('keydown', (e) => {
            const key = e.key;
            // Accept both ' ' and older 'Spacebar'
            if (key === 'Enter' || key === ' ' || key === 'Spacebar') {
              e.preventDefault();
              st.click();
              return;
            }
            // Move focus right -> next element in array (to the right in DOM)
            if (key === 'ArrowRight' || key === 'ArrowDown') {
              e.preventDefault();
              const next = stars[idx + 1] || stars[0];
              next.focus();
              return;
            }
            // Move focus left -> previous element
            if (key === 'ArrowLeft' || key === 'ArrowUp') {
              e.preventDefault();
              const prev = stars[idx - 1] || stars[stars.length - 1];
              prev.focus();
              return;
            }
            if (key === 'Home') { e.preventDefault(); stars[0].focus(); }
            if (key === 'End') { e.preventDefault(); stars[stars.length-1].focus(); }
          });
        });

        // Initialize visual state from hidden input
        const initVal = getSelectedRating();
        setSelectedRatingUI(initVal);
      }

      if (applyBtn) {
        applyBtn.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          try {
            applyProviderFilter();
            // close the dropdown and restore focus for accessibility
            if (filterDropdown) filterDropdown.classList.remove('show');
            if (filterBtn) filterBtn.focus();
            // reflect ARIA state if needed
            if (filterBtn && typeof filterBtn.setAttribute === 'function') filterBtn.setAttribute('aria-expanded', 'false');
          } catch(err) {
            console.error('Apply filter failed', err);
          }
        });

        // Allow pressing Enter while Apply has focus to trigger the same behavior
        applyBtn.addEventListener('keydown', (e) => {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            applyBtn.click();
          }
        });
      }

      // Initialize counts on load
      updateSummaryCounts();
    })();

    // Arrow click: navigate to provider profile page with id
    (function(){
      const tbody = document.querySelector('.table-container tbody');
      if (!tbody) return;
      tbody.addEventListener('click', function(e){
        // Only respond when the arrow span is clicked (or a child of it)
        const arrow = e.target.closest('span.arrow');
        if (!arrow) return;
        const tr = arrow.closest('tr');
        if (!tr) return;
        const idCell = tr.querySelector('td:first-child');
        const id = idCell ? idCell.textContent.trim() : '';
        if (!id) return;
        // Navigate to profile page (pass id as query param)
        const url = 'manage_provider_profile.php?id=' + encodeURIComponent(id);
        window.location.href = url;
      });
    })();

    // Arrow click handler that opened provider details modal removed (no popups)

    // ----------------------
    // Search input: live filter for providers
    // ----------------------
    (function(){
      const input = document.querySelector('.search-input');
      const tbody = document.querySelector('.table-container tbody');
      if (!input || !tbody) return;

      const rows = Array.from(tbody.querySelectorAll('tr'));
      function norm(s){ return (s||'').toString().replace(/\s+/g,' ').trim().toLowerCase(); }

      function applySearch(query){
        const q = norm(query);
        rows.forEach(row => {
          // If a row is already hidden by provider filters, preserve that (we set dataset.filterHidden there).
          const id = norm(row.querySelector('td:first-child')?.textContent);
          const name = norm(row.querySelector('td:nth-child(2)')?.textContent);
          const skills = norm(row.querySelector('td:nth-child(3)')?.textContent);
          const rating = norm(row.querySelector('.provider-rating')?.getAttribute('data-rating') || row.querySelector('td:nth-child(4)')?.textContent);
          const date = norm(row.querySelector('td:nth-child(5)')?.textContent);
          const status = norm(row.querySelector('.status')?.textContent);
          const combined = [id,name,skills,rating,date,status].join(' ');

          // if there's no query, clear search-hidden flag
          if (!q) {
            row.dataset.searchHidden = '';
            if (typeof window.updateProviderRowVisibility === 'function') window.updateProviderRowVisibility(row);
            return;
          }

          const matches = combined.indexOf(q) !== -1;
          row.dataset.searchHidden = matches ? '' : 'true';
          if (typeof window.updateProviderRowVisibility === 'function') window.updateProviderRowVisibility(row);
        });

        // update counts
        if (typeof window.updateProviderSummaryCounts === 'function') window.updateProviderSummaryCounts();
      }

      let t = null;
      input.addEventListener('input', function(e){ clearTimeout(t); t = setTimeout(() => applySearch(e.target.value), 160); });
      input.addEventListener('keydown', function(e){ if (e.key === 'Escape'){ input.value = ''; applySearch(''); } });
    })();

    // Provider modal handlers removed (no popups to open/close)
  </script>
</body>
</html>


