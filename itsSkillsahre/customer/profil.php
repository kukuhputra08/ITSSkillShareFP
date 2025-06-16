<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['user_id'];

// Ambil data user
$q = mysqli_query($conn, "SELECT * FROM User WHERE user_id = '$id_user' LIMIT 1");

$user = mysqli_fetch_assoc($q);

// Proses update profil jika form disubmit
$msg = "";
if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    // Tambah validasi dan proses update password jika ada

    $update = mysqli_query($conn, "UPDATE User SET nama='$nama', email='$email' WHERE user_id='$id_user'");

    if ($update) {
        $msg = "<div class='alert alert-success'>Profil berhasil diperbarui.</div>";
        $_SESSION['nama'] = $nama; // update session nama
    } else {
        $msg = "<div class='alert alert-danger'>Gagal memperbarui profil.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Profil Saya | ITS SkillShare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;700&display=swap" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(120deg, #e0eaff 0%, #f3f9ff 100%);
            font-family: 'Inter', Arial, sans-serif;
            min-height: 100vh;
            padding: 40px 15px;
        }

        .main-card {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 8px 40px 0 rgba(55, 78, 180, 0.10), 0 2px 4px 0 rgba(0, 0, 0, 0.05);
            border-radius: 22px;
            max-width: 600px;
            margin: 0 auto;
            padding: 36px 32px 32px 32px;
        }

        h2 {
            color: #2563eb;
            font-weight: 700;
            font-size: 2.2rem;
            margin-bottom: 24px;
        }

        label {
            font-weight: 600;
            color: #1746a0;
        }

        input[type=text],
        input[type=email],
        input[type=password] {
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            padding: 10px 14px;
            width: 100%;
            font-size: 1rem;
            margin-bottom: 18px;
            transition: border-color 0.3s;
        }

        input[type=text]:focus,
        input[type=email]:focus,
        input[type=password]:focus {
            border-color: #2563eb;
            outline: none;
        }

        button {
            background: linear-gradient(90deg, #2563eb 0%, #60a5fa 100%);
            border: none;
            color: white;
            font-weight: 700;
            padding: 12px 0;
            border-radius: 20px;
            width: 100%;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: linear-gradient(90deg, #1746a0 0%, #4894ff 100%);
        }

        .back-link {
            margin-top: 20px;
            display: inline-block;
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
        }

        .back-link:hover {
            color: #1746a0;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="main-card">
            <h2>Profil Saya</h2>
            <?= $msg ?>
            <form method="post" action="profil.php">
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required />

                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required />

                <!-- Jika mau tambah ganti password, bisa ditambahkan disini -->

                <button type="submit" name="update">Perbarui Profil</button>
            </form>
            <a href="dashboard_customer.php" class="back-link">&larr; Kembali ke Dashboard</a>
        </div>
    </div>
</body>

</html>