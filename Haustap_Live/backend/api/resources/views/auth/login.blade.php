<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; margin: 2rem; }
        form { max-width: 360px; margin: 0 auto; display: grid; gap: 0.75rem; }
        input { padding: 0.5rem 0.75rem; }
        button { padding: 0.5rem 0.75rem; }
    </style>
    <script>
        async function submitLogin(e) {
            e.preventDefault();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const res = await fetch('/api/auth/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            const data = await res.json().catch(() => ({}));
            alert(res.ok ? 'Login success' : (data.message || 'Login failed'));
        }
    </script>
</head>
<body>
<form onsubmit="submitLogin(event)">
    <h1>Login</h1>
    <label>Email
        <input id="email" type="email" required />
    </label>
    <label>Password
        <input id="password" type="password" required />
    </label>
    <button type="submit">Sign In</button>
    <p><a href="/home">Back to Home</a></p>
</form>
</body>
</html>