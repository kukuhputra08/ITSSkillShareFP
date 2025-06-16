<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'provider') {
    header("Location: login.php");
    exit();
}
$id_user = $_SESSION['user_id'];

// Update status pesanan
if (isset($_POST['update_status'])) {
    $orderId = $_POST['orderId'];
    $new_status = $_POST['statusOrder'];
    // Hanya update jika order memang milik provider ini
    $sql = "UPDATE `Order` o
        JOIN service s ON o.Service_serviceId = s.serviceId
        JOIN service_provider sp ON s.serviceId = sp.Service_serviceId
        JOIN provider p ON sp.Provider_providerID = p.providerID
        SET o.statusOrder = ?
        WHERE o.orderId = ? AND p.User_user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $new_status, $orderId, $id_user);
    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Status pesanan berhasil diubah.";
    } else {
        $_SESSION['error_msg'] = "Gagal mengubah status pesanan.";
    }
    $stmt->close();
    header('Location: manage_orders.php');
    exit();
}

// Tampilkan semua pesanan milik provider ini
$sql = "SELECT o.orderId, o.tanggalOrder, o.statusOrder, o.TotalBayar, o.User_user_id, u.nama as nama_user, s.namaServive as nama_jasa
    FROM `Order` o
    JOIN user u ON o.User_user_id = u.user_id
    JOIN service s ON o.Service_serviceId = s.serviceId
    JOIN service_provider sp ON s.serviceId = sp.Service_serviceId
    JOIN provider p ON sp.Provider_providerID = p.providerID
    WHERE p.User_user_id = ?
    ORDER BY o.tanggalOrder DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_user);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Pesanan | Provider</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa, #e4ebf5);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            color: #1c1c1e;
            padding: 60px 20px 80px;
        }

        .container {
            max-width: 1100px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 28px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
            padding: 36px 40px 48px 40px;
            margin: 0 auto;
        }

        h1 {
            color: #2563eb;
            font-weight: 800;
            font-size: 2.2rem;
            margin-bottom: 24px;
            text-align: center;
        }

        .form-select {
            border-radius: 10px;
        }

        .btn-save {
            background: #2563eb;
            color: #fff;
            font-weight: 700;
            border-radius: 12px;
        }

        .status-badge {
            font-weight: 700;
            border-radius: 12px;
            padding: 5px 14px;
            font-size: .98rem;
        }

        .status-proses {
            background: #fef9c3;
            color: #b45309;
        }

        .status-selesai {
            background: #d1fae5;
            color: #065f46;
        }

        .status-batal {
            background: #fee2e2;
            color: #b91c1c;
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="dashboard_provider.php" class="btn btn-outline-primary mb-3">&larr; Kembali ke Dashboard</a>
        <h1>Kelola Pesanan</h1>
        <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:16px;">
                <?= htmlspecialchars($_SESSION['success_msg']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success_msg']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_msg'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:16px;">
                <?= htmlspecialchars($_SESSION['error_msg']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error_msg']); ?>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Nama Customer</th>
                        <th>Jasa</th>
                        <th>Tanggal Order</th>
                        <th>Status</th>
                        <th>Total Bayar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['orderId']) ?></td>
                            <td><?= htmlspecialchars($row['nama_user']) ?></td>
                            <td><?= htmlspecialchars($row['nama_jasa']) ?></td>
                            <td><?= htmlspecialchars($row['tanggalOrder']) ?></td>
                            <td>
                                <?php
                                if ($row['statusOrder'] == 'proses') {
                                    echo '<span class="status-badge status-proses">Proses</span>';
                                } elseif ($row['statusOrder'] == 'selesai') {
                                    echo '<span class="status-badge status-selesai">Selesai</span>';
                                } elseif ($row['statusOrder'] == 'batal') {
                                    echo '<span class="status-badge status-batal">Batal</span>';
                                } else {
                                    echo htmlspecialchars($row['statusOrder']);
                                }
                                ?>
                            </td>
                            <td>Rp<?= number_format($row['TotalBayar'], 0, ',', '.') ?></td>
                            <td>
                                <?php if ($row['statusOrder'] != 'selesai' && $row['statusOrder'] != 'batal'): ?>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="orderId" value="<?= htmlspecialchars($row['orderId']) ?>">
                                        <select name="statusOrder" class="form-select form-select-sm d-inline w-auto me-2" required>
                                            <option value="proses" <?= $row['statusOrder'] == 'proses' ? 'selected' : '' ?>>Proses</option>
                                            <option value="selesai" <?= $row['statusOrder'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                            <option value="batal" <?= $row['statusOrder'] == 'batal' ? 'selected' : '' ?>>Batal</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-save btn-sm">Simpan</button>
                                    </form>
                                <?php else: ?>
                                    <span style="color:#64748b;font-size:0.95rem;">-</span>
                                <?php endif; ?>
                            </td>
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
$stmt->close();
$conn->close();
?>