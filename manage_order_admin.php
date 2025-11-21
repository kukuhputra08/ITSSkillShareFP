<?php
include 'config.php';
include 'template.php';
session_start();

// Cek login admin (optional security)
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$query = "
SELECT 
  o.orderId, o.Service_serviceId, o.tanggalOrder, o.statusOrder, 
  o.TotalBayar, s.namaServive, u.nama AS nama_user
FROM `order` o
JOIN service s ON o.Service_serviceId = s.serviceId
JOIN user u ON o.User_user_id = u.user_id
ORDER BY o.tanggalOrder DESC
";

$result = mysqli_query($conn, $query);
?>

<h2>Daftar Semua Pesanan</h2>
<table border="1">
    <tr>
        <th>Order ID</th>
        <th>Nama Service</th>
        <th>Nama Customer</th>
        <th>Tanggal Order</th>
        <th>Status Order</th>
        <th>Total Bayar</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $row['orderId'] ?></td>
            <td><?= $row['namaServive'] ?></td>
            <td><?= $row['nama_user'] ?></td>
            <td><?= $row['tanggalOrder'] ?></td>
            <td><?= $row['statusOrder'] ?></td>
            <td><?= number_format($row['TotalBayar'], 0, ",", ".") ?></td>
        </tr>
    <?php endwhile; ?>
</table>