<?php
session_start();
include 'config.php';

// Validasi login dan role customer
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
  header("Location: login.php");
  exit();
}

$id_user = $_SESSION['user_id'];

// Query ambil data order + payment
$sql = "SELECT o.*, s.namaServive, p.statusPembayaran, p.metodePembayaran
        FROM `Order` o
        JOIN Service s ON o.Service_serviceId = s.serviceId
        LEFT JOIN Payment p 
        ON o.orderId = p.Order_orderId 
        AND o.User_user_id = p.Order_User_user_id 
        AND o.Service_serviceId = p.Order_Service_serviceId
        WHERE o.User_user_id = '$id_user'
        ORDER BY o.tanggalOrder DESC";

$result = mysqli_query($conn, $sql) or die("Query error: " . mysqli_error($conn));
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <title>Riwayat Order | ITS SkillShare</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background: linear-gradient(120deg, #e0eaff 0%, #f3f9ff 100%);
      font-family: "Inter", Arial, sans-serif;
      min-height: 100vh;
      padding: 40px 20px;
    }

    .container {
      max-width: 900px;
      margin: auto;
      background: rgba(255, 255, 255, 0.98);
      border-radius: 16px;
      padding: 30px;
      box-shadow: 0 8px 40px rgba(55, 78, 180, 0.1);
    }

    .order-card {
      background: #f8fafc;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 20px;
    }

    .kategori-tag {
      font-size: 0.9rem;
      color: #2563eb;
      background: #e3edfd;
      border-radius: 8px;
      padding: 4px 12px;
      font-weight: 600;
      margin-right: 10px;
    }

    .provider-tag {
      font-size: 0.9rem;
      color: #555;
      background: #e4e4e7;
      border-radius: 8px;
      padding: 4px 12px;
      font-weight: 600;
    }

    .status-badge {
      font-weight: 600;
      padding: 6px 14px;
      border-radius: 20px;
      color: #fff;
      display: inline-block;
    }

    .status-selesai {
      background: #22c55e;
    }

    .status-proses {
      background: #3b82f6;
    }

    .status-batal {
      background: #ef4444;
    }

    .btn-detail {
      font-weight: 600;
      border-radius: 16px;
    }
  </style>
</head>

<body>
  <div class="container">
    <h2 class="mb-4 text-primary fw-bold">Riwayat Order Saya</h2>

    <?php if (mysqli_num_rows($result) == 0): ?>
      <p>Tidak ada riwayat order.</p>
    <?php else: ?>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="order-card">
          <div class="mb-2">
            <span class="kategori-tag"><?= htmlspecialchars($row['namaServive']) ?></span>
          </div>
          <h4 class="fw-bold mb-1"><?= htmlspecialchars($row['namaServive']) ?></h4>
          <p class="mb-2">Tanggal Order: <?= date('d M Y', strtotime($row['tanggalOrder'])) ?></p>
          <p class="fw-semibold text-primary mb-2">Rp <?= number_format($row['TotalBayar']) ?></p>

          <p>Status Pembayaran: <strong><?= $row['statusPembayaran'] ?? 'Belum Bayar' ?></strong></p>
          <p>Metode Pembayaran: <strong><?= $row['metodePembaya'] ?? '-' ?></strong></p>

          <span class="status-badge 
            <?= ($row['statusOrder'] == 'selesai') ? 'status-selesai' : (($row['statusOrder'] == 'proses') ? 'status-proses' : 'status-batal') ?>">
            <?= ucfirst($row['statusOrder']) ?>
          </span>

          <a href="order_detail.php?id=<?= $row['orderId'] ?>" class="btn btn-primary btn-sm float-end btn-detail">Detail</a>
          <div style="clear: both;"></div>
        </div>
      <?php endwhile; ?>
    <?php endif; ?>

    <a href="dashboard_customer.php" class="btn btn-link">&larr; Kembali ke Dashboard</a>
  </div>
</body>

</html>