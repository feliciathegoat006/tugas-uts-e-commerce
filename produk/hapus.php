<?php
require_once '../config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

try {
    // Produk yang sudah pernah dipesan tidak boleh dihapus
    $stmt = mysqli_prepare($koneksi, "SELECT COUNT(*) AS total FROM detail_pembayaran WHERE kd_produk = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $sudahDipesan = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];

    if ($sudahDipesan > 0) {
        $_SESSION['pesan'] = "Produk tidak bisa dihapus karena sudah pernah dipesan.";
        $_SESSION['tipe']  = "danger";
        header("Location: index.php");
        exit;
    }

    // Catat dulu lokasi file fotonya
    $stmt = mysqli_prepare($koneksi, "SELECT path FROM foto WHERE kd_produk = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $paths = array_column(mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC), 'path');

    mysqli_begin_transaction($koneksi);

    // Hapus riwayat stok dulu (kalau tidak, produk tidak bisa dihapus)
    $stmt = mysqli_prepare($koneksi, "DELETE FROM stok_produk WHERE kd_barang = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    // Hapus produk (data foto dan keranjang ikut terhapus otomatis)
    $stmt = mysqli_prepare($koneksi, "DELETE FROM produk WHERE kd_produk = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    mysqli_commit($koneksi);

    // Database sukses, baru hapus file fotonya
    foreach ($paths as $path) {
        $file = __DIR__ . '/../' . $path;
        if (file_exists($file)) unlink($file);
    }

    $_SESSION['pesan'] = "Produk berhasil dihapus.";
} catch (mysqli_sql_exception $ex) {
    mysqli_rollback($koneksi);
    $_SESSION['pesan'] = "Produk tidak bisa dihapus.";
    $_SESSION['tipe']  = "danger";
}

header("Location: index.php");
exit;