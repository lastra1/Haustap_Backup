<?php require_once __DIR__ . '/includes/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Feedback & Reviews | Admin Dashboard</title>
  <link rel="stylesheet" href="css/feedback_reviews.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="js/lazy-images.js" defer></script>
<script src="js/filter-utils.js" defer></script></head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <?php $active = 'feedback'; include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
      <header class="topbar">
        <h3>Feedback & Reviews</h3>
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
        <button class="tab active" data-target="feedback_reviews.php">Service Provider</button>
        <button class="tab" data-target="feedback_reviews_client.php">Client</button>
      </div>

      <!-- Search and Filter -->
      <div class="search-filter">
        <input type="text" placeholder="Search">

        <div class="filter-dropdown">
<button class="filter-btn"><i class="fa-solid fa-sliders"></i> Filter ▼</button>
          <div class="dropdown-content">
            <!-- Filter by Date -->
            <div class="filter-date">
              <p class="filter-title">Filter by Date</p>
              <div class="date-row">
                <label for="from-date">From:</label>
                <input type="date" id="from-date" value="2025-10-01">
              </div>
              <div class="date-row">
                <label for="to-date">Return:</label>
                <input type="date" id="to-date" value="2025-10-31">
              </div>
            </div>

            <button class="apply-btn">Apply</button>
          </div>
        </div>
      </div>

      <!-- Reviews Table -->
      <div class="table-container">
        <table class="reviews-table">
          <thead>
            <tr>
              <th>Id</th>
              <th>Provider</th>
              <th>Service</th>
              <th>Rating</th>
              <th>Date</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>Juan Dela Cruz</td>
              <td>Plumbing</td>
              <td class="stars">★★★★★</td>
              <td>2025-06-07</td>
              <td><span class="status reviewed">Reviewed</span></td>
              <td><span class="open-popup">></span></td>
            </tr>
            <tr>
              <td>2</td>
              <td>Ramon Ang</td>
              <td>Cleaning</td>
              <td class="stars">★★★★★</td>
              <td>2025-06-07</td>
              <td><span class="status pending">Pending</span></td>
              <td><span class="open-popup">></span></td>
            </tr>
            <tr>
              <td>3</td>
              <td>Juana Ramos</td>
              <td>Gardening</td>
              <td class="stars">★★★★★</td>
              <td>2025-06-07</td>
              <td><span class="status mark">Mark as reviewed</span></td>
              <td><span class="open-popup">></span></td>
            </tr>
          </tbody>
        </table>

        <div class="pagination">
          <span>[ ◀ Prev ]</span>
          <p>Showing 10–10 of 120 Clients</p>
          <span>[ Next ▶ ]</span>
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="summary-section">
        <div class="summary-card">
          <h4>Average Rating</h4>
          <p class="highlight">4.8 / 5</p>
        </div>
        <div class="summary-card">
          <h4>Total Reviews</h4>
          <p class="highlight">92</p>
        </div>
        <div class="summary-card">
          <h4>Recent Feedback</h4>
          <p class="highlight">Oct 27, 2025</p>
        </div>
      </div>
    </main>
  </div>

  <!-- Feedback Popup -->
  <div id="feedbackModal" class="modal">
    <div class="modal-content">
      <span class="close-btn">&times;</span>
      <h3>Feedback Details</h3>
      <p><strong>Client:</strong> Jenn Bornilla</p>
      <p><strong>Service:</strong> Plumbing</p>
      <p><strong>Rating:</strong> <span class="stars">★★★★★</span></p>
      <p><strong>Feedback reason:</strong> Service Not Rendered</p>
      <p><strong>Feedback Description:</strong> ano ba yan!</p>
      <p><strong>Date:</strong> 10-31-2025</p>
      <div class="modal-actions">
        <button class="btn green">Mark as reviewed</button>
        <button class="btn red">Send Warning</button>
      </div>
    </div>
  </div>

  <script>
    // === USER DROPDOWN (defensive) ===
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

    // === FILTER DROPDOWN (defensive) ===
    const filterBtn = document.querySelector('.filter-btn');
    const dropdownContent = document.querySelector('.dropdown-content');
    if (filterBtn && dropdownContent) {
      filterBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdownContent.classList.toggle('show');
        filterBtn.innerHTML = dropdownContent.classList.contains('show')
          ? '<i class="fa-solid fa-sliders"></i> Filter ▲'
          : '<i class="fa-solid fa-sliders"></i> Filter ▼';
      });
      window.addEventListener('click', () => {
        dropdownContent.classList.remove('show');
        filterBtn.innerHTML = '<i class="fa-solid fa-sliders"></i> Filter ▼';
      });

      // === DATE FILTER ===
      const fromInput = document.getElementById('from-date');
      const toInput = document.getElementById('to-date');
      const applyBtn = dropdownContent.querySelector('.apply-btn');

      function parseRowDate(text) {
        if (!text) return null;
        const m = text.match(/(\d{4})\D(\d{2})\D(\d{2})(?:[^\d]*(\d{2}):?(\d{2}))?/);
        if (m) {
          const y = m[1], mo = m[2], d = m[3];
          const hh = m[4] || '00', mm = m[5] || '00';
          const iso = `${y}-${mo}-${d}T${hh}:${mm}:00`;
          const dt = new Date(iso);
          if (!isNaN(dt.getTime())) return dt;
        }
        const p = Date.parse(text);
        if (!isNaN(p)) return new Date(p);
        return null;
      }

      function applyDateFilter() {
        const fromVal = fromInput ? fromInput.value : '';
        const toVal = toInput ? toInput.value : '';
        const fromDate = fromVal ? new Date(fromVal) : null;
        const toDateRaw = toVal ? new Date(toVal) : null;
        const toDate = toDateRaw ? new Date(toDateRaw.getTime() + 86399999) : null;

        const rows = document.querySelectorAll('.reviews-table tbody tr');
        let matched = 0;
        rows.forEach(row => {
          const dateCell = row.querySelector('td:nth-child(5)');
          const rowDate = parseRowDate(dateCell ? dateCell.textContent.trim() : '');
          if (!rowDate) { row.dataset.filterHidden = ''; return; }
          const within = (!fromDate || rowDate >= fromDate) && (!toDate || rowDate <= toDate);
          row.dataset.filterHidden = within ? '' : 'true';
          if (within) matched++;
        });
        // Update visibility
        rows.forEach(row => {
          const fHidden = row.dataset.filterHidden === 'true';
          const sHidden = row.dataset.searchHidden === 'true';
          row.style.display = (fHidden || sHidden) ? 'none' : '';
        });
        console.debug('Feedback filter applied:', { fromVal, toVal, matched, total: rows.length });
      }

      if (applyBtn && fromInput && toInput) {
        applyBtn.addEventListener('click', (e) => {
          e.preventDefault();
          applyDateFilter();
          dropdownContent.classList.remove('show');
          filterBtn.innerHTML = '<i class="fa-solid fa-sliders"></i> Filter ▼';
        });
        fromInput.addEventListener('change', applyDateFilter);
        toInput.addEventListener('change', applyDateFilter);
      }
    }

    // === SEARCH (debounced + immediate) ===
    (function(){
      const input = document.querySelector('.search-filter input[type="text"]');
      if (!input) return;
      const searchBtn = document.querySelector('.search-filter .search-btn');
      const rows = Array.from(document.querySelectorAll('.reviews-table tbody tr'));
      const norm = s => (s||'').toString().replace(/\s+/g,' ').trim().toLowerCase();
      let timer = null;

      // expose a simple composer so other filters can call it later
      window.updateReviewsRowVisibility = function(row){
        try {
          const searchHidden = row.dataset.searchHidden === 'true';
          row.style.display = searchHidden ? 'none' : '';
        } catch (err) { row.style.display = ''; }
      };

      function applySearch(q){
        const text = norm(q);
        rows.forEach(row => {
          const provider = norm(row.querySelector('td:nth-child(2)')?.textContent);
          const service = norm(row.querySelector('td:nth-child(3)')?.textContent);
          const matches = !text || provider.indexOf(text) !== -1 || service.indexOf(text) !== -1;
          row.dataset.searchHidden = matches ? '' : 'true';
          window.updateReviewsRowVisibility(row);
        });
      }

      input.addEventListener('input', (e) => { clearTimeout(timer); timer = setTimeout(() => applySearch(e.target.value), 180); });
      input.addEventListener('keydown', (e) => {
        if (e.key === 'Escape'){ input.value = ''; applySearch(''); }
        if (e.key === 'Enter'){ e.preventDefault(); clearTimeout(timer); applySearch(input.value); }
      });
      if (searchBtn) searchBtn.addEventListener('click', (ev) => { ev.preventDefault(); clearTimeout(timer); applySearch(input.value); });

      // initialize
      applySearch(input.value || '');
    })();

  // === FEEDBACK MODAL (defensive) ===
  const modal = document.getElementById("feedbackModal");
  const closeBtn = document.querySelector(".close-btn");
  const openPopupButtons = document.querySelectorAll(".open-popup");
  const markBtn = document.querySelector('.btn.green');
  const warnBtn = document.querySelector('.btn.red');

    // Track which table row opened the modal so actions can update it
    let currentRow = null;

    if (openPopupButtons && openPopupButtons.length && modal) {
      openPopupButtons.forEach(button => {
        button.addEventListener("click", (e) => {
          e.stopPropagation();
          // Find the closest row for this button and store it
          currentRow = button.closest('tr');
          // Optionally populate modal details from the row (id, provider, service, rating, date)
          try {
            const cols = currentRow.querySelectorAll('td');
            const clientEl = document.querySelector('#feedbackModal .modal-content p strong');
            // (Keeping the modal static for now; could update content here.)
          } catch (err) {
            // ignore if DOM shape isn't as expected
          }
          modal.style.display = "flex";
        });
      });
    }

    // Mark as reviewed: update the row's status badge client-side and close modal
    if (markBtn) {
      markBtn.addEventListener('click', () => {
        if (currentRow) {
          const statusSpan = currentRow.querySelector('.status');
          if (statusSpan) {
            statusSpan.textContent = 'Reviewed';
            statusSpan.classList.remove('pending', 'mark');
            statusSpan.classList.add('reviewed');
          }
        }
        if (modal) modal.style.display = 'none';
      });
    }

    // Send warning: simple prompt for now (no server call) and close modal
    if (warnBtn) {
      warnBtn.addEventListener('click', () => {
        const msg = prompt('Enter warning message to send to client:');
        if (msg && currentRow) {
          // TODO: wire to a server endpoint to persist/send the warning.
          console.log('Warning message (not sent):', msg);
          alert('Warning queued (demo).');
        }
        if (modal) modal.style.display = 'none';
      });
    }

    // Tabs: navigate between provider and client feedback pages (robust by data-target)
    (function(){
      const tabs = document.querySelectorAll('.tabs .tab[data-target]');
      if (!tabs || tabs.length === 0) return;
      tabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
          try {
            e.stopPropagation();
            const dst = tab.getAttribute('data-target');
            if (!dst) return;
            console.debug('Feedback tab clicked, navigating to', dst);
            window.location.assign(dst);
          } catch (err) { console.error('Tab navigation failed', err); }
        });
      });
    })();

    closeBtn.addEventListener("click", () => {
      modal.style.display = "none";
    });

    window.addEventListener("click", (e) => {
      if (e.target === modal) {
        modal.style.display = "none";
      }
    });
  </script>
</body>
</html>



