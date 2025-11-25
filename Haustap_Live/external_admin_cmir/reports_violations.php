<?php require_once __DIR__ . '/includes/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reports & Violations</title>
  <link rel="stylesheet" href="css/manage_provider.css" />
  <link rel="stylesheet" href="css/reports_violations.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <?php $active = 'reports'; include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Topbar -->
      <header class="topbar">
        <h3>Reports</h3>
        <div class="user">
          <button class="notif-btn">🔔</button>
          <div class="user-menu">
            <button id="userDropdownBtn" class="user-dropdown-btn">Mj Punzalan ▼</button>
            <div class="user-dropdown" id="userDropdown">
              <a href="admin_profile.php">View Profile</a>
              <a href="change_password.php">Change Password</a>
              <a href="logout.php" class="logout">Log out</a>
            </div>
          </div>
        </div>
      </header>

      <section class="content-area">
        <div class="controls">
          <div class="tabs">
            <button class="tab active" data-target="all">All</button>
            <button class="tab" data-target="pending">Pending</button>
            <button class="tab" data-target="resolved">Resolved</button>
          </div>
          <div class="search-box">
            <div class="search-field">
              <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
              <input type="text" id="reportSearch" placeholder="Search Name" />
            </div>
            <div class="filter-wrap">
              <button class="filter-btn" id="filterBtn"><i class="fa-solid fa-sliders"></i> Filter</button>
              <div id="filterDropdown" class="filter-dropdown">
                <label for="dateFrom">From</label>
                <input type="date" id="dateFrom" />
                <label for="dateTo">To</label>
                <input type="date" id="dateTo" />
                <div class="filter-actions">
                  <button class="btn" id="filterClear">Clear</button>
                  <button class="btn review" id="filterApply">Apply</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="table-container">
          <table class="reports-table">
            <thead>
              <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Booking ID</th>
                <th>User-Type</th>
                <th>Category</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="reportsTbody">
              <tr data-status="resolved">
                <td>1</td>
                <td>Jenn Bonrilla</td>
                <td>123</td>
                <td>Client</td>
                <td>No Prior Notice</td>
                <td>2025-06-07</td>
                <td>8:00</td>
                <td><span class="badge resolved">Resolved</span></td>
                <td><button class="btn view">></button></td>
              </tr>
              <tr data-status="pending">
                <td>2</td>
                <td>Ana Santos</td>
                <td>234</td>
                <td>Service Provider</td>
                <td>Last Minute Cancellation</td>
                <td>2025-06-07</td>
                <td>8:00</td>
                <td><span class="badge pending">Pending</span></td>
                <td><button class="btn view">></button></td>
              </tr>
            </tbody>
          </table>
          <div class="pagination">
            <span>[ ◀ Prev ]</span>
            <span>Showing 2–10 of 120 Reviews</span>
            <span>[ Next ▶ ]</span>
          </div>
        </div>
        <div class="bottom-summary">
          <div class="card"><div class="label">Total Reports (This Month)</div><div class="value">18</div></div>
          <div class="card"><div class="label">Pending</div><div class="value">5</div></div>
          <div class="card"><div class="label">Resolved</div><div class="value">11</div></div>
          <div class="card highlight"><div class="label">Top Reason</div><div class="value">Poor Service Quality</div></div>
        </div>
        <div id="reportModal" class="modal">
          <div class="modal-content">
            <button id="reportModalClose" class="close-btn">&times;</button>
            <h4 id="reportModalTitle"></h4>
            <div id="reportModalBody"></div>
            <div class="modal-actions">
              <button class="btn" id="reportModalCancel">Cancel</button>
              <button class="btn review" id="reportModalPrimary">Continue</button>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>

  <script>
    // User dropdown (same behaviour as other admin pages)
    (function(){
      try {
        const dropdownBtn = document.getElementById("userDropdownBtn");
        const dropdown = document.getElementById("userDropdown");
        if (dropdownBtn && dropdown) {
          dropdownBtn.addEventListener("click", function(e){
            e.stopPropagation();
            dropdown.classList.toggle('show');
          });
          window.addEventListener('click', function(e){
            try { if (!dropdown.contains(e.target)) dropdown.classList.remove('show'); } catch(_){}
          });
        }
      } catch(err){ console.warn('user dropdown init failed', err); }
    })();

    function applyFilters(){
      var targetBtn = document.querySelector('.tab.active');
      var target = targetBtn ? targetBtn.getAttribute('data-target') : 'all';
      var qEl = document.getElementById('reportSearch');
      var q = qEl ? qEl.value.toLowerCase() : '';
      var fromEl = document.getElementById('dateFrom');
      var toEl = document.getElementById('dateTo');
      var from = fromEl && fromEl.value ? new Date(fromEl.value) : null;
      var to = toEl && toEl.value ? new Date(toEl.value) : null;
      var rows = document.querySelectorAll('#reportsTbody tr');
      rows.forEach(function(r){
        var matchesTab = target === 'all' || r.dataset.status === target;
        var text = r.textContent.toLowerCase();
        var matchesSearch = !q || text.indexOf(q) !== -1;
        var dateCell = r.children[5];
        var d = dateCell ? new Date(dateCell.textContent.trim()) : null;
        var matchesDate = true;
        if (from && d) { if (d < from) matchesDate = false; }
        if (to && d) { if (d > to) matchesDate = false; }
        r.style.display = (matchesTab && matchesSearch && matchesDate) ? '' : 'none';
      });
    }
    document.querySelectorAll('.tab').forEach(function(btn){
      btn.addEventListener('click', function(){
        document.querySelectorAll('.tab').forEach(function(t){ t.classList.remove('active'); });
        btn.classList.add('active');
        applyFilters();
      });
    });

    // Simple client-side search
    document.getElementById('reportSearch').addEventListener('input', function(){ applyFilters(); });
    (function(){
      var btn = document.getElementById('filterBtn');
      var dd = document.getElementById('filterDropdown');
      var apply = document.getElementById('filterApply');
      var clear = document.getElementById('filterClear');
      if (btn && dd) {
        btn.addEventListener('click', function(e){ e.stopPropagation(); dd.classList.toggle('show'); });
        window.addEventListener('click', function(e){ if (dd && !dd.contains(e.target) && e.target !== btn) dd.classList.remove('show'); });
      }
      if (apply) apply.addEventListener('click', function(){ dd.classList.remove('show'); applyFilters(); });
      if (clear) clear.addEventListener('click', function(){ var f=document.getElementById('dateFrom'); var t=document.getElementById('dateTo'); if(f) f.value=''; if(t) t.value=''; dd.classList.remove('show'); applyFilters(); });
    })();
  </script>
  <script>
    (function(){
      var tbody = document.getElementById('reportsTbody');
      var modal = document.getElementById('reportModal');
      var modalTitle = document.getElementById('reportModalTitle');
      var modalBody = document.getElementById('reportModalBody');
      var modalPrimary = document.getElementById('reportModalPrimary');
      var modalCancel = document.getElementById('reportModalCancel');
      var modalClose = document.getElementById('reportModalClose');
      var primaryHandler = null;
      function openModal(title, bodyHTML, primaryText, primaryClass, onPrimary){
        modalTitle.textContent = title;
        modalBody.innerHTML = bodyHTML;
        modalPrimary.textContent = primaryText || 'Continue';
        modalPrimary.className = 'btn ' + (primaryClass || 'review');
        modal.style.display = 'flex';
        if (primaryHandler) modalPrimary.removeEventListener('click', primaryHandler);
        primaryHandler = onPrimary || null;
        if (primaryHandler) modalPrimary.addEventListener('click', primaryHandler);
        modalPrimary.style.display = primaryText ? '' : 'none';
      }
      function closeModal(){
        if (primaryHandler) modalPrimary.removeEventListener('click', primaryHandler);
        primaryHandler = null;
        modal.style.display = 'none';
        try { modalCancel.style.display = ''; } catch(_){}
      }
      if (modalCancel) modalCancel.addEventListener('click', closeModal);
      if (modalClose) modalClose.addEventListener('click', closeModal);
      if (!tbody) return;
      tbody.addEventListener('click', function(e){
        var btn = e.target.closest('button.btn');
        if (!btn) return;
        var row = btn.closest('tr');
        if (!row) return;
        var reportId = row.children[0] ? row.children[0].textContent.trim() : '';
        var statusCell = row.children[5];
        var providerText = row.children[2] ? row.children[2].textContent : '';
        var m = providerText.match(/SP-(\d+)/i);
        var providerId = m ? parseInt(m[1], 10) : 0;
        if (btn.classList.contains('review')) {
          var body = '<p class="muted">Report ' + reportId + '</p>' +
                     '<label>Notes</label>' +
                     '<textarea id="reviewNotes" placeholder="Add review notes"></textarea>';
          openModal('Review Report', body, 'Mark In Review', 'review', function(){
            row.dataset.status = 'in-review';
            if (statusCell) statusCell.innerHTML = '<span class="badge review">In Review</span>';
            closeModal();
          });
          return;
        }
        if (btn.classList.contains('suspend')) {
          if (!providerId) { openModal('Suspend Provider', '<p>Provider ID not found.</p>', null, null, null); return; }
          var body2 = '<p>Provider SP-' + providerId + '</p>' +
                      '<label>Reason</label>' +
                      '<textarea id="suspendReason" placeholder="Enter reason (optional)"></textarea>';
          openModal('Suspend Provider', body2, 'Suspend', 'suspend danger', function(){
            var fd = new FormData();
            fd.append('id', providerId);
            fd.append('action', 'suspended');
            fetch('api/update_provider_status.php', { method: 'POST', body: fd, credentials: 'same-origin' })
              .then(function(res){ return res.json(); })
              .then(function(data){
                if (data && data.success) {
                  closeModal();
                  alert('Provider SP-' + providerId + ' suspended');
                } else {
                  alert('Failed: ' + (data && data.error ? data.error : 'Unknown error'));
                }
              })
              .catch(function(){ alert('Network error'); });
          });
          return;
        }
        if (btn.classList.contains('view')) {
          var userId = row.children[0] ? row.children[0].textContent.trim() : '';
          var name = row.children[1] ? row.children[1].textContent.trim() : '';
          var bookingId = row.children[2] ? row.children[2].textContent.trim() : '';
          var userType = row.children[3] ? row.children[3].textContent.trim() : '';
          var category = row.children[4] ? row.children[4].textContent.trim() : '';
          var bodyHtml = ''+
            '<div class="details">'+
              '<div class="detail"><span class="label">User ID:</span><span class="value">'+userId+'</span></div>'+
              '<div class="detail"><span class="label">Booking ID:</span><span class="value">'+bookingId+'</span></div>'+
              '<div class="detail"><span class="label">Name(Reported):</span><span class="value">'+name+'</span></div>'+
              '<div class="detail"><span class="label">User Type(Reported):</span><span class="value">'+userType+'</span></div>'+
              '<div class="detail"><span class="label">Category:</span><span class="value">'+category+'</span></div>'+
            '</div>'+
            '<div class="modal-actions">'+
              '<button class="btn success" id="modalResolveBtn">Mark as Resolved</button>'+
              '<button class="btn danger" id="modalWarnBtn">Send Warning</button>'+
            '</div>';
          openModal('Report Details', bodyHtml, null, null, null);
          try { modalCancel.style.display = 'none'; } catch(_){}
          var resolveBtn = document.getElementById('modalResolveBtn');
          var warnBtn = document.getElementById('modalWarnBtn');
          if (resolveBtn) {
            resolveBtn.addEventListener('click', function(){
              row.dataset.status = 'resolved';
              var statusCell2 = row.children[7];
              if (statusCell2) statusCell2.innerHTML = '<span class="badge resolved">Resolved</span>';
              closeModal();
            });
          }
          if (warnBtn) {
            warnBtn.addEventListener('click', function(){
              alert('Warning sent to '+name);
              closeModal();
            });
          }
          return;
        }
      });
    })();
  </script>
</body>
</html>
