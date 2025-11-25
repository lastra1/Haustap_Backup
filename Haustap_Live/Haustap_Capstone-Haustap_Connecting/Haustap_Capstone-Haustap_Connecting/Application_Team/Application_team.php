<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HausTap - Services</title>
  <link rel="stylesheet" href="css/Application-team.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="/client/css/homepage.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></head>
<body>

  <!-- Header -->
    <?php include dirname(__DIR__) . "/client/includes/header.php"; ?>

  <!-- Main Content -->
  <main>
    <div class="service-box">
      <h3>What Services do you offer?</h3>

      <div class="services-grid">
        <div class="service-column">
          <h4>Cleaning Services</h4>
          <label><input type="checkbox"> Home cleaning</label><br>
          <label><input type="checkbox"> AC cleaning</label>
        </div>

        <div class="service-column">
          <h4>Beauty Services</h4>
          <label><input type="checkbox"> Hair Services</label><br>
          <label><input type="checkbox"> Nail Care</label><br>
          <label><input type="checkbox"> Make-up</label><br>
          <label><input type="checkbox"> Lashes</label>
        </div>

        <div class="service-column">
          <h4>Outdoor Services</h4>
          <label><input type="checkbox"> Gardening & Landscaping</label><br>
          <label><input type="checkbox"> Pest Control</label>
        </div>

        <div class="service-column">
          <h4>Wellness Services</h4>
          <label><input type="checkbox"> Massage</label>
        </div>

        <div class="service-column">
          <h4>Home Repairs</h4>
          <label><input type="checkbox"> Carpentry</label><br>
          <label><input type="checkbox"> Plumbing</label><br>
          <label><input type="checkbox"> Electrical</label><br>
          <label><input type="checkbox"> Appliance Repair</label>
        </div>

        <div class="service-column">
          <h4>Tech & Gadget Services</h4>
          <label><input type="checkbox"> Massage</label>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="pagination">
      <button>&lt;</button>
      
      
      
      
      <button>&gt;</button>
    </div>
  </main>

  <!-- FOOTER -->
  <?php include dirname(__DIR__) . "/client/includes/footer.php"; ?>
</body>
</html>



