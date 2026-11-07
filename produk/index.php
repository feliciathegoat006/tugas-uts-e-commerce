<?php
$judul = "Data Produk";
$menu  = "produk";
include '../partials/header.php';

$sql = "SELECT p.*, b.nm_brand, k.nm_kategori, w.nm_warna, u.nm_ukuran,
               (SELECT path FROM foto WHERE kd_produk = p.kd_produk
                ORDER BY kd_foto LIMIT 1) AS foto
        FROM produk p
        JOIN brand b    ON p.kd_brand    = b.kd_brand
        JOIN kategori k ON p.kd_kategori = k.kd_kategori
        JOIN warna w    ON p.kd_warna    = w.kd_warna
        JOIN ukuran u   ON p.kd_ukuran   = u.kd_ukuran
        ORDER BY p.kd_produk DESC";
$data = mysqli_query($koneksi, $sql);
?>

<div class="app-content-header">
  <div class="container-fluid">
    <h3 class="mb-0">Data Produk</h3>
  </div>
</div>

<div class="app-content">
  <div class="container-fluid">

    <?php if (isset($_SESSION['pesan'])): ?>
      <div class="alert alert-<?= $_SESSION['tipe'] ?? 'success' ?> alert-dismissible fade show">
        <?= e($_SESSION['pesan']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['pesan'], $_SESSION['tipe']); ?>
    <?php endif; ?>

    <div class="card">
      <div class="card-header">
        <a href="tambah.php" class="btn btn-primary btn-sm">
          <i class="bi bi-plus-lg"></i> Tambah Produk
        </a>
      </div>
      <div class="card-body p-0 table-responsive">
        <table class="table table-striped align-middle mb-0 datatable">
          <thead>
            <tr>
              <th>No</th>
              <th>Foto</th>
              <th>Nama Produk</th>
              <th>Brand</th>
              <th>Kategori</th>
              <th>Warna</th>
              <th>Ukuran</th>
              <th>Harga Jual</th>
              <th>Stok</th>
              <th style="width:160px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($data)): ?>
              <?php $adaFoto = $row['foto'] && file_exists(__DIR__ . '/../' . $row['foto']); ?>
              <tr>
                <td><?= $no++ ?></td>
                <td>
                  <?php if ($adaFoto): ?>
                    <img src="<?= $base ?>/<?= e($row['foto']) ?>" alt="" style="width:60px;height:60px;object-fit:cover;border-radius:6px">
                  <?php else: ?>
                    <div class="bg-secondary-subtle text-center small text-muted d-flex align-items-center justify-content-center"
                         style="width:60px;height:60px;border-radius:6px">No foto</div>
                  <?php endif; ?>
                </td>
                <td><?= e($row['nama_produk']) ?></td>
                <td><?= e($row['nm_brand']) ?></td>
                <td><?= e($row['nm_kategori']) ?></td>
                <td><?= e($row['nm_warna']) ?></td>
                <td><?= e($row['nm_ukuran']) ?></td>
                <td data-order="<?= (float) $row['harga_jual'] ?>">Rp <?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
                <td><?= $row['stok'] ?></td>
                <td>
                  <a href="edit.php?id=<?= $row['kd_produk'] ?>" class="btn btn-warning btn-sm">Edit</a>
                  <a href="hapus.php?id=<?= $row['kd_produk'] ?>" class="btn btn-danger btn-sm"
                     onclick="return confirm('Yakin hapus produk ini?')">Hapus</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<?php include '../partials/footer.php'; ?>