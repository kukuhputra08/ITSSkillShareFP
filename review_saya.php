<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
  header("Location: login.php");
  exit();
}

$id_user = $_SESSION['user_id'];

// Query review sudah diperbaiki sesuai kolom 'id_user_pemesan' di tabel orders
$sql = "SELECT f.*, o.tanggalOrder, s.namaServive, u.nama AS provider
        FROM Feedback f
        JOIN `Order` o ON f.Order_orderId = o.orderId
        JOIN Service s ON f.Order_Service_serviceId = s.serviceId
        JOIN Service_Provider sp ON s.serviceId = sp.Service_serviceId
        JOIN Provider p ON sp.Provider_providerID = p.providerID
        JOIN User u ON p.User_user_id = u.user_id
        WHERE f.Order_User_user_id = '$id_user'
        ORDER BY f.tanggalFeedback DESC";

$q = mysqli_query($conn, $sql);
if (!$q) {
  die("Error query: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Review Saya | ITS SkillShare</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;700&display=swap" rel="stylesheet">
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
      max-width: 900px;
      margin: 0 auto;
      padding: 36px 32px 32px 32px;
    }

    .star {
      color: #fbbf24;
      font-size: 1.2em;
    }

    .review-box {
      background: #f8fafc;
      border-radius: 18px;
      box-shadow: 0 2px 10px 0 rgba(55, 78, 180, 0.08);
      margin-bottom: 24px;
      padding: 20px 24px;
    }

    .provider-badge {
      color: #2563eb;
      background: #e3edfd;
      padding: 2px 9px;
      border-radius: 7px;
      font-size: .95rem;
      margin-right: 7px;
      font-weight: 600;
    }

    .kategori-badge {
      color: #555;
      background: #e4e4e7;
      padding: 2px 9px;
      border-radius: 7px;
      font-size: .95rem;
      font-weight: 600;
    }

    .back-link {
      text-decoration: none;
      color: #2563eb;
      font-weight: 500;
      font-size: 1rem;
      display: inline-block;
      margin-top: 12px;
    }

    .back-link:hover {
      text-decoration: underline;
      color: #1746a0;
    }

    h2 {
      color: #2563eb;
      font-weight: 700;
      font-size: 2.2rem;
      margin-bottom: 24px;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="main-card">
      <h2>Review & Rating Saya</h2>
      <?php if (mysqli_num_rows($q) == 0): ?>
        <div class="alert alert-info text-center">Anda belum memberikan review jasa apapun.</div>
        <?php else: while ($r = mysqli_fetch_assoc($q)): ?>
          <div class="review-box">
            <div class="mb-2">
              <span class="provider-badge"><?= htmlspecialchars($r['provider']) ?></span>
            </div>
            <h5 class="mb-1" style="color:#1746a0;font-weight:600;">
              <?= htmlspecialchars($r['namaServive']) ?>
            </h5>
            <div class="mb-2" style="color:#475569;">
              Order: <?= date('d M Y', strtotime($r['tanggalOrder'])) ?>
            </div>
            <div class="mb-2">
              <?php
              for ($i = 1; $i <= 5; $i++) {
                echo $i <= $r['rating']
                  ? '<span class="star">&#9733;</span>'
                  : '<span class="star" style="color:#cbd5e1">&#9733;</span>';
              }
              ?>
              <span class="ms-2 text-secondary" style="font-size:.98em;">
                (<?= $r['rating'] ?>/5)
              </span>
              <span class="text-muted ms-2" style="font-size:.97em;">
                <?= date('d M Y', strtotime($r['tanggalFeedb'])) ?>
              </span>
            </div>
            <div style="color:#444;font-size:1.05em;">
              <?= nl2br(htmlspecialchars($r['feedbackId'])) ?>
            </div>
          </div>
      <?php endwhile;
      endif; ?>

      <a href="dashboard_customer.php" class="back-link">&larr; Kembali ke Dashboard</a>
    </div>
  </div>
</body>

</html>