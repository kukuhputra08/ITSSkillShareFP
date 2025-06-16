<?php
session_start();
include 'config.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

// Statistik ringkas
// Statistik ringkas
$res_user = $conn->query("SELECT COUNT(*) as total FROM user");
$total_user = $res_user->fetch_assoc()['total'];
$res_jasa = $conn->query("SELECT COUNT(*) as total FROM service");
$total_jasa = $res_jasa->fetch_assoc()['total'];
$res_order = $conn->query("SELECT COUNT(*) as total FROM `order`");
$total_order = $res_order->fetch_assoc()['total'];
$res_transaksi = $conn->query("SELECT SUM(TotalBayar) as total FROM `order` WHERE statusOrder='selesai'");
$total_transaksi = $res_transaksi->fetch_assoc()['total'] ?? 0;


// Tabel riwayat order (join biar lebih informatif)
$sql_riwayat = "SELECT o.*, u.nama as nama_user, s.namaServive as nama_jasa
    FROM `order` o
    LEFT JOIN user u ON o.User_user_id = u.user_id
    LEFT JOIN service s ON o.Service_serviceId = s.serviceId
    ORDER BY o.tanggalOrder DESC
    LIMIT 50";
$res_riwayat = $conn->query($sql_riwayat);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan | ITS SkillShare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa, #e4ebf5);
            font-family: 'SF Pro Text', sans-serif;
            min-height: 100vh;
            color: #1c1c1e;
            padding: 60px 20px 80px;
        }

        .container {
            max-width: 1120px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 28px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
            padding: 36px 40px 48px 40px;
            margin: 0 auto;
        }

        h1 {
            color: #007aff;
            font-weight: 800;
            font-size: 2.4rem;
            margin-bottom: 30px;
            text-align: center;
        }

        .stat-card {
            background: #e0e7ff;
            border-radius: 18px;
            padding: 30px 26px;
            box-shadow: 0 6px 26px rgba(38, 57, 254, 0.07);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .stat-card h2 {
            font-size: 2.1rem;
            color: #2563eb;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .stat-card span {
            font-size: 1.1rem;
            color: #374151;
        }

        .table thead th {
            background: #f1f5ff;
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="dashboard_admin.php" class="btn btn-outline-primary mb-3">&larr; Kembali ke Dashboard</a>
        <h1>Laporan & Rekap Data</h1>

        <div class="row mb-5">
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <h2><?= $total_user ?></h2>
                    <span>Total User</span>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <h2><?= $total_jasa ?></h2>
                    <span>Total Jasa</span>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <h2><?= $total_order ?></h2>
                    <span>Total Order</span>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <h2>Rp<?= number_format($total_transaksi, 0, ',', '.') ?></h2>
                    <span>Total Transaksi Selesai</span>
                </div>
            </div>
        </div>

        <h4 class="mb-4" style="font-weight:700;color:#374151;">Riwayat Order Terbaru</h4>
        <div class="table-responsive">
            <button class="btn btn-success mb-3" onclick="downloadCSV()">Download CSV</button>

            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Tanggal</th>
                        <th>User</th>
                        <th>Jasa</th>
                        <th>Status</th>
                        <th>Total Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $res_riwayat->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['orderId']) ?></td>
                            <td><?= htmlspecialchars($row['tanggalOrder']) ?></td>
                            <td><?= htmlspecialchars($row['nama_user'] ?? $row['User_user_id']) ?></td>
                            <td><?= htmlspecialchars($row['nama_jasa'] ?? $row['Service_serviceId']) ?></td>
                            <td><?= htmlspecialchars($row['statusOrder']) ?></td>
                            <td>Rp<?= number_format($row['TotalBayar'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function downloadCSV() {
            let table = document.querySelector('table');
            let rows = Array.from(table.querySelectorAll('tr'));
            let csv = [];
            for (let row of rows) {
                let cols = Array.from(row.querySelectorAll('th,td'));
                let rowData = cols.map(col => `"${col.innerText.replace(/"/g, '""')}"`);
                csv.push(rowData.join(','));
            }
            let csvContent = "data:text/csv;charset=utf-8," + csv.join("\n");
            let link = document.createElement("a");
            link.setAttribute("href", csvContent);
            link.setAttribute("download", "laporan_order.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>



</body>

</html>
<?php
$conn->close();
?>