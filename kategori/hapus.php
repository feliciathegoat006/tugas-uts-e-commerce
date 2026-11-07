<?php
require_once '../config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

try {
    $stmt = mysqli_prepare($koneksi, "DELETE FROM kategori WHERE kd_kategori = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $_SESSION['pesan'] = "Kategori berhasil dihapus.";
} catch (mysqli_sql_exception $ex) {
    $_SESSION['pesan'] = "Kategori tidak bisa dihapus karena masih dipakai oleh produk.";
    $_SESSION['tipe']  = "danger";
}

header("Location: index.php");
exit;