<?php
$judul = "Data Distributor";
$menu  = "distributor";
include '../partials/header.php';

$data = mysqli_query($koneksi, "SELECT * FROM distributor ORDER BY kd_distributor");
?>

<div class="app-content-header">
  <div class="container-fluid">
    <h3 class="mb-0">Data Distributor</h3>
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
          <i class="bi bi-plus-lg"></i> Tambah Distributor
        </a>
      </div>
      <div class="card-body p-0 table-responsive">
        <table class="table table-striped mb-0 datatable">
          <thead>
            <tr>
              <th style="width:60px">No</th>
              <th>Nama Distributor</th>
              <th>Alamat</th>
              <th>No. Telp</th>
              <th>Email</th>
              <th>Sales</th>
              <th style="width:160px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($data)): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= e($row['nm_distributor']) ?></td>
                <td><?= e($row['alamat']) ?></td>
                <td><?= e($row['no_telp']) ?></td>
                <td><?= e($row['email']) ?></td>
                <td>
                  <?= e($row['nm_sales']) ?>
                  <?php if ($row['hp_sales']): ?>
                    <br><small class="text-muted"><?= e($row['hp_sales']) ?></small>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="edit.php?id=<?= $row['kd_distributor'] ?>" class="btn btn-warning btn-sm">Edit</a>
                  <a href="hapus.php?id=<?= $row['kd_distributor'] ?>" class="btn btn-danger btn-sm"
                     onclick="return confirm('Yakin hapus distributor ini?')">Hapus</a>
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