<?php
$judul = "Pesanan Saya";
require_once __DIR__ . '/../config/pelanggan.php';

$uid = (int) $_SESSION['user']['kd_user'];

$stmt = mysqli_prepare($koneksi,
    "SELECT p.kd_pembayaran, p.invoice, p.tgl_bayar, p.status,
            COALESCE(SUM(d.harga_jual * d.qty), 0) AS total,
            COALESCE(SUM(d.qty), 0) AS jml
     FROM pembayaran p
     LEFT JOIN detail_pembayaran d ON d.kd_pembayaran = p.kd_pembayaran
     WHERE p.kd_user = ?
     GROUP BY p.kd_pembayaran, p.invoice, p.tgl_bayar, p.status
     ORDER BY p.kd_pembayaran DESC");
mysqli_stmt_bind_param($stmt, "i", $uid);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

include __DIR__ . '/partials/header.php';
?>

<div class="container mt-4">
  <h1 class="h4 judul-seksi mb-3">Pesanan Saya</h1>

  <?php if (isset($_SESSION['pesan'])): ?>
    <div class="alert alert-<?= $_SESSION['tipe'] ?? 'success' ?> alert-dismissible fade show">
      <?= e($_SESSION['pesan']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['pesan'], $_SESSION['tipe']); ?>
  <?php endif; ?>

  <?php if (!$data): ?>
    <div class="text-center py-5">
      <i class="bi bi-receipt display-3 text-muted"></i>
      <p class="mt-3 text-muted">Kamu belum punya pesanan.</p>
      <a href="<?= $tokoUrl ?>/produk.php" class="btn btn-dark">Mulai belanja</a>
    </div>
  <?php else: ?>
    <div class="card">
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead>
            <tr>
              <th>Invoice</th>
              <th>Tanggal</th>
              <th>Barang</th>
              <th>Total</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($data as $row): ?>
              <tr>
                <td><?= e($row['invoice']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($row['tgl_bayar'])) ?></td>
                <td><?= $row['jml'] ?></td>
                <td><?= rupiah($row['total']) ?></td>
                <td>
                  <span class="badge text-bg-<?= badge_status($row['status']) ?>">
                    <?= e(ucfirst($row['status'])) ?>
                  </span>
                </td>
                <td>
                  <a href="riwayat_detail.php?id=<?= $row['kd_pembayaran'] ?>" class="btn btn-sm btn-outline-dark">Detail</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>