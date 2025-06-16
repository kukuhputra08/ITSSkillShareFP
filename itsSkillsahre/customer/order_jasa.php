<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='alert alert-danger mt-4'>ID jasa tidak valid.</div>";
    exit();
}
$id_user = $_SESSION['user_id'];
$service_id = $_GET['id'];

// Ambil data jasa
$stmt = $conn->prepare("SELECT s.*, p.namaUsaha
    FROM service s
    JOIN service_provider sp ON s.serviceId = sp.Service_serviceId
    JOIN provider p ON sp.Provider_providerID = p.providerID
    WHERE s.serviceId = ?");
$stmt->bind_param("s", $service_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo "<div class='alert alert-danger mt-4'>Jasa tidak ditemukan.</div>";
    exit();
}
$jasa = $result->fetch_assoc();
$stmt->close();

$error_msg = "";
$success_msg = "";

// Proses form order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_jasa'])) {
    $keterangan = trim($_POST['keterangan']);
    $total_bayar = $jasa['hargaDasar']; // asumsikan 1x jasa, langsung hargaDasar
    $stmt = $conn->prepare("INSERT INTO `order` (orderId, tanggalOrder, statusOrder, TotalBayar, User_user_id, Service_serviceId, statusPembayaran) 
                            VALUES (?, NOW(), 'menunggu', ?, ?, ?, 'menunggu')");
    $new_order_id = uniqid("O"); // misal O123xyz
    $stmt->bind_param("sdss", $new_order_id, $total_bayar, $id_user, $service_id);
    if ($stmt->execute()) {
        $success_msg = "Pesanan berhasil dibuat! Silakan lanjutkan ke pembayaran.";
        header("Location: detail_order.php?id=" . $new_order_id);
        exit();
    } else {
        $error_msg = "Gagal melakukan order jasa. Coba lagi!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Order Jasa | ITS SkillShare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;700&display=swap" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(120deg, #e0eaff 0%, #f3f9ff 100%);
            font-family: 'Inter', Arial, sans-serif;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .main-card {
            background: #fff;
            box-shadow: 0 8px 40px rgba(55, 78, 180, 0.11);
            border-radius: 22px;
            max-width: 700px;
            margin: auto;
            padding: 36px 38px 32px;
        }

        .order-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2563eb;
            margin-bottom: 12px;
        }
    </style>
</head>

<body>
    <div class="main-card">
        <a href="javascript:history.back()" class="btn btn-outline-primary mb-3">&larr; Kembali</a>
        <h2 class="order-title mb-1">Order Jasa: <?= htmlspecialchars($jasa['namaServive']) ?></h2>
        <div class="mb-2">
            <b>Provider:</b> <?= htmlspecialchars($jasa['namaUsaha']) ?>
        </div>
        <div class="mb-2">
            <b>Harga:</b> Rp <?= number_format($jasa['hargaDasar'], 0, ',', '.') ?>
        </div>
        <div class="mb-2">
            <b>Deskripsi:</b> <?= htmlspecialchars($jasa['deskripsi']) ?>
        </div>
        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>
        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success_msg); ?></div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <div class="mb-3">
                <label class="form-label">Keterangan Tambahan (opsional):</label>
                <textarea class="form-control" name="keterangan" rows="3" maxlength="255" placeholder="Tulis keterangan tambahan jika ada..."></textarea>
            </div>
            <button type="submit" name="order_jasa" class="btn btn-primary btn-lg">Pesan Sekarang</button>
        </form>
    </div>
</body>

</html>
<?php $conn->close(); ?>