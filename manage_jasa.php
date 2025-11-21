<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['provider', 'admin'])) {
    header("Location: login.php");
    exit();
}

$is_admin = $_SESSION['role'] === 'admin';
$is_customer = $_SESSION['role'] === 'customer';
$id_user = $_SESSION['user_id'];


// Tampil data
if ($is_admin) {
    $sql = "
            SELECT s.serviceId, s.namaServive, s.hargaDasar, s.durasi, p.namaUsaha 
            FROM Service s
            JOIN Service_Provider sp ON s.serviceId = sp.Service_serviceId
            JOIN Provider p ON sp.Provider_providerID = p.providerID
            GROUP BY s.serviceId
    ";
} else {
    $sql = "
        SELECT s.*
        FROM Service s
        JOIN Service_Provider sp ON s.serviceId = sp.Service_serviceId
        JOIN Provider p ON sp.Provider_providerID = p.providerID
        WHERE p.User_user_id = '$id_user'
    ";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Jasa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="container py-5">

    <h2 class="mb-4">Kelola Jasa</h2>

    <?php if (!$is_admin): ?>
        <a href="tambah_jasa.php" class="btn btn-primary mb-3">+ Tambah Jasa</a>
        <a href="dashboard_provider.php" class="btn btn-primary mb-3">Kembali</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Jasa</th>
                    <th>Harga</th>
                    <th>Durasi</th>
                    <?php if ($is_admin): ?><th>Provider</th><?php endif; ?>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['namaServive']) ?></td>
                        <td>Rp <?= number_format($row['hargaDasar']) ?></td>
                        <td><?= $row['durasi'] ?></td>
                        <?php if ($is_admin): ?><td><?= htmlspecialchars($row['namaUsaha']) ?></td><?php endif; ?>
                        <td>
                            <a href="edit_jasa.php?id=<?= $row['serviceId'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete_jasa.php?id=<?= $row['serviceId'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if ($is_admin): ?>
        <a href="dashboard_admin.php" class="btn btn-primary mb-3">Kembali</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Jasa</th>
                    <th>Harga</th>
                    <th>Durasi</th>
                    <?php if ($is_admin): ?><th>Provider</th><?php endif; ?>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['namaServive']) ?></td>
                        <td>Rp <?= number_format($row['hargaDasar']) ?></td>
                        <td><?= $row['durasi'] ?></td>
                        <?php if ($is_admin): ?><td><?= htmlspecialchars($row['namaUsaha']) ?></td><?php endif; ?>
                        <td>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $row['serviceId'] ?>">Hapus</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>

        </table>
        <script>
            // SweetAlert2 untuk konfirmasi hapus
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    const serviceId = this.getAttribute('data-id');
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data jasa akan dihapus secara permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'delete_jasa.php?id=' + serviceId;
                        }
                    });
                });
            });
        </script>
    <?php endif; ?>



</body>


</html>