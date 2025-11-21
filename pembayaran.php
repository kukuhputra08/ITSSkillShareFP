<?php
session_start();
include 'config.php';

// Cek login customer
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['user_id'];
$orderId = isset($_GET['orderId']) ? $_GET['orderId'] : '';

if (empty($orderId)) {
    die("Order tidak ditemukan.");
}

// Validasi bahwa order milik user
$q = mysqli_query($conn, "
    SELECT o.*, s.namaServive, s.hargaDasar 
    FROM `Order` o 
    JOIN Service s ON o.Service_serviceId = s.serviceId 
    WHERE o.orderId = '$orderId' AND o.User_user_id = '$id_user'
");
$order = mysqli_fetch_assoc($q);

if (!$order) {
    die("Order tidak valid.");
}

// Proses pembayaran
if (isset($_POST['bayar'])) {
    $paymentId = 'P' . bin2hex(random_bytes(3));
    $jumlahPembayar = $order['TotalBayar'];
    $metode = mysqli_real_escape_string($conn, $_POST['metode']);
    $tanggalPembayar = date('Y-m-d H:i:s');
    $statusPembayar = 'paid';

    $insert = mysqli_query($conn, "
        INSERT INTO Payment (paymentId, jumlahPembayar, metodePembaya, tanggalPembayar, statusPembayar, 
            Order_orderId, Order_User_user_id, Order_Service_serviceId)
        VALUES ('$paymentId', '$jumlahPembayar', '$metode', '$tanggalPembayar', '$statusPembayar',
            '{$order['orderId']}', '{$order['User_user_id']}', '{$order['Service_serviceId']}')
    ");

    if ($insert) {
        mysqli_query($conn, "UPDATE `Order` SET statusOrder = 'menunggu_verifikasi' WHERE orderId = '$orderId'");
        header("Location: riwayat_order.php");
        exit();
    } else {
        $error = mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body class="container py-5">
    <h2>Konfirmasi Pembayaran</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <h5><?= htmlspecialchars($order['namaServive']) ?></h5>
            <p>Total Bayar: <b>Rp <?= number_format($order['TotalBayar']) ?></b></p>
        </div>
    </div>

    <form method="POST">
        <div class="mb-3">
            <label for="metode" class="form-label">Pilih Metode Pembayaran:</label>
            <select name="metode" class="form-select" required>
                <option value="">-- Pilih --</option>
                <option value="Transfer Bank">Transfer Bank</option>
                <option value="E-Wallet">E-Wallet</option>
                <option value="Kartu Kredit">Kartu Kredit</option>
            </select>
        </div>

        <button type="submit" name="bayar" class="btn btn-success">Bayar Sekarang</button>
        <a href="riwayat_order.php" class="btn btn-secondary">Batal</a>
    </form>
</body>

</html>