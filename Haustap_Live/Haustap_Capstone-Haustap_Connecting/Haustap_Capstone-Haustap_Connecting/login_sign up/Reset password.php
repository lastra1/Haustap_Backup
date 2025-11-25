<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password | HausTap</title>
  <link rel="stylesheet" href="/css/global.css">
<link rel="stylesheet" href="/login_sign%20up/css/reset%20password.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head>
<body>

  <!-- Header -->
<?php include dirname(__DIR__) . "/client/includes/header.php"; ?>

  <!-- Reset Password Box -->
  <main>
    <div class="reset-box">
      <div class="back-arrow" aria-label="Back">&larr;</div>
      <h4>Reset Password</h4>
      <form id="resetForm">
        <input type="email" id="email" placeholder="Email" required>
        <p id="errorMsg" class="error">Invalid email address. Please try again.</p>
        <button type="submit">Next</button>
      </form>
    </div>
  </main>

       <!-- FOOTER -->
<?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>

  <script>
    const form = document.getElementById("resetForm");
    const emailInput = document.getElementById("email");
    const errorMsg = document.getElementById("errorMsg");

    form.addEventListener("submit", (e) => {
      e.preventDefault();
      const emailValue = emailInput.value.trim();
      if (!emailValue || !emailValue.includes("@")) {
        emailInput.classList.add("invalid");
        errorMsg.style.display = "block";
      } else {
        emailInput.classList.remove("invalid");
        errorMsg.style.display = "none";
        alert("Password reset link sent!");
      }
    });
  </script>

</body>
</html>


