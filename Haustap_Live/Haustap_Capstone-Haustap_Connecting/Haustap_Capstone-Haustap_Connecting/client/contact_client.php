<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Haustap Chat</title>
  <link rel="stylesheet" href="/css/global.css">
  <link rel="stylesheet" href="css/homepage.css">
  <link rel="stylesheet" href="css/contact-client.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>
  <?php include __DIR__ . '/includes/header.php'; ?>

  <!-- CHAT SECTION -->
  <section class="chat-container">
    <div class="chat-header">Chats</div>

    <div class="chat-box">
      <!-- Sidebar -->
      <div class="chat-sidebar">
        <div class="search-bar">
          <input type="text" placeholder="Search Name">
          <select>
            <option>All</option>
          </select>
        </div>

        <div class="chat-list">
          <div class="chat-user">
<i class="fa-solid fa-user"></i>
            <div class="user-info">
              <p class="name">Name</p>
              <small>+639000000000</small>
            </div>
            <span class="time">Now</span>
          </div>

          <div class="chat-user">
<i class="fa-solid fa-user"></i>
            <div class="user-info">
              <p class="name">Name</p>
              <small>+639111111111</small>
            </div>
            <span class="time">Yesterday</span>
          </div>

          <div class="chat-user">
<i class="fa-solid fa-user"></i>
            <div class="user-info">
              <p class="name">Name</p>
              <small>+639222222222</small>
            </div>
            <span class="time">Monday</span>
          </div>

          <div class="chat-user">
<i class="fa-solid fa-user"></i>
            <div class="user-info">
              <p class="name">Name</p>
              <small>+639333333333</small>
            </div>
            <span class="time">Now</span>
          </div>
        </div>
      </div>

      <!-- Chat main -->
      <div class="chat-main">
        <div class="chat-top">Ana Santos</div>

        <div class="chat-messages">
          <div class="message left">
<i class="fa-solid fa-user"></i>
            <div class="bubble"></div>
          </div>

          <div class="message right">
            <div class="bubble"></div>
<i class="fa-solid fa-user"></i>
          </div>
        </div>

        <div class="chat-input">
          <input type="text" placeholder="Input message">
          <div class="icons">
<i class="fa-solid fa-paperclip"></i>
<i class="fa-solid fa-image"></i>
<i class="fa-solid fa-smile"></i>
          </div>
<button><i class="fa-solid fa-paper-plane"></i></button>
        </div>
      </div>
    </div>
  </section>

  <?php include __DIR__ . '/includes/footer.php'; ?>
  <!-- Chat scripts -->
  <script src="/login_sign up/js/api.js"></script>
  <!-- Socket.IO CDN -->
  <script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
  <!-- Optional: keep HTTP helper available, but sockets take precedence -->
  <script src="/client/js/chat-api.js"></script>
  <!-- Real-time chat over sockets -->
  <script>
    // Configure Socket server URL if different
    window.SOCKET_URL = window.SOCKET_URL || 'http://localhost:3000';
    window.CHAT_ROLE = window.CHAT_ROLE || 'client';
    // If no booking_id in URL, inject the last booking_id from localStorage
    (function(){
      try {
        var p = new URLSearchParams(window.location.search);
        if (!p.get('booking_id')) {
          var last = localStorage.getItem('last_booking_id');
          if (last) {
            p.set('booking_id', String(last));
            var url = window.location.pathname + '?' + p.toString();
            window.history.replaceState(null, document.title, url);
          }
        }
      } catch(e) {}
    })();
  </script>
  <script src="/client/js/socket-chat.js"></script>
</body>
</html>

