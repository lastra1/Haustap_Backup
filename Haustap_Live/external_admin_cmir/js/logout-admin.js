document.addEventListener('DOMContentLoaded', function () {
  var links = document.querySelectorAll('a.logout');
  links.forEach(function (el) {
    el.addEventListener('click', function (e) {
      e.preventDefault();
      // Navigate to admin logout endpoint which clears session
      window.location.href = 'logout.php';
    });
  });
});
