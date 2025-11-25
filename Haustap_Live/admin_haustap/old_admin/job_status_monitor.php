<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - Job Status Monitor</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="css/job_status_monitor.css" />
<script src="js/lazy-images.js" defer></script></head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <?php $active = 'job_status'; include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Topbar -->
      <header class="topbar">
        <h3>Job Status Monitor</h3>
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

      <!-- Status summary cards -->
      <section class="status-cards">
        <div class="card pending">
          <i class="fa-regular fa-clock"></i>
          <div>
            <h3>4</h3>
            <p>Pending</p>
          </div>
        </div>
        <div class="card ongoing">
          <i class="fa-solid fa-spinner"></i>
          <div>
            <h3>5</h3>
            <p>Ongoing</p>
          </div>
        </div>
        <div class="card completed">
          <i class="fa-solid fa-check"></i>
          <div>
            <h3>3</h3>
            <p>Completed</p>
          </div>
        </div>
        <div class="card cancelled">
          <i class="fa-solid fa-xmark"></i>
          <div>
            <h3>1</h3>
            <p>Cancelled</p>
          </div>
        </div>
        <div class="card return">
          <i class="fa-solid fa-rotate-left"></i>
          <div>
            <h3>1</h3>
            <p>Return</p>
          </div>
        </div>
      </section>

      <!-- Table -->
      <section class="table-section">
        <h3>Real-time Job Status</h3>
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Booking Id</th>
                <th>Provider</th>
                <th>Client</th>
                <th>Service</th>
                <th>Date & Time</th>
                <th>Status</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>Ramon Ang</td>
                <td>Juan Dela Cruz</td>
                <td>Home Cleaning</td>
                <td>2025-06-07 14:30</td>
                <td><span class="status completed">Completed</span></td>
                <td class="open-completed">></td>
              </tr>
              <tr>
                <td>2</td>
                <td>Juan Dela Cruz</td>
                <td>Ramon Ang</td>
                <td>Plumbing</td>
                <td>2025-06-07 14:30</td>
                <td><span class="status ongoing">Ongoing</span></td>
                <td class="open-ongoing">></td>
              </tr>
              <tr>
                <td>3</td>
                <td>Calvin Tyler</td>
                <td>Maria Dasco</td>
                <td>Pest Control</td>
                <td>2025-06-07 14:30</td>
                <td><span class="status ongoing">Ongoing</span></td>
                <td class="open-ongoing">></td>
              </tr>
              <td>4</td>
                <td>Calvin Tyler</td>
                <td>Maria Dasco</td>
                <td>Pest Control</td>
                <td>2025-06-07 14:30</td>
                <td><span class="status pending">Pending</span></td>
                <td class="open-pending">></td>
              </tr>
              <td>5</td>
                <td>Calvin Tyler</td>
                <td>Maria Dasco</td>
                <td>Pest Control</td>
                <td>2025-06-07 14:30</td>
                <td><span class="status cancelled">Cancelled</span></td>
                <td class="open-cancelled">></td>
              </tr>
              <td>2</td>
                <td>Juan Dela Cruz</td>
                <td>Ramon Ang</td>
                <td>Plumbing</td>
                <td>2025-06-07 14:30</td>
                <td><span class="status return">Return</span></td>
                <td class="open-return">></td>
              </tr>
            </tbody>
          </table>

          <div class="pagination">
            <span>[ ◀ Prev ]</span>
            <p>Showing 10–10 of 120 Clients</p>
            <span>[ Next ▶ ]</span>
          </div>
        </div>
      </section>
    </main>
  </div>

  <!-- Completed Popup -->
  <div class="popup-overlay" id="completedPopup">
    <div class="popup-content">
      <div class="popup-header">
        <span class="status completed">Completed</span>
        <button class="close-popup" id="closeCompletedPopup">&times;</button>
      </div>
      <div class="popup-body">
        <p><strong>Service Provider:</strong> Ana Santos</p>
        <p><strong>Client:</strong> Jenn Bornilla</p>
        <p class="service-title">Home Cleaning - Bungalow - Basic Cleaning</p>
        <div class="booking-info">
          <div>
            <p class="label">Date</p>
            <p>May 21, 2025</p>
          </div>
          <div>
            <p class="label">Time</p>
            <p>8:00 AM</p>
          </div>
        </div>
        <p class="label">Address</p>
        <p>B1 L50 Mango st. Phase 1 Saint Joseph Village 10 Barangay Langgam, City of San Pedro, Laguna 4023</p>
        <hr>
        <p class="label">Selected:</p>
        <p><strong>Bungalow 80–150 sqm</strong><br>Basic Cleaning – 1 Cleaner</p>
        <p class="label">Inclusions:</p>
        <p class="inclusion">
          Living Room: walis, mop, dusting furniture, trash removal,<br>
          Bedrooms: bed making, sweeping, dusting, trash removal,<br>
          Hallways: mop & sweep, remove cobwebs,<br>
          Windows & Mirrors: quick wipe
        </p>
        <div class="voucher-box">
          <i class="fa-solid fa-ticket"></i>
          <span>No voucher added</span>
        </div>
        <div class="payment-summary">
          <div class="line-item"><span>Sub Total</span><span>₱1,000.00</span></div>
          <div class="line-item"><span>Voucher Discount</span><span>₱0</span></div>
          <div class="line-item total"><span>TOTAL</span><span>₱1,000.00</span></div>
          <p class="note">Full payment will be collected directly by the service provider upon completion of the service.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Ongoing Popup -->
  <div class="popup-overlay" id="ongoingPopup">
    <div class="popup-content">
      <div class="popup-header">
        <span class="status ongoing">Ongoing</span>
        <button class="close-popup" id="closeOngoingPopup">&times;</button>
      </div>
      <div class="popup-body">
        <p><strong>Service Provider:</strong> Ana Santos</p>
        <p><strong>Client:</strong> Jenn Bornilla</p>
        <p class="service-title">Home Cleaning - Bungalow - Basic Cleaning</p>
        <div class="booking-info">
          <div>
            <p class="label">Date</p>
            <p>May 21, 2025</p>
          </div>
          <div>
            <p class="label">Time</p>
            <p>8:00 AM</p>
          </div>
        </div>
        <p class="label">Address</p>
        <p>B1 L50 Mango st. Phase 1 Saint Joseph Village 10 Barangay Langgam, City of San Pedro, Laguna 4023</p>
        <hr>
        <p class="label">Selected:</p>
        <p><strong>Bungalow 80–150 sqm</strong><br>Basic Cleaning – 1 Cleaner</p>
        <p class="label">Inclusions:</p>
        <p class="inclusion">
          Living Room: walis, mop, dusting furniture, trash removal,<br>
          Bedrooms: bed making, sweeping, dusting, trash removal,<br>
          Hallways: mop & sweep, remove cobwebs,<br>
          Windows & Mirrors: quick wipe
        </p>
        <p class="label">Notes:</p>
        <textarea placeholder="Enter notes here..."></textarea>
        <div class="voucher-box">
          <i class="fa-solid fa-ticket"></i>
          <span>No voucher added</span>
        </div>
        <div class="payment-summary">
          <div class="line-item"><span>Sub Total</span><span>₱1,000.00</span></div>
          <div class="line-item"><span>Voucher Discount</span><span>₱0</span></div>
          <div class="line-item total"><span>TOTAL</span><span>₱1,000.00</span></div>
          <p class="note">Full payment will be collected directly by the service provider upon completion of the service.</p>
        </div>
        <div class="uploaded-photo">
          <h4>Upload Photo</h4>
          <div class="photos">
            <div>
              <p>Before</p>
              <div class="upload-box"><i class="fa-solid fa-upload"></i></div>
            </div>
            <div>
              <p>After</p>
              <div class="upload-box"><i class="fa-solid fa-upload"></i></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

    <!-- Pending Popup -->
  <div class="popup-overlay" id="pendingPopup">
    <div class="popup-content">
      <div class="popup-header">
        <span class="status pending">Pending</span>
        <button class="close-popup" id="closePendingPopup">&times;</button>
      </div>
      <div class="popup-body">
        <p><strong>Client:</strong> Jenn Bornilla</p>
        <p class="service-title">Home Cleaning - Bungalow - Basic Cleaning</p>
        <div class="booking-info">
          <div>
            <p class="label">Date</p>
            <p>May 22, 2025</p>
          </div>
          <div>
            <p class="label">Time</p>
            <p>8:00 AM</p>
          </div>
        </div>
        <p class="label">Address</p>
        <p>B1 L50 Mango St. Phase 1 Saint Joseph Village 10 Barangay Langgam, City of San Pedro, Laguna 4023</p>
        <hr>
        <p class="label">Selected:</p>
        <p><strong>Bungalow 80–150 sqm</strong><br>Basic Cleaning – 1 Cleaner</p>
        <p class="label">Inclusions:</p>
        <p class="inclusion">
          Living Room: walls, mop, dusting furniture, trash removal,<br>
          Bedrooms: bed making, sweeping, dusting, trash removal,<br>
          Hallways: mop & sweep, remove cobwebs,<br>
          Windows & Mirrors: quick wipe
        </p>
        <p class="label">Notes:</p>
        <textarea placeholder="Enter notes here..."></textarea>
        <div class="voucher-box">
          <i class="fa-solid fa-ticket"></i>
          <span>No voucher added</span>
        </div>
        <div class="payment-summary">
          <div class="line-item"><span>Sub Total</span><span>₱1,000.00</span></div>
          <div class="line-item"><span>Voucher Discount</span><span>₱0</span></div>
          <div class="line-item total"><span>TOTAL</span><span>₱1,000.00</span></div>
          <p class="note">Full payment will be collected directly by the service provider upon completion of the service.</p>
        </div>
        <div class="uploaded-photo">
          <h4>Upload Photo</h4>
          <div class="photos">
            <div>
              <p>Before</p>
              <div class="upload-box"><i class="fa-solid fa-upload"></i></div>
            </div>
            <div>
              <p>After</p>
              <div class="upload-box"><i class="fa-solid fa-upload"></i></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

    <!-- Cancelled Popup -->
<div class="popup-overlay" id="cancelledPopup">
  <div class="popup-content">
    <div class="popup-header">
      <span class="status cancelled">Cancelled</span>
      <button class="close-popup" id="closeCancelledPopup">&times;</button>
    </div>
    <div class="popup-body">
      <p><strong>Client:</strong> Jenn Bornilla</p>
      <p class="service-title">Home Cleaning - Bungalow - Basic Cleaning</p>
      <div class="booking-info">
        <div>
          <p class="label">Date</p>
          <p>May 22, 2025</p>
        </div>
        <div>
          <p class="label">Time</p>
          <p>8:00 AM</p>
        </div>
      </div>
      <p class="label">Address</p>
      <p>B1 L50 Mango St. Phase 1 Saint Joseph Village 10 Barangay Langgam, City of San Pedro, Laguna 4023</p>
      <hr>
      <p class="label">Selected:</p>
      <p><strong>Bungalow 80–150 sqm</strong><br>Basic Cleaning – 1 Cleaner</p>
      <p class="label">Inclusions:</p>
      <p class="inclusion">
        Living Room: walls, mop, dusting furniture, trash removal,<br>
        Bedrooms: bed making, sweeping, dusting, trash removal,<br>
        Hallways: mop & sweep, remove cobwebs,<br>
        Windows & Mirrors: quick wipe
      </p>
      <p class="label">Notes:</p>
      <textarea placeholder="Enter notes here..."></textarea>
      <div class="voucher-box">
        <i class="fa-solid fa-ticket"></i>
        <span>No voucher added</span>
      </div>
      <div class="payment-summary">
        <div class="line-item"><span>Sub Total</span><span>₱1,000.00</span></div>
        <div class="line-item"><span>Voucher Discount</span><span>₱0</span></div>
        <div class="line-item total"><span>TOTAL</span><span>₱1,000.00</span></div>
        <p class="note">Full payment will be collected directly by the service provider upon completion of the service.</p>
      </div>

      <!-- Cancellation Details -->
      <div class="cancel-details">
        <h4>Cancellation Details</h4>
        <div class="cancel-info">
          <div><p><strong>Date:</strong> May 22, 2025</p></div>
          <div><p><strong>Time:</strong> 8:00 AM</p></div>
        </div>
        <p><strong>Reason:</strong> Change of Schedule</p>
        <p><strong>Description:</strong> Sorry po, namali ako schedule</p>
      </div>
    </div>
  </div>
</div>



    <!-- Return Popup -->
<div class="popup-overlay" id="returnPopup">
  <div class="popup-content">
    <div class="popup-header">
      <span class="status return">Return</span>
      <button class="close-popup" id="closeReturnPopup">&times;</button>
    </div>
    <div class="popup-body">
      <p><strong>Service Provider:</strong> Ana Santos</p>
      <p><strong>Client:</strong> Jenn Bornilla</p>
      <p class="service-title">Home Cleaning - Bungalow - Basic Cleaning</p>
      <div class="booking-info">
        <div>
          <p class="label">Date</p>
          <p>May 22, 2025</p>
        </div>
        <div>
          <p class="label">Time</p>
          <p>8:00 AM</p>
        </div>
      </div>
      <p class="label">Address</p>
      <p>B1 L50 Mango St. Phase 1 Saint Joseph Village 10 Barangay Langgam, City of San Pedro, Laguna 4023</p>
      <hr>
      <p class="label">Selected:</p>
      <p><strong>Bungalow 80–150 sqm</strong><br>Basic Cleaning – 1 Cleaner</p>
      <p class="label">Inclusions:</p>
      <p class="inclusion">
        Living Room: walls, mop, dusting furniture, trash removal,<br>
        Bedrooms: bed making, sweeping, dusting, trash removal,<br>
        Hallways: mop & sweep, remove cobwebs,<br>
        Windows & Mirrors: quick wipe
      </p>
      <p class="label">Notes:</p>
      <textarea placeholder="Enter notes here..."></textarea>
      <div class="voucher-box">
        <i class="fa-solid fa-ticket"></i>
        <span>No voucher added</span>
      </div>
      <div class="payment-summary">
        <div class="line-item"><span>Sub Total</span><span>₱1,000.00</span></div>
        <div class="line-item"><span>Voucher Discount</span><span>₱0</span></div>
        <div class="line-item total"><span>TOTAL</span><span>₱1,000.00</span></div>
        <p class="note">Full payment will be collected directly by the service provider upon completion of the service.</p>
      </div>

      <!-- Return Details -->
      <div class="return-details">
        <h4>Return Reason</h4>
        <div class="return-info">
          <div><p><strong>Date:</strong> May 22, 2025</p></div>
          <div><p><strong>Time:</strong> 8:00 AM</p></div>
        </div>
        <p><strong>Reason:</strong> Unsatisfactory Service</p>
        <p><strong>Description:</strong> The quality of the service did not meet the expected standards or description.</p>

        <div class="return-actions">
          <button class="reject-btn">Reject Return</button>
          <button class="approve-btn">Approve Return</button>
        </div>
      </div>
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

    // Popup logic
    const completedPopup = document.getElementById("completedPopup");
    const ongoingPopup = document.getElementById("ongoingPopup");
    const closeCompletedPopup = document.getElementById("closeCompletedPopup");
    const closeOngoingPopup = document.getElementById("closeOngoingPopup");

    document.querySelectorAll(".open-completed").forEach(btn => {
      btn.addEventListener("click", () => completedPopup.classList.add("show"));
    });

    document.querySelectorAll(".open-ongoing").forEach(btn => {
      btn.addEventListener("click", () => ongoingPopup.classList.add("show"));
    });

    closeCompletedPopup.addEventListener("click", () => completedPopup.classList.remove("show"));
    closeOngoingPopup.addEventListener("click", () => ongoingPopup.classList.remove("show"));

        const pendingPopup = document.getElementById("pendingPopup");
    const closePendingPopup = document.getElementById("closePendingPopup");

    document.querySelectorAll(".open-pending").forEach(btn => {
      btn.addEventListener("click", () => pendingPopup.classList.add("show"));
    });

    closePendingPopup.addEventListener("click", () => pendingPopup.classList.remove("show"));

    // Cancelled Popup Logic
const cancelledPopup = document.getElementById("cancelledPopup");
const closeCancelledPopup = document.getElementById("closeCancelledPopup");

document.querySelectorAll(".open-cancelled").forEach(btn => {
  btn.addEventListener("click", () => cancelledPopup.classList.add("show"));
});

closeCancelledPopup.addEventListener("click", () => cancelledPopup.classList.remove("show"));

// Return Popup Logic
const returnPopup = document.getElementById("returnPopup");
const closeReturnPopup = document.getElementById("closeReturnPopup");

document.querySelectorAll(".open-return").forEach(btn => {
  btn.addEventListener("click", () => returnPopup.classList.add("show"));
});

closeReturnPopup.addEventListener("click", () => returnPopup.classList.remove("show"));

  </script>
</body>
</html>



