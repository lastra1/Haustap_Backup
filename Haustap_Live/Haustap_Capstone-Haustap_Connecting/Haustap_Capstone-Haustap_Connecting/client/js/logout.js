document.addEventListener('DOMContentLoaded', function () {
  function doLogout() {
    try {
      // Clear client-side auth tokens and user cache
      localStorage.removeItem('haustap_token');
      localStorage.removeItem('haustap_user');
      sessionStorage.removeItem('haustap_token');
      sessionStorage.removeItem('haustap_user');
      // Best-effort clear of non-HttpOnly cookies
      document.cookie = 'haustap_token=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/';
      document.cookie = 'haustap_user=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/';
    } catch (e) {}
    // Redirect to the login route
    window.location.href = '/login';
  }

  // Bind to both anchor and button variants used across pages
  var els = document.querySelectorAll('a.logout, button.logout-btn');
  els.forEach(function (el) {
    el.addEventListener('click', function (e) {
      e.preventDefault();
      doLogout();
    });
  });
});
