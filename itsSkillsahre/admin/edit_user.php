<?php
session_start();
include '../config.php'; // Pastikan path ke config.php benar!

// Cek login & role
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Cek user_id yang mau diedit
if (!isset($_GET['user_id'])) {
    header("Location: manage_users.php");
    exit();
}
$user_id = $conn->real_escape_string($_GET['user_id']);

// Ambil data user
$sql = "SELECT * FROM user WHERE user_id='$user_id'";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    echo "<div class='alert alert-danger'>User tidak ditemukan!</div>";
    exit();
}
$user = $result->fetch_assoc();

// Proses update jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $conn->real_escape_string($_POST['nama']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    if (!empty($password)) {
        // Simpan password baru (tidak di-hash, karena database kamu masih plain)
        $sql_update = "UPDATE user SET nama='$nama', email='$email', password='$password' WHERE user_id='$user_id'";
    } else {
        // Password tidak diubah
        $sql_update = "UPDATE user SET nama='$nama', email='$email' WHERE user_id='$user_id'";
    }

    if ($conn->query($sql_update)) {
        header("Location: manage_users.php?success=1");
        exit();
    } else {
        $error = "Gagal update user: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Edit User | ITS SkillShare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(120deg, #f9fafb 0%, #e2e8f0 100%);
            font-family: 'Inter', Arial, sans-serif;
            min-height: 100vh;
        }

        .edit-container {
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
    <div class="edit-container">
        <h2 class="mb-4 text-center" style="color:#2563eb;font-weight:800;">Edit User</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST" autocomplete="off">
            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password (biarkan kosong jika tidak ingin mengubah)</label>
                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diganti">
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-3">Simpan Perubahan</button>
            <a href="manage_users.php" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
        </form>
    </div>
</body>

</html>