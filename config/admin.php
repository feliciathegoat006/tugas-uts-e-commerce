<?php
require_once __DIR__ . '/koneksi.php';

// Kalau belum login sebagai admin, lempar ke halaman login
if (($_SESSION['user']['role'] ?? '') !== 'admin') {
    header("Location: /toko-sepatu/login.php");
    exit;
}