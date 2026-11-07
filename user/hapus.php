<?php
require_once '../config/admin.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = mysqli_prepare($koneksi, "SELECT role FROM `user` WHERE kd_user = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$u = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$u) {
    $_SESSION['pesan'] = "User tidak ditemukan.";
    $_SESSION['tipe']  = "danger";
} elseif ($u['role'] !== 'customer') {
    $_SESSION['pesan'] = "Akun admin tidak bisa dihapus dari sini.";
    $_SESSION['tipe']  = "danger";
} else {
    try {
        $stmt = mysqli_prepare($koneksi, "DELETE FROM `user` WHERE kd_user = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $_SESSION['pesan'] = "User berhasil dihapus.";
    } catch (mysqli_sql_exception $ex) {
        $_SESSION['pesan'] = "User tidak bisa dihapus karena sudah punya riwayat pesanan.";
        $_SESSION['tipe']  = "danger";
    }
}

header("Location: index.php");
exit;