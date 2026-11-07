<?php
require_once __DIR__ . '/../config/koneksi.php';

$_SESSION = [];
session_destroy();

header("Location: /toko-sepatu/toko/index.php");
exit;