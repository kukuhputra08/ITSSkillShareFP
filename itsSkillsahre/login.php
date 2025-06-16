<?php
session_start();
include 'config.php';

$err = '';
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $pass = trim($_POST['password']);

    // Pastikan password sudah di-hash di DB, contoh ini verifikasi sederhana (plain text)
    $query = mysqli_query($conn, "SELECT * FROM user WHERE email='$email' AND password='$pass'");
    if (mysqli_num_rows($query) == 1) {
        $user = mysqli_fetch_assoc($query);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];

        // Redirect sesuai role
        if ($user['role'] === 'admin') {
            header("Location: /itsSkillsahre/admin/dashboard_admin.php");
        } elseif ($user['role'] === 'provider') {
            header("Location: /itsSkillsahre/provider/dashboard_provider.php");
        } else {
            header("Location: /itsSkillsahre/customer/dashboard_customer.php");
        }
        exit();
    } else {
        $err = "Email atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Masuk | ITS SkillShare</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=SF+Pro+Text&display=swap');

        body {
            font-family: 'SF Pro Text', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen,
                Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background: #f5f5f7;
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1d1d1f;
        }

        .container {
            background: #fff;
            width: 380px;
            padding: 48px 40px 56px;
            border-radius: 32px;
            box-shadow: 0 20px 40px rgb(0 0 0 / 0.1);
            text-align: center;
        }

        h1 {
            font-weight: 700;
            font-size: 2.6rem;
            color: #0071e3;
            /* Apple Blue */
            margin-bottom: 36px;
            letter-spacing: 0.03em;
        }

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
        }

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
        }

        a:hover {
            text-decoration: underline;
        }

        p {
            margin-top: 20px;
            color: #1d1d1f;
        }
    </style>
</head>

<body>
    <main class="container" role="main" aria-label="Form Login ITS SkillShare">
        <h1>Masuk ke ITS SkillShare</h1>
        <?php if ($err): ?>
            <div class="error-message" role="alert"><?= htmlspecialchars($err) ?></div>
        <?php endif; ?>
        <form method="POST" novalidate>
            <input type="email" name="email" placeholder="nama@its.ac.id" required aria-label="Email ITS" autofocus />
            <input type="password" name="password" placeholder="Password" required aria-label="Password" />
            <button type="submit" name="login" aria-label="Login ke ITS SkillShare">Masuk</button>
        </form>
        <p>
            Belum punya akun? <a href="registrasi.php" aria-label="Daftar akun ITS SkillShare">Daftar di sini</a>
        </p>
    </main>
</body>

</html>