<?php
require_once __DIR__ . '/koneksi.php';

// Belum login: arahkan ke login, lalu balik ke halaman ini setelah berhasil
if (!isset($_SESSION['user'])) {
    $next = urlencode($SERVER['REQUEST_URI']);
    header("Location: /toko-sepatu/toko/login.php?next=$next");
    exit;
}

// Admin tidak bisa berbelanja
if ($_SESSION['user']['role'] !== 'customer') {
    header("Location: /toko-sepatu/toko/index.php");
    exit;
}