<?php
session_start();
include 'config.php';

// Validasi role (hanya provider bisa akses)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'provider') {
    header('Location: login.php');
    exit();
}

$id_user = $_SESSION['user_id'];
$error = "";

// Ambil provider ID berdasarkan user login
$provider = mysqli_fetch_assoc(mysqli_query($conn, "SELECT providerID FROM Provider WHERE User_user_id = '$id_user'"));
if (!$provider) {
    die("Anda belum terdaftar sebagai Provider.");
}
$providerID = $provider['providerID'];

if (isset($_POST['submit'])) {
    $namaServive = mysqli_real_escape_string($conn, $_POST['namaServive']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $hargaDasar = mysqli_real_escape_string($conn, $_POST['hargaDasar']);
    $durasi = mysqli_real_escape_string($conn, $_POST['durasi']);

    $serviceId = 'S' . bin2hex(random_bytes(4));

    $insertService = mysqli_query($conn, "INSERT INTO Service (serviceId, namaServive, deskripsi, hargaDasar, durasi)
                        VALUES ('$serviceId', '$namaServive', '$deskripsi', '$hargaDasar', '$durasi')");

    if ($insertService) {
        // Insert ke Service_Provider (ISI 3 FIELD sesuai FK)
        $insertMapping = mysqli_query($conn, "
            INSERT INTO Service_Provider (Service_serviceId, Provider_providerID, Provider_User_user_id)
            VALUES ('$serviceId', '$providerID', '$id_user')
        ");

        if ($insertMapping) {
            header("Location: manage_jasa.php");
            exit();
        } else {
            $error = "Gagal menambahkan ke Service_Provider: " . mysqli_error($conn);
        }
    } else {
        $error = "Gagal menambahkan jasa: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Jasa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body class="container py-5">
    <h2 class="mb-4">Tambah Jasa</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Nama Jasa:</label>
            <input type="text" name="namaServive" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Deskripsi:</label>
            <textarea name="deskripsi" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label>Harga Dasar:</label>
            <input type="number" name="hargaDasar" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Durasi (menit):</label>
            <input type="number" name="durasi" class="form-control" required>
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
        <a href="manage_jasa.php" class="btn btn-secondary">Kembali</a>
    </form>
</body>

</html>