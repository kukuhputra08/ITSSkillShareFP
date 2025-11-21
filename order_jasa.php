<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'config.php';

// Cek login dan role harus customer
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
  header("Location: login.php");
  exit();
}

$id_jasa = isset($_GET['id']) ? $_GET['id'] : '';
if (empty($id_jasa)) {
  echo "<div style='margin:40px; font-family: Arial, sans-serif;'><b>Jasa tidak ditemukan.</b></div>";
  exit();
}


// Ambil data jasa beserta kategori dan provider
$qjasa = mysqli_query($conn, "
    SELECT s.serviceId, s.namaServive, s.deskripsi, s.hargaDasar, s.durasi, 
           p.namaUsaha AS provider, u.nama AS nama_user
    FROM Service s
    JOIN Service_Provider sp ON s.serviceId = sp.Service_serviceId
    JOIN Provider p ON sp.Provider_providerID = p.providerID
    JOIN User u ON p.User_user_id = u.user_id
    WHERE s.serviceId = '$id_jasa'
    LIMIT 1
");


$jasa = mysqli_fetch_assoc($qjasa);
if (!$jasa) {
  echo "<div style='margin:40px; font-family: Arial, sans-serif;'><b>Jasa tidak ditemukan atau tidak aktif.</b></div>";
  exit();
}

$msg = "";
if (isset($_POST['order'])) {
  $id_user = $_SESSION['user_id'];  // INI YANG KAMU LUPA
  $orderId = uniqid('O');
  $tanggal_order = date('Y-m-d H:i:s');
  $status_order = "menunggu";
  $total_bayar = $jasa['hargaDasar'];

  $sql = "INSERT INTO `Order` 
        (orderId, tanggalOrder, statusOrder, TotalBayar, User_user_id, Service_serviceId)
        VALUES 
        ('$orderId', '$tanggal_order', '$status_order', '$total_bayar', '$id_user', '$id_jasa')";

  $insert = mysqli_query($conn, $sql);

  if ($insert) {
    $msg = "<div class='alert alert-success text-center' style='font-family: Arial, sans-serif;'>
            Pesanan berhasil dibuat! Lihat di <a href='riwayat_order.php'>Riwayat Order</a>
            </div>";
  } else {
    $msg = "<div class='alert alert-danger text-center' style='font-family: Arial, sans-serif;'>
            Sudah melakukan order. Silahkan melakukan pembayaran.
            </div>";
  }
  if ($insert) {
    header("Location: pembayaran.php?orderId=$orderId");
    exit();
  }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <title>Order Jasa | ITS SkillShare</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background: linear-gradient(120deg, #e0eaff 0%, #f3f9ff 100%);
      font-family: 'Inter', Arial, sans-serif;
      min-height: 100vh;
    }

    .order-card {
      background: rgba(255, 255, 255, 0.98);
      box-shadow: 0 8px 40px 0 rgba(55, 78, 180, 0.10), 0 2px 4px 0 rgba(0, 0, 0, 0.05);
      border-radius: 20px;
      max-width: 540px;
      margin: 44px auto 0 auto;
      padding: 32px 32px 26px 32px;
    }

    .kategori-tag {
      font-size: 0.97rem;
      color: #2563eb;
      background: #e3edfd;
      border-radius: 8px;
      padding: 3px 11px;
      font-weight: 500;
      margin-right: 10px;
    }

    .provider-tag {
      font-size: 0.96rem;
      color: #555;
      background: #e4e4e7;
      border-radius: 8px;
      padding: 3px 11px;
      font-weight: 500;
    }

    .btn-order {
      border-radius: 20px;
      font-weight: 600;
      background: linear-gradient(90deg, #2563eb 0%, #60a5fa 100%);
      color: #fff;
      border: none;
    }

    .btn-order:hover {
      background: linear-gradient(90deg, #1746a0 0%, #4894ff 100%);
      color: #fff;
    }

    .back-link {
      text-decoration: none;
      color: #2563eb;
      font-weight: 500;
      font-size: 1rem;
    }

    .back-link:hover {
      text-decoration: underline;
      color: #1746a0;
    }

    .alert {
      border-radius: 12px;
      font-weight: 600;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="order-card">
      <h3 class="fw-bold text-primary mb-2">Pesan Jasa</h3>
      <?= $msg ?>
      <div class="mb-2">
        <span class="kategori-tag"><?= htmlspecialchars($jasa['namaServive']) ?></span>
        <span class="provider-tag"><?= htmlspecialchars($jasa['provider']) ?></span>
      </div>
      <h4 class="fw-bold mb-2"><?= htmlspecialchars($jasa['namaServive']) ?></h4>
      <div style="color:#475569;" class="mb-2"><?= htmlspecialchars($jasa['deskripsi']) ?></div>
      <div class="fw-bold mb-3" style="color:#2563eb;font-size:1.15rem;">Rp <?= number_format($jasa['hargaDasar']) ?></div>
      <?php if (!$msg): ?>
        <form method="post" class="mb-3">
          <div class="mb-3">
            <label class="form-label">Catatan untuk Provider <span style="color:#888;">(opsional)</span></label>
            <textarea name="catatan" class="form-control" rows="2" placeholder="Contoh: Kapan bisa mulai?"></textarea>
          </div>
          <button type="submit" name="order" class="btn btn-order btn-lg w-100">Konfirmasi & Pesan</button>
        </form>
      <?php endif; ?>
      <a href="cari_jasa.php" class="back-link mt-3 d-inline-block">&larr; Kembali ke Cari Jasa</a>
    </div>
  </div>
</body>

</html>