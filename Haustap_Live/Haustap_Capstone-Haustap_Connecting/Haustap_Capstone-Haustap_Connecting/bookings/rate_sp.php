<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HausTap Bookings</title>
  <link rel="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/rate_sp.css">
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
 
 <main class="rate-container">
    <p>Rate Your Service Provider</p>

    <div class="service-info">
      <h3>Home Cleaning</h3>
      <h3>Bungalow - Basic Cleaning</h3>
      <p>Provider: Ana Santos | Date: May 21, 2025</p>
    </div>

      <div class="rating-overall">
        <span class="label">Overall Rating</span>
        <div class="stars">
        <span class="star">&#9733;</span>
        <span class="star">&#9733;</span>
        <span class="star">&#9733;</span>
        <span class="star">&#9733;</span>
        <span class="star">&#9733;</span>
        <span class="rating-text">Amazing</span>
      </div>
    </div>

    <div class="experience-box">
      <p class="experience-title">Tell us about your experience with the service</p>
      <textarea placeholder="Share your thoughts here..."></textarea>

      <div class="experience-actions">
        <div class="left-buttons">
          <button class="btn-photo">&#128247; Add Photo</button>
          <button class="btn-video">&#127909; Add Video</button>
        </div>
        <small class="right-note">Add 100 characters with 1 photo and 1 video</small>
      </div>
    </div>

    <div class="privacy-section">
      <label>
        <input type="checkbox">
        Show username on your review
      </label>
      <p class="small-text">Your name will be shown as "username"</p>
    </div>

    <div class="aspect-section">
      <span class="section-title">Rate Specific Aspects</span>
      <ul>
        <li>Professionalism <span class="star-group">&#9733;&#9733;&#9733;&#9733;&#9733;</span></li>
        <li>Punctuality <span class="star-group">&#9733;&#9733;&#9733;&#9733;&#9733;</span></li>
        <li>Quality of Work <span class="star-group">&#9733;&#9733;&#9733;&#9733;&#9733;</span></li>
        <li>Communication <span class="star-group">&#9733;&#9733;&#9733;&#9733;&#9733;</span></li>
      </ul>

      <span class="section-title">About Service</span>
      <ul>
        <li>Booking Process <span class="star-group">&#9733;&#9733;&#9733;&#9733;&#9733;</span></li>
        <li>Value for Money <span class="star-group">&#9733;&#9733;&#9733;&#9733;&#9733;</span></li>
      </ul>
    </div>

    <div class="form-actions">
      <button class="btn-cancel">Cancel</button>
      <button class="btn-submit">Submit</button>
    </div>
  </main>

  <!-- FOOTER -->
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
</body>
</html>


