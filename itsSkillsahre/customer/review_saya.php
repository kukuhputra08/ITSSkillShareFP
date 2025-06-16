<?php
session_start();
include 'config.php';

// Cek login & role
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}
$id_user = $_SESSION['user_id'];

// Query daftar feedback milik customer ini
$sql = "SELECT f.*, o.tanggalOrder, s.namaServive, p.namaUsaha as provider
    FROM feedback f
    JOIN `order` o ON f.Order_orderId = o.orderId
    JOIN service s ON o.Service_serviceId = s.serviceId
    JOIN service_provider sp ON s.serviceId = sp.Service_serviceId
    JOIN provider p ON sp.Provider_providerID = p.providerID
    WHERE f.Order_User_user_id = ?
    ORDER BY f.tanggalFeedback DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_user);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Review Saya | ITS SkillShare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;700&display=swap" rel="stylesheet" />
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        body {
            background: linear-gradient(120deg, #e0eaff 0%, #f3f9ff 100%);
            font-family: 'Inter', Arial, sans-serif;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .main-card {
            background: rgba(255, 255, 255, 0.97);
            box-shadow: 0 8px 40px rgba(55, 78, 180, 0.1);
            border-radius: 22px;
            max-width: 900px;
            margin: auto;
            padding: 36px 40px 26px;
        }

        .review-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 2px 12px rgba(55, 78, 180, 0.08);
            margin-bottom: 30px;
            padding: 18px 28px;
            transition: .18s;
            border-left: 6px solid #2563eb;
            display: flex;
            flex-direction: column;
        }

        .review-head {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .provider-tag {
            font-size: .99rem;
            color: #555;
            background: #e4e4e7;
            border-radius: 8px;
            padding: 3px 13px;
            font-weight: 500;
        }

        .review-date {
            font-size: .97rem;
            color: #2563eb;
            margin-left: auto;
            font-weight: 500;
        }

        .review-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2563eb;
        }

        .desc-box {
            background: #f3f6fd;
            border-radius: 11px;
            padding: 10px 16px;
            color: #374151;
            font-size: 1.01rem;
            margin-bottom: 0;
        }

        .rating-star {
            color: #fbbf24;
            font-size: 1.28rem;
            margin-right: 2px;
            vertical-align: bottom;
        }
    </style>
</head>

<body>
    <div class="main-card">
        <h2 class="mb-4 fw-bold text-primary text-center" style="font-size:2rem;">Review Saya</h2>
        <?php if ($result->num_rows == 0): ?>
            <div class="alert alert-warning text-center">Belum ada review yang Anda berikan.</div>
        <?php else: ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="review-card">
                    <div class="review-head">
                        <span class="review-title"><?= htmlspecialchars($row['namaServive']) ?></span>
                        <span class="provider-tag"><i data-feather="user"></i> <?= htmlspecialchars($row['provider']) ?></span>
                        <span class="review-date"><?= date('d M Y', strtotime($row['tanggalFeedback'])) ?></span>
                    </div>
                    <div class="mb-1">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="rating-star"><?= $i <= $row['rating'] ? '★' : '☆' ?></span>
                        <?php endfor; ?>
                        <span style="color:#374151;font-size:1.04rem;">
                            (<?= $row['rating'] ?>/5)
                        </span>
                    </div>
                    <?php if ($row['deskripsi']): ?>
                        <div class="desc-box mt-2">
                            <?= nl2br(htmlspecialchars($row['deskripsi'])) ?>
                        </div>
                    <?php endif; ?>
                    <div class="text-secondary" style="font-size:.97rem; margin-top:6px;">
                        Order tanggal: <?= date('d M Y', strtotime($row['tanggalOrder'])) ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
        <a href="dashboard_customer.php" class="btn btn-outline-primary mt-3">&larr; Kembali ke Dashboard</a>
    </div>
    <script>
        feather.replace();
    </script>
</body>

</html>
<?php
$stmt->close();
$conn->close();
?>