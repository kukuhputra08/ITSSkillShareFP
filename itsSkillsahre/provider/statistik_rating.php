<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'provider') {
    header("Location: login.php");
    exit();
}
$id_user = $_SESSION['user_id'];

// Cari providerID milik user ini
$stmt = $conn->prepare("SELECT providerID FROM provider WHERE User_user_id = ?");
$stmt->bind_param("s", $id_user);
$stmt->execute();
$prov = $stmt->get_result()->fetch_assoc();
$providerID = $prov ? $prov['providerID'] : null;
$stmt->close();

if (!$providerID) {
    echo "<p class='text-danger text-center mt-5'>Anda belum terdaftar sebagai provider.</p>";
    exit();
}

// Query statistik rating dari feedback
$sql_stats = "
SELECT 
    AVG(f.rating) as avg_rating,
    COUNT(f.feedbackId) as total_feedback,
    MAX(f.rating) as max_rating,
    MIN(f.rating) as min_rating
FROM feedback f
JOIN `order` o ON f.Order_orderId = o.orderId
JOIN service s ON o.Service_serviceId = s.serviceId
JOIN service_provider sp ON s.serviceId = sp.Service_serviceId
WHERE sp.Provider_providerID = ?
";
$stmt = $conn->prepare($sql_stats);
$stmt->bind_param("s", $providerID);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
$stmt->close();

$avg_rating = $stats['avg_rating'] ? number_format($stats['avg_rating'], 2) : '0.00';
$total_feedback = $stats['total_feedback'];
$max_rating = $stats['max_rating'] ?? '-';
$min_rating = $stats['min_rating'] ?? '-';

// Query daftar feedback detail
$sql_detail = "
SELECT f.*, u.nama as nama_user, s.namaServive as nama_jasa, o.tanggalOrder
FROM feedback f
JOIN `order` o ON f.Order_orderId = o.orderId
JOIN user u ON o.User_user_id = u.user_id
JOIN service s ON o.Service_serviceId = s.serviceId
JOIN service_provider sp ON s.serviceId = sp.Service_serviceId
WHERE sp.Provider_providerID = ?
ORDER BY f.tanggalFeedback DESC
LIMIT 50
";
$stmt = $conn->prepare($sql_detail);
$stmt->bind_param("s", $providerID);
$stmt->execute();
$res_detail = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Statistik Rating | ITS SkillShare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f3f6fd, #e4ebf5);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            color: #1c1c1e;
            padding: 60px 20px 80px;
        }

        .container {
            max-width: 1000px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 28px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
            padding: 36px 40px 48px 40px;
            margin: 0 auto;
        }

        h1 {
            color: #2563eb;
            font-weight: 800;
            font-size: 2.1rem;
            margin-bottom: 28px;
            text-align: center;
        }

        .stat-card {
            background: #e0e7ff;
            border-radius: 15px;
            padding: 28px 16px;
            box-shadow: 0 4px 16px rgba(38, 57, 254, 0.06);
            text-align: center;
        }

        .stat-val {
            font-size: 2.2rem;
            color: #2563eb;
            font-weight: 900;
        }

        .table thead th {
            background: #f1f5ff;
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="dashboard_provider.php" class="btn btn-outline-primary mb-3">&larr; Kembali ke Dashboard</a>
        <h1>Statistik Rating Jasa Anda</h1>
        <div class="row mb-5 g-4">
            <div class="col-12 col-md-3">
                <div class="stat-card">
                    <div class="stat-val"><?= $avg_rating ?></div>
                    <span>Rata-rata Rating</span>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="stat-card">
                    <div class="stat-val"><?= $total_feedback ?></div>
                    <span>Total Feedback</span>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="stat-card">
                    <div class="stat-val"><?= $max_rating ?></div>
                    <span>Rating Tertinggi</span>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="stat-card">
                    <div class="stat-val"><?= $min_rating ?></div>
                    <span>Rating Terendah</span>
                </div>
            </div>
        </div>

        <h5 class="mb-4" style="font-weight:700;color:#374151;">Riwayat Feedback Terbaru</h5>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Jasa</th>
                        <th>Rating</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $res_detail->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['tanggalFeedback']) ?></td>
                            <td><?= htmlspecialchars($row['nama_user']) ?></td>
                            <td><?= htmlspecialchars($row['nama_jasa']) ?></td>
                            <td><?= htmlspecialchars($row['rating']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
$conn->close();
?>