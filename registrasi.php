<?php
include 'config.php';
$err = '';

if (isset($_POST['register'])) {
  $nama = trim($_POST['nama']);
  $email = trim($_POST['email']);
  $pass  = trim($_POST['password']);
  $role = "customer"; // Default role saat registrasi

  // Cek email sudah terdaftar
  $cek = mysqli_query($conn, "SELECT * FROM User WHERE email='$email'");
  if (mysqli_num_rows($cek) > 0) {
    $err = "Email sudah terdaftar!";
  } else {
    // Simpan ke database
    $simpan = mysqli_query($conn, "INSERT INTO User (nama,email,password,role) VALUES ('$nama','$email','$pass','$role')");
    if ($simpan) {
      header("Location: login.php");
      exit();
    } else {
      $err = "Gagal mendaftar, silakan coba lagi.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <title>Registrasi Akun | ITS SkillShare</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    @import url('https://fonts.googleapis.com/css2?family=SF+Pro+Text&display=swap');

    body {
      font-family: 'SF Pro Text', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen,
        Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
      background: #f5f5f7;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      color: #1d1d1f;
      -webkit-font-smoothing: antialiased;
    }

    .container {
      background: #fff;
      width: 380px;
      padding: 48px 40px 56px;
      border-radius: 32px;
      box-shadow: 0 20px 40px rgb(0 0 0 / 0.1);
      text-align: center;
      user-select: none;
    }

    h1 {
      font-weight: 700;
      font-size: 2.6rem;
      color: #0071e3;
      margin-bottom: 36px;
      letter-spacing: 0.03em;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 14px 18px;
      margin-bottom: 24px;
      font-size: 1rem;
      border-radius: 14px;
      border: 1px solid #d2d2d7;
      background: #f5f5f7;
      color: #1d1d1f;
      transition: border-color 0.3s ease;
      box-sizing: border-box;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus {
      border-color: #0071e3;
      outline: none;
      background: #fff;
    }

    button {
      width: 100%;
      background: #0071e3;
      border: none;
      border-radius: 24px;
      padding: 16px 0;
      font-weight: 700;
      font-size: 1.2rem;
      color: white;
      cursor: pointer;
      box-shadow: 0 6px 20px rgb(0 113 227 / 0.5);
      transition: background 0.3s ease;
    }

    button:hover {
      background: #005bb5;
    }

    .error-message {
      margin-bottom: 24px;
      padding: 14px;
      background: #ff3b30;
      border-radius: 14px;
      color: white;
      font-weight: 600;
      font-size: 0.9rem;
      box-shadow: 0 4px 12px rgb(255 59 48 / 0.5);
    }

    a {
      font-weight: 600;
      color: #0071e3;
      text-decoration: none;
      user-select: none;
      font-size: 1rem;
      display: inline-block;
      margin-top: 20px;
    }

    a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <main class="container" role="main" aria-label="Form Registrasi ITS SkillShare">
    <h1>Daftar Akun ITS SkillShare</h1>
    <?php if ($err): ?>
      <div class="error-message" role="alert"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>
    <form method="post" novalidate>
      <input type="text" name="nama" placeholder="Nama Lengkap" required aria-label="Nama Lengkap" autofocus />
      <input type="email" name="email" placeholder="nama@its.ac.id" required aria-label="Email ITS" />
      <input type="password" name="password" placeholder="Password" required aria-label="Password" />
      <button type="submit" name="register" aria-label="Daftar akun ITS SkillShare">Daftar</button>
    </form>
    <a href="login.php" aria-label="Login ITS SkillShare">Sudah punya akun? Masuk di sini</a>
  </main>
</body>

</html>