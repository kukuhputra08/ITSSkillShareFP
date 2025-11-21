<?php
session_start();
include 'config.php';

// Cek login customer
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['user_id'];
$orderId = isset($_GET['orderId']) ? mysqli_real_escape_string($conn, $_GET['orderId']) : '';

// Cek apakah order valid dan milik user ini
$sql = "
    SELECT o.*, s.namaServive, s.hargaDasar, p.statusPembayaran
    FROM `Order` o
    JOIN Service s ON o.Service_serviceId = s.serviceId
    LEFT JOIN Payment p ON o.orderId = p.Order_orderId
    WHERE o.orderId = '$orderId' AND o.User_user_id = '$id_user'
    LIMIT 1
";

$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

$order = mysqli_fetch_assoc($result);
if (!$order) {
    echo "<div style='margin:40px; font-weight:bold; color:red;'>Order tidak ditemukan atau bukan milik Anda.</div>";
    exit();
}

// Jika sudah dibayar maka redirect
if ($order['statusPembayaran'] == 'paid') {
    header("Location: order_detail.php?id=$orderId");
    exit();
}

// Proses pembayaran
if (isset($_POST['submit'])) {
    $paymentId = 'P' . bin2hex(random_bytes(4));
    $jumlahPembayar = $order['TotalBayar'];
    $metode = mysqli_real_escape_string($conn, $_POST['metode']);
    $tanggalPembayar = date('Y-m-d H:i:s');
    $statusPembayar = 'paid';

    // Insert ke Payment
    $insert = mysqli_query($conn, "
        INSERT INTO Payment (paymentId, jumlahPembayaran, metodePembayaran, tanggalPembayaran, statusPembayaran, 
            Order_orderId, Order_User_user_id, Order_Service_serviceId)
        VALUES ('$paymentId', '$jumlahPembayar', '$metode', '$tanggalPembayar', '$statusPembayar',
            '{$order['orderId']}', '{$order['User_user_id']}', '{$order['Service_serviceId']}')
    ");

    if ($insert) {
        // Update status order
        mysqli_query($conn, "UPDATE `Order` SET statusOrder = 'menunggu_verifikasi' WHERE orderId = '$orderId'");

        header("Location: order_detail.php?id=$orderId");
        exit();
    } else {
        $error = "Gagal menyimpan pembayaran: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Pembayaran Order | ITS SkillShare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body class="container py-5">
    <h2>Pembayaran Order</h2>

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
                <option value="Kartu Kredit">Tunai</option>
            </select>
        </div>

        <button type="submit" name="submit" class="btn btn-success">Bayar Sekarang</button>
        <a href="order_detail.php?id=<?= $orderId ?>" class="btn btn-secondary">Kembali</a>
    </form>
</body>

</html>