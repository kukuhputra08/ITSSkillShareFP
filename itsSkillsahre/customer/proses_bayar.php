<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $metode = $_POST['payment_method'];
    $jumlah = intval($_POST['jumlahPembayaran']);
    $user_id = $_SESSION['user_id'];

    // Validasi input
    if (empty($order_id) || empty($metode) || $jumlah <= 0) {
        $_SESSION['error_msg'] = "Lengkapi form pembayaran!";
        header("Location: detail_order.php?id=" . urlencode($order_id));
        exit();
    }

    // Ambil info order
    $sql_order = "SELECT * FROM `order` WHERE orderId=? AND User_user_id=?";
    $stmt = $conn->prepare($sql_order);
    $stmt->bind_param("ss", $order_id, $user_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$order) {
        $_SESSION['error_msg'] = "Pesanan tidak ditemukan.";
        header("Location: riwayat_order.php");
        exit();
    }

    if ($jumlah < $order['TotalBayar']) {
        $_SESSION['error_msg'] = "Nominal pembayaran kurang dari tagihan!";
        header("Location: detail_order.php?id=" . urlencode($order_id));
        exit();
    }

    // Simulasi: catat pembayaran ke tabel payment & update status
    // Asumsi: Satu order satu pembayaran
    $payment_id = uniqid('PAY');
    $service_id = $order['Service_serviceId'];

    $stmt = $conn->prepare("INSERT INTO payment (paymentId, jumlahPembayaran, metodePembayaran, tanggalPembayaran, statusPembayaran, Order_orderId, Order_User_user_id, Order_Service_serviceId)
        VALUES (?, ?, ?, NOW(), 'berhasil', ?, ?, ?)");
    $stmt->bind_param("sissss", $payment_id, $jumlah, $metode, $order_id, $user_id, $service_id);
    $stmt->execute();
    $stmt->close();

    // Update status pembayaran di tabel order
    $stmt = $conn->prepare("UPDATE `order` SET statusPembayaran='berhasil', waktuBayar=NOW() WHERE orderId=?");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success_msg'] = "Pembayaran berhasil disimpan (simulasi)!";
    header("Location: detail_order.php?id=" . urlencode($order_id));
    exit();
} else {
    header("Location: index.php");
    exit();
}
