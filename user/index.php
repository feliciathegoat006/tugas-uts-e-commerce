<?php
$judul = "Data User";
$menu  = "user";
include '../partials/header.php';

$data = mysqli_query($koneksi,
    "SELECT u.kd_user, u.username, u.email, u.no_telpon, u.role, u.created_at,
            COUNT(p.kd_pembayaran) AS jml_pesanan
     FROM `user` u
     LEFT JOIN pembayaran p ON p.kd_user = u.kd_user
     GROUP BY u.kd_user, u.username, u.email, u.no_telpon, u.role, u.created_at
     ORDER BY u.kd_user");
?>

<div class="app-content-header">
  <div class="container-fluid">
    <h3 class="mb-0">Data User</h3>
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
      <div class="card-body p-0 table-responsive">
        <table class="table table-striped mb-0 datatable">
          <thead>
            <tr>
              <th style="width:60px">No</th>
              <th>Username</th>
              <th>Email</th>
              <th>No. Telpon</th>
              <th>Role</th>
              <th>Pesanan</th>
              <th>Terdaftar</th>
              <th style="width:100px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($data)): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= e($row['username']) ?></td>
                <td><?= e($row['email']) ?></td>
                <td><?= e($row['no_telpon']) ?></td>
                <td>
                  <span class="badge text-bg-<?= $row['role'] === 'admin' ? 'primary' : 'secondary' ?>">
                    <?= e(ucfirst($row['role'])) ?>
                  </span>
                </td>
                <td><?= $row['jml_pesanan'] ?></td>
                <td data-order="<?= strtotime($row['created_at']) ?>"><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                <td>
                  <?php if ($row['role'] === 'customer'): ?>
                    <a href="hapus.php?id=<?= $row['kd_user'] ?>" class="btn btn-danger btn-sm"
                       onclick="return confirm('Yakin hapus user ini?')">Hapus</a>
                  <?php else: ?>
                    <span class="text-muted">-</span>
                  <?php endif; ?>
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