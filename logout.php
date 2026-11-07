<?php
require_once 'config/koneksi.php';

$_SESSION = [];
session_destroy();

header("Location: /toko-sepatu/login.php");
exit;