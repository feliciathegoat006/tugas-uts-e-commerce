<?php
require_once __DIR__ . '/../../config/koneksi.php';

$base    = '/toko-sepatu';
$tokoUrl = $base . '/toko';
$user    = $_SESSION['user'] ?? null;

// Kembalikan URL foto kalau filenya ada, kalau tidak kembalikan null
function foto_produk($path) {
    if ($path && file_exists(__DIR__ . '/../../' . $path)) {
        return '/toko-sepatu/' . $path;
    }
    return null;
}

// Jumlah isi keranjang (untuk ikon keranjang)
$jumlahKeranjang = 0;
if ($user && $user['role'] === 'customer') {
    $uid  = (int) $user['kd_user'];
    $stmt = mysqli_prepare($koneksi,
        "SELECT COALESCE(SUM(qty), 0) AS total FROM keranjang_belanja WHERE kd_user = ?");
    mysqli_stmt_bind_param($stmt, "i", $uid);
    mysqli_stmt_execute($stmt);
    $jumlahKeranjang = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total'];
}

$kategoriNav = mysqli_query($koneksi, "SELECT kd_kategori, nm_kategori FROM kategori ORDER BY nm_kategori");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($judul ?? 'Footstep') ?> | Footstep</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= $tokoUrl ?>/css/toko.css">
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
  <div class="container">
    <a class="navbar-brand" href="<?= $tokoUrl ?>/index.php">FOOT<span>STEP</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuToko">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="menuToko">
      <form class="d-flex flex-grow-1 mx-lg-4 my-2 my-lg-0" action="<?= $tokoUrl ?>/produk.php" method="get">
        <input class="form-control" type="search" name="q" placeholder="Cari produk atau brand"
               value="<?= e($_GET['q'] ?? '') ?>">
      </form>

      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item">
          <a class="nav-link" href="<?= $tokoUrl ?>/produk.php">Semua Produk</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Kategori</a>
          <ul class="dropdown-menu">
            <?php while ($k = mysqli_fetch_assoc($kategoriNav)): ?>
              <li><a class="dropdown-item" href="<?= $tokoUrl ?>/produk.php?kategori=<?= $k['kd_kategori'] ?>">
                <?= e($k['nm_kategori']) ?></a></li>
            <?php endwhile; ?>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link position-relative" href="<?= $tokoUrl ?>/keranjang.php">
            <i class="bi bi-cart3 fs-5"></i>
            <?php if ($jumlahKeranjang > 0): ?>
              <span class="badge rounded-pill text-bg-danger position-absolute top-0 start-100 translate-middle">
                <?= $jumlahKeranjang ?>
              </span>
            <?php endif; ?>
          </a>
        </li>

        <?php if ($user): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
              <i class="bi bi-person-circle"></i> <?= e($user['username']) ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <?php if ($user['role'] === 'admin'): ?>
                <li><a class="dropdown-item" href="<?= $base ?>/index.php">Panel Admin</a></li>
              <?php else: ?>
                <li><a class="dropdown-item" href="<?= $tokoUrl ?>/riwayat.php">Pesanan Saya</a></li>
              <?php endif; ?>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="<?= $tokoUrl ?>/logout.php">Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item ms-lg-2">
            <a class="btn btn-outline-dark btn-sm" href="<?= $tokoUrl ?>/login.php">Login</a>
            <a class="btn btn-dark btn-sm" href="<?= $tokoUrl ?>/daftar.php">Daftar</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<main class="flex-grow-1">