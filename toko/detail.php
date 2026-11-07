<?php
require_once __DIR__ . '/../config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

// ---- Data produk ----
$stmt = mysqli_prepare($koneksi,
    "SELECT p.*, b.nm_brand, k.nm_kategori, w.nm_warna, u.nm_ukuran
     FROM produk p
     JOIN brand b    ON p.kd_brand    = b.kd_brand
     JOIN kategori k ON p.kd_kategori = k.kd_kategori
     JOIN warna w    ON p.kd_warna    = w.kd_warna
     JOIN ukuran u   ON p.kd_ukuran   = u.kd_ukuran
     WHERE p.kd_produk = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$p = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$judul = $p['nama_produk'] ?? 'Produk tidak ditemukan';
include __DIR__ . '/partials/header.php';

if (!$p) {
    http_response_code(404);
    ?>
    <div class="container my-5 text-center">
      <h1 class="h4">Produk tidak ditemukan</h1>
      <a href="<?= $tokoUrl ?>/produk.php" class="btn btn-dark mt-3">Lihat semua produk</a>
    </div>
    <?php
    include __DIR__ . '/partials/footer.php';
    exit;
}

// ---- Foto ----
$stmt = mysqli_prepare($koneksi, "SELECT path FROM foto WHERE kd_produk = ? ORDER BY kd_foto");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$fotos = [];
foreach (mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC) as $f) {
    $url = foto_produk($f['path']);
    if ($url) $fotos[] = $url;
}

// ---- Ukuran lain untuk produk dengan nama yang sama ----
$stmt = mysqli_prepare($koneksi,
    "SELECT p.kd_produk, p.stok, u.nm_ukuran
     FROM produk p JOIN ukuran u ON p.kd_ukuran = u.kd_ukuran
     WHERE p.nama_produk = ?
     ORDER BY u.nm_ukuran");
mysqli_stmt_bind_param($stmt, "s", $p['nama_produk']);
mysqli_stmt_execute($stmt);
$ukuranLain = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

$stok = (int) $p['stok'];
?>

<div class="container mt-4">

  <nav class="small mb-3">
    <a href="<?= $tokoUrl ?>/index.php">Beranda</a> /
    <a href="<?= $tokoUrl ?>/produk.php">Semua Produk</a> /
    <span class="text-muted"><?= e($p['nama_produk']) ?></span>
  </nav>

  <?php if (isset($_SESSION['pesan'])): ?>
    <div class="alert alert-<?= $_SESSION['tipe'] ?? 'success' ?> alert-dismissible fade show">
      <?= e($_SESSION['pesan']) ?>
      <?php if (($_SESSION['tipe'] ?? 'success') === 'success'): ?>
        <a href="<?= $tokoUrl ?>/keranjang.php" class="alert-link ms-2">Lihat keranjang</a>
      <?php endif; ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['pesan'], $_SESSION['tipe']); ?>
  <?php endif; ?>

  <div class="row g-4">

    <!-- Foto -->
    <div class="col-md-6">
      <?php if ($fotos): ?>
        <img id="fotoUtama" src="<?= e($fotos[0]) ?>" alt="<?= e($p['nama_produk']) ?>" class="foto-besar">
        <?php if (count($fotos) > 1): ?>
          <div class="d-flex flex-wrap gap-2 mt-2">
            <?php foreach ($fotos as $url): ?>
              <img src="<?= e($url) ?>" class="thumb" alt=""
                   onclick="document.getElementById('fotoUtama').src = this.src">
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      <?php else: ?>
        <div class="foto-kosong"><i class="bi bi-image"></i></div>
      <?php endif; ?>
    </div>

    <!-- Info -->
    <div class="col-md-6">
      <div class="text-muted text-uppercase small"><?= e($p['nm_brand']) ?></div>
      <h1 class="h3 fw-bold"><?= e($p['nama_produk']) ?></h1>
      <div class="fs-4 fw-bold mb-3"><?= rupiah($p['harga_jual']) ?></div>

      <table class="table table-sm w-auto mb-3">
        <tr><td class="text-muted pe-4">Kategori</td><td><?= e($p['nm_kategori']) ?></td></tr>
        <tr><td class="text-muted pe-4">Warna</td><td><?= e($p['nm_warna']) ?></td></tr>
        <tr><td class="text-muted pe-4">Ukuran</td><td><?= e($p['nm_ukuran']) ?></td></tr>
        <tr>
          <td class="text-muted pe-4">Stok</td>
          <td>
            <?php if ($stok <= 0): ?>
              <span class="badge text-bg-danger">Habis</span>
            <?php elseif ($stok <= 5): ?>
              <span class="badge text-bg-warning">Sisa <?= $stok ?></span>
            <?php else: ?>
              <span class="badge text-bg-success">Tersedia</span>
            <?php endif; ?>
          </td>
        </tr>
      </table>

      <?php if (count($ukuranLain) > 1): ?>
        <div class="mb-3">
          <div class="small text-muted mb-1">Pilih ukuran</div>
          <div class="d-flex flex-wrap gap-2">
            <?php foreach ($ukuranLain as $uk): ?>
              <?php
                $aktif = ($uk['kd_produk'] == $id);
                $kelas = $aktif ? 'btn-dark' : 'btn-outline-dark';
                if ($uk['stok'] <= 0) $kelas .= ' disabled text-decoration-line-through';
              ?>
              <a href="detail.php?id=<?= $uk['kd_produk'] ?>" class="btn btn-sm <?= $kelas ?>">
                <?= e($uk['nm_ukuran']) ?>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <!-- Tombol beli -->
      <?php if ($stok <= 0): ?>
        <button class="btn btn-secondary btn-lg w-100" disabled>Stok habis</button>

      <?php elseif (!$user): ?>
        <a href="<?= $tokoUrl ?>/login.php?next=<?= urlencode($tokoUrl . '/detail.php?id=' . $id) ?>"
           class="btn btn-dark btn-lg w-100">Login untuk membeli</a>

      <?php elseif ($user['role'] !== 'customer'): ?>
        <div class="alert alert-light border mb-0">Akun admin tidak bisa berbelanja. Login sebagai pelanggan.</div>

      <?php else: ?>
        <form action="tambah_keranjang.php" method="post" class="d-flex gap-2">
          <input type="hidden" name="kd_produk" value="<?= $id ?>">
          <input type="number" name="qty" value="1" min="1" max="<?= $stok ?>"
                 class="form-control" style="max-width:100px" required>
          <button type="submit" class="btn btn-dark btn-lg flex-grow-1">
            <i class="bi bi-cart-plus"></i> Masukkan Keranjang
          </button>
        </form>
      <?php endif; ?>

      <?php if (trim($p['deskripsi'] ?? '') !== ''): ?>
        <h2 class="h6 fw-bold mt-4">Deskripsi</h2>
        <p class="text-muted"><?= nl2br(e($p['deskripsi'])) ?></p>
      <?php endif; ?>
    </div>

  </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>