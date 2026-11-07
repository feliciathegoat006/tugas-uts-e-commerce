<?php
require_once __DIR__ . '/../config/koneksi.php';

$tokoUrl = '/toko-sepatu/toko';

// Harus login sebagai pelanggan, dan lewat tombol (POST)
if (($_SESSION['user']['role'] ?? '') !== 'customer') {
    header("Location: $tokoUrl/login.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $tokoUrl/index.php");
    exit;
}

$uid = (int) $_SESSION['user']['kd_user'];
$kd  = (int) ($_POST['kd_produk'] ?? 0);
$qty = max(1, (int) ($_POST['qty'] ?? 1));

try {
    // Cek produk dan stoknya
    $stmt = mysqli_prepare($koneksi, "SELECT stok FROM produk WHERE kd_produk = ?");
    mysqli_stmt_bind_param($stmt, "i", $kd);
    mysqli_stmt_execute($stmt);
    $p = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$p) {
        $_SESSION['pesan'] = "Produk tidak ditemukan.";
        $_SESSION['tipe']  = "danger";
        header("Location: $tokoUrl/index.php");
        exit;
    }

    // Sudah ada di keranjang?
    $stmt = mysqli_prepare($koneksi,
        "SELECT kd_keranjang_belanja, qty FROM keranjang_belanja WHERE kd_user = ? AND kd_produk = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "ii", $uid, $kd);
    mysqli_stmt_execute($stmt);
    $ada = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    $qtyLama = $ada ? (int) $ada['qty'] : 0;
    $qtyBaru = $qtyLama + $qty;

    if ($qtyBaru > (int) $p['stok']) {
        $_SESSION['pesan'] = "Stok tidak cukup. Stok tersedia " . $p['stok'] . ", di keranjang kamu sudah " . $qtyLama . ".";
        $_SESSION['tipe']  = "danger";
    } else {
        if ($ada) {
            $stmt = mysqli_prepare($koneksi, "UPDATE keranjang_belanja SET qty = ? WHERE kd_keranjang_belanja = ?");
            mysqli_stmt_bind_param($stmt, "ii", $qtyBaru, $ada['kd_keranjang_belanja']);
        } else {
            $stmt = mysqli_prepare($koneksi, "INSERT INTO keranjang_belanja (kd_user, kd_produk, qty) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "iii", $uid, $kd, $qty);
        }
        mysqli_stmt_execute($stmt);

        $_SESSION['pesan'] = "Produk dimasukkan ke keranjang.";
        $_SESSION['tipe']  = "success";
    }
} catch (Exception $ex) {
    $_SESSION['pesan'] = "Gagal menambahkan ke keranjang. Coba lagi.";
    $_SESSION['tipe']  = "danger";
}

header("Location: $tokoUrl/detail.php?id=$kd");
exit;