<?php
$judul = "Beranda";
include __DIR__ . '/partials/header.php';

$produk = mysqli_query($koneksi,
    "SELECT p.kd_produk, p.nama_produk, p.harga_jual, p.stok, b.nm_brand,
            (SELECT path FROM foto WHERE kd_produk = p.kd_produk ORDER BY kd_foto LIMIT 1) AS foto
     FROM produk p
     JOIN brand b ON p.kd_brand = b.kd_brand
     ORDER BY p.kd_produk DESC
     LIMIT 8");
?>

<div class="container mt-4">

  <div class="hero text-center">
    <h1>BEST PRICE. GRAB IT TODAY</h1>
    <p class="mb-3">Sneakers original pilihan, harga bersahabat.</p>
    <div class="mb-4">
      <span class="chip">FREE SHIPPING</span>
      <span class="chip">GARANSI TUKAR UKURAN</span>
      <span class="chip">ORIGINAL 100%</span>
    </div>
    <a href="<?= $tokoUrl ?>/produk.php" class="btn btn-success btn-lg px-5 fw-bold">SHOP NOW</a>
  </div>

  <div class="text-center my-5">
    <h2 class="h4 fw-bold">Footstep Sneakers Original 100% Authentic</h2>
    <p class="text-muted mb-0">Ribuan pelanggan &bull; Bisa kirim ke seluruh Indonesia</p>
  </div>

  <div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="h4 judul-seksi mb-0">Recommended For You</h2>
    <a href="<?= $tokoUrl ?>/produk.php" class="text-decoration-none">Lihat semua</a>
  </div>

  <div class="row g-3">
    <?php while ($row = mysqli_fetch_assoc($produk)): ?>
      <?php include __DIR__ . '/partials/kartu.php'; ?>
    <?php endwhile; ?>
  </div>

</div>

<?php include __DIR__ . '/partials/footer.php'; ?>