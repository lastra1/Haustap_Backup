<?php require_once __DIR__ . '/includes/auth.php'; ?>
<?php
// Load data sources for analytics (applicants, providers, clients, bookings)
$dataDir = realpath(__DIR__ . '/../../storage/data');
$backendBookings = realpath(__DIR__ . '/../../backend/api/storage/data/bookings.json');

$applicants = [];
$providers = [];
$clients = [];
$bookings = [];

if ($dataDir) {
  $aPath = $dataDir . DIRECTORY_SEPARATOR . 'applicants.json';
  if (is_file($aPath)) {
    $raw = @file_get_contents($aPath);
    $applicants = json_decode($raw ?: '[]', true) ?: [];
  }

  $pPath = $dataDir . DIRECTORY_SEPARATOR . 'providers.json';
  if (is_file($pPath)) {
    $raw = @file_get_contents($pPath);
    $providers = json_decode($raw ?: '[]', true) ?: [];
  }

  $cPath = $dataDir . DIRECTORY_SEPARATOR . 'clients.json';
  if (is_file($cPath)) {
    $raw = @file_get_contents($cPath);
    $clients = json_decode($raw ?: '[]', true) ?: [];
  }
}

// bookings live in backend mock storage
if ($backendBookings && is_file($backendBookings)) {
  $raw = @file_get_contents($backendBookings);
  $bookings = json_decode($raw ?: '[]', true) ?: [];
}

// Summary counts
$totalApplicants = count($applicants);
$totalProviders = count($providers);
$totalClients = count($clients);
$totalBookings = count($bookings);

// Helpers: convert various date formats/fields to timestamp
function to_timestamp($val) {
  if (empty($val)) return null;
  // numeric (ms or s)
  if (is_numeric($val)) {
    $n = intval($val);
    // milliseconds
    if ($n > 9999999999) return intval($n / 1000);
    return $n;
  }
  // try parseable date string
  try { $dt = new DateTime($val); return $dt->getTimestamp(); } catch (Exception $e) { return null; }
}

function count_periods($items, $field) {
  $res = ['day' => 0, 'week' => 0, 'month' => 0];
  $now = new DateTime();
  foreach ($items as $it) {
    if (empty($it[$field])) continue;
    $ts = to_timestamp($it[$field]);
    if (!$ts) continue;
    $dt = (new DateTime())->setTimestamp($ts);
    if ($dt->format('Y-m-d') === $now->format('Y-m-d')) $res['day']++;
    // ISO week-year (o) and week number (W)
    if ($dt->format('oW') === $now->format('oW')) $res['week']++;
    if ($dt->format('Y-m') === $now->format('Y-m')) $res['month']++;
  }
  return $res;
}

// Compute per-period counts
$applicantCounts = count_periods($applicants, 'applied_at');
$providerCounts = count_periods($providers, 'hired_at');
$clientCounts = count_periods($clients, 'joined_at');

// Bookings may have created_at (ms) or scheduled_date
$bookingCounts = ['day'=>0,'week'=>0,'month'=>0];
$now = new DateTime();
foreach ($bookings as $b) {
  $ts = null;
  if (!empty($b['created_at'])) {
    $ts = to_timestamp($b['created_at']);
  } elseif (!empty($b['scheduled_date'])) {
    $ts = to_timestamp($b['scheduled_date']);
  }
  if (!$ts) continue;
  $dt = (new DateTime())->setTimestamp($ts);
  if ($dt->format('Y-m-d') === $now->format('Y-m-d')) $bookingCounts['day']++;
  if ($dt->format('oW') === $now->format('oW')) $bookingCounts['week']++;
  if ($dt->format('Y-m') === $now->format('Y-m')) $bookingCounts['month']++;
}

// Top providers by rating (descending)
usort($providers, function($a, $b){
  $ra = isset($a['rating']) ? floatval($a['rating']) : 0;
  $rb = isset($b['rating']) ? floatval($b['rating']) : 0;
  return $rb <=> $ra;
});
$topProviders = array_slice($providers, 0, 4);

// Monthly bookings for last 8 months (based on created_at if available, else scheduled_date)
$monthlyLabels = [];
$monthlyCounts = [];
$monthsToShow = 8;
$now = new DateTime();
for ($i = $monthsToShow - 1; $i >= 0; $i--) {
  $dt = (clone $now)->modify("-{$i} months");
  $label = $dt->format('M');
  $monthlyLabels[] = $label;
  $monthlyCounts[$label] = 0;
}

foreach ($bookings as $b) {
  $ts = null;
  if (!empty($b['created_at'])) {
    // created_at stored as milliseconds in some mocks
    $created = $b['created_at'];
    if (is_numeric($created) && $created > 9999999999) {
      $ts = intval($created / 1000);
    } else if (is_numeric($created)) {
      $ts = intval($created);
    }
  } elseif (!empty($b['scheduled_date'])) {
    $d = $b['scheduled_date'];
    try { $dt = new DateTime($d); $ts = $dt->getTimestamp(); } catch (Exception $e) { $ts = null; }
  }
  if ($ts) {
    $m = (new DateTime())->setTimestamp($ts)->format('M');
    if (array_key_exists($m, $monthlyCounts)) $monthlyCounts[$m]++;
  }
}

// Prepare arrays for JS
$js_monthly_labels = array_values($monthlyLabels);
$js_monthly_counts = array_values($monthlyCounts);

// Provider performance data (average rating) - repeat across months to fit chart labels
$avgProviderRating = 0;
if (!empty($providers)) {
  $sum = 0; $cnt = 0;
  foreach ($providers as $p) {
    if (isset($p['rating']) && is_numeric($p['rating'])) { $sum += floatval($p['rating']); $cnt++; }
  }
  if ($cnt) $avgProviderRating = $sum / $cnt;
}
$js_provider_performance = array_fill(0, count($js_monthly_labels), round($avgProviderRating,2));

// User activity trends (last N days) - clients joined and providers hired
$activityDays = 15;
$activityLabels = [];
$activityClients = [];
$activityProviders = [];
$today = new DateTime();
for ($i = $activityDays - 1; $i >= 0; $i--) {
  $d = (clone $today)->modify("-{$i} days");
  $label = $d->format('M j');
  $activityLabels[] = $label;
  $activityClients[$label] = 0;
  $activityProviders[$label] = 0;
}

// Count clients by joined_at
foreach ($clients as $c) {
  if (empty($c['joined_at'])) continue;
  try {
    $dt = new DateTime($c['joined_at']);
    $lbl = $dt->format('M j');
    if (array_key_exists($lbl, $activityClients)) $activityClients[$lbl]++;
  } catch (Exception $e) { }
}

// Count providers by hired_at
foreach ($providers as $p) {
  if (empty($p['hired_at'])) continue;
  try {
    $dt = new DateTime($p['hired_at']);
    $lbl = $dt->format('M j');
    if (array_key_exists($lbl, $activityProviders)) $activityProviders[$lbl]++;
  } catch (Exception $e) { }
}

$js_activity_labels = array_values($activityLabels);
$js_activity_clients = array_values($activityClients);
$js_activity_providers = array_values($activityProviders);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - Analytics & Report</title>
  <link rel="stylesheet" href="css/analytics_report.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="js/lazy-images.js" defer></script>
  <style>
    /* Top-Rated Providers small-card styles */
    .top-providers { margin-top:12px; }
    .top-providers h4 { margin:0 0 8px; font-size:14px; color:#0b7a72; }
    .top-providers ul { list-style:none; padding:0; margin:6px 0 0; }
    .top-providers li { display:flex; align-items:center; justify-content:space-between; padding:8px 6px; border-radius:8px; transition:background .12s; }
    .top-providers li + li { margin-top:8px; }
    .top-providers li:hover { background:#f6fbfb; }
    .top-providers .left { display:flex; align-items:center; gap:10px; }
    .top-providers .badge { width:36px; height:36px; border-radius:50%; background:#e0f7f9; color:#007b8f; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; }
    .top-providers .name { font-weight:600; color:#223; font-size:13px; }
    .top-providers .jobs { font-size:12px; color:#7a8a8c; }
    .top-providers .rating { font-weight:700; color:#0b7a72; font-size:13px; min-width:44px; text-align:right; }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <?php $active = 'analytics'; include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Topbar -->
      <header class="topbar">
        <h3>Analytics & Report</h3>
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

     <!-- Summary Cards -->
  <section class="summary-section">
    <div class="summary-card" data-day="<?php echo intval($applicantCounts['day']); ?>" data-week="<?php echo intval($applicantCounts['week']); ?>" data-month="<?php echo intval($applicantCounts['month']); ?>">
      <div class="card-header">
        <button class="time-btn active">DAY</button>
        <button class="time-btn">WEEK</button>
        <button class="time-btn">MONTH</button>
      </div>
        <div class="card-body">
          <h2><?php echo number_format(intval($applicantCounts['day'])); ?></h2>
          <p>Total Applicants</p>
        </div>
    </div>

    <div class="summary-card" data-day="<?php echo intval($providerCounts['day']); ?>" data-week="<?php echo intval($providerCounts['week']); ?>" data-month="<?php echo intval($providerCounts['month']); ?>">
      <div class="card-header">
        <button class="time-btn active">DAY</button>
        <button class="time-btn">WEEK</button>
        <button class="time-btn">MONTH</button>
      </div>
      <div class="card-body">
        <h2><?php echo number_format(intval($providerCounts['day'])); ?></h2>
        <p>Total Providers</p>
      </div>
    </div>

    <div class="summary-card" data-day="<?php echo intval($clientCounts['day']); ?>" data-week="<?php echo intval($clientCounts['week']); ?>" data-month="<?php echo intval($clientCounts['month']); ?>">
      <div class="card-header">
        <button class="time-btn active">DAY</button>
        <button class="time-btn">WEEK</button>
        <button class="time-btn">MONTH</button>
      </div>
      <div class="card-body">
        <h2><?php echo number_format(intval($clientCounts['day'])); ?></h2>
        <p>Total Clients</p>
      </div>
    </div>

    <div class="summary-card" data-day="<?php echo intval($bookingCounts['day']); ?>" data-week="<?php echo intval($bookingCounts['week']); ?>" data-month="<?php echo intval($bookingCounts['month']); ?>">
      <div class="card-header">
        <button class="time-btn active">DAY</button>
        <button class="time-btn">WEEK</button>
        <button class="time-btn">MONTH</button>
      </div>
      <div class="card-body">
        <h2><?php echo number_format(intval($bookingCounts['day'])); ?></h2>
        <p>Total Bookings</p>
      </div>
    </div>
  </section>

  <!-- Charts Row -->
 <section class="chart-row">
  <div class="chart-card">
    <h3>User Activity Trend</h3>
    <canvas id="userActivityChart"></canvas>

    <!-- NEW BOOKING CHART -->
    <div class="booking-chart">
      <h3>Booking & Job Statistics</h3>
      <canvas id="bookingChart"></canvas>
    </div>
  </div>

  <div class="chart-card">
    <h3>Provider Performance</h3>
    <canvas id="providerPerformanceChart"></canvas>
    <div class="top-providers">
      <h4>Top-Rated Providers</h4>
      <ul>
        <?php foreach ($topProviders as $tp):
          $name = $tp['name'] ?? 'Unknown';
          $parts = preg_split('/\s+/', trim($name));
          $initials = '';
          if (count($parts) >= 2) {
            $initials = strtoupper(substr($parts[0],0,1) . substr($parts[1],0,1));
          } else {
            $initials = strtoupper(substr($parts[0],0,2));
          }
          $rating = isset($tp['rating']) ? number_format(floatval($tp['rating']),1) : '–';
        ?>
          <li><span class="badge"><?php echo htmlspecialchars($initials); ?></span> <?php echo htmlspecialchars($name); ?> — <strong><?php echo $rating; ?></strong></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</section>


 <!-- SERVICE DEMAND SECTION -->
<section class="service-demand">
  <h3>Service Demand</h3>
  <p class="section-subtitle">Top Booked Services</p>

  <div class="service-grid">
    <!-- Cleaning Services -->
    <div class="service-card">
      <h4>Cleaning Services</h4>
      <ul>
        <li><span>Home Cleaning</span><div class="bar"><div style="width:80%"></div></div></li>
        <li><span>AC Cleaning</span><div class="bar"><div style="width:60%"></div></div></li>
        <li><span>AC Deep Cleaning (Chemical Cleaning)</span><div class="bar"><div style="width:90%"></div></div></li>
      </ul>
    </div>

    <!-- Indoor Services -->
    <div class="service-card">
      <h4>Indoor Services</h4>
      <ul>
        <li><span>Handyman</span><div class="bar"><div style="width:75%"></div></div></li>
        <li><span>Plumbing</span><div class="bar"><div style="width:65%"></div></div></li>
        <li><span>Electrical</span><div class="bar"><div style="width:55%"></div></div></li>
        <li><span>Appliance Repair</span><div class="bar"><div style="width:70%"></div></div></li>
        <li><span>Pest Control</span><div class="bar"><div style="width:50%"></div></div></li>
      </ul>
    </div>

    <!-- Beauty Services -->
    <div class="service-card">
      <h4>Beauty Services</h4>
      <ul>
        <li><span>Hair Services</span><div class="bar"><div style="width:80%"></div></div></li>
        <li><span>Nail Care</span><div class="bar"><div style="width:60%"></div></div></li>
        <li><span>Make-up</span><div class="bar"><div style="width:70%"></div></div></li>
        <li><span>Lashes</span><div class="bar"><div style="width:85%"></div></div></li>
        <li><span>Packages</span><div class="bar"><div style="width:75%"></div></div></li>
      </ul>
    </div>

    <!-- Outdoor Services -->
    <div class="service-card">
      <h4>Outdoor Services</h4>
      <ul>
        <li><span>Gardening & Landscaping</span><div class="bar"><div style="width:80%"></div></div></li>
        <li><span>Pest Control</span><div class="bar"><div style="width:60%"></div></div></li>
      </ul>
    </div>

    <!-- Tech & Gadget Services -->
    <div class="service-card">
      <h4>Tech & Gadget Services</h4>
      <ul>
        <li><span>Mobile Phone</span><div class="bar"><div style="width:70%"></div></div></li>
        <li><span>Laptop & Desktop</span><div class="bar"><div style="width:60%"></div></div></li>
        <li><span>PC</span><div class="bar"><div style="width:50%"></div></div></li>
        <li><span>Tablet & iPad</span><div class="bar"><div style="width:65%"></div></div></li>
        <li><span>Game & Console</span><div class="bar"><div style="width:80%"></div></div></li>
      </ul>
    </div>

    <!-- Wellness Services -->
    <div class="service-card">
      <h4>Wellness Services</h4>
      <ul>
        <li><span>Massage</span><div class="bar"><div style="width:85%"></div></div></li>
        <li><span>Packages</span><div class="bar"><div style="width:70%"></div></div></li>
      </ul>
    </div>
  </div>
</section>
        <section class="analytics">
          <h2><i class="fa-solid fa-chart-column" aria-hidden="true"></i> Reports Analytics</h2>
          <div class="metrics-cards">
            <div class="card">
              <div class="label">Total Reports Filed</div>
              <div class="value">1,248</div>
            </div>
            <div class="card">
              <div class="label">Resolved Reports</div>
              <div class="value success">1,020</div>
            </div>
            <div class="card">
              <div class="label">Pending Reports</div>
              <div class="value">228</div>
            </div>
          </div>
          <div class="analytics-grid">
            <div class="panel">
              <h4>Reports by Category</h4>
              <div class="bar-row">
                <div class="name">Last-Minute Cancellation</div>
                <div class="bar"><div class="fill bar-blue" style="width:35%"></div></div>
                <div class="pct">35%</div>
              </div>
              <div class="bar-row">
                <div class="name">No Prior Notice</div>
                <div class="bar"><div class="fill bar-orange" style="width:30%"></div></div>
                <div class="pct">30%</div>
              </div>
              <div class="bar-row">
                <div class="name">Unprofessional Communication</div>
                <div class="bar"><div class="fill bar-yellow" style="width:25%"></div></div>
                <div class="pct">25%</div>
              </div>
              <div class="bar-row">
                <div class="name">Failure to Arrive / No-Show</div>
                <div class="bar"><div class="fill bar-blue" style="width:20%"></div></div>
                <div class="pct">20%</div>
              </div>
              <div class="bar-row">
                <div class="name">Cancelled to Force Outside-App Transaction</div>
                <div class="bar"><div class="fill bar-orange" style="width:15%"></div></div>
                <div class="pct">15%</div>
              </div>
              <div class="bar-row">
                <div class="name">Fake or Incorrect Availability</div>
                <div class="bar"><div class="fill bar-yellow" style="width:15%"></div></div>
                <div class="pct">15%</div>
              </div>
              <div class="bar-row">
                <div class="name">Misleading Information / Dishonesty</div>
                <div class="bar"><div class="fill bar-blue" style="width:15%"></div></div>
                <div class="pct">15%</div>
              </div>
              <div class="bar-row">
                <div class="name">Safety or Security Concern</div>
                <div class="bar"><div class="fill bar-orange" style="width:10%"></div></div>
                <div class="pct">10%</div>
              </div>
              <div class="bar-row">
                <div class="name">Other Valid Concerns</div>
                <div class="bar"><div class="fill bar-gray" style="width:10%"></div></div>
                <div class="pct">10%</div>
              </div>
            </div>
            <div class="panel">
              <h4>Most Reported Providers</h4>
              <table class="providers-table">
                <thead>
                  <tr>
                    <th>Provider</th>
                    <th>Reports</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Callix Bryle</td>
                    <td>3</td>
                    <td class="status-cell"><i class="fa-solid fa-triangle-exclamation status-flag" aria-hidden="true"></i> Under Review</td>
                  </tr>
                  <tr>
                    <td>Ana Santos</td>
                    <td>2</td>
                    <td class="status-cell"><i class="fa-solid fa-circle-check status-ok" aria-hidden="true"></i> Active</td>
                  </tr>
                  <tr>
                    <td>CJ Garcia</td>
                    <td>1</td>
                    <td class="status-cell"><i class="fa-solid fa-circle-check status-ok" aria-hidden="true"></i> Resolved</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </section>
</main>
  </div>

  <script>
    // Dropdown
    (function(){
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
    })();

    // Charts setup
(function(){
  if (typeof Chart === 'undefined') return;

  const ctx1 = document.getElementById('userActivityChart');
  new Chart(ctx1, {
    type: 'line',
    data: {
      labels: <?php echo json_encode($js_activity_labels); ?>,
      datasets: [
        { label: 'Clients', data: <?php echo json_encode($js_activity_clients); ?>, borderColor: '#00bcd4', fill: false },
        { label: 'Service Providers', data: <?php echo json_encode($js_activity_providers); ?>, borderColor: '#03a9f4', fill: false }
      ]
    },
    options: { responsive: true, plugins:{ legend:{ position:'bottom' } } }
  });

  const ctx2 = document.getElementById('providerPerformanceChart');
  new Chart(ctx2, {
    type: 'line',
    data: {
      labels: <?php echo json_encode($js_monthly_labels); ?>,
      datasets: [{ label: 'Performance', data: <?php echo json_encode($js_provider_performance); ?>, borderColor:'#2196f3', backgroundColor:'rgba(33,150,243,0.1)', fill:true }]
    },
    options: { responsive: true, plugins:{ legend:{ display:false } } }
  });

  // Booking & Job Statistics Chart (data populated from server-side analytics)
  const bookingCtx = document.getElementById('bookingChart');
  new Chart(bookingCtx, {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($js_monthly_labels); ?>,
      datasets: [{
        label: 'Bookings',
        data: <?php echo json_encode($js_monthly_counts); ?>,
        backgroundColor: 'rgba(0, 102, 255, 0.3)',
        borderColor: '#0066ff',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true
        }
      },
      plugins: {
        legend: { display: false }
      }
    }
  });
})();

    // Time period buttons functionality
    (function(){
      const timeButtons = document.querySelectorAll('.time-btn');
      if (!timeButtons.length) return;

      // Data for different time periods
      const cardData = {
        'Today': {
          day: { value: 890, label: 'Today' },
          week: { value: 6230, label: 'This Week' },
          month: { value: 28450, label: 'This Month' }
        },
        'This Week': {
          day: { value: 890, label: 'Today' },
          week: { value: 6230, label: 'This Week' },
          month: { value: 28450, label: 'This Month' }
        },
        'This Month': {
          day: { value: 890, label: 'Today' },
          week: { value: 6230, label: 'This Week' },
          month: { value: 28450, label: 'This Month' }
        }
      };

      timeButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          const period = this.textContent.toLowerCase(); // 'day', 'week', 'month'
          const card = this.closest('.summary-card');
          if (!card) return;

          // Update button states (active/inactive)
          const buttons = card.querySelectorAll('.time-btn');
          buttons.forEach(b => b.classList.remove('active'));
          this.classList.add('active');

          // Get the card body and update values from data-* attributes
          const cardBody = card.querySelector('.card-body');
          const h2 = cardBody.querySelector('h2');
          const p = cardBody.querySelector('p');
          const val = card.dataset[period] || card.dataset['day'] || '0';
          h2.textContent = Number(val).toLocaleString();

          if (period === 'day') {
            p.textContent = 'Today';
          } else if (period === 'week') {
            p.textContent = 'This Week';
          } else if (period === 'month') {
            p.textContent = 'This Month';
          }
        });
      });
    })();

  </script>
</body>
</html>
