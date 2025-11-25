<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Booking Edit Address</title>
  <link rel="stylesheet" href="/css/global.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/booking_edit_address.css" />
  <link rel="stylesheet" href="/client/css/homepage.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>
  <!-- HEADER -->
  <?php include dirname(__DIR__) . "/client/includes/header.php"; ?>

   <main class="edit-address-page">
    <div class="edit-address-box">

      <!-- Header -->
      <div class="box-header">
        <button class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back</button>
        <h2>Edit Address</h2>
        <button class="delete-btn"><i class="fa-solid fa-trash"></i> Delete</button>
      </div>

      <h3 class="section-title">Address Details</h3>

      <!-- Form Section -->
      <form class="address-form">

        <div class="row">
          <div class="form-group">
            <label for="houseNumber">House Number</label>
            <input type="text" id="houseNumber" name="houseNumber" placeholder="Enter house number" />
          </div>

          <div class="form-group">
            <label for="street">Street</label>
            <input type="text" id="street" name="street" placeholder="Enter street name" />
          </div>
        </div>

        <div class="form-group">
          <label for="barangay">Barangay Name</label>
          <select id="barangay" name="barangay">
            <option value="">Select Barangay</option>
            <option value="Langgam">Langgam</option>
            <option value="Santo Niño">Santo Niño</option>
            <option value="San Vicente">San Vicente</option>
          </select>
        </div>

        <div class="form-group">
          <label for="municipal">Municipality</label>
          <select id="municipal" name="municipal">
            <option value="">Select Municipality</option>
            <option value="San Pedro">San Pedro</option>
            <option value="Biñan">Biñan</option>
            <option value="Santa Rosa">Santa Rosa</option>
          </select>
        </div>

        <div class="form-group">
          <label for="province">Province</label>
          <select id="province" name="province">
            <option value="">Select Province</option>
            <option value="Laguna">Laguna</option>
            <option value="Cavite">Cavite</option>
            <option value="Batangas">Batangas</option>
          </select>
        </div>

        <div class="form-actions">
          <button type="submit" class="submit-btn">Submit</button>
        </div>
      </form>
    </div>
  </main>
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>

</body>
</html>