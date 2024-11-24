<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Halaman Login</title>
  <!-- Link ke Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Gaya tambahan untuk menyesuaikan tampilan halaman -->
  <style>
    body {
      background-color: #f4f6f9;
    }
    .login-container {
      max-width: 400px;
      margin: 100px auto;
      background-color: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .login-container h2 {
      margin-bottom: 20px;
      font-size: 24px;
      font-weight: bold;
      text-align: center;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <h2>Login</h2>
    <img src="https://ebc-didactic.com/wp-content/uploads/2023/01/smk-pusdikhub-cimahi.webp" style="width: 350px;" alt="Logo" class="center-image">
    <p class="text-center"><strong>SEKOLAH MENENGAH KEJURUAN PUSDIKHUBAD CIMAHI</strong></p>
    <hr>
    <form id="loginForm">
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
  </div>

  <!-- Link ke Bootstrap JS (untuk interaksi jika diperlukan) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
      e.preventDefault();
      var formData = new FormData(this);
      fetch('/login', {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => response.json())
      .then(data => {
        if(data.success) {
          window.location.href = '/agenda';
        } else {
          alert('Username atau password salah.');
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
    });
  </script>
</body>
</html>
