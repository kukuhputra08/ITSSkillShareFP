<?php
session_start();
include 'config.php';

// Cek jika dari menu Kelola Pengguna
$is_admin = false;
if (isset($_GET['from']) && $_GET['from'] == 'manage_users') {
    // Pastikan hanya admin yang bisa akses dari menu ini
    if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        $is_admin = true;
    } else {
        header('Location: login.php');
        exit();
    }
}

// Proses jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $conn->real_escape_string($_POST['nama']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);
    $role = 'customer'; // selalu customer!

    // Validasi
    $error = "";
    if (empty($nama) || empty($email) || empty($password)) {
        $error = "Semua field harus diisi!";
    } else {
        $cek = $conn->query("SELECT * FROM user WHERE email='$email'");
        if ($cek->num_rows > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            // Generate user_id baru dengan prefix 'U'
            $getMax = $conn->query("SELECT MAX(CAST(SUBSTRING(user_id, 2) AS UNSIGNED)) as maxid FROM user WHERE user_id LIKE 'U%'");
            $row = $getMax->fetch_assoc();
            $nextid = str_pad(($row['maxid'] ?? 0) + 1, 2, '0', STR_PAD_LEFT);
            $user_id = 'U' . $nextid;

            $sql = "INSERT INTO user (user_id, nama, email, password, role) VALUES ('$user_id', '$nama', '$email', '$password', '$role')";
            if ($conn->query($sql)) {
                if ($is_admin) {
                    header('Location: manage_users.php?success=1');
                } else {
                    header('Location: login.php?register=success');
                }
                exit();
            } else {
                $error = "Gagal mendaftar: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Registrasi Pengguna | ITS SkillShare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(120deg, #f9fafb 0%, #e2e8f0 100%);
            font-family: 'Inter', Arial, sans-serif;
            min-height: 100vh;
        }

        .regis-container {
            max-width: 480px;
            margin: 60px auto;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(55, 78, 180, 0.10);
            padding: 36px 32px 28px;
        }

        .form-label {
            font-weight: 600;
        }

        .btn-primary {
            border-radius: 30px;
            font-weight: 700;
            padding: 10px 0;
        }
    </style>
</head>

<body>
    <div class="regis-container">
        <h2 class="mb-4 text-center" style="color:#2563eb;font-weight:800;">
            <?= $is_admin ? 'Tambah Pengguna Baru' : 'Registrasi Akun' ?>
        </h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST" autocomplete="off">
            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" class="form-control" required value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-3"><?= $is_admin ? 'Tambah Pengguna' : 'Registrasi' ?></button>
            <?php if ($is_admin): ?>
                <a href="manage_users.php" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-secondary w-100 mt-2">Kembali ke Login</a>
            <?php endif; ?>
        </form>
    </div>
</body>

</html>