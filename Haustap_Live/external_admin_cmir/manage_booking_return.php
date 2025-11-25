<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Bookings - Return</title>
  <link rel="stylesheet" href="css/manage_booking_return.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    /* Return Modal Styles */
    .return-modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.45); display: none; align-items: center; justify-content: center; z-index: 1200; overflow-y: auto; }
    .return-modal-backdrop.show { display: flex; }
    .return-modal-content { background: #fff; border-radius: 8px; width: 600px; max-width: 95%; padding: 30px; box-shadow: 0 8px 30px rgba(0,0,0,0.2); font-family: Arial,sans-serif; max-height: 90vh; overflow-y: auto; margin: 20px auto; position: relative; }
    .return-modal-content .close-btn { position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 28px; cursor: pointer; color: #999; }
    .return-modal-content .close-btn:hover { color: #333; }
    .modal-header { margin-bottom: 24px; }
    .return-badge { display: inline-block; background: #fff3e0; color: #e65100; padding: 8px 14px; border-radius: 20px; font-weight: 600; font-size: 12px; text-transform: uppercase; }
    .info-section { margin-bottom: 24px; }
    .info-section-title { font-size: 13px; font-weight: 700; color: #000; text-transform: uppercase; margin-bottom: 12px; }
    .info-grid { display: grid; grid-template-columns: 120px 1fr; gap: 16px; }
    .info-label { font-size: 13px; color: #666; font-weight: 600; }
    .info-value { font-size: 13px; color: #000; line-height: 1.4; }
    textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: Arial,sans-serif; font-size: 13px; resize: vertical; min-height: 80px; }
    .voucher-section { background: #f5f5f5; padding: 12px; border-radius: 6px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; }
    .voucher-icon { font-size: 24px; }
    .voucher-text { font-size: 13px; color: #666; }
    .pricing-section { background: #f5f5f5; padding: 16px; border-radius: 6px; margin-bottom: 24px; }
    .pricing-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px; }
    .pricing-row.total { font-weight: 700; color: #000; border-top: 1px solid #ddd; padding-top: 8px; margin-top: 8px; }
    .payment-note { font-size: 12px; color: #666; margin-top: 16px; padding-top: 16px; border-top: 1px solid #e0e0e0; }
    .divider { height: 1px; background: #e0e0e0; margin: 24px 0; }
    .modal-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 24px; }
    .modal-actions button { padding: 10px 16px; border-radius: 6px; border: 0; cursor: pointer; font-size: 13px; font-weight: 600; }
    .btn-approve { background: #2ecc71; color: #fff; }
    .btn-approve:hover { background: #27ae60; }
    .btn-decline { background: #e74c3c; color: #fff; }
    .btn-decline:hover { background: #c0392b; }
    .btn-close { background: #f0f0f0; color: #111; }
    .btn-close:hover { background: #ddd; }
  </style>
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

      <section class="content">
        <!-- Tabs -->
        <div class="tabs">
          <button>All</button>
          <button>Pending</button>
          <button>Ongoing</button>
          <button>Completed</button>
          <button>Cancelled</button>
          <button class="active">Return</button>
        </div>

        <!-- Search and Filter -->
<div class="search-filter">
  <input type="text" placeholder="Search">

  <div class="filter-dropdown">
<button class="filter-btn"><i class="fa-solid fa-sliders"></i> Filter</button>
    <div class="dropdown-content">
            <p class="filter-title">Filter by Status</p>
            <div class="checkbox-group">
              <label><input type="checkbox" value="pending" checked> Approved</label>
              <label><input type="checkbox" value="ongoing" checked> Pending</label>
              <label><input type="checkbox" value="complete" checked> Declined</label>
            <button class="apply-btn">Apply</button>
          </div>
        </div>
      </div>

        <!-- Table -->
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Booking Id</th>
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
                <td>Juan Dela Cruz</td>
                <td>Ramon Ang</td>
                <td>Home Cleaning</td>
                <td>2025-06-07 14:30</td>
                <td><span class="status approved">Approved</span></td>
                <td>&gt;</td>
              </tr>
              <tr>
                <td>2</td>
                <td>Ramon Ang</td>
                <td>Juan Dela Cruz</td>
                <td>Home Cleaning</td>
                <td>2025-06-07 14:30</td>
                <td><span class="status pending">Pending</span></td>
                <td>&gt;</td>
              </tr>
              <tr>
                <td>3</td>
                <td>Cj Pogi</td>
                <td>Juan Dela Cruz</td>
                <td>Home Cleaning</td>
                <td>2025-06-07 14:30</td>
                <td><span class="status declined">Declined</span></td>
                <td>&gt;</td>
              </tr>
            </tbody>
          </table>

          <div class="pagination">
            <span>[◄ Prev]</span>
            <span>Showing 2–10 of 120 Clients</span>
            <span>[Next ►]</span>
          </div>
        </div>
      </section>
    </main>
  </div>

  <!-- Return Details Modal -->
  <div id="returnModal" class="return-modal-backdrop">
    <div class="return-modal-content">
      <span class="close-btn">&times;</span>
      <div class="modal-header">
        <span class="return-badge">Return</span>
      </div>

      <div class="info-section">
        <div class="info-grid">
          <div class="info-label">Service Provider:</div>
          <div class="info-value" id="mdProvider"></div>
          <div class="info-label">Client:</div>
          <div class="info-value" id="mdClient"></div>
          <div class="info-label">Service:</div>
          <div class="info-value" id="mdService"></div>
        </div>
      </div>

      <div class="info-section">
        <div class="info-grid">
          <div class="info-label">Date:</div>
          <div class="info-value" id="mdDate"></div>
        </div>
      </div>

      <div class="info-section">
        <div class="info-label">Address:</div>
        <div class="info-value" id="mdAddress"></div>
      </div>

      <div class="info-section">
        <div class="info-label">Selected:</div>
        <div class="info-value" id="mdSelected"></div>
      </div>

      <div class="info-section">
        <div class="info-label">Inclusions:</div>
        <div class="info-value" id="mdInclusions"></div>
      </div>

      <div class="info-section">
        <div class="info-label">Notes:</div>
        <textarea id="mdNotes" readonly></textarea>
      </div>

      <div class="voucher-section" id="mdVoucherSection">
        <div class="voucher-icon">🎟️</div>
        <div class="voucher-text" id="mdVoucherText"></div>
      </div>

      <div class="pricing-section">
        <div class="pricing-row">
          <span>Sub Total</span>
          <span id="mdSubTotal">₱0.00</span>
        </div>
        <div class="pricing-row">
          <span>Voucher Discount</span>
          <span id="mdDiscount">₱0</span>
        </div>
        <div class="pricing-row total">
          <span>TOTAL</span>
          <span id="mdTotal">₱0.00</span>
        </div>
        <div class="payment-note">Full payment will be collected directly by the service provider upon completion of the service.</div>
      </div>

      <div id="mdReturnReason" style="display:none">
        <div class="divider"></div>
        <div class="info-section-title">Return Reason</div>
        <div class="info-grid">
          <div class="info-label">Date:</div>
          <div class="info-value" id="mdReturnDate"></div>
          <div class="info-label">Reason:</div>
          <div class="info-value" id="mdReturnReasonText"></div>
          <div class="info-label">Description:</div>
          <div class="info-value" id="mdReturnDesc"></div>
        </div>
      </div>

      <div class="modal-actions">
        <button class="btn-approve" id="mdApproveBtn">Approve</button>
        <button class="btn-close" id="mdCloseBtn">Close</button>
      </div>
    </div>
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

    // Modal functionality for Return bookings
    (function(){
      const modal = document.getElementById('returnModal');
      const closeBtn = document.querySelector('.return-modal-content .close-btn');
      const mdCloseBtn = document.getElementById('mdCloseBtn');
      const actionButtons = document.querySelectorAll('tbody tr td:last-child');

      // Open modal on ">" button click
      actionButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.stopPropagation();
          const row = btn.closest('tr');
          if (!row) return;

          const cells = row.querySelectorAll('td');
          const bookingId = cells[0]?.textContent.trim() || '';
          const client = cells[1]?.textContent.trim() || '';
          const provider = cells[2]?.textContent.trim() || '';
          const service = cells[3]?.textContent.trim() || '';
          const dateTime = cells[4]?.textContent.trim() || '';
          const date = (dateTime.split(' ')[0]) || dateTime || '';

          // Populate modal
          document.getElementById('mdProvider').textContent = provider;
          document.getElementById('mdClient').textContent = client;
          document.getElementById('mdService').textContent = service;
          document.getElementById('mdDate').textContent = date;
          // Also populate the return reason date field if present
          const mdReturnDateEl = document.getElementById('mdReturnDate');
          if (mdReturnDateEl) mdReturnDateEl.textContent = date;
          document.getElementById('mdAddress').textContent = 'B1 L50 Mango st. Phase 1 Saint Joseph Village 10 Barangay Langgam, City of San Pedro, Laguna 4023';
          document.getElementById('mdSelected').textContent = 'Bungalow 80-150 sqm\nBasic Cleaning - 1 Cleaner';
          document.getElementById('mdInclusions').textContent = 'Living Room: walls, mop, dusting furniture, trash removal, Bedrooms: bed making, sweeping, dusting, trash removal, Hallways: mop & sweep, remove cobwebs, Windows & Mirrors: quick wipe';
          document.getElementById('mdNotes').value = '';
          document.getElementById('mdVoucherText').textContent = 'No voucher added';
          document.getElementById('mdSubTotal').textContent = '₱1,000.00';
          document.getElementById('mdDiscount').textContent = '₱0';
          document.getElementById('mdTotal').textContent = '₱1,000.00';
          document.getElementById('mdReturnReasonText').textContent = 'Unsatisfactory Service';
          document.getElementById('mdReturnDesc').textContent = 'The quality of the service did not meet the expected standards or description.';
          document.getElementById('mdReturnReason').style.display = 'block';

          modal.classList.add('show');
        });
      });

      // Close modal
      const closeModal = () => modal.classList.remove('show');
      closeBtn.addEventListener('click', closeModal);
      mdCloseBtn.addEventListener('click', closeModal);
      modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
      });
      window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
      });

      // Approve button
      document.getElementById('mdApproveBtn').addEventListener('click', () => {
        alert('Return request approved successfully!');
        closeModal();
      });

      // Note: Decline/Reject button removed by request to simplify return modal actions
    })();
    
    // Date filter: show rows within selected date range (use dataset flags so it composes with other filters)
    (function(){
      const fromInput = document.getElementById('from-date');
      const toInput = document.getElementById('to-date');
      const applyBtn = document.querySelector('.apply-btn');

      function parseRowDate(text){
        if (!text) return null;
        const m = text.match(/(\d{4})\s*-\s*(\d{2})\s*-\s*(\d{2})/);
        if (!m) return null;
        const iso = `${m[1]}-${m[2]}-${m[3]}`;
        const d = new Date(iso);
        return isNaN(d.getTime()) ? null : d;
      }

      function updateRowVisibility(){
        const rows = document.querySelectorAll('.table-container tbody tr');
        rows.forEach(row => {
          const fHidden = row.dataset.filterHidden === 'true';
          const sHidden = row.dataset.searchHidden === 'true';
          row.style.display = (fHidden || sHidden) ? 'none' : '';
        });
      }

      function applyDateFilter(){
        const fromVal = fromInput ? fromInput.value : '';
        const toVal = toInput ? toInput.value : '';
        const fromDate = fromVal ? new Date(fromVal) : null;
        const toDateRaw = toVal ? new Date(toVal) : null;
        const toDate = toDateRaw ? new Date(toDateRaw.setHours(23,59,59,999)) : null;

        const rows = document.querySelectorAll('.table-container tbody tr');
        rows.forEach(row => {
          const dateCell = row.querySelector('td:nth-child(5)');
          const rowDate = parseRowDate(dateCell ? dateCell.textContent.trim() : '');
          if (!rowDate) { row.dataset.filterHidden = ''; return; }
          const within = (!fromDate || rowDate >= fromDate) && (!toDate || rowDate <= toDate);
          row.dataset.filterHidden = within ? '' : 'true';
        });
        updateRowVisibility();
      }

      if (applyBtn) applyBtn.addEventListener('click', (e) => { e.preventDefault(); applyDateFilter(); if (dropdownContent) dropdownContent.classList.remove('show'); });
      updateRowVisibility();
    })();
  </script>
</body>
</html>


