<?php
date_default_timezone_set('Asia/Jakarta');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "db_ecommerce_sepatu";

$koneksi = mysqli_connect($host, $user, $pass, $db);
mysqli_set_charset($koneksi, "utf8mb4");

// Pengaman: dipakai tiap kali menampilkan data ke halaman
function e($teks) {
    return htmlspecialchars($teks ?? '', ENT_QUOTES, 'UTF-8');
}

// Bikin isi <option> untuk dropdown dari tabel apa pun
function opsi($koneksi, $tabel, $kd, $nm, $dipilih = null) {
    $html = '<option value="">-- Pilih --</option>';
    $hasil = mysqli_query($koneksi, "SELECT $kd, $nm FROM $tabel ORDER BY $nm");
    while ($r = mysqli_fetch_assoc($hasil)) {
        $sel = ($r[$kd] == $dipilih) ? 'selected' : '';
        $html .= '<option value="' . $r[$kd] . '" ' . $sel . '>' . e($r[$nm]) . '</option>';
    }
    return $html;
}

// Warna label status pesanan
function badge_status($status) {
    $warna = [
        'menunggu'   => 'warning',
        'diproses'   => 'info',
        'dikirim'    => 'primary',
        'selesai'    => 'success',
        'dibatalkan' => 'danger',
    ];
    return $warna[$status] ?? 'secondary';
}

// Format angka jadi rupiah
function rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Dipakai untuk error yang pesannya aman ditampilkan ke pelanggan
class PesananException extends Exception {}

// Rapikan foto: putar sesuai EXIF, kecilkan, taruh di kanvas persegi putih, simpan JPG
function proses_foto($tmp, $tujuan, $maks = 1000) {
    $info = @getimagesize($tmp);
    if (!$info) return false;

    switch ($info[2]) {
        case IMAGETYPE_JPEG: $src = @imagecreatefromjpeg($tmp); break;
        case IMAGETYPE_PNG:  $src = @imagecreatefrompng($tmp);  break;
        case IMAGETYPE_WEBP: $src = @imagecreatefromwebp($tmp); break;
        default: return false;
    }
    if (!$src) return false;

    // Foto dari HP sering "miring" karena orientasi disimpan di EXIF
    if ($info[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
        $exif = @exif_read_data($tmp);
        $o = $exif['Orientation'] ?? 1;
        if ($o == 3)      $src = imagerotate($src, 180, 0);
        elseif ($o == 6)  $src = imagerotate($src, -90, 0);
        elseif ($o == 8)  $src = imagerotate($src, 90, 0);
    }

    $w = imagesx($src);
    $h = imagesy($src);

    // Kecilkan kalau terlalu besar, tapi jangan dibesarkan
    $skala = min(1, $maks / max($w, $h));
    $nw    = (int) round($w * $skala);
    $nh    = (int) round($h * $skala);
    $sisi  = max($nw, $nh);

    // Kanvas persegi putih, foto ditaruh di tengah
    $dst = imagecreatetruecolor($sisi, $sisi);
    imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
    imagecopyresampled($dst, $src,
        (int) (($sisi - $nw) / 2), (int) (($sisi - $nh) / 2), 0, 0,
        $nw, $nh, $w, $h);

    $ok = imagejpeg($dst, $tujuan, 85);
    imagedestroy($src);
    imagedestroy($dst);
    return $ok;
}