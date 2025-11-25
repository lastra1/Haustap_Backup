document.addEventListener('DOMContentLoaded', function () {
  var pass = document.getElementById('password');
  var toggle = document.querySelector('.toggle-pass');
  var form = document.querySelector('.login-form');
  var btn = document.getElementById('loginBtn');
  if (toggle && pass) {
    toggle.addEventListener('click', function () {
      var show = pass.getAttribute('type') === 'password';
      pass.setAttribute('type', show ? 'text' : 'password');
      toggle.textContent = show ? 'Hide' : 'Show';
      toggle.setAttribute('aria-pressed', show ? 'true' : 'false');
      pass.focus();
    });
  }

  if (form) {
    form.addEventListener('submit', function () {
      if (btn) {
        btn.disabled = true;
        btn.textContent = 'Signing in…';
      }
    });
  }
});
