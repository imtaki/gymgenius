<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Welcome</title>
</head>
<body>
  <h1>Welcome, {{ $user->name }}!</h1>
  <p>Your 5‑digit verification code is:</p>
  <h2 style="letter-spacing:6px;">{{ $code }}</h2>
  <p>Enter this code in the app to verify your account.</p>
</body>
</html>