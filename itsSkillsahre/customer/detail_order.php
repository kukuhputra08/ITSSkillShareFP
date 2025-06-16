<?php
session_start();
include 'config.php';

// Cek login & role
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || $_GET['id'] == '') {
    echo "<div class='alert alert-danger mt-4'>ID pesanan tidak valid.</div>";
    exit();
}
$id_user = $_SESSION['user_id'];
$orderId = $_GET['id'];

// --- Query detail pesanan (error handling!)
$sql = "SELECT o.*, 
            s.namaServive, s.deskripsi, s.hargaDasar, s.durasi, 
            p.namaUsaha as provider, u.nama as nama_provider
        FROM `order` o
        JOIN service s ON o.Service_serviceId = s.serviceId
        JOIN service_provider sp ON s.serviceId = sp.Service_serviceId
        JOIN provider p ON sp.Provider_providerID = p.providerID
        JOIN user u ON p.User_user_id = u.user_id
        WHERE o.orderId = ? AND o.User_user_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error . "<br>SQL: " . htmlspecialchars($sql));
}
$stmt->bind_param("ss", $orderId, $id_user);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
$result = $stmt->get_result();
if (!$result) {
    die("Get result failed: " . $conn->error);
}
if ($result->num_rows == 0) {
    echo "<div class='alert alert-danger mt-4'>Pesanan tidak ditemukan.</div>";
    exit();
}
$order = $result->fetch_assoc();
$stmt->close();

// --- Ambil feedback jika sudah ada (error handling!)
$sql_feedback = "SELECT f.*, u.nama as nama_user
    FROM feedback f
    JOIN user u ON f.Order_User_user_id = u.user_id
    WHERE f.Order_orderId = ?";
$stmt = $conn->prepare($sql_feedback);
if (!$stmt) {
    die("Prepare failed (feedback): " . $conn->error . "<br>SQL: " . htmlspecialchars($sql_feedback));
}
$stmt->bind_param("s", $orderId);
if (!$stmt->execute()) {
    die("Execute failed (feedback): " . $stmt->error);
}
$feedback_result = $stmt->get_result();
if (!$feedback_result) {
    die("Get result failed (feedback): " . $conn->error);
}
$feedback = $feedback_result->fetch_assoc();
$stmt->close();

// --- Proses SIMPAN FEEDBACK jika ada post
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_feedback'])) {
    $rating = intval($_POST['rating']);
    $deskripsi = trim($_POST['deskripsi']);
    if ($rating >= 1 && $rating <= 5) {
        $sql_insert = "INSERT INTO feedback (Order_orderId, Order_User_user_id, Order_Service_serviceId, rating, deskripsi, tanggalFeedback) 
                       VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql_insert);
        if (!$stmt) {
            die("Prepare failed (insert): " . $conn->error . "<br>SQL: " . htmlspecialchars($sql_insert));
        }
        $stmt->bind_param("sssis", $orderId, $id_user, $order['Service_serviceId'], $rating, $deskripsi);
        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "Feedback berhasil dikirim!";
            header("Location: detail_order.php?id=" . $orderId);
            exit();
        } else {
            $error_msg = "Gagal mengirim feedback. Coba lagi.";
        }
        $stmt->close();
    } else {
        $error_msg = "Rating harus 1-5 bintang!";
    }
}

// --- Pembayaran: cek/update status otomatis
$alasan_batal = '';
if ($order['statusPembayaran'] == 'menunggu') {
    $order_time = strtotime($order['tanggalOrder']);
    if (time() - $order_time > 1800) {
        $alasan_batal = "Melebihi batas waktu pembayaran (30 menit).";
    }
}
if ($order['statusPembayaran'] == 'batal' && empty($order['alasanPembayaran'])) {
    $alasan_batal = "Dibatalkan oleh sistem/admin.";
    $order['alasanPembayaran'] = $alasan_batal;
}

// --- List metode pembayaran (jika menunggu)
$list_payment = [];
if ($order['statusPembayaran'] == 'menunggu') {
    $sql_pay = "SELECT paymentId, namaMetode, infoRekening FROM payment";
    $r_pay = $conn->query($sql_pay);
    if ($r_pay) {
        while ($p = $r_pay->fetch_assoc()) $list_payment[] = $p;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Detail Pesanan | ITS SkillShare</title>
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
            max-width: 700px;
            margin: auto;
            padding: 36px 38px 32px;
        }

        .order-status {
            font-weight: 700;
            border-radius: 12px;
            font-size: 1.05rem;
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

        .tag-provider {
            font-size: 1.01rem;
            color: #555;
            background: #e4e4e7;
            border-radius: 8px;
            padding: 3px 16px;
            font-weight: 500;
        }

        .order-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2563eb;
            margin-bottom: 6px;
        }

        .order-total {
            font-weight: 600;
            color: #0e293b;
            font-size: 1.09rem;
        }

        .desc-box {
            background: #f3f6fd;
            border-radius: 12px;
            padding: 16px 18px;
            color: #374151;
            font-size: 1.05rem;
        }

        .rating-star {
            color: #fbbf24;
            font-size: 1.55rem;
            margin-right: 3px;
            vertical-align: bottom;
        }

        .form-label {
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="main-card">
        <a href="riwayat_order.php" class="btn btn-outline-primary mb-3">&larr; Kembali ke Riwayat Pesanan</a>
        <h2 class="order-title mb-1"><?= htmlspecialchars($order['namaServive']) ?></h2>
        <div class="mb-3">
            <span class="tag-provider"><i data-feather="user"></i> <?= htmlspecialchars($order['provider']) ?> (<?= htmlspecialchars($order['nama_provider']) ?>)</span>
            <span class="ms-2 order-status <?= $order['statusOrder'] ?>">
                <?= ucfirst($order['statusOrder']) ?>
            </span>
            <span class="ms-2 badge
                <?php
                if ($order['statusPembayaran'] == 'berhasil') echo 'bg-success';
                elseif ($order['statusPembayaran'] == 'menunggu') echo 'bg-warning text-dark';
                else echo 'bg-danger';
                ?>">
                <?php
                if ($order['statusPembayaran'] == 'berhasil') echo 'Pembayaran Berhasil';
                elseif ($order['statusPembayaran'] == 'menunggu') echo 'Menunggu Pembayaran';
                else echo 'Pembayaran Batal';
                ?>
            </span>
        </div>

        <div class="mb-2">
            <b>Tanggal Order:</b> <?= date('d M Y, H:i', strtotime($order['tanggalOrder'])) ?>
        </div>
        <div class="mb-2 order-total">
            Total Bayar: Rp <?= number_format($order['TotalBayar'], 0, ',', '.') ?>
        </div>
        <div class="mb-2">
            <b>Harga Jasa:</b> Rp <?= number_format($order['hargaDasar'], 0, ',', '.') ?>
            | <b>Durasi:</b> <?= htmlspecialchars($order['durasi']) ?> jam
        </div>
        <div class="desc-box mb-3">
            <b>Deskripsi Jasa:</b><br>
            <?= nl2br(htmlspecialchars($order['deskripsi'])) ?>
        </div>

        <!-- STATUS PEMBAYARAN -->
        <?php if ($order['statusPembayaran'] == 'berhasil'): ?>
            <div class="alert alert-success mb-3">
                Pembayaran berhasil dilakukan
                <?= isset($order['waktuBayar']) && $order['waktuBayar'] ? 'pada ' . date('d M Y, H:i', strtotime($order['waktuBayar'])) : '' ?>.
            </div>
        <?php elseif ($order['statusPembayaran'] == 'batal'): ?>
            <div class="alert alert-danger mb-3">
                Pembayaran dibatalkan. <br>
                <b>Alasan:</b> <?= htmlspecialchars($order['alasanPembayaran'] ?? $alasan_batal) ?>
            </div>
        <?php elseif ($order['statusPembayaran'] == 'menunggu'): ?>
            <div class="alert alert-warning mb-3">
                Anda belum melakukan pembayaran. Silakan lakukan pembayaran sebelum <b><?= date('H:i', strtotime($order['tanggalOrder'] . ' +30 minutes')) ?></b>.
            </div>
            <form method="post" action="proses_bayar.php">
                <input type="hidden" name="order_id" value="<?= htmlspecialchars($orderId) ?>">
                <div class="mb-3">
                    <label class="form-label">Pilih Metode Pembayaran:</label>
                    <select name="payment_method" class="form-select" required>
                        <option value="">-- Pilih Metode --</option>
                        <option value="E-wallet">E-wallet (Dana, Gopay, OVO)</option>
                        <option value="Transfer Bank">Transfer Bank (BCA, Mandiri, dsb.)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nominal Bayar:</label>
                    <input type="number" name="jumlahPembayaran" class="form-control" placeholder="Masukkan nominal pembayaran" required min="<?= $order['TotalBayar'] ?>" value="<?= $order['TotalBayar'] ?>">
                </div>
                <button type="submit" class="btn btn-primary">Bayar Sekarang</button>
            </form>
        <?php endif; ?>

        <!-- FEEDBACK -->
        <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success_msg'];
                                                unset($_SESSION['success_msg']); ?></div>
        <?php endif; ?>
        <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>

        <?php if ($feedback): ?>
            <div class="mb-3">
                <h5 class="mb-2" style="color:#fbbf24;">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="rating-star"><?= $i <= $feedback['rating'] ? '★' : '☆' ?></span>
                    <?php endfor; ?>
                    <span style="color:#374151;font-size:1.1rem;">
                        (<?= $feedback['rating'] ?>/5)
                    </span>
                </h5>
                <?php if ($feedback['deskripsi']): ?>
                    <div class="desc-box">
                        <b>Ulasan Anda:</b><br>
                        <?= nl2br(htmlspecialchars($feedback['deskripsi'])) ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif (in_array($order['statusOrder'], ['selesai', 'batal', 'cancel'])): ?>
            <div class="mb-3">
                <form method="post" autocomplete="off">
                    <label class="form-label mb-2">Beri Rating untuk Jasa Ini:</label><br>
                    <div class="mb-2">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" required style="display:none;">
                            <label for="star<?= $i ?>" style="font-size:2rem;cursor:pointer;color:#fbbf24;">★</label>
                        <?php endfor; ?>
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="deskripsi">Ulasan (opsional):</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" maxlength="255" placeholder="Tulis ulasan Anda di sini..."></textarea>
                    </div>
                    <button type="submit" name="submit_feedback" class="btn btn-primary">Kirim Feedback</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script>
        feather.replace();
        // efek radio bintang
        document.querySelectorAll('label[for^="star"]').forEach(function(label) {
            label.addEventListener('click', function() {
                document.querySelectorAll('label[for^="star"]').forEach(function(l) {
                    l.style.opacity = '0.5';
                });
                let val = this.getAttribute('for').replace('star', '');
                for (let i = 1; i <= val; i++) {
                    document.querySelector('label[for="star' + i + '"]').style.opacity = '1';
                }
            });
        });
    </script>
</body>

</html>
<?php
$conn->close();
?>