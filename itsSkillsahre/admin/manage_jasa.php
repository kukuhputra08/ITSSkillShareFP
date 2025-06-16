<?php
session_start();
include 'config.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

// Proses hapus jasa
if (isset($_GET['serviceId'])) {
    $delete_id = $_GET['serviceId'];
    $stmt = $conn->prepare('DELETE FROM service WHERE serviceId = ?');
    $stmt->bind_param('s', $delete_id);
    if ($stmt->execute()) {
        $_SESSION['success_msg'] = "Jasa berhasil dihapus.";
    } else {
        $_SESSION['error_msg'] = "Gagal menghapus jasa.";
    }
    $stmt->close();
    header('Location: manage_jasa.php');
    exit();
}


// Pencarian
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Hitung total jasa untuk pagination
if ($search != '') {
    $sql_count = "SELECT COUNT(*) AS total FROM service WHERE service LIKE '%$search%' OR deskripsi LIKE '%$search%'";
} else {
    $sql_count = "SELECT COUNT(*) AS total FROM service";
}
$result_count = $conn->query($sql_count);
if (!$result_count) {
    die('Query error: ' . $conn->error);
}
$total_jasa = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_jasa / $limit);

// Ambil data jasa
if ($search != '') {
    $stmt = $conn->prepare("SELECT * FROM service WHERE namaServive LIKE ? OR deskripsi LIKE ? ORDER BY serviceId ASC LIMIT ? OFFSET ?");
    $search_like = "%$search%";
    $stmt->bind_param('ssii', $search_like, $search_like, $limit, $offset);
} else {
    $stmt = $conn->prepare('SELECT * FROM service ORDER BY serviceId ASC LIMIT ? OFFSET ?');
    $stmt->bind_param('ii', $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Jasa | ITS SkillShare</title>
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
            max-width: 960px;
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
            margin-bottom: 24px;
            text-align: center;
        }

        .btn-edit {
            background-color: #2563eb;
            color: white;
            border-radius: 14px;
            padding: 6px 16px;
            margin-right: 8px;
            text-decoration: none;
        }

        .btn-delete {
            background-color: #ef4444;
            color: white;
            border-radius: 14px;
            padding: 6px 16px;
            text-decoration: none;
        }

        .pagination {
            justify-content: center;
            margin-top: 36px;
        }

        .page-link {
            border-radius: 12px !important;
            font-weight: 600;
            color: #2563eb !important;
            border: 1.8px solid #2563eb !important;
        }

        .page-link:hover {
            background-color: #2563eb !important;
            color: white !important;
        }

        .page-item.active .page-link {
            background-color: #2563eb !important;
            border-color: #2563eb !important;
            color: white !important;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>
    <div class="container">
        <a href="dashboard_admin.php" class="btn btn-outline-primary mb-3">&larr; Kembali ke Dashboard</a>
        <h1>Kelola Jasa</h1>

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
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Jasa</th>
                        <th>Deskripsi</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['serviceId']) ?></td>
                            <td><?= htmlspecialchars($row['namaServive']) ?></td>
                            <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                            <td>Rp<?= number_format($row['hargaDasar'], 0, ',', '.') ?></td>
                            <td class="d-flex gap-2">
                                <button class="btn-delete swal-hapus"
                                    data-id="<?= htmlspecialchars($row['serviceId']) ?>"
                                    data-nama="<?= htmlspecialchars($row['namaServive']) ?>">
                                    Hapus
                                </button>

                            </td>


                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <nav aria-label="Pagination">
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">&laquo; Sebelumnya</a>
                    </li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">Berikutnya &raquo;</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.swal-hapus').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var serviceId = this.getAttribute('data-id');
                var namaJasa = this.getAttribute('data-nama');
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    html: "<b>" + namaJasa + "</b> akan dihapus permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "manage_jasa.php?serviceId=" + encodeURIComponent(serviceId);
                    }
                });
            });
        });
    </script>


</body>

</html>
<?php
$stmt->close();
$conn->close();
?>