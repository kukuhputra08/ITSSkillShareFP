<?php
include 'config.php';
session_start();

if ($_SESSION['role'] != 'provider') {
    header("Location: login.php");
    exit();
}

$providerID = $_SESSION['providerID'];

$query = "SELECT * FROM view_jasa_provider WHERE providerID = '$providerID'";
$result = mysqli_query($conn, $query);
?>

<h2>Statistik Jasa Provider</h2>
<table border="1">
    <tr>
        <th>Service ID</th>
        <th>Nama Jasa</th>
        <th>Jumlah Order</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $row['serviceId'] ?></td>
            <td><?= $row['namaServive'] ?></td>
            <td><?= $row['jumlah_order'] ?></td>
        </tr>
    <?php endwhile; ?>
</table>