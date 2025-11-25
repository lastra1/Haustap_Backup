<?php require_once __DIR__ . '/includes/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin | Manage Applicants</title>
  <link rel="stylesheet" href="css/manage_applicant.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<script src="js/lazy-images.js" defer></script>
<script src="js/app.js" defer></script>
<style>
  /* Filter dropdown styles (embedded so dev server shows immediately) */
  .search-filter { display:flex; gap:12px; align-items:center; }
  .search-filter .search-bar { padding:8px 12px; border-radius:6px; border:1px solid #ddd; width:250px; }
  @media (max-width:640px) { .search-filter { flex-direction:column; align-items:flex-start; } }
  
  /* Filter Button and Dropdown */
  .table-header { display:flex; gap:12px; align-items:center; margin-bottom:15px; flex-wrap:wrap; }
  .table-header .search-bar { width:250px; padding:8px 12px; border-radius:6px; border:1px solid #ddd; }
  
  .filter-dropdown { position:relative; display:inline-block; }
  
  .filter-btn { background:#fff; border:1px solid #ccc; border-radius:6px; padding:8px 12px; cursor:pointer; font-size:14px; display:flex; align-items:center; gap:6px; transition:background 0.2s; }
  .filter-btn:hover { background:#f7f7f7; border-color:#999; }
  
  .dropdown-content { display:none; position:absolute; right:0; top:40px; background:#fff; border:1px solid #ddd; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.1); padding:12px; width:220px; z-index:999; }
  .dropdown-content.show { display:block; }
  
  .dropdown-content .filter-title { font-size:13px; font-weight:700; margin:0 0 10px 0; padding-bottom:4px; }
  
  .date-row { display:flex; align-items:center; gap:8px; margin-bottom:10px; flex-wrap:wrap; }
  .date-row label { font-size:13px; font-weight:600; color:#333; margin:0; padding:0; width:auto; background:transparent !important; cursor:default; }
  .date-row input[type="date"] { padding:6px 8px; border:1px solid #ccc; border-radius:4px; font-size:13px; background:#fff; cursor:pointer; flex:1; min-width:120px; }
  .date-row input[type="date"]:hover { border-color:#999; }
  .date-row input[type="date"]:focus { outline:none; border-color:#06b6d4; box-shadow:0 0 4px rgba(6,182,212,0.3); }
  
  .apply-btn { margin-top:10px; width:100%; background:#06b6d4; color:white; border:none; border-radius:6px; padding:8px 16px; font-size:13px; font-weight:600; cursor:pointer; transition:background 0.2s; }
  .apply-btn:hover { background:#0891b2; }
  .apply-btn:active { transform:scale(0.98); }
</style>
</head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <?php $active = 'applicants'; include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Topbar -->
      <header class="topbar">
        <h3>Manage Applicants</h3>
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

      <!-- Header -->
      <div class="page-header">
        
      </div>

      <!-- Applicant Tabs -->
      <div class="tabs">
        <button class="tab active">All</button>
        <button class="tab">Pending Review</button>
        <button class="tab">Scheduled</button>
        <button class="tab">Hired</button>
        <button class="tab">Rejected</button>
      </div>

      <!-- Table Section -->
      <div class="table-container">
        <div class="table-header">
          <input id="searchInput" type="text" placeholder="Search Applicant" class="search-bar" />
          
          <!-- Filter Button -->
          <div class="filter-dropdown">
            <button class="filter-btn"><i class="fa-solid fa-sliders"></i> Filter</button>
            <div class="dropdown-content">
              <p class="filter-title">Filter by Date</p>
              <div class="date-row">
                <label for="from-date">From:</label>
                <input type="date" id="from-date" value="2025-01-01">
              </div>
              <div class="date-row">
                <label for="to-date">To:</label>
                <input type="date" id="to-date" value="2025-12-31">
              </div>
              <button class="apply-btn">Apply</button>
            </div>
          </div>
        </div>

        <table>
          <thead>
            <tr>
              <th>Id</th>
              <th>Name</th>
              <th>Date Applied</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="applicantTableBody">
            <!-- Rows injected by JS -->
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination">
          <span id="prevPage" style="cursor:pointer">◀ Prev</span>
          <span id="paginationInfo">&nbsp;</span>
          <span id="nextPage" style="cursor:pointer">Next ▶</span>
        </div>
      </div>
    </main>
  </div>

  <script>
    // Dropdown logic
    const dropdownBtn = document.getElementById("userDropdownBtn");
    const dropdown = document.getElementById("userDropdown");

    dropdownBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      dropdown.classList.toggle("show");
    });

    window.addEventListener("click", (e) => {
      if (!dropdown.contains(e.target)) dropdown.classList.remove("show");
    });

    // Tabs (highlight only; data loading handled in app.js)
    const tabs = document.querySelectorAll(".tab");
    tabs.forEach(tab => {
      tab.addEventListener("click", () => {
        tabs.forEach(t => t.classList.remove("active"));
        tab.classList.add("active");
      });
    });

    // === Filter Button Toggle ===
    (function(){
      const filterBtn = document.querySelector('.filter-btn');
      if (!filterBtn) return;
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

    // === Date Filter Function ===
    (function initDateFilter(){
      const filterBtn = document.querySelector('.filter-btn');
      const dropdownContent = filterBtn && (filterBtn.parentElement && filterBtn.parentElement.querySelector('.dropdown-content')) || document.querySelector('.dropdown-content');
      if (!dropdownContent) return;
      
      const fromInput = dropdownContent.querySelector('#from-date');
      const toInput = dropdownContent.querySelector('#to-date');
      const applyBtn = dropdownContent.querySelector('.apply-btn');
      const tableBody = document.getElementById('applicantTableBody');
      
      if (!tableBody || !fromInput || !toInput || !applyBtn) return;

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
        const fromVal = fromInput.value;
        const toVal = toInput.value;
        const fromDate = fromVal ? new Date(fromVal) : null;
        const toDateRaw = toVal ? new Date(toVal) : null;
        const toDate = toDateRaw ? new Date(toDateRaw.getTime() + 86399999) : null;

        const rows = tableBody.querySelectorAll('tr');
        let matched = 0;
        rows.forEach(row => {
          const dateCell = row.querySelector('td:nth-child(3)');
          const rowDate = parseRowDate(dateCell ? dateCell.textContent.trim() : '');
          if (!rowDate) { row.dataset.filterHidden = ''; return; }
          const within = (!fromDate || rowDate >= fromDate) && (!toDate || rowDate <= toDate);
          row.dataset.filterHidden = within ? '' : 'true';
          if (within) matched++;
          row.style.display = within ? '' : 'none';
        });
        console.debug('applyDateFilter', { fromVal, toVal, matched, total: rows.length });
      }

      applyBtn.addEventListener('click', (e) => {
        e.preventDefault();
        applyDateFilter();
        if (dropdownContent) dropdownContent.classList.remove('show');
        if (filterBtn) { 
          filterBtn.innerHTML = '<i class="fa-solid fa-sliders"></i> Filter ▼'; 
          filterBtn.setAttribute('aria-expanded','false'); 
        }
      });
      
      fromInput.addEventListener('change', applyDateFilter);
      toInput.addEventListener('change', applyDateFilter);

      // Initialize
      applyDateFilter();
    })();

    // Status filter removed: filtering now handled by overall search or app.js if needed

    // === Applicant row popup (open details when clicking the arrow) ===
    (function(){
      const tableBody = document.getElementById('applicantTableBody');
      if (!tableBody) return;

      // Create modal HTML inserted into DOM
      const modalHtml = `
      <div id="applicantModal" class="modal" style="display:none; position:fixed; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.35); z-index:999; justify-content:center; align-items:center;">
        <div class="modal-content" style="background:#fff; padding:30px; border-radius:8px; width:500px; max-width:95%; max-height:90vh; overflow-y:auto; font-family:Arial,sans-serif;">
          <button id="applicantModalClose" style="float:right; background:none;border:0;font-size:20px;cursor:pointer;color:#666;">&times;</button>
          
          <h2 style="margin:0 0 12px 0; font-size:16px; font-weight:600; color:#000;">Application Form</h2>
          
          <hr style="margin:12px 0 16px 0; border:none; border-top:2px solid #000;">
          
          <!-- Choose Account Type -->
          <div style="margin-bottom:16px;">
            <p style="margin:0 0 4px 0; font-size:13px; font-weight:600; color:#000;">Choose account type:</p>
          </div>
          
          <!-- Basic Information Section -->
          <div style="margin-bottom:24px;">
            <h3 style="margin:0 0 12px 0; font-size:13px; font-weight:700; color:#000;">Basic Information</h3>
            <div style="display:grid; gap:8px;">
              <div style="display:grid; grid-template-columns:120px 1fr; gap:16px;">
                <span style="font-size:13px; font-weight:500; color:#333;">Last Name:</span>
                <span style="font-size:13px; color:#333;"><span id="modalLastName">-</span></span>
              </div>
              <div style="display:grid; grid-template-columns:120px 1fr; gap:16px;">
                <span style="font-size:13px; font-weight:500; color:#333;">First Name:</span>
                <span style="font-size:13px; color:#333;"><span id="modalFirstName">-</span></span>
              </div>
              <div style="display:grid; grid-template-columns:120px 1fr; gap:16px;">
                <span style="font-size:13px; font-weight:500; color:#333;">Middle Name:</span>
                <span style="font-size:13px; color:#333;"><span id="modalMiddleName">-</span></span>
              </div>
              <div style="display:grid; grid-template-columns:120px 1fr; gap:16px;">
                <span style="font-size:13px; font-weight:500; color:#333;">Email:</span>
                <span style="font-size:13px; color:#333;"><span id="modalEmail">-</span></span>
              </div>
              <div style="display:grid; grid-template-columns:120px 1fr; gap:16px;">
                <span style="font-size:13px; font-weight:500; color:#333;">Mobile number:</span>
                <span style="font-size:13px; color:#333;"><span id="modalMobile">-</span></span>
              </div>
              <div style="display:grid; grid-template-columns:120px 1fr; gap:16px;">
                <span style="font-size:13px; font-weight:500; color:#333;">Birthdate:</span>
                <span style="font-size:13px; color:#333;"><span id="modalBirthdate">-</span></span>
              </div>
            </div>
          </div>
          
          <!-- Full Address Section -->
          <div style="margin-bottom:24px;">
            <h3 style="margin:0 0 12px 0; font-size:13px; font-weight:700; color:#000;">Full Address</h3>
            <div style="display:grid; gap:8px;">
              <div style="display:grid; grid-template-columns:120px 1fr; gap:16px;">
                <span style="font-size:13px; font-weight:500; color:#333;">House no. & Street Name:</span>
                <span style="font-size:13px; color:#333;"><span id="modalHouse">-</span></span>
              </div>
              <div style="display:grid; grid-template-columns:120px 1fr; gap:16px;">
                <span style="font-size:13px; font-weight:500; color:#333;">Barangay:</span>
                <span style="font-size:13px; color:#333;"><span id="modalBarangay">-</span></span>
              </div>
              <div style="display:grid; grid-template-columns:120px 1fr; gap:16px;">
                <span style="font-size:13px; font-weight:500; color:#333;">Municipal:</span>
                <span style="font-size:13px; color:#333;"><span id="modalMunicipal">-</span></span>
              </div>
              <div style="display:grid; grid-template-columns:120px 1fr; gap:16px;">
                <span style="font-size:13px; font-weight:500; color:#333;">Province:</span>
                <span style="font-size:13px; color:#333;"><span id="modalProvince">-</span></span>
              </div>
            </div>
          </div>
          
          <!-- Service/s Offer Section -->
          <div style="margin-bottom:24px;">
            <h3 style="margin:0 0 12px 0; font-size:13px; font-weight:700; color:#000;">Service/s offer:</h3>
            <textarea id="modalServiceOffer" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; font-family:inherit; font-size:13px; min-height:80px; resize:vertical; color:#333;"></textarea>
          </div>
          
          <!-- Action Buttons -->
          <div style="margin-top:24px; display:flex; align-items:center; gap:12px; justify-content:flex-end;">
            <label for="modalStatus" style="font-size:13px; font-weight:600;">Update Status:</label>
            <select id="modalStatus" style="padding:8px 12px; border:1px solid #ddd; border-radius:4px; font-size:13px;">
              <option value="pending_review">Pending Review</option>
              <option value="scheduled">Scheduled</option>
              <option value="hired">Hired</option>
              <option value="rejected">Rejected</option>
            </select>
            <button id="modalUpdateBtn" style="background:#06b6d4;border:0;padding:10px 16px;border-radius:6px;color:#fff;cursor:pointer;font-size:13px;font-weight:600;">Update Status</button>
          </div>
        </div>
      </div>`;

      document.body.insertAdjacentHTML('beforeend', modalHtml);
      const modal = document.getElementById('applicantModal');
      const modalClose = document.getElementById('applicantModalClose');
      const modalFirstName = document.getElementById('modalFirstName');
      const modalLastName = document.getElementById('modalLastName');
      const modalEmail = document.getElementById('modalEmail');
      const modalMobile = document.getElementById('modalMobile');
      const modalBirthdate = document.getElementById('modalBirthdate');
      const modalHouse = document.getElementById('modalHouse');
      const modalBarangay = document.getElementById('modalBarangay');
      const modalMunicipal = document.getElementById('modalMunicipal');
      const modalProvince = document.getElementById('modalProvince');
      const modalServiceOffer = document.getElementById('modalServiceOffer');
      const modalStatus = document.getElementById('modalStatus');
      const modalUpdateBtn = document.getElementById('modalUpdateBtn');

      let currentRow = null;

      // Event delegation: open modal when clicking arrow cell
      tableBody.addEventListener('click', function(e){
        const td = e.target.closest('td');
        if (!td) return;
        if (!td.classList.contains('arrow')) return;
        const tr = td.closest('tr');
        if (!tr) return;
        currentRow = tr;
        // Extract basic fields (id, name, applied, status). Additional fields left blank unless your backend provides them.
        const cols = tr.querySelectorAll('td');
        const id = cols[0] ? cols[0].textContent.trim() : '';
        const name = cols[1] ? cols[1].textContent.trim() : '';
        const applied = cols[2] ? cols[2].textContent.trim() : '';
        const statusText = cols[3] ? cols[3].textContent.trim() : '';

        // Try splitting name into parts (best-effort)
        let first = name, last = '';
        const parts = name.split(' ');
        if (parts.length >= 2) { last = parts.pop(); first = parts.shift(); }

        // Populate form fields
        modalFirstName.textContent = first;
        modalLastName.textContent = last;
        modalEmail.textContent = '-';
        modalMobile.textContent = '-';
        modalBirthdate.textContent = '-';
        modalHouse.textContent = '-';
        modalBarangay.textContent = '-';
        modalMunicipal.textContent = '-';
        modalProvince.textContent = '-';
        modalServiceOffer.value = '';

        // Set select to current status if we can map it
        const mapLabelToValue = {
          'pending review': 'pending_review',
          'scheduled': 'scheduled',
          'hired': 'hired',
          'rejected': 'rejected'
        };
        const sKey = statusText.toLowerCase();
        const val = mapLabelToValue[sKey] || (sKey.replace(/\s+/g,'_')) || 'pending_review';
        modalStatus.value = val;

        // Show modal
        modal.style.display = 'flex';
      });

      function closeModal(){ modal.style.display = 'none'; currentRow = null; }
      modalClose.addEventListener('click', closeModal);
      modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

      // Update status: call API, then reload the current tab list to reflect movement
      modalUpdateBtn.addEventListener('click', async function(){
        if (!currentRow) return;
        const cols = currentRow.querySelectorAll('td');
        const idText = cols[0] ? cols[0].textContent.trim() : '';
        const idNum = parseInt(idText, 10);
        const newStatus = modalStatus.value;
        try {
          if (!isNaN(idNum)) {
            await fetch('/api/admin/applicants/'+idNum+'/status', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json', 'Accept':'application/json' },
              body: JSON.stringify({ status: newStatus })
            });
          }
        } catch(err) { /* non-fatal */ }
        closeModal();
        const activeTab = document.querySelector('.tabs .tab.active');
        if (activeTab) { activeTab.click(); }
      });
    })();
  </script>
</body>
</html>


