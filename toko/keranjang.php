<?php
$judul = "Keranjang Belanja";
require_once __DIR__ . '/../config/pelanggan.php';

$uid = (int) $_SESSION['user']['kd_user'];

$stmt = mysqli_prepare($koneksi,
    "SELECT kb.kd_keranjang_belanja, kb.qty,
            p.kd_produk, p.nama_produk, p.harga_jual, p.stok,
            b.nm_brand, u.nm_ukuran, w.nm_warna,
            (SELECT path FROM foto WHERE kd_produk = p.kd_produk ORDER BY kd_foto LIMIT 1) AS foto
     FROM keranjang_belanja kb
     JOIN produk p ON kb.kd_produk = p.kd_produk
     JOIN brand b  ON p.kd_brand   = b.kd_brand
     JOIN ukuran u ON p.kd_ukuran  = u.kd_ukuran
     JOIN warna w  ON p.kd_warna   = w.kd_warna
     WHERE kb.kd_user = ?
     ORDER BY kb.kd_keranjang_belanja DESC");
mysqli_stmt_bind_param($stmt, "i", $uid);
mysqli_stmt_execute($stmt);
$items = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

$totalHarga = 0;
$totalQty   = 0;
$bermasalah = false;   // true kalau ada item yang melebihi stok
foreach ($items as $it) {
    $totalHarga += $it['harga_jual'] * $it['qty'];
    $totalQty   += $it['qty'];
    if ($it['qty'] > $it['stok']) $bermasalah = true;
}

include __DIR__ . '/partials/header.php';
?>

<div class="container mt-4">
  <h1 class="h4 judul-seksi mb-3">Keranjang Belanja</h1>

  <?php if (isset($_SESSION['pesan'])): ?>
    <div class="alert alert-<?= $_SESSION['tipe'] ?? 'success' ?> alert-dismissible fade show">
      <?= e($_SESSION['pesan']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['pesan'], $_SESSION['tipe']); ?>
  <?php endif; ?>

  <?php if (!$items): ?>
    <div class="text-center py-5">
      <i class="bi bi-cart-x display-3 text-muted"></i>
      <p class="mt-3 text-muted">Keranjang kamu masih kosong.</p>
      <a href="<?= $tokoUrl ?>/produk.php" class="btn btn-dark">Mulai belanja</a>
    </div>

  <?php else: ?>
  <div class="row g-4">

    <!-- Daftar item -->
    <div class="col-lg-8">
      <div class="card">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead>
              <tr>
                <th>Produk</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th class="text-end">Subtotal</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $it): ?>
                <?php $foto = foto_produk($it['foto']); ?>
                <tr>
                  <td>
                    <div class="d-flex gap-3 align-items-center">
                      <?php if ($foto): ?>
                        <img src="<?= e($foto) ?>" alt="" class="thumb" style="cursor:default">
                      <?php else: ?>
                        <div class="thumb bg-light d-flex align-items-center justify-content-center text-muted"
                             style="cursor:default"><i class="bi bi-image"></i></div>
                      <?php endif; ?>
                      <div>
                        <a href="<?= $tokoUrl ?>/detail.php?id=<?= $it['kd_produk'] ?>"
                           class="fw-semibold text-decoration-none text-dark"><?= e($it['nama_produk']) ?></a>
                        <div class="small text-muted">
                          <?= e($it['nm_brand']) ?> &bull; Ukuran <?= e($it['nm_ukuran']) ?> &bull; <?= e($it['nm_warna']) ?>
                        </div>
                        <?php if ($it['stok'] <= 0): ?>
                          <div class="small text-danger">Stok habis, hapus dari keranjang</div>
                        <?php elseif ($it['qty'] > $it['stok']): ?>
                          <div class="small text-danger">Stok tersisa <?= $it['stok'] ?>, kurangi jumlahnya</div>
                        <?php endif; ?>
                      </div>
                    </div>
                  </td>
                  <td><?= rupiah($it['harga_jual']) ?></td>
                  <td>
                    <form action="keranjang_aksi.php" method="post" class="d-flex gap-1">
                      <input type="hidden" name="aksi" value="ubah">
                      <input type="hidden" name="id" value="<?= $it['kd_keranjang_belanja'] ?>">
                      <input type="number" name="qty" value="<?= $it['qty'] ?>" min="1"
                             max="<?= max(1, $it['stok']) ?>" class="form-control form-control-sm" style="width:80px">
                      <button type="submit" class="btn btn-sm btn-outline-dark">Ubah</button>
                    </form>
                  </td>
                  <td class="text-end"><?= rupiah($it['harga_jual'] * $it['qty']) ?></td>
                  <td>
                    <form action="keranjang_aksi.php" method="post">
                      <input type="hidden" name="aksi" value="hapus">
                      <input type="hidden" name="id" value="<?= $it['kd_keranjang_belanja'] ?>">
                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <form action="keranjang_aksi.php" method="post" class="mt-3"
            onsubmit="return confirm('Kosongkan seluruh keranjang?')">
        <input type="hidden" name="aksi" value="kosongkan">
        <button type="submit" class="btn btn-link text-danger p-0">Kosongkan keranjang</button>
      </form>
    </div>

    <!-- Ringkasan -->
    <div class="col-lg-4">
      <div class="card card-body">
        <h2 class="h6 fw-bold mb-3">Ringkasan Belanja</h2>
        <div class="d-flex justify-content-between mb-1">
          <span class="text-muted">Jumlah barang</span>
          <span><?= $totalQty ?></span>
        </div>
        <hr>
        <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
          <span>Total</span>
          <span><?= rupiah($totalHarga) ?></span>
        </div>

        <?php if ($bermasalah): ?>
          <button class="btn btn-secondary w-100" disabled>Lanjut ke Checkout</button>
          <div class="small text-danger mt-2">Sesuaikan jumlah barang yang melebihi stok dulu.</div>
        <?php else: ?>
          <a href="<?= $tokoUrl ?>/checkout.php" class="btn btn-dark w-100">Lanjut ke Checkout</a>
        <?php endif; ?>

        <a href="<?= $tokoUrl ?>/produk.php" class="btn btn-link w-100 mt-2">Lanjut belanja</a>
      </div>
    </div>

  </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>