<?php
session_start();
include 'config.php';

// Cek login & role
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}
$id_user = $_SESSION['user_id'];

// Query riwayat order customer
$sql = "SELECT o.orderId, o.tanggalOrder, o.statusOrder, o.TotalBayar, 
        s.namaServive, s.hargaDasar, 
        p.namaUsaha as provider, u.nama as nama_provider
    FROM `order` o
    JOIN service s ON o.Service_serviceId = s.serviceId
    JOIN service_provider sp ON s.serviceId = sp.Service_serviceId
    JOIN provider p ON sp.Provider_providerID = p.providerID
    JOIN user u ON p.User_user_id = u.user_id
    WHERE o.User_user_id = ?
    ORDER BY o.tanggalOrder DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_user);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Riwayat Pesanan Saya | ITS SkillShare</title>
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

        .order-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 2px 12px rgba(55, 78, 180, 0.08);
            margin-bottom: 32px;
            padding: 22px 28px;
            display: flex;
            flex-direction: column;
            transition: .18s;
            border-left: 7px solid #2563eb;
        }

        .order-card.selesai {
            border-left-color: #22c55e;
        }

        .order-card.proses {
            border-left-color: #f59e42;
        }

        .order-card.batal {
            border-left-color: #ef4444;
        }

        .order-head {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
        }

        .order-status {
            font-weight: 700;
            border-radius: 12px;
            font-size: .99rem;
            padding: 5px 18px;
        }

        .order-status.selesai {
            background: #d1fae5;
            color: #15803d;
        }

        .order-status.proses {
            background: #fef9c3;
            color: #b45309;
        }

        .order-status.batal {
            background: #fee2e2;
            color: #b91c1c;
        }

        .order-provider {
            font-size: .99rem;
            color: #555;
            background: #e4e4e7;
            border-radius: 8px;
            padding: 2px 14px;
            font-weight: 500;
        }

        .order-date {
            font-size: .97rem;
            color: #2563eb;
            margin-left: auto;
            font-weight: 500;
        }

        .order-title {
            font-size: 1.22rem;
            font-weight: 700;
            color: #2563eb;
        }

        .order-total {
            font-weight: 600;
            color: #0e293b;
            font-size: 1.09rem;
            margin-bottom: 3px;
        }

        .btn-detail {
            border-radius: 18px;
            font-weight: 600;
            background: linear-gradient(90deg, #2563eb 0%, #60a5fa 100%);
            color: #fff;
            border: none;
            margin-top: 7px;
            box-shadow: 0 2px 10px rgba(38, 132, 255, 0.09);
            padding: 8px 20px;
            transition: .18s;
        }

        .btn-detail:hover {
            background: linear-gradient(90deg, #1746a0 0%, #4894ff 100%);
            color: #fff;
        }

        @media (max-width: 700px) {
            .main-card {
                padding: 22px 10px;
            }

            .order-card {
                padding: 16px 9px;
            }
        }
    </style>
</head>

<body>
    <div class="main-card">
        <h2 class="mb-4 fw-bold text-primary text-center" style="font-size:2rem;">Riwayat Pesanan Anda</h2>
        <?php if ($result->num_rows == 0): ?>
            <div class="alert alert-warning text-center">Belum ada riwayat pesanan.</div>
        <?php else: ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="order-card <?= $row['statusOrder'] ?>">
                    <div class="order-head">
                        <span class="order-status <?= $row['statusOrder'] ?>">
                            <?= ucfirst($row['statusOrder']) ?>
                        </span>
                        <span class="order-provider">
                            <i data-feather="user"></i>
                            <?= htmlspecialchars($row['provider']) ?> (<?= htmlspecialchars($row['nama_provider']) ?>)
                        </span>
                        <span class="order-date"><?= date('d M Y', strtotime($row['tanggalOrder'])) ?></span>
                    </div>
                    <div class="order-title mb-1"><?= htmlspecialchars($row['namaServive']) ?></div>
                    <div class="order-total mb-1">
                        Total Bayar: Rp <?= number_format($row['TotalBayar'], 0, ',', '.') ?>
                    </div>
                    <div class="text-secondary" style="font-size: 0.97rem;">
                        Harga Jasa: Rp <?= number_format($row['hargaDasar'], 0, ',', '.') ?>
                    </div>
                    <a href="detail_order.php?id=<?= $row['orderId'] ?>" class="btn btn-detail mt-2">
                        <i data-feather="file-text"></i> Lihat Detail
                    </a>
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