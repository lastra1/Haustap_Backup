<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enter Verification Code | HausTap</title>
<link rel="stylesheet" href="/css/global.css">
<link rel="stylesheet" href="/login_sign%20up/css/re-password.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head>
<body>

  <!-- Header -->
<?php include dirname(__DIR__) . "/client/includes/header.php"; ?>

  <!-- Verification Box -->
  <main>
    <div class="verify-box">
      <div class="back-arrow" aria-label="Back">&larr;</div>
      <h4>Enter Verification Code</h4>
      <p>Your verification code is sent via Email to<br>
        <strong>JennBornilla@gmail.com</strong>
      </p>

      <div class="otp-inputs">
        <input type="text" maxlength="1">
        <input type="text" maxlength="1">
        <input type="text" maxlength="1">
        <input type="text" maxlength="1">
        <input type="text" maxlength="1">
        <input type="text" maxlength="1">
      </div>

      <p class="countdown">Pls wait until <span id="timer">300</span> seconds to resend</p>
      <button type="button" id="nextBtn">Next</button>

      <p id="errorMsg" class="error">Invalid OTP. Please try again.</p>
    </div>
  </main>

  <!-- FOOTER -->
<?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>

  <script>
    // Countdown timer
    let timeLeft = 300;
    const timer = document.getElementById("timer");
    const countdown = setInterval(() => {
      timeLeft--;
      timer.textContent = timeLeft;
      if (timeLeft <= 0) clearInterval(countdown);
    }, 1000);

    // OTP validation
    const nextBtn = document.getElementById("nextBtn");
    const inputs = document.querySelectorAll(".otp-inputs input");
    const errorMsg = document.getElementById("errorMsg");

    nextBtn.addEventListener("click", () => {
      const otp = Array.from(inputs).map(i => i.value).join("");
      if (otp !== "123456") {
        errorMsg.style.display = "block";
        inputs.forEach(i => i.style.border = "1px solid red");
      } else {
        errorMsg.style.display = "none";
        alert("OTP verified successfully!");
      }
    });
  </script>

</body>
</html>


