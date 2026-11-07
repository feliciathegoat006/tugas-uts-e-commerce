<?php
require_once '../config/admin.php';

$id = (int) ($_GET['id'] ?? 0);

try {
    $stmt = mysqli_prepare($koneksi, "DELETE FROM warna WHERE kd_warna = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $_SESSION['pesan'] = "Warna berhasil dihapus.";
} catch (mysqli_sql_exception $ex) {
    $_SESSION['pesan'] = "Warna tidak bisa dihapus karena masih dipakai oleh produk.";
    $_SESSION['tipe']  = "danger";
}

header("Location: index.php");
exit;