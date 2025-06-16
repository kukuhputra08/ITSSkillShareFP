<?php
session_start();
include 'config.php';

// Cek login & role
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

// Proses pencarian & filter
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'izud'; // Default filter rating tinggi
$filter = in_array($filter, ['izud', 'jua', 'harga']) ? $filter : 'izud';

$where = $search ? "AND (s.namaServive LIKE '%$search%' OR s.deskripsi LIKE '%$search%' OR p.namaUsaha LIKE '%$search%')" : "";

// Logika Query berdasarkan filter
if ($filter == 'izud') {
    // View Provider Rating Tinggi
    $sql = "SELECT s.serviceId, s.namaServive, s.deskripsi, s.hargaDasar, s.durasi,
                 p.namaUsaha as provider, u.nama as nama_user, v.ratingRata
          FROM Service s
          JOIN Service_Provider sp ON s.serviceId = sp.Service_serviceId
          JOIN Provider p ON sp.Provider_providerID = p.providerID
          JOIN User u ON p.User_user_id = u.user_id
          JOIN view_provider_rating_tinggi v ON p.providerID = v.providerID
          WHERE 1=1
          $where
          GROUP BY s.serviceId
          ORDER BY v.ratingRata DESC, s.durasi DESC";
} elseif ($filter == 'jua') {
    // View Jasa Terpopuler
    $sql = "SELECT s.serviceId, s.namaServive, s.deskripsi, s.hargaDasar, s.durasi,
                 p.namaUsaha as provider, u.nama as nama_user, vt.jumlah_order
          FROM Service s
          JOIN Service_Provider sp ON s.serviceId = sp.Service_serviceId
          JOIN Provider p ON sp.Provider_providerID = p.providerID
          JOIN User u ON p.User_user_id = u.user_id
          JOIN view_jasa_terpopuler vt ON s.serviceId = vt.serviceId
          WHERE 1=1
          $where
          GROUP BY s.serviceId
          ORDER BY vt.jumlah_order DESC";
} else {
    // View Jasa Filter Harga
    $sql = "SELECT s.serviceId, s.namaServive, s.deskripsi, s.hargaDasar, s.durasi,
                 p.namaUsaha as provider, u.nama as nama_user
          FROM view_jasa_filter_harga s
          JOIN Service_Provider sp ON s.serviceId = sp.Service_serviceId
          JOIN Provider p ON sp.Provider_providerID = p.providerID
          JOIN User u ON p.User_user_id = u.user_id
          WHERE 1=1
          $where
          GROUP BY s.serviceId
          ORDER BY s.hargaDasar ASC";
}

$q = mysqli_query($conn, $sql);
if (!$q) {
    die("Error query: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Cari Jasa | ITS SkillShare</title>
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

        .search-card {
            background: rgba(255, 255, 255, 0.97);
            box-shadow: 0 8px 40px rgba(55, 78, 180, 0.1);
            border-radius: 22px;
            max-width: 900px;
            margin: auto;
            padding: 36px 40px 26px;
        }

        .service-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 2px 12px rgba(55, 78, 180, 0.08);
            transition: .18s;
            margin-bottom: 30px;
            padding: 24px 28px;
        }

        .service-card:hover {
            box-shadow: 0 6px 28px rgba(55, 78, 180, 0.14);
            transform: translateY(-3px) scale(1.01);
        }

        .btn-order {
            border-radius: 20px;
            font-weight: 600;
            background: linear-gradient(90deg, #2563eb 0%, #60a5fa 100%);
            color: #fff;
            border: none;
            box-shadow: 0 4px 12px rgba(38, 132, 255, 0.6);
            transition: .25s;
        }

        .btn-order:hover {
            background: linear-gradient(90deg, #1746a0 0%, #4894ff 100%);
            color: #fff;
        }

        .kategori-tag {
            font-size: .97rem;
            color: #2563eb;
            background: #e3edfd;
            border-radius: 8px;
            padding: 3px 11px;
            font-weight: 500;
            margin-right: 10px;
            display: inline-flex;
            gap: 4px;
        }

        .provider-tag {
            font-size: .96rem;
            color: #555;
            background: #e4e4e7;
            border-radius: 8px;
            padding: 3px 11px;
            font-weight: 500;
            display: inline-flex;
            gap: 4px;
        }

        .order-count {
            font-size: .95rem;
            color: #16a34a;
            font-weight: 500;
            margin-top: 4px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="search-card">
            <h2 class="mb-3 fw-bold text-primary text-center" style="font-size:2rem;">Cari & Pesan Jasa Mahasiswa ITS</h2>
            <form class="row justify-content-center g-2" method="get" autocomplete="off">
                <div class="col-md-6 col-12">
                    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" class="form-control form-control-lg" placeholder="Cari jasa..." autofocus />
                </div>
                <div class="col-md-3 col-12">
                    <select name="filter" class="form-select form-select-lg">
                        <option value="izud" <?= $filter == 'izud' ? 'selected' : '' ?>>Rating Tinggi</option>
                        <option value="jua" <?= $filter == 'jua' ? 'selected' : '' ?>>Jasa Paling Diminati</option>
                        <option value="harga" <?= $filter == 'harga' ? 'selected' : '' ?>>Filter Harga</option>
                    </select>
                </div>
                <div class="col-md-3 col-12 d-grid">
                    <button class="btn btn-order btn-lg" type="submit">
                        <i data-feather="search"></i> Cari
                    </button>
                </div>
            </form>
        </div>

        <main>
            <?php if (mysqli_num_rows($q) == 0): ?>
                <div class="alert alert-warning text-center">Tidak ada jasa ditemukan.</div>
            <?php else: ?>
                <?php while ($r = mysqli_fetch_assoc($q)): ?>
                    <article class="service-card">
                        <div class="d-flex align-items-center mb-2 flex-wrap">
                            <span class="kategori-tag"><?= htmlspecialchars($r['namaServive']) ?></span>
                            <span class="provider-tag"><?= htmlspecialchars($r['provider']) ?>
                                <?php if ($filter == 'izud') echo " ({$r['ratingRata']}★)"; ?>
                            </span>
                        </div>
                        <h3 class="fw-bold mb-1" style="color:#1746a0"><?= htmlspecialchars($r['namaServive']) ?></h3>
                        <p class="mb-2" style="color:#475569;"><?= htmlspecialchars($r['deskripsi']) ?></p>
                        <div class="fw-bold mb-1" style="color:#2563eb;font-size:1.15rem;">Rp <?= number_format($r['hargaDasar']) ?></div>
                        <?php if ($filter == 'jua'): ?>
                            <div class="order-count">Total Order: <?= $r['jumlah_order'] ?>x</div>
                        <?php endif; ?>
                        <div class="pt-2">
                            <a href="order_jasa.php?id=<?= $r['serviceId'] ?>" class="btn btn-order btn-lg shadow">Pesan Sekarang</a>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php endif; ?>
        </main>

        <a href="dashboard_customer.php" class="btn btn-outline-primary mt-3">&larr; Kembali ke Dashboard</a>
    </div>
    <script>
        feather.replace();
    </script>
</body>

</html>