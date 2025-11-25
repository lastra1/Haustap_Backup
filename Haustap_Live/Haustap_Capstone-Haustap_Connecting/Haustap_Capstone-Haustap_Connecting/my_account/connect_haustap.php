<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Connect Haustap</title>
  <link rel="stylesheet" href="../client/css/homepage.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/my_account/css/connect_haustap.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
  <?php include __DIR__ . '/../client/includes/header.php'; ?>
    <main class="connect-page">
  <div class="connect-container">
    <!-- LEFT SIDE -->
    <div class="connect-info">
      <h2>Connect Haustap</h2>
      <h1>GET<br>IN<br>TOUCH</h1>
      <p>
        Got a question, feedback, or a service request? Don’t hesitate to contact us.<br>
        We’re always happy to assist and make your HausTap experience smoother.
      </p>
      <div class="social-icons">
        <i class="fab fa-facebook-f"></i>
        <i class="fab fa-instagram"></i>
      </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="connect-form">
      <form>
        <div class="name-fields">
          <div class="input-group">
            <label>First name</label>
            <input type="text" placeholder="First name">
          </div>
          <div class="input-group mi">
            <label>MI</label>
            <input type="text" placeholder="M.I">
          </div>
          <div class="input-group">
            <label>Last name</label>
            <input type="text" placeholder="Last name">
          </div>
        </div>

        <div class="input-group">
          <label>Email</label>
          <input type="email" placeholder="you@gmail.com">
        </div>

        <div class="input-group">
          <label>Phone Number</label>
          <input type="text" placeholder="PH ▼   +63">
        </div>

        <div class="input-group">
          <label>Message</label>
          <textarea rows="4" placeholder=""></textarea>
        </div>

        <div class="checkbox-group">
          <input type="checkbox" id="agree">
          <label for="agree">You agree to our friendly <a href="#">privacy policy</a></label>
        </div>

        <button type="submit" class="send-btn">Send Message</button>
      </form>
    </div>
  </div>
</main>
<?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
</body>
</html>