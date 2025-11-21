<?php
session_start();
include 'config.php';

// Pastikan user login & customer
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
  header("Location: login.php");
  exit();
}

$id_user = $_SESSION['user_id']; // pastikan ini user_id dari session kamu
$id_order = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

if (empty($id_order)) {
  echo "<div style='margin:40px; font-weight:bold; color:red;'>Order tidak ditemukan.</div>";
  exit();
}

// Query data order + payment
$sql = "
    SELECT o.*, s.namaServive, s.deskripsi, s.hargaDasar, p.statusPembayaran
    FROM `Order` o
    JOIN Service s ON o.Service_serviceId = s.serviceId
    LEFT JOIN Payment p ON o.orderId = p.Order_orderId
    WHERE o.orderId = '$id_order' AND o.User_user_id = '$id_user'
    LIMIT 1
";

$result = mysqli_query($conn, $sql);
if (!$result) {
  die("Query error: " . mysqli_error($conn));
}

$order = mysqli_fetch_assoc($result);
if (!$order) {
  echo "<div style='margin:40px; font-weight:bold; color:red;'>Order tidak ditemukan atau Anda tidak memiliki akses.</div>";
  exit();
}

// Untuk badge status order
function badgeOrder($status)
{
  $colors = [
    'menunggu' => 'warning',
    'proses' => 'primary',
    'selesai' => 'success',
    'batal' => 'secondary',
  ];
  $color = isset($colors[$status]) ? $colors[$status] : 'secondary';
  return '<span class="badge bg-' . $color . '">' . ucfirst($status) . '</span>';
}

// Untuk badge status pembayaran
function badgePayment($status)
{
  if ($status === 'paid') {
    return '<span class="badge bg-success">Sudah Dibayar</span>';
  } else {
    return '<span class="badge bg-danger">Belum Dibayar</span>';
  }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <title>Detail Order | ITS SkillShare</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background: linear-gradient(120deg, #e0eaff 0%, #f3f9ff 100%);
      font-family: 'Inter', Arial, sans-serif;
      min-height: 100vh;
      padding-top: 40px;
      padding-bottom: 40px;
    }

    .detail-card {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 8px 40px rgba(55, 78, 180, 0.1);
      max-width: 700px;
      margin: 0 auto;
      padding: 30px 40px;
    }

    .detail-label {
      font-weight: 600;
      color: #2563eb;
      margin-top: 12px;
    }

    .detail-value {
      color: #374151;
      font-size: 1.1rem;
      margin-bottom: 12px;
    }

    .back-link {
      display: inline-block;
      margin-top: 20px;
      font-weight: 600;
      color: #2563eb;
      text-decoration: none;
      font-size: 1rem;
    }

    .back-link:hover {
      text-decoration: underline;
      color: #1746a0;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="detail-card">
      <h2 class="text-primary mb-4">Detail Order</h2>

      <div class="detail-label">Nama Jasa:</div>
      <div class="detail-value"><?= htmlspecialchars($order['namaServive']) ?></div>

      <div class="detail-label">Deskripsi:</div>
      <div class="detail-value"><?= htmlspecialchars($order['deskripsi']) ?></div>

      <div class="detail-label">Tanggal Order:</div>
      <div class="detail-value"><?= date('d M Y', strtotime($order['tanggalOrder'])) ?></div>

      <div class="detail-label">Status Order:</div>
      <div class="detail-value"><?= badgeOrder($order['statusOrder']) ?></div>

      <div class="detail-label">Harga:</div>
      <div class="detail-value">Rp <?= number_format($order['TotalBayar']) ?></div>

      <div class="detail-label">Status Pembayaran:</div>
      <div class="detail-value"><?= badgePayment($order['statusPembayaran']) ?></div>

      <!-- Tombol Bayar -->
      <?php if ($order['statusPembayaran'] != 'paid') : ?>
        <a href="bayar.php?orderId=<?= $order['orderId'] ?>" class="btn btn-success mt-3">Bayar Sekarang</a>
      <?php else : ?>
        <button class="btn btn-secondary mt-3" disabled>Sudah Dibayar</button>
      <?php endif; ?>

      <a href="riwayat_order.php" class="back-link d-block">&larr; Kembali ke Riwayat Order</a>
    </div>
  </div>
</body>

</html>