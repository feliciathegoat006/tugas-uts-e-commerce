<?php
require_once __DIR__ . '/../config/pelanggan.php';

$tokoUrl = '/toko-sepatu/toko';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $tokoUrl/keranjang.php");
    exit;
}

$uid  = (int) $_SESSION['user']['kd_user'];
$aksi = $_POST['aksi'] ?? '';
$id   = (int) ($_POST['id'] ?? 0);

try {
    if ($aksi === 'hapus') {
        $stmt = mysqli_prepare($koneksi,
            "DELETE FROM keranjang_belanja WHERE kd_keranjang_belanja = ? AND kd_user = ?");
        mysqli_stmt_bind_param($stmt, "ii", $id, $uid);
        mysqli_stmt_execute($stmt);
        $_SESSION['pesan'] = "Produk dihapus dari keranjang.";
        $_SESSION['tipe']  = "success";

    } elseif ($aksi === 'kosongkan') {
        $stmt = mysqli_prepare($koneksi, "DELETE FROM keranjang_belanja WHERE kd_user = ?");
        mysqli_stmt_bind_param($stmt, "i", $uid);
        mysqli_stmt_execute($stmt);
        $_SESSION['pesan'] = "Keranjang dikosongkan.";
        $_SESSION['tipe']  = "success";

    } elseif ($aksi === 'ubah') {
        $qty = max(1, (int) ($_POST['qty'] ?? 1));

        // Ambil stok produk, sekaligus memastikan item ini milik user yang login
        $stmt = mysqli_prepare($koneksi,
            "SELECT p.stok
             FROM keranjang_belanja kb
             JOIN produk p ON kb.kd_produk = p.kd_produk
             WHERE kb.kd_keranjang_belanja = ? AND kb.kd_user = ?");
        mysqli_stmt_bind_param($stmt, "ii", $id, $uid);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if (!$row) {
            $_SESSION['pesan'] = "Item tidak ditemukan.";
            $_SESSION['tipe']  = "danger";
        } elseif ((int) $row['stok'] <= 0) {
            $stmt = mysqli_prepare($koneksi,
                "DELETE FROM keranjang_belanja WHERE kd_keranjang_belanja = ? AND kd_user = ?");
            mysqli_stmt_bind_param($stmt, "ii", $id, $uid);
            mysqli_stmt_execute($stmt);
            $_SESSION['pesan'] = "Stok produk habis, item dihapus dari keranjang.";
            $_SESSION['tipe']  = "danger";
        } else {
            $stok = (int) $row['stok'];
            if ($qty > $stok) {
                $qty = $stok;
                $_SESSION['pesan'] = "Jumlah disesuaikan dengan stok yang tersedia ($stok).";
                $_SESSION['tipe']  = "warning";
            } else {
                $_SESSION['pesan'] = "Jumlah barang diperbarui.";
                $_SESSION['tipe']  = "success";
            }
            $stmt = mysqli_prepare($koneksi,
                "UPDATE keranjang_belanja SET qty = ? WHERE kd_keranjang_belanja = ? AND kd_user = ?");
            mysqli_stmt_bind_param($stmt, "iii", $qty, $id, $uid);
            mysqli_stmt_execute($stmt);
        }
    }
} catch (Exception $ex) {
    $_SESSION['pesan'] = "Terjadi kesalahan. Coba lagi.";
    $_SESSION['tipe']  = "danger";
}

header("Location: $tokoUrl/keranjang.php");
exit;