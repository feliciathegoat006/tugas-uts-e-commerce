<?php
$judul = "Data Pesanan";
$menu  = "pesanan";
include '../partials/header.php';

$daftarStatus = ['menunggu', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];
$status       = $_GET['status'] ?? '';
$pakaiFilter  = in_array($status, $daftarStatus);

$sql = "SELECT p.kd_pembayaran, p.invoice, p.tgl_bayar, p.status, u.username,
               COALESCE(SUM(d.harga_jual * d.qty), 0) AS total
        FROM pembayaran p
        JOIN `user` u ON p.kd_user = u.kd_user
        LEFT JOIN detail_pembayaran d ON d.kd_pembayaran = p.kd_pembayaran "
     . ($pakaiFilter ? "WHERE p.status = ? " : "")
     . "GROUP BY p.kd_pembayaran, p.invoice, p.tgl_bayar, p.status, u.username
        ORDER BY p.kd_pembayaran DESC";

$stmt = mysqli_prepare($koneksi, $sql);
if ($pakaiFilter) {
    mysqli_stmt_bind_param($stmt, "s", $status);
}
mysqli_stmt_execute($stmt);
$data = mysqli_stmt_get_result($stmt);
?>

<div class="app-content-header">
  <div class="container-fluid">
    <h3 class="mb-0">Data Pesanan</h3>
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

    <div class="mb-3">
      <a href="index.php" class="btn btn-sm <?= !$pakaiFilter ? 'btn-primary' : 'btn-outline-primary' ?>">Semua</a>
      <?php foreach ($daftarStatus as $s): ?>
        <a href="index.php?status=<?= $s ?>"
           class="btn btn-sm <?= $status === $s ? 'btn-primary' : 'btn-outline-primary' ?>"><?= ucfirst($s) ?></a>
      <?php endforeach; ?>
    </div>

    <div class="card">
      <div class="card-body p-0 table-responsive">
        <table class="table table-striped mb-0 datatable">
          <thead>
            <tr>
              <th style="width:60px">No</th>
              <th>Invoice</th>
              <th>Tanggal</th>
              <th>Pelanggan</th>
              <th>Total</th>
              <th>Status</th>
              <th style="width:100px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($data)): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= e($row['invoice']) ?></td>
                <td data-order="<?= strtotime($row['tgl_bayar']) ?>"><?= date('d/m/Y H:i', strtotime($row['tgl_bayar'])) ?></td>
                <td><?= e($row['username']) ?></td>
                <td data-order="<?= (float) $row['total'] ?>"><?= rupiah($row['total']) ?></td>
                <td>
                  <span class="badge text-bg-<?= badge_status($row['status']) ?>">
                    <?= e(ucfirst($row['status'])) ?>
                  </span>
                </td>
                <td>
                  <a href="detail.php?id=<?= $row['kd_pembayaran'] ?>" class="btn btn-info btn-sm">Detail</a>
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