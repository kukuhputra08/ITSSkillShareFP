<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "fp_its_skillshare";
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
