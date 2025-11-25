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
      <img src="/Application_Individual/image/logo.png"   alt="HausTap Logo" width="120" height="100">
    </div>
    <form class="app-form">
      <h2>Application Form</h2>
      <div class="account-type">
        <label>Choose account Type:</label>
        <input type="checkbox" id="accountIndividual" value="individual" checked>
        <label for="accountIndividual">Individual</label>
        <input type="checkbox" id="accountTeam" value="team">
        <label for="accountTeam">Team</label>
      </div>
      <!-- Individual Section -->
      <div class="individual-section">
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
      </div>

      <!-- Team Section -->
      <div class="team-section" style="display:none;">
        <div class="section-title">Team Information</div>
        <div class="row">
          <div>
            <label for="teamName">Team Name</label>
            <input type="text" id="teamName" name="teamName">
          </div>
          <div>
            <label for="teamMembers">Number of members</label>
            <select id="teamMembers" name="teamMembers">
              <option value="">Select</option>
              <option>2</option>
              <option>3</option>
              <option>4</option>
              <option>5</option>
              <option>6</option>
              <option>7</option>
              <option>8</option>
              <option>9</option>
              <option>10</option>
            </select>
          </div>
        </div>
        <div class="row">
          <div>
            <label for="teamLeadName">Team Leader (Full Name)</label>
            <input type="text" id="teamLeadName" name="teamLeadName">
          </div>
          <div>
            <label for="teamLeadEmail">Team Leader Email</label>
            <input type="email" id="teamLeadEmail" name="teamLeadEmail">
          </div>
        </div>
        <div class="row">
          <div>
            <label for="teamLeadPhone">Team Leader contact number</label>
            <input type="text" id="teamLeadPhone" name="teamLeadPhone">
          </div>
        </div>

        <div class="section-title">Full Address (Team Leader only)</div>
        <div class="address-row">
          <div>
            <label for="teamHouse">House no. & Street Name</label>
            <input type="text" id="teamHouse" name="teamHouse">
          </div>
          <div>
            <label for="teamBarangay">Barangay</label>
            <select id="teamBarangay" name="teamBarangay">
              <option value="">Select</option>
              <option value="barangay1">Barangay 1</option>
              <option value="barangay2">Barangay 2</option>
            </select>
          </div>
          <div>
            <label for="teamMunicipal">Municipal</label>
            <input type="text" id="teamMunicipal" name="teamMunicipal">
          </div>
        </div>
        <div class="address-row">
          <div>
            <label for="teamProvince">Province</label>
            <select id="teamProvince" name="teamProvince">
              <option value="">Select</option>
              <option value="province1">Province 1</option>
              <option value="province2">Province 2</option>
            </select>
          </div>
        </div>
      </div>
    </form>
    <div class="nav-buttons">
      <button id="prevBtn" type="button">&lt;</button>
      <button id="nextBtn" type="button">&gt;</button>
    </div>
  </div>
    <!-- FOOTER -->
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
  <script>
    (function(){
      var cbInd = document.getElementById('accountIndividual');
      var cbTeam = document.getElementById('accountTeam');
      var indSec = document.querySelector('.individual-section');
      var teamSec = document.querySelector('.team-section');
      var prevBtn = document.getElementById('prevBtn');
      var nextBtn = document.getElementById('nextBtn');
      var emailInput = document.getElementById('email');
      if (!cbInd || !cbTeam || !indSec || !teamSec) return;
      function sync(){
        var ind = !!cbInd.checked;
        var team = !!cbTeam.checked;
        // Enforce mutual exclusivity while keeping checkbox UI
        if (ind && team) {
          // Prefer the last toggled; handled via event listeners
        }
        indSec.style.display = ind && !team ? 'block' : 'none';
        teamSec.style.display = team && !ind ? 'block' : 'none';
      }
      cbInd.addEventListener('change', function(){
        if (cbInd.checked) cbTeam.checked = false; // only one active
        sync();
      });
      cbTeam.addEventListener('change', function(){
        if (cbTeam.checked) cbInd.checked = false; // only one active
        sync();
      });
      // Initial state
      sync();

      // Navigation: Next goes to Services selection page
      if (nextBtn) {
        nextBtn.addEventListener('click', function(){
          try {
            var v = emailInput && emailInput.value ? String(emailInput.value).trim() : '';
            if (v) { localStorage.setItem('ht.app.email', v); }
          } catch(e) {}
          window.location.href = 'application_services.php';
        });
      }
      // (Optional) Prev: no earlier step yet, keep disabled or no-op
      if (prevBtn) {
        prevBtn.addEventListener('click', function(){
          // No previous page defined; stay on current form
        });
      }

      // Persist email as user types (so later steps can send OTP)
      if (emailInput) {
        emailInput.addEventListener('input', function(){
          try { localStorage.setItem('ht.app.email', String(emailInput.value || '').trim()); } catch(e) {}
        });
      }
    })();
  </script>
</body>
</html>


