<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HausTap Bookings</title>
  <link rel="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/cancellation_reason.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="/client/css/homepage.css"></head>
<body><?php include dirname(__DIR__) . "/client/includes/header.php"; ?>
    
<!-- Header -->
   <div class="header">
    <img src="/bookings/images/logo.png" alt="HausTap" class="logo-img">
    <nav class="nav">
      <a href="#">Home</a>
      <a href="#">Services</a>
      <a href="#" class="active">Bookings</a>
      <a href="#">About</a>
      <a href="#">Contact</a>
    </nav>
    <div class="header-right">
      <div class="search-box">
        <input type="text" placeholder="Search services">
<i class="fa-solid fa-search"></i>
      </div>
      <a href="#" class="icon-button account-link">
        <i class="bi bi-person-circle"></i>
        <span>My Account</span>
      </a>
    </div>
  </div>

  <main class="cancel-container">
  <div class="cancel-box">
    <h3>Select Cancellation Reason</h3>
    <p class="cancel-note">
      Once cancelled, the service provider will be notified and this action cannot be undone.
    </p>

    <form class="cancel-form">
      <label><input type="radio" name="reason"> Change of Schedule</label>
      <label><input type="radio" name="reason"> Found Another Service Provider</label>
      <label><input type="radio" name="reason"> Service No Longer Needed</label>
      <label><input type="radio" name="reason"> Incorrect Booking Details</label>
      <label><input type="radio" name="reason"> Price Concerns</label>
      <label><input type="radio" name="reason"> Payment Issues</label>
      <label><input type="radio" name="reason"> Health/Safety Concerns</label>
      <label><input type="radio" name="reason"> Emergency/Personal Reasons</label>
    </form>

    <div class="cancel-buttons">
      <button class="btn-keep">No, Keep Booking</button>
      <button class="btn-cancel">Cancel Booking</button>
    </div>
  </div>
</main>

<!-- FOOTER -->
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
</body>
</html>


