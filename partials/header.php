<?php
require_once __DIR__ . '/../config/admin.php';
$base = '/toko-sepatu';
$menu = $menu ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($judul ?? 'Admin') ?> | Footstep</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= $base ?>/css/adminlte.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">
  <style>
    /* Beri jarak pada kotak pencarian dan nomor halaman DataTables di dalam card */
    .card-body .dt-container .dt-layout-row { padding: .75rem 1rem; margin: 0; }
    .card-body .dt-container .dt-layout-row.dt-layout-table { padding: 0; }
  </style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">

  <nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
            <i class="bi bi-list"></i>
          </a>
        </li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <span class="nav-link"><i class="bi bi-person-circle"></i> <?= e($_SESSION['user']['username'] ?? '') ?></span>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= $base ?>/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </li>
      </ul>
    </div>
  </nav>

  <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
      <a href="<?= $base ?>/index.php" class="brand-link">
        <span class="brand-text fw-bold">Footstep</span>
      </a>
    </div>
    <div class="sidebar-wrapper">
      <nav class="mt-2">
        <ul class="nav sidebar-menu flex-column" role="menu">
         <li class="nav-header">TRANSAKSI</li>
          <li class="nav-item">
            <a href="<?= $base ?>/pesanan/index.php" class="nav-link <?= $menu === 'pesanan' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-receipt"></i><p>Pesanan</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?= $base ?>/index.php" class="nav-link <?= $menu === 'dashboard' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-speedometer2"></i><p>Dashboard</p>
            </a>
          </li>

          <li class="nav-header">DATA PRODUK</li>
          <li class="nav-item">
            <a href="<?= $base ?>/produk/index.php" class="nav-link <?= $menu === 'produk' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-box-seam"></i><p>Produk</p>
            </a>
          </li>

          <li class="nav-header">MASTER DATA</li>
          <li class="nav-item">
            <a href="<?= $base ?>/brand/index.php" class="nav-link <?= $menu === 'brand' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-tag"></i><p>Brand</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= $base ?>/kategori/index.php" class="nav-link <?= $menu === 'kategori' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-grid"></i><p>Kategori</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= $base ?>/ukuran/index.php" class="nav-link <?= $menu === 'ukuran' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-rulers"></i><p>Ukuran</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= $base ?>/warna/index.php" class="nav-link <?= $menu === 'warna' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-palette"></i><p>Warna</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= $base ?>/distributor/index.php" class="nav-link <?= $menu === 'distributor' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-truck"></i><p>Distributor</p>
            </a>
          </li>
          <li class="nav-header">PENGGUNA</li>
          <li class="nav-item">
            <a href="<?= $base ?>/user/index.php" class="nav-link <?= $menu === 'user' ? 'active' : '' ?>">
              <i class="nav-icon bi bi-people"></i><p>User</p>
            </a>
          </li>

        </ul>
      </nav>
    </div>
  </aside>

  <main class="app-main">