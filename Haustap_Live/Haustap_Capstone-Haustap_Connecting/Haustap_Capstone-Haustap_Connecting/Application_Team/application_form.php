<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Application Form | HausTap</title>
  <link rel="stylesheet" href="css/application-form.css">
<link rel="stylesheet" href="/client/css/homepage.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head>
<body><?php include dirname(__DIR__) . "/client/includes/header.php"; ?>
  <div class="container">
    <div class="logo">
      <img src="/Application_Team/image/logo.png"   alt="HausTap Logo" width="120" height="100">
    </div>
    <form class="app-form">
      <h2>Application Form</h2>
      <div class="account-type">
        <label>Choose account Type:</label>
        <input type="radio" id="individual" name="accountType" value="individual" checked>
        <label for="individual">Individual</label>
        <input type="radio" id="team" name="accountType" value="team">
        <label for="team">Team</label>
      </div>
      <div class="section-title">Basic Information</div>
      <div class="row">
        <div>
          <label for="firstName">First Name</label>
          <input type="text" id="firstName" name="firstName">
        </div>
        <div>
          <label for="lastName">Last Name</label>
          <input type="text" id="lastName" name="lastName">
        </div>
        <div>
          <label for="middleName">Middle Name</label>
          <input type="text" id="middleName" name="middleName">
        </div>
      </div>
      <div class="row">
        <div>
          <label for="email">Email</label>
          <input type="email" id="email" name="email">
        </div>
        <div>
          <label for="mobile">Mobile number</label>
          <input type="text" id="mobile" name="mobile">
        </div>
      </div>
      <div class="birth-row">
        <label for="birthMonth">Birthdate</label>
        <input type="text" id="birthMonth" name="birthMonth" placeholder="MM" maxlength="2">
        <span>/</span>
        <input type="text" id="birthDay" name="birthDay" placeholder="DD" maxlength="2">
        <span>/</span>
        <input type="text" id="birthYear" name="birthYear" placeholder="YYYY" maxlength="4">
      </div>
      <div class="section-title">Full Address</div>
      <div class="address-row">
        <div>
          <label for="house">House # & Street Name</label>
          <input type="text" id="house" name="house">
        </div>
        <div>
          <label for="barangay">Barangay</label>
          <input type="text" id="barangay" name="barangay">
        </div>
        <div>
          <label for="municipal">Municipal</label>
          <select id="municipal" name="municipal">
            <option value="">Select</option>
            <option value="municipal1">Municipal 1</option>
            <option value="municipal2">Municipal 2</option>
          </select>
        </div>
      </div>
      <div class="address-row">
        <div>
          <label for="province">Province</label>
          <select id="province" name="province">
            <option value="">Select</option>
            <option value="province1">Province 1</option>
            <option value="province2">Province 2</option>
          </select>
        </div>
        <div>
          <label for="zipcode">Zip Code</label>
          <input type="text" id="zipcode" name="zipcode">
        </div>
      </div>
    </form>
    <div class="nav-buttons">
      <button>&lt;</button>
      
      
      
      
      <button>&gt;</button>
    </div>
  </div>
    <!-- FOOTER -->
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
</body>
</html>



