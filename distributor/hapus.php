<?php
require_once '../config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

try {
    $stmt = mysqli_prepare($koneksi, "DELETE FROM distributor WHERE kd_distributor = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $_SESSION['pesan'] = "Distributor berhasil dihapus.";
} catch (mysqli_sql_exception $ex) {
    $_SESSION['pesan'] = "Distributor tidak bisa dihapus.";
    $_SESSION['tipe']  = "danger";
}

header("Location: index.php");
exit;