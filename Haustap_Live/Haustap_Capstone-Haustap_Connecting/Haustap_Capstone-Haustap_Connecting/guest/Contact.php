<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact - HausTap</title>
  <link rel="stylesheet" href="/css/global.css" />
  <link rel="stylesheet" href="css/homepage.css" />
  <link rel="stylesheet" href="css/contact.css" />

</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

  <!-- MAIN SECTION -->
  <section class="contact-section">
    <div class="tab-container">
      <div class="tabs">
        <button class="tab active">Chat</button>
        <button class="tab">Connect Haustap with</button>
      </div>

      <div class="tab-content chat-tab active">
        <div class="chat-box">
          <div class="chat-messages" style="height:300px; overflow:auto; border:1px solid #e3e3e3; border-radius:8px; padding:8px"></div>
          <div class="chat-input" style="display:flex; gap:8px; align-items:center; margin-top:8px">
            <input type="text" placeholder="Type a message" style="flex:1; padding:8px">
            <button type="button" style="padding:8px 12px">Send</button>
          </div>
        </div>
      </div>

      <div class="tab-content connect-tab">
        <div class="social-icons">
          <div class="social-card">
            <img src="images/facebook.png" alt="Facebook">
            <a href="#">Facebook page</a>
          </div>
          <div class="social-card">
            <img src="images/instagram.png" alt="Instagram">
            <a href="#">Instagram page</a>
          </div>
          <div class="social-card">
            <img src="images/twitter.png" alt="X">
            <a href="#">X</a>
          </div>
        </div>

        <p class="email-text">Or send us your concern via email:</p>

        <div class="contact-info">
          <h4>Contact Us</h4>
          <p>Email: <a href="mailto:haustap_ph@gmail.com">haustap_ph@gmail.com</a></p>
          <p>Phone: 09451234521</p>
          <p>Phone: 09264502561</p>
          <p>Address: 29 San Pedro, Laguna City of Sta Rosa, Laguna</p>
          <p>If you have any questions or feedback, feel free to reach out!</p>
        </div>
  </div>
    </div>
  </section>


  <script>
    // Tab switching functionality
    const tabs = document.querySelectorAll('.tab');
    const contents = document.querySelectorAll('.tab-content');

    tabs.forEach((tab, index) => {
      tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        contents.forEach(c => c.classList.remove('active'));
        tab.classList.add('active');
        contents[index].classList.add('active');
      });
    });
  </script>
  <!-- Socket chat wiring -->
  <script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
  <script src="/client/js/chat-api.js"></script>
  <script>
    window.SOCKET_URL = window.SOCKET_URL || 'http://localhost:3000';
    window.CHAT_ROLE = 'client';
    (function(){
      try {
        var p = new URLSearchParams(window.location.search);
        if (!p.get('booking_id')) {
          p.set('booking_id', '0');
          var url = window.location.pathname + '?' + p.toString();
          window.history.replaceState(null, document.title, url);
        }
      } catch(e){}
    })();
  </script>
  <script src="/client/js/socket-chat.js"></script>
  <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>

