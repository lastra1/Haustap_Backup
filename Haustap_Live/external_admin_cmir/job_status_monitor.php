<?php require_once __DIR__ . '/includes/auth.php'; ?>
<?php
// Fetch connected data from storage
$bookings = [];
$providers = [];
$clients = [];

// Load providers data
$providersPath = realpath(__DIR__ . '/../../storage/data/providers.json');
if ($providersPath && is_file($providersPath)) {
  $raw = @file_get_contents($providersPath);
  $providers = json_decode($raw ?: '[]', true) ?: [];
  // Index by ID for quick lookup
  $providers = array_reduce($providers, function($carry, $item) {
    if (isset($item['id'])) {
      $carry[$item['id']] = $item;
    }
    return $carry;
  }, []);
}

// Load clients data
$clientsPath = realpath(__DIR__ . '/../../storage/data/clients.json');
if ($clientsPath && is_file($clientsPath)) {
  $raw = @file_get_contents($clientsPath);
  $clients = json_decode($raw ?: '[]', true) ?: [];
  // Index by ID for quick lookup
  $clients = array_reduce($clients, function($carry, $item) {
    if (isset($item['id'])) {
      $carry[$item['id']] = $item;
    }
    return $carry;
  }, []);
}

// Load bookings data
$bookingsPath = realpath(__DIR__ . '/../../backend/api/storage/data/bookings.json');
if ($bookingsPath && is_file($bookingsPath)) {
  $raw = @file_get_contents($bookingsPath);
  $allBookings = json_decode($raw ?: '[]', true) ?: [];
  
  // Enrich bookings with provider and client data
  foreach ($allBookings as $booking) {
    if (!isset($booking['id']) || !isset($booking['status'])) continue;
    
    // Get provider info
    $provider = [];
    if (isset($booking['provider_id'], $providers[$booking['provider_id']])) {
      $provider = $providers[$booking['provider_id']];
    }
    
    // Get client info (from booking or infer from context)
    $client = [];
    if (isset($booking['client_id'], $clients[$booking['client_id']])) {
      $client = $clients[$booking['client_id']];
    }
    
    // Enrich the booking
    $booking['provider_name'] = $provider['name'] ?? 'Unknown Provider';
    $booking['provider_id_display'] = $provider['id'] ?? 'N/A';
    $booking['client_name'] = $client['name'] ?? 'Unknown Client';
    $booking['service'] = $booking['service_name'] ?? 'Service';
    $booking['dateTime'] = ($booking['scheduled_date'] ?? '2025-06-07') . ' ' . ($booking['scheduled_time'] ?? '14:30');
    $booking['address'] = $booking['address'] ?? 'Address not provided';
    
    $bookings[] = $booking;
  }
}

// If no real bookings, use sample data for demonstration
if (empty($bookings)) {
  $bookings = [
    ['id' => 1, 'provider_name' => 'Ramon Ang', 'client_name' => 'Juan Dela Cruz', 'service' => 'Home Cleaning', 'dateTime' => '2025-06-07 14:30', 'status' => 'completed', 'address' => 'Address 1', 'price' => 1000],
    ['id' => 2, 'provider_name' => 'Ana Santos', 'client_name' => 'Maria Garcia', 'service' => 'Plumbing', 'dateTime' => '2025-06-08 10:00', 'status' => 'ongoing', 'address' => 'Address 2', 'price' => 1500],
    ['id' => 3, 'provider_name' => 'Juan Cruz', 'client_name' => 'Pedro Santos', 'service' => 'Electrical', 'dateTime' => '2025-06-09 09:00', 'status' => 'pending', 'address' => 'Address 3', 'price' => 2000],
    ['id' => 4, 'provider_name' => 'Carlo Reyes', 'client_name' => 'Lisa Wong', 'service' => 'Cleaning', 'dateTime' => '2025-06-10 11:00', 'status' => 'cancelled', 'address' => 'Address 4', 'price' => 800],
    ['id' => 5, 'provider_name' => 'Rosa Flores', 'client_name' => 'Anna Kim', 'service' => 'Gardening', 'dateTime' => '2025-06-11 08:00', 'status' => 'return', 'address' => 'Address 5', 'price' => 1200],
  ];
}

// Count bookings by status
$statusCounts = array_count_values(array_map(function($b) { return $b['status'] ?? 'unknown'; }, $bookings));
?>
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
            <button id="userDropdownBtn" class="user-dropdown-btn">Mj Punzalan ▼</button>
            <div class="user-dropdown" id="userDropdown">
              <a href="admin_profile.php">View Profile</a>
              <a href="/admin_haustap/admin_haustap/change_password.php">Change Password</a>
              <a href="logout.php" class="logout">Log out</a>
            </div>
          </div>
        </div>
      </header>

      <!-- Status summary cards -->
      <section class="status-cards">
        <div class="card pending">
          <i class="fa-regular fa-clock"></i>
          <div>
            <h3><?php echo $statusCounts['pending'] ?? 0; ?></h3>
            <p>Pending</p>
          </div>
        </div>
        <div class="card ongoing">
          <i class="fa-solid fa-spinner"></i>
          <div>
            <h3><?php echo $statusCounts['ongoing'] ?? 0; ?></h3>
            <p>Ongoing</p>
          </div>
        </div>
        <div class="card completed">
          <i class="fa-solid fa-check"></i>
          <div>
            <h3><?php echo $statusCounts['completed'] ?? 0; ?></h3>
            <p>Completed</p>
          </div>
        </div>
        <div class="card cancelled">
          <i class="fa-solid fa-xmark"></i>
          <div>
            <h3><?php echo $statusCounts['cancelled'] ?? 0; ?></h3>
            <p>Cancelled</p>
          </div>
        </div>
        <div class="card return">
          <i class="fa-solid fa-rotate-left"></i>
          <div>
            <h3><?php echo $statusCounts['return'] ?? 0; ?></h3>
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
        <p><strong>Booking ID:</strong> 1</p>
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
          <div class="line-item"><span>Transportation Fee</span><span>₱50.00</span></div>
          <div class="line-item"><span>Voucher Discount</span><span>₱0</span></div>
          <div class="line-item total"><span>TOTAL</span><span>₱1,050.00</span></div>
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
        <p><strong>Booking ID:</strong> 2</p>
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
          <div class="line-item"><span>Transportation Fee</span><span>₱50.00</span></div>
          <div class="line-item"><span>Voucher Discount</span><span>₱0</span></div>
          <div class="line-item total"><span>TOTAL</span><span>₱1,050.00</span></div>
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
        <p><strong>Booking ID:</strong> 4</p>
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
          <div class="line-item"><span>Transportation Fee</span><span>₱50.00</span></div>
          <div class="line-item"><span>Voucher Discount</span><span>₱0</span></div>
          <div class="line-item total"><span>TOTAL</span><span>₱1,050.00</span></div>
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
      <p><strong>Booking ID:</strong> 5</p>
      <p><strong>Service Provider:</strong> Calvin Tyler</p>
      <p><strong>Client:</strong> Jenn Bornilla</p>
      <p class="service-title">Home Cleaning - Bungalow - Basic Cleaning</p>
      <div class="booking-info">
        <div>
          <p class="label">Date</p>
          <p>May 22, 2025</p>
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
        <div class="line-item"><span>Transportation Fee</span><span>₱50.00</span></div>
        <div class="line-item"><span>Voucher Discount</span><span>₱0</span></div>
        <div class="line-item total"><span>TOTAL</span><span>₱1,050.00</span></div>
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
         <!-- Footer actions for Cancelled popup -->
         <div class="popup-footer cancel-popup-footer">
           <button type="button" class="reject-cancel-btn">Decline Cancellation</button>
           <button type="button" class="approve-cancel-btn primary">Approve Cancellation</button>
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
      <p><strong>Booking ID:</strong> 2</p>
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
        <div class="line-item"><span>Transportation Fee</span><span>₱50.00</span></div>
        <div class="line-item"><span>Voucher Discount</span><span>₱0</span></div>
        <div class="line-item total"><span>TOTAL</span><span>₱1,050.00</span></div>
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
          <button class="approve-btn">Approve Return</button>
        </div>
      </div>
    </div>
  </div>
</div>

  <script>
    // Update status card counts based on table data - call on page load
    function initializeStatusCounts() {
      const rows = document.querySelectorAll('.table-container tbody tr');
      const counts = { pending: 0, ongoing: 0, completed: 0, cancelled: 0, return: 0 };
      
      rows.forEach(row => {
        const statusBadge = row.querySelector('.status');
        if (!statusBadge) return;
        
        const classList = statusBadge.classList;
        if (classList.contains('pending')) counts.pending++;
        else if (classList.contains('ongoing')) counts.ongoing++;
        else if (classList.contains('completed')) counts.completed++;
        else if (classList.contains('cancelled')) counts.cancelled++;
        else if (classList.contains('return')) counts.return++;
      });
      
      // Update the card numbers
      const pendingCard = document.querySelector('.card.pending h3');
      const ongoingCard = document.querySelector('.card.ongoing h3');
      const completedCard = document.querySelector('.card.completed h3');
      const cancelledCard = document.querySelector('.card.cancelled h3');
      const returnCard = document.querySelector('.card.return h3');
      
      if (pendingCard) pendingCard.textContent = counts.pending;
      if (ongoingCard) ongoingCard.textContent = counts.ongoing;
      if (completedCard) completedCard.textContent = counts.completed;
      if (cancelledCard) cancelledCard.textContent = counts.cancelled;
      if (returnCard) returnCard.textContent = counts.return;
    }
    
    // Initialize counts on page load
    window.addEventListener('DOMContentLoaded', initializeStatusCounts);
    
    // Status card filter - connect summary cards to table filtering
    (function(){
      const statusCards = document.querySelectorAll('.status-cards .card');
      let activeFilter = null; // Track which status filter is active
      
      statusCards.forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', () => {
          const statusClass = Array.from(card.classList).find(cls => 
            ['pending', 'ongoing', 'completed', 'cancelled', 'return'].includes(cls)
          );
          
          if (!statusClass) return;
          
          // Toggle filter: if clicking same card, clear filter
          if (activeFilter === statusClass) {
            activeFilter = null;
            card.classList.remove('active');
            // Show all rows
            document.querySelectorAll('.table-container tbody tr').forEach(row => {
              row.style.display = '';
            });
          } else {
            // Remove active state from previous card
            document.querySelectorAll('.status-cards .card.active').forEach(c => {
              c.classList.remove('active');
            });
            // Set new active filter
            activeFilter = statusClass;
            card.classList.add('active');
            // Filter table rows to show only matching status
            document.querySelectorAll('.table-container tbody tr').forEach(row => {
              const statusBadge = row.querySelector('.status');
              if (statusBadge && statusBadge.classList.contains(statusClass)) {
                row.style.display = '';
              } else {
                row.style.display = 'none';
              }
            });
          }
        });
      });
    })();

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

let currentBookingId = null;

document.querySelectorAll('.open-cancelled').forEach(btn => {
  btn.addEventListener('click', (e) => {
    try {
      // remember which booking row opened the popup
      const bid = btn.getAttribute('data-booking-id') || (btn.closest && btn.closest('tr') ? btn.closest('tr').getAttribute('data-booking-id') : null);
      console.log('[job_status_monitor] open-cancelled clicked, booking id=', bid);
      currentBookingId = bid;
      // TODO: populate cancelledPopup fields with booking-specific data if available
      if (cancelledPopup) cancelledPopup.classList.add('show');
      else console.warn('[job_status_monitor] cancelledPopup element not found');
    } catch (err) {
      console.error('[job_status_monitor] error handling open-cancelled click', err);
    }
  });
});

closeCancelledPopup.addEventListener('click', () => {
  cancelledPopup.classList.remove('show');
  currentBookingId = null;
});

// action buttons inside cancelled popup
const approveCancelBtn = cancelledPopup ? cancelledPopup.querySelector('.approve-cancel-btn') : null;
const rejectCancelBtn = cancelledPopup ? cancelledPopup.querySelector('.reject-cancel-btn') : null;

// recompute the counts shown in the status cards based on row badges
function updateStatusCards() {
  const rows = document.querySelectorAll('.table-container tbody tr');
  const counts = { pending:0, ongoing:0, completed:0, cancelled:0, return:0 };
  rows.forEach(r => {
    const badge = r.querySelector('.status');
    if (!badge) return;
    const c = badge.classList;
    if (c.contains('pending')) counts.pending++;
    else if (c.contains('ongoing')) counts.ongoing++;
    else if (c.contains('completed') || c.contains('complete')) counts.completed++;
    else if (c.contains('cancelled')) counts.cancelled++;
    else if (c.contains('return')) counts.return++;
  });
  const cardPending = document.querySelector('.card.pending h3');
  const cardOngoing = document.querySelector('.card.ongoing h3');
  const cardCompleted = document.querySelector('.card.completed h3');
  const cardCancelled = document.querySelector('.card.cancelled h3');
  const cardReturn = document.querySelector('.card.return h3');
  if (cardPending) cardPending.textContent = counts.pending;
  if (cardOngoing) cardOngoing.textContent = counts.ongoing;
  if (cardCompleted) cardCompleted.textContent = counts.completed;
  if (cardCancelled) cardCancelled.textContent = counts.cancelled;
  if (cardReturn) cardReturn.textContent = counts.return;
}

function setBadgeStatus(badge, status, displayText) {
  if (!badge) return;
  const statusClasses = ['pending','ongoing','completed','complete','cancelled','return'];
  statusClasses.forEach(c => badge.classList.remove(c));
  badge.classList.remove('cancel-approved');
  if (status) badge.classList.add(status);
  if (displayText !== undefined && displayText !== null) badge.textContent = displayText;
}

if (approveCancelBtn) approveCancelBtn.addEventListener('click', () => {
  try {
    console.log('[job_status_monitor] approveCancelBtn clicked, currentBookingId=', currentBookingId);
    if (!currentBookingId) {
      alert('No booking selected for approve cancellation');
      return;
    }
    if (!confirm(`Approve cancellation for booking #${currentBookingId}?`)) return;
    const row = document.querySelector(`tr[data-booking-id="${currentBookingId}"]`);
    if (!row) {
      console.warn('Booking row not found for id', currentBookingId);
      if (cancelledPopup) cancelledPopup.classList.remove('show');
      currentBookingId = null;
      return;
    }
    const badge = row.querySelector('.status');
    setBadgeStatus(badge, 'cancelled', 'Cancelled (Approved)');
    if (badge) badge.classList.add('cancel-approved');
    row.classList.add('row-cancel-approved');
    if (cancelledPopup) cancelledPopup.classList.remove('show');
    
    // Save notification to database
    const notification = {
      message: `Cancellation approved for booking #${currentBookingId}`,
      href: 'job_status_monitor.php',
      created_at: new Date().toISOString(),
      read: false,
      type: 'cancellation_approved'
    };
    fetch('/admin_haustap/admin_haustap/api/save-notification.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(notification)
    }).catch(err => console.warn('Could not save notification', err));
    
    alert(`Cancellation approved for booking #${currentBookingId}`);
    currentBookingId = null;
    updateStatusCards();
    console.log('[job_status_monitor] approve cancellation applied for booking', currentBookingId);
  } catch (err) {
    console.error('[job_status_monitor] error in approveCancelBtn handler', err);
    alert('Error approving cancellation: ' + err.message);
  }
});

if (rejectCancelBtn) rejectCancelBtn.addEventListener('click', () => {
  try {
    console.log('[job_status_monitor] rejectCancelBtn clicked, currentBookingId=', currentBookingId);
    if (!currentBookingId) {
      alert('No booking selected for reject cancellation');
      return;
    }
    if (!confirm(`Reject cancellation for booking #${currentBookingId}?`)) return;
    const row = document.querySelector(`tr[data-booking-id="${currentBookingId}"]`);
    if (!row) {
      console.warn('Booking row not found for id', currentBookingId);
      if (cancelledPopup) cancelledPopup.classList.remove('show');
      currentBookingId = null;
      return;
    }
    const badge = row.querySelector('.status');
    setBadgeStatus(badge, 'ongoing', 'Ongoing');
    row.classList.remove('row-cancel-approved');
    if (cancelledPopup) cancelledPopup.classList.remove('show');
    
    // Save notification to database
    const notification = {
      message: `Cancellation declined for booking #${currentBookingId}`,
      href: 'job_status_monitor.php',
      created_at: new Date().toISOString(),
      read: false,
      type: 'cancellation_declined'
    };
    fetch('/admin_haustap/admin_haustap/api/save-notification.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(notification)
    }).catch(err => console.warn('Could not save notification', err));
    
    alert(`Cancellation rejected for booking #${currentBookingId}`);
    currentBookingId = null;
    updateStatusCards();
    console.log('[job_status_monitor] reject cancellation applied for booking', currentBookingId);
  } catch (err) {
    console.error('[job_status_monitor] error in rejectCancelBtn handler', err);
    alert('Error rejecting cancellation: ' + err.message);
  }
});

// Return Popup Logic
const returnPopup = document.getElementById("returnPopup");
const closeReturnPopup = document.getElementById("closeReturnPopup");

document.querySelectorAll('.open-return').forEach(btn => {
  btn.addEventListener('click', (e) => {
    try {
      const bid = btn.getAttribute('data-booking-id') || (btn.closest && btn.closest('tr') ? btn.closest('tr').getAttribute('data-booking-id') : null);
      console.log('[job_status_monitor] open-return clicked, booking id=', bid);
      currentBookingId = bid;
      // optionally populate return popup fields here
      if (returnPopup) returnPopup.classList.add('show');
      else console.warn('[job_status_monitor] returnPopup element not found');
    } catch (err) {
      console.error('[job_status_monitor] error handling open-return click', err);
    }
  });
});

closeReturnPopup.addEventListener('click', () => {
  if (returnPopup) returnPopup.classList.remove('show');
  currentBookingId = null;
});

// Return approve handler (reject button removed)
const approveReturnBtn = returnPopup ? returnPopup.querySelector('.approve-btn') : null;

if (approveReturnBtn) approveReturnBtn.addEventListener('click', () => {
  try {
    console.log('[job_status_monitor] approveReturnBtn clicked, currentBookingId=', currentBookingId);
    if (!currentBookingId) {
      alert('No booking selected for approve return');
      return;
    }
    if (!confirm(`Approve return for booking #${currentBookingId}?`)) return;
    const row = document.querySelector(`tr[data-booking-id="${currentBookingId}"]`);
    if (!row) {
      console.warn('Booking row not found for id', currentBookingId);
      if (returnPopup) returnPopup.classList.remove('show');
      currentBookingId = null;
      return;
    }
    const badge = row.querySelector('.status');
    // Mark as return approved
    setBadgeStatus(badge, 'return', 'Return (Approved)');
    badge.classList.add('return-approved');
    row.classList.add('row-return-approved');
    if (returnPopup) returnPopup.classList.remove('show');
    
    // Save notification to database
    const notification = {
      message: `Return approved for booking #${currentBookingId}`,
      href: 'job_status_monitor.php',
      created_at: new Date().toISOString(),
      read: false,
      type: 'return_approved'
    };
    fetch('/admin_haustap/admin_haustap/api/save-notification.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(notification)
    }).catch(err => console.warn('Could not save notification', err));
    
    alert(`Return approved for booking #${currentBookingId}`);
    currentBookingId = null;
    updateStatusCards();
    console.log('[job_status_monitor] approve return applied for booking', currentBookingId);
  } catch (err) {
    console.error('[job_status_monitor] error in approveReturnBtn handler', err);
    alert('Error approving return: ' + err.message);
  }
});

// Note: reject return action/button removed per request to simplify the Return popup

  </script>
</body>
</html>



