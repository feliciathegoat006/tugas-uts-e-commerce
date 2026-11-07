<?php
$judul = "Dashboard";
$menu  = "dashboard";
include 'partials/header.php';

// Ambil satu angka dari sebuah query
function satu($koneksi, $sql) {
    $hasil = mysqli_query($koneksi, $sql);
    return mysqli_fetch_row($hasil)[0] ?? 0;
}

$totalProduk    = satu($koneksi, "SELECT COUNT(*) FROM produk");
$totalPelanggan = satu($koneksi, "SELECT COUNT(*) FROM `user` WHERE role = 'customer'");
$pesananBaru    = satu($koneksi, "SELECT COUNT(*) FROM pembayaran WHERE status = 'menunggu'");
$pendapatan     = satu($koneksi,
    "SELECT COALESCE(SUM(d.harga_jual * d.qty), 0)
     FROM detail_pembayaran d
     JOIN pembayaran p ON d.kd_pembayaran = p.kd_pembayaran
     WHERE p.status = 'selesai'");
$laba           = satu($koneksi,
    "SELECT COALESCE(SUM((d.harga_jual - d.harga_beli) * d.qty), 0)
     FROM detail_pembayaran d
     JOIN pembayaran p ON d.kd_pembayaran = p.kd_pembayaran
     WHERE p.status = 'selesai'");

// Pesanan terbaru
$pesananTerbaru = mysqli_query($koneksi,
    "SELECT p.kd_pembayaran, p.invoice, p.tgl_bayar, p.status, u.username,
            COALESCE(SUM(d.harga_jual * d.qty), 0) AS total
     FROM pembayaran p
     JOIN `user` u ON p.kd_user = u.kd_user
     LEFT JOIN detail_pembayaran d ON d.kd_pembayaran = p.kd_pembayaran
     GROUP BY p.kd_pembayaran, p.invoice, p.tgl_bayar, p.status, u.username
     ORDER BY p.kd_pembayaran DESC
     LIMIT 5");

// Stok menipis (5 atau kurang)
$stokTipis = mysqli_query($koneksi,
    "SELECT p.kd_produk, p.nama_produk, p.stok, u.nm_ukuran
     FROM produk p
     JOIN ukuran u ON p.kd_ukuran = u.kd_ukuran
     WHERE p.stok <= 5
     ORDER BY p.stok ASC, p.nama_produk
     LIMIT 8");

// Produk terlaris (pesanan yang dibatalkan tidak dihitung)
$terlaris = mysqli_query($koneksi,
    "SELECT pr.kd_produk, pr.nama_produk, u.nm_ukuran, SUM(d.qty) AS terjual
     FROM detail_pembayaran d
     JOIN pembayaran p ON d.kd_pembayaran = p.kd_pembayaran
     JOIN produk pr    ON d.kd_produk     = pr.kd_produk
     JOIN ukuran u     ON pr.kd_ukuran    = u.kd_ukuran
     WHERE p.status <> 'dibatalkan'
     GROUP BY pr.kd_produk, pr.nama_produk, u.nm_ukuran
     ORDER BY terjual DESC
     LIMIT 5");
?>

<div class="app-content-header">
  <div class="container-fluid">
    <h3 class="mb-0">Dashboard</h3>
  </div>
</div>

<div class="app-content">
  <div class="container-fluid">

    <!-- Kotak ringkasan -->
    <div class="row">
      <div class="col-lg-3 col-6">
        <div class="small-box text-bg-primary">
          <div class="inner"><h3><?= $totalProduk ?></h3><p>Total Produk</p></div>
          <a href="produk/index.php" class="small-box-footer link-light link-underline-opacity-0">Kelola produk</a>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box text-bg-warning">
          <div class="inner"><h3><?= $pesananBaru ?></h3><p>Pesanan Menunggu</p></div>
          <a href="pesanan/index.php?status=menunggu" class="small-box-footer link-dark link-underline-opacity-0">Lihat pesanan</a>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box text-bg-success">
          <div class="inner"><h3><?= $totalPelanggan ?></h3><p>Pelanggan</p></div>
          <a href="user/index.php" class="small-box-footer link-light link-underline-opacity-0">Lihat user</a>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box text-bg-danger">
          <div class="inner">
            <h3 class="fs-3"><?= rupiah($pendapatan) ?></h3>
            <p>Pendapatan (selesai)<br><small>Laba <?= rupiah($laba) ?></small></p>
          </div>
          <a href="pesanan/index.php?status=selesai" class="small-box-footer link-light link-underline-opacity-0">Lihat pesanan selesai</a>
        </div>
      </div>
    </div>

    <div class="row">

      <!-- Pesanan terbaru -->
      <div class="col-lg-7">
        <div class="card mb-4">
          <div class="card-header"><h3 class="card-title">Pesanan Terbaru</h3></div>
          <div class="card-body p-0 table-responsive">
            <table class="table table-striped mb-0">
              <thead>
                <tr><th>Invoice</th><th>Pelanggan</th><th>Total</th><th>Status</th><th></th></tr>
              </thead>
              <tbody>
                <?php if (mysqli_num_rows($pesananTerbaru) === 0): ?>
                  <tr><td colspan="5" class="text-center text-muted py-3">Belum ada pesanan.</td></tr>
                <?php endif; ?>
                <?php while ($r = mysqli_fetch_assoc($pesananTerbaru)): ?>
                  <tr>
                    <td><?= e($r['invoice']) ?></td>
                    <td><?= e($r['username']) ?></td>
                    <td><?= rupiah($r['total']) ?></td>
                    <td>
                      <span class="badge text-bg-<?= badge_status($r['status']) ?>"><?= e(ucfirst($r['status'])) ?></span>
                    </td>
                    <td><a href="pesanan/detail.php?id=<?= $r['kd_pembayaran'] ?>" class="btn btn-info btn-sm">Detail</a></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-lg-5">

        <!-- Stok menipis -->
        <div class="card mb-4">
          <div class="card-header"><h3 class="card-title">Stok Menipis (5 atau kurang)</h3></div>
          <div class="card-body p-0">
            <table class="table mb-0">
              <tbody>
                <?php if (mysqli_num_rows($stokTipis) === 0): ?>
                  <tr><td class="text-center text-muted py-3">Semua stok aman.</td></tr>
                <?php endif; ?>
                <?php while ($r = mysqli_fetch_assoc($stokTipis)): ?>
                  <tr>
                    <td>
                      <a href="produk/edit.php?id=<?= $r['kd_produk'] ?>" class="text-decoration-none">
                        <?= e($r['nama_produk']) ?></a>
                      <small class="text-muted">(ukuran <?= e($r['nm_ukuran']) ?>)</small>
                    </td>
                    <td class="text-end">
                      <span class="badge text-bg-<?= $r['stok'] <= 0 ? 'danger' : 'warning' ?>">
                        <?= $r['stok'] <= 0 ? 'Habis' : 'Sisa ' . $r['stok'] ?>
                      </span>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Produk terlaris -->
        <div class="card mb-4">
          <div class="card-header"><h3 class="card-title">Produk Terlaris</h3></div>
          <div class="card-body p-0">
            <table class="table mb-0">
              <tbody>
                <?php if (mysqli_num_rows($terlaris) === 0): ?>
                  <tr><td class="text-center text-muted py-3">Belum ada penjualan.</td></tr>
                <?php endif; ?>
                <?php while ($r = mysqli_fetch_assoc($terlaris)): ?>
                  <tr>
                    <td>
                      <?= e($r['nama_produk']) ?>
                      <small class="text-muted">(ukuran <?= e($r['nm_ukuran']) ?>)</small>
                    </td>
                    <td class="text-end"><strong><?= $r['terjual'] ?></strong> terjual</td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>

<?php include 'partials/footer.php'; ?>