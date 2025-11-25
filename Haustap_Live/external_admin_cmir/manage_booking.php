<?php require_once __DIR__ . '/includes/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Bookings</title>
  <link rel="stylesheet" href="css/manage_booking.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="js/lazy-images.js" defer></script></head>
<body>
   <div class="dashboard-container">
    <!-- Sidebar -->
    <?php $active = 'bookings'; include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Topbar -->
      <header class="topbar">
        <h3>Manage Bookings</h3>
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
        <button class="tab active" data-status="all" data-target="manage_booking.php">All</button>
        <button class="tab" data-status="pending" data-target="manage_booking_pending.php">Pending</button>
        <button class="tab" data-status="ongoing" data-target="manage_booking_ongoing.php">Ongoing</button>
        <button class="tab" data-status="complete" data-target="manage_booking_completed.php">Completed</button>
        <button class="tab" data-status="cancelled" data-target="manage_booking_cancelled.php">Cancelled</button>
        <button class="tab" data-status="return" data-target="manage_booking_return.php">Return</button>
      </div>

       <!-- Search and Filter -->
<div class="search-filter">
  <input type="text" class="search-input" placeholder="Search bookings (id, client, provider, service, date, status)" aria-label="Search bookings">

  <div class="filter-dropdown">
<button class="filter-btn"><i class="fa-solid fa-sliders"></i> Filter</button>
    <div class="dropdown-content">
      <!-- Filter by Date -->
      <div class="filter-date">
        <p class="filter-title">Filter by Date</p>
        <div class="date-row">
          <label for="from-date">From:</label>
          <input type="date" id="from-date" value="2025-06-01">
        </div>
        <div class="date-row">
          <label for="to-date">To:</label>
          <input type="date" id="to-date" value="2025-06-30">
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
              <th>Booking ID</th>
              <th>Client</th>
              <th>Provider</th>
              <th>Service</th>
              <th>Date & Time</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>Juan Ewan Dela Cruz</td>
              <td>Ramon Ang</td>
              <td>Home Cleaning</td>
              <td>2025-06-07 14:30</td>
              <td><span class="status complete">Complete</span></td>
              <td class="arrow" role="button" aria-label="View details">›</td>
            </tr>
            <tr>
              <td>2</td>
              <td>Ramon Ang</td>
              <td>Juan Dela Cruz</td>
              <td>Home Cleaning</td>
              <td>2025-06-07 14:30</td>
              <td><span class="status cancelled">Cancelled</span></td>
              <td class="arrow" role="button" aria-label="View details">›</td>
            </tr>
          </tbody>
        </table>
       <div class="pagination">
          <span>[ ◀ Prev ]</span>
          <span>Showing 2–10 of 120 Clients</span>
          <span>[ Next ▶ ]</span>
        </div>
      </div>
      <div id="bookingModalOverlay" class="modal-overlay" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="bookingModalTitle">
          <div class="modal-header">
            <h4 id="bookingModalTitle">Booking Details</h4>
            <span class="modal-status"></span>
            <button class="modal-close" type="button" aria-label="Close">×</button>
          </div>
          <div class="modal-body">
            <div class="modal-row"><span class="label">Booking ID:</span><span class="value" data-field="id"></span></div>
            <div class="modal-row"><span class="label">Client:</span><span class="value" data-field="client"></span></div>
            <div class="modal-row"><span class="label">Provider:</span><span class="value" data-field="provider"></span></div>
            <div class="modal-row"><span class="label">Service:</span><span class="value" data-field="service"></span></div>
            <div class="modal-row"><span class="label">Date & Time:</span><span class="value" data-field="datetime"></span></div>
            <div class="modal-row"><span class="label">Update Status:</span>
              <select class="status-select" id="modalStatusSelect">
                <option value="pending">Pending</option>
                <option value="ongoing">Ongoing</option>
                <option value="complete">Completed</option>
                <option value="cancelled">Cancelled</option>
                <option value="return">Return</option>
              </select>
            </div>
            <div class="modal-section" data-section="pending">
              <div class="section-title">Pending</div>
              <p class="section-text">This booking is pending. Actions are limited until confirmation.</p>
            </div>
            <div class="modal-section" data-section="ongoing">
              <div class="section-title">Ongoing</div>
              <p class="section-text">Service is in progress.</p>
            </div>
            <div class="modal-section" data-section="complete">
              <div class="section-title">Completed</div>
              <p class="section-text">Service completed. Review and settlement available.</p>
            </div>
            <div class="modal-section" data-section="cancelled">
              <div class="section-title">Cancelled</div>
              <p class="section-text">This booking was cancelled.</p>
            </div>
            <div class="modal-section" data-section="return">
              <div class="section-title">Return</div>
              <p class="section-text">Return requested. Review reason and evidence.</p>
            </div>
          </div>
          <div class="modal-footer">
            <button class="modal-action" data-action="close">Close</button>
            <button class="modal-action" data-action="update-status">Update Status</button>
          </div>
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

    // Global helper: show rows matching a given booking status (or all)
    function showRowsForStatus(status){
      // Tab-based filtering sets data-tab-hidden so it composes with other filters (status/search)
      const rows = document.querySelectorAll('.table-container tbody tr');
      rows.forEach(row => {
        const badge = row.querySelector('.status');
        let s = '';
        if (badge) {
          const c = badge.classList;
          if (c.contains('complete') || c.contains('completed')) s = 'complete';
          else if (c.contains('ongoing')) s = 'ongoing';
          else if (c.contains('pending')) s = 'pending';
          else if (c.contains('cancelled')) s = 'cancelled';
          else if (c.contains('return')) s = 'return';
        }
        // set per-row tab-hidden flag instead of directly changing style so search + status filters combine
        if (!status || status === 'all') row.dataset.tabHidden = '';
        else row.dataset.tabHidden = (s === status) ? '' : 'true';
        if (typeof window.updateBookingRowVisibility === 'function') window.updateBookingRowVisibility(row);
      });
    }

    // Filter dropdown toggle (scoped to this filter button)
    (function(){
      const filterBtn = document.querySelector('.filter-btn');
      if (!filterBtn) return;
      // prefer the dropdown located inside the same parent as the button
      const dropdownContent = filterBtn.parentElement && filterBtn.parentElement.querySelector('.dropdown-content') || document.querySelector('.dropdown-content');
      if (!dropdownContent) return;
      filterBtn.setAttribute('aria-expanded', 'false');
      filterBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdownContent.classList.toggle('show');
        const expanded = dropdownContent.classList.contains('show');
        filterBtn.innerHTML = expanded ? '<i class="fa-solid fa-sliders"></i> Filter ▲' : '<i class="fa-solid fa-sliders"></i> Filter ▼';
        filterBtn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
      });
      window.addEventListener('click', (e) => {
        if (!dropdownContent.contains(e.target) && !filterBtn.contains(e.target)) {
          dropdownContent.classList.remove('show');
          filterBtn.innerHTML = '<i class="fa-solid fa-sliders"></i> Filter ▼';
          filterBtn.setAttribute('aria-expanded','false');
        }
      });
    })();

    // Date filter: encapsulated initializer for date range filtering
    (function initDateFilter(){
      const filterBtn = document.querySelector('.filter-btn');
      const dropdownContent = filterBtn && (filterBtn.parentElement && filterBtn.parentElement.querySelector('.dropdown-content')) || document.querySelector('.dropdown-content');
      if (!dropdownContent) return;
      
      const fromInput = dropdownContent.querySelector('#from-date');
      const toInput = dropdownContent.querySelector('#to-date');
      const applyBtn = dropdownContent.querySelector('.apply-btn');

      function parseRowDate(text){
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

      function applyDateFilter(){
        const fromVal = fromInput ? fromInput.value : '';
        const toVal = toInput ? toInput.value : '';
        const fromDate = fromVal ? new Date(fromVal) : null;
        const toDateRaw = toVal ? new Date(toVal) : null;
        const toDate = toDateRaw ? new Date(toDateRaw.getTime() + 86399999) : null;

        const rows = document.querySelectorAll('.table-container tbody tr');
        let matched = 0;
        rows.forEach(row => {
          const dateCell = row.querySelector('td:nth-child(5)');
          const rowDate = parseRowDate(dateCell ? dateCell.textContent.trim() : '');
          if (!rowDate) { row.dataset.filterHidden = ''; return; }
          const within = (!fromDate || rowDate >= fromDate) && (!toDate || rowDate <= toDate);
          row.dataset.filterHidden = within ? '' : 'true';
          if (within) matched++;
          if (typeof window.updateBookingRowVisibility === 'function') window.updateBookingRowVisibility(row);
        });
        console.debug('applyDateFilter', { fromVal, toVal, matched, total: rows.length });
      }

      if (applyBtn && fromInput && toInput) {
        applyBtn.addEventListener('click', (e) => {
          e.preventDefault();
          applyDateFilter();
          if (dropdownContent) dropdownContent.classList.remove('show');
          if (filterBtn) { filterBtn.innerHTML = '<i class="fa-solid fa-sliders"></i> Filter ▼'; filterBtn.setAttribute('aria-expanded','false'); }
        });
        fromInput.addEventListener('change', applyDateFilter);
        toInput.addEventListener('change', applyDateFilter);
      }

      // Initialize
      applyDateFilter();
    })();

    // Helper: combine tab, filter and search hidden flags to set final visibility
    window.updateBookingRowVisibility = function(row){
      try {
        const tabHidden = row.dataset.tabHidden === 'true';
        const filterHidden = row.dataset.filterHidden === 'true';
        const searchHidden = row.dataset.searchHidden === 'true';
        row.style.display = (tabHidden || filterHidden || searchHidden) ? 'none' : '';
      } catch(err) {
        row.style.display = '';
      }
    };

    // Tabs navigation: navigate to specific manage_booking_* pages
    (function(){
      const tabs = document.querySelectorAll('.tabs .tab');
      if (!tabs || tabs.length === 0) return;
      tabs.forEach(t => {
        t.addEventListener('click', (e) => {
          // If the tab has a data-status attribute, filter in-place instead of navigating.
          const status = t.getAttribute('data-status');
          if (status !== null) {
            e.preventDefault();
            // set active class
            tabs.forEach(x => x.classList.remove('active'));
            t.classList.add('active');
            // show matching rows
            if (typeof showRowsForStatus === 'function') showRowsForStatus(status);
            return;
          }

          // Fallback: navigate using data-target (legacy behaviour)
          const target = t.getAttribute('data-target');
          if (!target) return;
          try {
            const targetUrl = new URL(target, window.location.href).href;
            if (e.ctrlKey || e.metaKey || e.button === 1) {
              window.open(targetUrl, '_blank');
              return;
            }
            window.location.assign(targetUrl);
          } catch (err) {
            console.warn('Tab navigation failed for target:', target, err);
          }
        });
      });
    })();

    // Auto-set active tab based on current filename (so direct visits highlight correct tab)
    (function(){
      try {
        const path = (window.location.pathname || '').split('/').pop() || '';
        const filename = path || 'manage_booking.php';
        const tabs = document.querySelectorAll('.tabs .tab');
        if (!tabs || tabs.length === 0) return;
        let matched = false;
        tabs.forEach(t => {
          const dt = (t.getAttribute('data-target') || '').toLowerCase();
          if (!dt) return;
          if (dt === filename.toLowerCase()) {
            tabs.forEach(x => x.classList.remove('active'));
            t.classList.add('active');
            matched = true;
          }
        });
        // If no explicit match, keep 'All' active for manage_booking.php or default pages
        if (!matched) {
          // try matching by status name in filename (e.g. manage_booking_pending.php -> data-status="pending")
          const lower = filename.toLowerCase();
          const byStatus = Array.from(tabs).find(t => {
            const st = (t.getAttribute('data-status') || '').toLowerCase();
            return st && lower.indexOf(st) !== -1;
          });
          if (byStatus) {
            tabs.forEach(x => x.classList.remove('active'));
            byStatus.classList.add('active');
            // apply initial filter for this status
            if (typeof showRowsForStatus === 'function') showRowsForStatus(byStatus.getAttribute('data-status'));
            matched = true;
          } else {
            const all = Array.from(tabs).find(t => (t.getAttribute('data-target')||'').toLowerCase().endsWith('manage_booking.php'));
            if (all) {
              tabs.forEach(x => x.classList.remove('active'));
              all.classList.add('active');
              if (typeof showRowsForStatus === 'function') showRowsForStatus('all');
            }
          }
        }
      } catch (err) { /* non-fatal */ }
    })();

    // ----------------------
    // Search input: live filter for bookings
    // ----------------------
    (function(){
      const input = document.querySelector('.search-input');
      const tbody = document.querySelector('.table-container tbody');
      if (!input || !tbody) return;

      const rows = Array.from(tbody.querySelectorAll('tr'));
      const norm = s => (s||'').toString().replace(/\s+/g,' ').trim().toLowerCase();

      function applySearch(q){
        const text = norm(q);
        rows.forEach(row => {
          const id = norm(row.querySelector('td:first-child')?.textContent);
          const client = norm(row.querySelector('td:nth-child(2)')?.textContent);
          const provider = norm(row.querySelector('td:nth-child(3)')?.textContent);
          const service = norm(row.querySelector('td:nth-child(4)')?.textContent);
          const datetime = norm(row.querySelector('td:nth-child(5)')?.textContent);
          const status = norm(row.querySelector('.status')?.textContent);
          const combined = [id, client, provider, service, datetime, status].join(' ');

          if (!text) {
            row.dataset.searchHidden = '';
            if (typeof window.updateBookingRowVisibility === 'function') window.updateBookingRowVisibility(row);
            return;
          }

          const matches = combined.indexOf(text) !== -1;
          row.dataset.searchHidden = matches ? '' : 'true';
          if (typeof window.updateBookingRowVisibility === 'function') window.updateBookingRowVisibility(row);
        });
      }

      let timer = null;
      input.addEventListener('input', function(e){ clearTimeout(timer); timer = setTimeout(() => applySearch(e.target.value), 180); });
      input.addEventListener('keydown', function(e){ if (e.key === 'Escape'){ input.value = ''; applySearch(''); } });
    })();

    (function(){
      const overlay = document.getElementById('bookingModalOverlay');
      const modal = overlay ? overlay.querySelector('.modal') : null;
      if (!overlay || !modal) return;
      const closeBtn = modal.querySelector('.modal-close');
      const footerClose = modal.querySelector('.modal-action[data-action="close"]');
      const footerUpdate = modal.querySelector('.modal-action[data-action="update-status"]');
      const statusSelect = modal.querySelector('#modalStatusSelect');
      let currentRow = null;
      function setField(field, text){
        const el = modal.querySelector('[data-field="'+field+'"]');
        if (el) el.textContent = text || '';
      }
      function showSection(status){
        const sections = modal.querySelectorAll('.modal-section');
        sections.forEach(s => {
          const on = s.getAttribute('data-section');
          s.style.display = on === status ? '' : 'none';
        });
      }
      function setStatusBadge(statusText, statusClass){
        const badge = modal.querySelector('.modal-status');
        if (!badge) return;
        badge.className = 'modal-status';
        if (statusClass) badge.classList.add(statusClass);
        badge.textContent = statusText || '';
      }
      function normStatusFromClass(cls){
        if (!cls) return '';
        if (cls.contains('complete') || cls.contains('completed')) return 'complete';
        if (cls.contains('ongoing')) return 'ongoing';
        if (cls.contains('pending')) return 'pending';
        if (cls.contains('cancelled')) return 'cancelled';
        if (cls.contains('return')) return 'return';
        return '';
      }
      function cap(s){ return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }
      function getActiveTabStatus(){
        const active = document.querySelector('.tabs .tab.active');
        return active ? (active.getAttribute('data-status') || 'all') : 'all';
      }
      function applyStatusToRow(row, status){
        const badge = row.querySelector('.status');
        if (!badge) return;
        const classes = ['complete','completed','ongoing','pending','cancelled','return'];
        classes.forEach(c => badge.classList.remove(c));
        if (status === 'complete') badge.classList.add('complete');
        else badge.classList.add(status);
        badge.textContent = cap(status === 'complete' ? 'completed' : status);
        const st = getActiveTabStatus();
        if (typeof showRowsForStatus === 'function') showRowsForStatus(st);
        if (typeof window.updateBookingRowVisibility === 'function') window.updateBookingRowVisibility(row);
      }
      function openRowModal(row){
        const id = row.querySelector('td:nth-child(1)')?.textContent.trim() || '';
        const client = row.querySelector('td:nth-child(2)')?.textContent.trim() || '';
        const provider = row.querySelector('td:nth-child(3)')?.textContent.trim() || '';
        const service = row.querySelector('td:nth-child(4)')?.textContent.trim() || '';
        const datetime = row.querySelector('td:nth-child(5)')?.textContent.trim() || '';
        const statusEl = row.querySelector('.status');
        const statusText = statusEl ? statusEl.textContent.trim() : '';
        const statusClass = normStatusFromClass(statusEl ? statusEl.classList : null);
        setField('id', id);
        setField('client', client);
        setField('provider', provider);
        setField('service', service);
        setField('datetime', datetime);
        setStatusBadge(statusText, statusClass);
        showSection(statusClass || '');
        if (statusSelect) statusSelect.value = statusClass || 'pending';
        currentRow = row;
        overlay.setAttribute('aria-hidden','false');
        overlay.classList.add('show');
      }
      function closeModal(){
        overlay.classList.remove('show');
        overlay.setAttribute('aria-hidden','true');
      }
      Array.from(document.querySelectorAll('.table-container tbody tr .arrow')).forEach(cell => {
        cell.addEventListener('click', function(e){
          const row = e.currentTarget.closest('tr');
          if (row) openRowModal(row);
        });
      });
      closeBtn && closeBtn.addEventListener('click', closeModal);
      footerClose && footerClose.addEventListener('click', closeModal);
      footerUpdate && footerUpdate.addEventListener('click', function(){
        if (!currentRow || !statusSelect) return;
        const idText = modal.querySelector('[data-field="id"]').textContent.trim();
        const idNum = parseInt(idText, 10);
        const newStatus = statusSelect.value || '';
        applyStatusToRow(currentRow, newStatus);
        setStatusBadge(cap(newStatus === 'complete' ? 'completed' : newStatus), newStatus);
        showSection(newStatus);
        if (!isNaN(idNum) && newStatus) {
          try {
            fetch('/mock-api/bookings/'+idNum+'/status', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ status: newStatus }) });
          } catch (err) {}
        }
      });
      overlay.addEventListener('click', function(e){
        if (e.target === overlay) closeModal();
      });
    })();
  </script>
</body>
</html>



