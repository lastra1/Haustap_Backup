<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Verification Code</title>
  <link rel="stylesheet" href="css/verification_code.css">
<script src="js/lazy-images.js" defer></script></head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <?php $active = 'dashboard'; include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Top Bar -->
      <header class="topbar">
        <div class="user">
          <button class="notif-btn">🔔</button>
          <div class="user-menu">
            <button class="user-btn" id="userDropdownBtn"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?> ▼</button>
            <div class="dropdown" id="userDropdown">
              <a href="#">View Profile</a>
              <a href="#">Change Password</a>
              <a href="#">Activity Logs</a>
              <a href="#" class="logout">Log out</a>
            </div>
          </div>
        </div>
      </header>

      <!-- Page Header -->
      <section class="page-header">
        <h3>Admin &gt; Change Password</h3>
      </section>

      <!-- Verification Code Box -->
      <section class="verification-section">
        <div class="verification-card">
          <h2>Enter Verification Code</h2>
          <p>Please enter the verification code sent to your email address.</p>
          
          <div class="code-inputs">
            <input type="text" maxlength="1">
            <input type="text" maxlength="1">
            <input type="text" maxlength="1">
            <input type="text" maxlength="1">
            <input type="text" maxlength="1">
          </div>

          <div class="resend-section">
            <p>Didn’t receive the code? <a href="#" class="resend">Resend Code</a></p>
            <small>This code will expire in <b>01:00</b> minute</small>
          </div>

          <button class="next-btn">Next</button>
        </div>
      </section>
    </main>
  </div>

  <script>
    // Dropdown toggle
    const dropdownBtn = document.getElementById("userDropdownBtn");
    const dropdown = document.getElementById("userDropdown");
    dropdownBtn.addEventListener("click", () => {
      dropdown.classList.toggle("show");
    });
    window.addEventListener("click", (e) => {
      if (!dropdownBtn.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.remove("show");
      }
    });

    // Auto focus move between inputs
    const inputs = document.querySelectorAll(".code-inputs input");
    inputs.forEach((input, index) => {
      input.addEventListener("input", () => {
        // keep only digits
        input.value = input.value.replace(/\D/g, "");
        if (input.value && index < inputs.length - 1) {
          inputs[index + 1].focus();
        }
      });
      input.addEventListener("keydown", (e) => {
        if (e.key === "Backspace" && index > 0 && !input.value) {
          inputs[index - 1].focus();
        }
      });
    });

    // Handle Next: proceed to create password after code entry
    const nextBtn = document.querySelector('.next-btn');
    if (nextBtn) {
      nextBtn.addEventListener('click', function(e){
        e.preventDefault();
        const code = Array.from(document.querySelectorAll('.code-inputs input'))
          .map(i => i.value.trim())
          .join('');
        if (code.length === inputs.length) {
          // In a real flow, verify OTP via API before proceeding.
          window.location.href = 'create_password.php';
        } else {
          alert('Please enter the full verification code.');
        }
      });
    }
  </script>
</body>
</html>


