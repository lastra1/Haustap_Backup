<?php require_once __DIR__ . '/includes/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - View Profile</title>
  <link rel="stylesheet" href="css/profile_admin.css">
  <script src="js/lazy-images.js" defer></script>
</head>
<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <?php $active = 'dashboard'; include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
      <header class="topbar">
        <div class="user">
          <button class="notif-btn">🔔</button>
          <div class="user-menu">
            <button class="user-btn" id="userDropdownBtn">
              <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?> ▼
            </button>
            <div class="dropdown" id="userDropdown">
              <a href="admin_profile.php">View Profile</a>
              <a href="/admin_haustap/admin_haustap/change_password.php">Change Password</a>
              <a href="/admin_haustap/admin_haustap/activity_logs.php">Activity Logs</a>
              <a href="logout.php" class="logout">Log out</a>
            </div>
          </div>
        </div>
      </header>

      <!-- Page Header -->
      <section class="page-header">
        <h3>Admin &gt; View Profile</h3>
      </section>

      <!-- Profile Section -->
      <section class="profile-section">
        <div class="profile-card">
          <!-- LEFT SIDE -->
          <div class="profile-left">
            <h2>Profile</h2>
            <form>
              <label>Name</label>
              <input type="text" placeholder="Enter name"
                     value="<?php echo htmlspecialchars($_SESSION['admin_name'] ?? ''); ?>">

              <label>Email</label>
              <div class="email-field">
                <span><?php echo htmlspecialchars($_SESSION['admin_email'] ?? ''); ?></span>
                <a href="/admin_haustap/admin_haustap/change_password.php">Change</a>
              </div>

              <label>Role</label>
              <p>Superadmin</p>

              <label>Phone Number</label>
              <div class="phone-field">
                <span>********46</span>
                <a href="#">Change</a>
              </div>

              <label>Gender</label>
              <div class="gender">
                <label><input type="radio" name="gender"> Male</label>
                <label><input type="radio" name="gender"> Female</label>
              </div>

              <label>Date of Birth</label>
              <a href="#" class="add-link">Add</a>

              <div class="buttons">
                <button type="button" class="change-password">Change Password</button>
                <button type="submit" class="save-btn">Save</button>
              </div>
            </form>
          </div>

          <div class="divider"></div>

          <!-- RIGHT SIDE -->
          <div class="profile-right">
            <div class="image-upload">
              <!-- Avatar preview (shows uploaded or default image) -->
              <img
                id="avatarPreview"
                src="<?php echo htmlspecialchars($_SESSION['admin_avatar'] ?? '/storage/uploads/admins/default-avatar.svg'); ?>"
                alt="avatar"
                style="width:80px;height:80px;border-radius:50%;object-fit:cover;display:none;margin-bottom:10px;"
              >

              <!-- Fallback SVG icon -->
              <svg id="avatarFallback" xmlns="http://www.w3.org/2000/svg" width="80" height="80"
                   fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                <path fill-rule="evenodd"
                      d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7
                      a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10
                      8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
              </svg>

              <!-- File input -->
              <input type="file" id="avatarInput" name="avatar"
                     accept="image/png,image/jpeg" style="display:none">

              <button type="button" id="selectImageBtn" class="upload-btn">Select Image</button>
              <p class="file-info">File size: maximum 3 MB<br>File extension: .JPEG, .PNG</p>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>

  <!-- Scripts -->
  <script>
  document.addEventListener("DOMContentLoaded", () => {
    const selectBtn = document.getElementById('selectImageBtn');
    const input = document.getElementById('avatarInput');
    const preview = document.getElementById('avatarPreview');
    const fallback = document.getElementById('avatarFallback');

    // --- INITIAL DISPLAY STATE ---
    function setInitialDisplay() {
      const src = preview.getAttribute('src') || '';
      if (src && !src.endsWith('default-avatar.svg')) {
        preview.style.display = 'block';
        fallback.style.display = 'none';
      } else {
        preview.style.display = 'none';
        fallback.style.display = 'block';
      }
    }
    setInitialDisplay();

    // --- SHOW LOCAL PREVIEW ---
    function showPreview(file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        preview.src = e.target.result;
        preview.style.display = 'block';
        fallback.style.display = 'none';
        preview.dataset.temp = '1';
      };
      reader.readAsDataURL(file);
    }

    // --- UPLOAD FILE TO SERVER ---
    async function uploadFile(file) {
      const maxBytes = 3 * 1024 * 1024; // 3MB
      if (file.size > maxBytes) {
        alert('File exceeds 3 MB limit.');
        setInitialDisplay();
        return;
      }

      const allowed = ['image/png', 'image/jpeg'];
      if (!allowed.includes(file.type)) {
        alert('Invalid file type. Only JPEG and PNG allowed.');
        setInitialDisplay();
        return;
      }

      const fd = new FormData();
      fd.append('avatar', file);

      selectBtn.disabled = true;
      selectBtn.textContent = 'Uploading...';

      try {
        // ensure cookies are sent even if the page is served via a different hostname/port
        const res = await fetch('api/upload_image.php', {
          method: 'POST',
          body: fd,
          credentials: 'include'
        });

        // read raw text first (defensive) then try parse as JSON
        const text = await res.text();
        let data;
        try {
          data = text ? JSON.parse(text) : null;
        } catch (parseErr) {
          console.error('Failed to parse upload response as JSON:', parseErr, 'raw:', text);
          alert('Upload failed: server returned invalid JSON. See console for details.');
          setInitialDisplay();
          return;
        }

        // If the session is no longer valid the server returns 401 + JSON.
        if (res.status === 401) {
          console.warn('Upload endpoint returned 401 - unauthorized');
          alert('Your session appears to have expired. Please log in again.');
          // redirect to login in same directory
          const basePath = window.location.pathname.replace(/\/[^\/]*$/,'');
          window.location.href = basePath + '/login.php'.replace(/\/\/+/, '/');
          return;
        }

        if (data && data.ok && data.url) {
          preview.src = data.url;
          preview.dataset.temp = '0';
          preview.style.display = 'block';
          fallback.style.display = 'none';
          alert('Image uploaded successfully!');
        } else {
          const errMsg = (data && data.error) ? data.error : 'Unknown error';
          throw new Error(errMsg);
        }
      } catch (err) {
        console.error('Upload failed:', err);
        alert('Upload failed: ' + err.message);
        setInitialDisplay();
      } finally {
        selectBtn.disabled = false;
        selectBtn.textContent = 'Select Image';
      }
    }

    // --- EVENT HANDLERS ---
    selectBtn.addEventListener('click', () => input.click());
    input.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (!file) return;
      showPreview(file);
      uploadFile(file);
    });

    // Change password button should navigate to change_password.php
    const changePwdBtn = document.querySelector('.change-password');
    if (changePwdBtn) {
      changePwdBtn.addEventListener('click', () => {
        // build an absolute path to change_password.php in the same directory as this page
        const basePath = window.location.pathname.replace(/\/[^\/]*$/,'');
        window.location.href = basePath + '/change_password.php'.replace(/\/\/+/, '/');
      });
    }

    // --- DROPDOWN MENU ---
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
  });
  </script>
</body>
</html>
