<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Log In | HausTap</title>
  <link rel="stylesheet" href="/css/global.css">
  <link rel="stylesheet" href="/login_sign%20up/css/login.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head>
<body>
  <!-- Firebase SDK v9 - Use modular SDK instead of compat -->
  <script type="module">
    import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js';
    import { getAuth, signInWithEmailAndPassword, sendPasswordResetEmail } from 'https://www.gstatic.com/firebasejs/10.12.0/firebase-auth.js';
    
    // Make Firebase available globally for the login script
    window.firebase = {
      initializeApp,
      getAuth,
      signInWithEmailAndPassword,
      sendPasswordResetEmail
    };
  </script>
  <div class="container">
    <div class="logo">
      <a href="/guest/homepage.php" aria-label="Go to homepage">
        <img src="/login_sign%20up/image/logo.png" alt="HausTap Logo" width="120" height="100">
      </a>
    </div>
    <form class="login-form">
      <h2>Log In</h2>
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required>
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>
      <a href="#" class="forgot" id="forgot-password">Forgot Password?</a>
      <button type="submit">Log In</button>
      <div class="signup-link">
        New to HausTap? <a href="/signup">Sign Up</a>
      </div>
    </form>
  </div>
    <!-- FOOTER -->
<?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
  <script>
    (function() {
      // Firebase configuration - direct embedding to avoid API issues
      var firebaseConfig = {
        apiKey: "AIzaSyCfhR1vIh8_z4TAmdaQRESHB459CsVqJ9M",
        authDomain: "haustap-booking-system.firebaseapp.com",
        projectId: "haustap-booking-system",
        storageBucket: "haustap-booking-system.firebasestorage.app",
        messagingSenderId: "515769404711",
        appId: "1:515769404711:web:ddf0b32df0498eb18aad02"
      };
      
      function initFirebase(){
        if (!firebaseConfig) {
          console.error('Firebase config is missing');
          return false;
        }
        try { 
          console.log('Initializing Firebase with config:', firebaseConfig);
          const app = window.firebase.initializeApp(firebaseConfig); 
          console.log('Firebase initialized successfully');
          return app; 
        } catch(e){ 
          console.error('Firebase initialization error:', e);
          return null; 
        }
      }
      function fetchConfig(){
        // Return resolved promise since config is already available
        return Promise.resolve();
      }
      const form = document.querySelector('.login-form');
      if (!form) return;

      form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;

        // Basic client-side validation
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email) { alert('Email is required.'); return; }
        if (!emailPattern.test(email)) { alert('Please enter a valid email address.'); return; }
        if (!password || password.length < 6) { alert('Password must be at least 6 characters.'); return; }

        try {
          console.log('Starting login process for email:', email);
          await fetchConfig();
          const app = initFirebase();
          if (!app) { 
            alert('Unable to initialize authentication.'); 
            return; 
          }
          console.log('Firebase initialized, attempting login...');
          const auth = window.firebase.getAuth(app);
          console.log('Attempting Firebase authentication...');
          const userCred = await window.firebase.signInWithEmailAndPassword(auth, email, password);
          console.log('Firebase authentication successful:', userCred);
          const user = userCred.user;
          const token = await user.getIdToken();
          console.log('Firebase token obtained:', token.substring(0, 20) + '...');
          localStorage.setItem('haustap_token', token);
          var u = { uid: user.uid, email: user.email || email, name: (user.displayName || (email.split('@')[0])) };
          localStorage.setItem('haustap_user', JSON.stringify(u));
          console.log('User data saved:', u);
          
          console.log('Redirecting to homepage...');
          window.location.href = '../client/homepage.php';
        } catch (err) {
          console.error('Firebase login error:', err);
          let errorMessage = 'Login failed.';
          if (err.code) {
            switch (err.code) {
              case 'auth/user-not-found':
                errorMessage = 'User not found. Please check your email.';
                break;
              case 'auth/wrong-password':
                errorMessage = 'Incorrect password. Please try again.';
                break;
              case 'auth/invalid-credential':
                errorMessage = 'Invalid email or password. Please check your credentials.';
                break;
              case 'auth/invalid-email':
                errorMessage = 'Invalid email format.';
                break;
              case 'auth/user-disabled':
                errorMessage = 'This account has been disabled.';
                break;
              case 'auth/too-many-requests':
                errorMessage = 'Too many failed attempts. Please try again later.';
                break;
              case 'auth/network-request-failed':
                errorMessage = 'Network error. Please check your connection.';
                break;
              default:
                errorMessage = `Login failed: ${err.message}`;
            }
          } else if (err.message) {
            errorMessage = `Login failed: ${err.message}`;
          }
          alert(errorMessage);
        }
      });
      
      // Password reset functionality
      document.getElementById('forgot-password').addEventListener('click', async function(e) {
        e.preventDefault();
        const email = document.getElementById('email').value.trim();
        if (!email) {
          alert('Please enter your email address first.');
          return;
        }
        
        try {
          await fetchConfig();
          const app = initFirebase();
          if (!app) { 
            alert('Unable to initialize authentication.'); 
            return; 
          }
          const auth = window.firebase.getAuth(app);
          await window.firebase.sendPasswordResetEmail(auth, email);
          alert('Password reset email sent! Please check your inbox and spam folder.');
        } catch (err) {
          console.error('Password reset error:', err);
          let errorMessage = 'Failed to send password reset email.';
          if (err.code) {
            switch (err.code) {
              case 'auth/user-not-found':
                errorMessage = 'No account found with this email address.';
                break;
              case 'auth/invalid-email':
                errorMessage = 'Please enter a valid email address.';
                break;
              default:
                errorMessage = `Failed to send reset email: ${err.message}`;
            }
          }
          alert(errorMessage);
        }
      });
      
      fetchConfig();
    })();
  </script>
</body>
</html>





