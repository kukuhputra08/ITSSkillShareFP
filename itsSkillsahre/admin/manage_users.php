<?php
session_start();
include 'config.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

// Proses hapus user
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    if ($delete_id == $_SESSION['user_id']) {
        $_SESSION['error_msg'] = "Anda tidak dapat menghapus akun sendiri.";
    } else {
        $stmt = $conn->prepare('DELETE FROM user WHERE user_id = ?');
        $stmt->bind_param('s', $delete_id);
        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "User berhasil dihapus.";
        } else {
            $_SESSION['error_msg'] = "Gagal menghapus user.";
        }
        $stmt->close();
    }
    header('Location: manage_users.php');
    exit();
}

// Handle pencarian
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Hitung total user untuk paginasi
if ($search != '') {
    $sql_count = "SELECT COUNT(*) AS total FROM user WHERE nama LIKE '%$search%' OR email LIKE '%$search%'";
} else {
    $sql_count = "SELECT COUNT(*) AS total FROM user";
}
$result_count = $conn->query($sql_count);
$total_users = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_users / $limit);

// Ambil data user untuk halaman ini
if ($search != '') {
    $stmt = $conn->prepare("SELECT user_id, nama, email, role FROM user WHERE nama LIKE ? OR email LIKE ? ORDER BY user_id ASC LIMIT ? OFFSET ?");
    $search_like = "%$search%";
    $stmt->bind_param('ssii', $search_like, $search_like, $limit, $offset);
} else {
    $stmt = $conn->prepare('SELECT user_id, nama, email, role FROM user ORDER BY user_id ASC LIMIT ? OFFSET ?');
    $stmt->bind_param('ii', $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Pengguna | ITS SkillShare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=SF+Pro+Text&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa, #e4ebf5);
            font-family: 'SF Pro Text', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
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
            font-size: 2.8rem;
            margin-bottom: 24px;
            text-align: center;
        }

        table {
            border-collapse: separate !important;
            border-spacing: 0 10px !important;
            width: 100%;
        }

        thead tr th {
            background: #e0e7ff;
            color: #1e40af;
            font-weight: 700;
            padding: 14px 24px;
            border-radius: 18px;
        }

        tbody tr {
            background: #f8faff;
            border-radius: 22px;
            box-shadow: 0 4px 15px rgba(38, 57, 254, 0.05);
        }

        tbody tr td {
            padding: 18px 24px;
            font-weight: 600;
            color: #334155;
        }

        .btn-edit {
            background-color: #2563eb;
            color: white;
            border-radius: 14px;
            padding: 6px 16px;
            margin-right: 8px;
        }

        .btn-delete {
            background-color: #ef4444;
            color: white;
            border-radius: 14px;
            padding: 6px 16px;
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
        <h1>Kelola Pengguna</h1>
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert" style="border-radius:16px;">
                <strong>Berhasil!</strong> Pengguna baru berhasil ditambahkan.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success_msg']) ?></div>
            <?php unset($_SESSION['success_msg']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_msg'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error_msg']) ?></div>
            <?php unset($_SESSION['error_msg']); ?>
        <?php endif; ?>

        <a href="tambah_user.php?from=manage_users" class="btn btn-primary mb-4" style="border-radius: 24px;">Tambah Pengguna Baru</a>
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <form class="d-flex" method="GET" action="">
                    <input type="text" name="search" class="form-control me-2" placeholder="Cari pengguna..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    <button class="btn btn-outline-primary" type="submit">Cari</button>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['user_id']) ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars(ucfirst($row['role'])) ?></td>
                            <td>
                                <a href="edit_user.php?user_id=<?= $row['user_id'] ?>" class="btn-edit">Edit</a>
                                <?php if ($row['user_id'] != $_SESSION['user_id']): ?>
                                    <a href="manage_users.php?delete_id=<?= $row['user_id'] ?>" onclick="return confirm('Yakin ingin menghapus?')" class="btn-delete">Hapus</a>

                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <nav aria-label="Pagination">
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>">&laquo; Sebelumnya</a></li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li>
                <?php endfor; ?>
                <?php if ($page < $total_pages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>">Berikutnya &raquo;</a></li>
                <?php endif; ?>
            </ul>
        </nav>

    </div>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>