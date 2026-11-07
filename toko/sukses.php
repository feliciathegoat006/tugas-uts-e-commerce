<?php
$judul = "Pesanan Berhasil";
require_once __DIR__ . '/../config/pelanggan.php';

$tokoUrl = '/toko-sepatu/toko';
$uid     = (int) $_SESSION['user']['kd_user'];
$inv     = $_GET['inv'] ?? '';

$stmt = mysqli_prepare($koneksi,
    "SELECT p.kd_pembayaran, p.invoice, p.tgl_bayar, p.status,
            COALESCE(SUM(d.harga_jual * d.qty), 0) AS total
     FROM pembayaran p
     LEFT JOIN detail_pembayaran d ON d.kd_pembayaran = p.kd_pembayaran
     WHERE p.invoice = ? AND p.kd_user = ?
     GROUP BY p.kd_pembayaran, p.invoice, p.tgl_bayar, p.status");
mysqli_stmt_bind_param($stmt, "si", $inv, $uid);
mysqli_stmt_execute($stmt);
$p = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$p) {
    header("Location: riwayat.php");
    exit;
}

include __DIR__ . '/partials/header.php';
?>

<div class="container my-5" style="max-width:560px">
  <div class="text-center mb-4">
    <i class="bi bi-check-circle-fill text-success display-3"></i>
    <h1 class="h3 fw-bold mt-2">Pesanan berhasil dibuat</h1>
    <p class="text-muted mb-0">Terima kasih sudah berbelanja!</p>
  </div>

  <div class="card card-body mb-3">
    <div class="d-flex justify-content-between mb-2">
      <span class="text-muted">Nomor invoice</span>
      <strong><?= e($p['invoice']) ?></strong>
    </div>
    <div class="d-flex justify-content-between mb-2">
      <span class="text-muted">Tanggal</span>
      <span><?= date('d/m/Y H:i', strtotime($p['tgl_bayar'])) ?></span>
    </div>
    <div class="d-flex justify-content-between mb-2">
      <span class="text-muted">Status</span>
      <span class="badge text-bg-<?= badge_status($p['status']) ?>"><?= e(ucfirst($p['status'])) ?></span>
    </div>
    <hr>
    <div class="d-flex justify-content-between fs-5 fw-bold">
      <span>Total bayar</span>
      <span><?= rupiah($p['total']) ?></span>
    </div>
  </div>

  <div class="card card-body mb-4">
    <h2 class="h6 fw-bold">Cara pembayaran</h2>
    <p class="mb-2">Transfer sesuai total ke rekening berikut:</p>
    <p class="mb-2"><strong>BCA 1234567890</strong><br>a.n. Footstep</p>
    <p class="small text-muted mb-0">
      Setelah pembayaran dikonfirmasi admin, status pesanan berubah menjadi Diproses.
      Kamu bisa memantaunya di halaman Pesanan Saya.
    </p>
  </div>

  <div class="d-grid gap-2">
    <a href="riwayat_detail.php?id=<?= $p['kd_pembayaran'] ?>" class="btn btn-dark">Lihat pesanan</a>
    <a href="produk.php" class="btn btn-outline-dark">Lanjut belanja</a>
  </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>