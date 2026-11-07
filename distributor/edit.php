<?php
$judul = "Edit Distributor";
$menu  = "distributor";
require_once '../config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = mysqli_prepare($koneksi, "SELECT * FROM distributor WHERE kd_distributor = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$dist = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$dist) {
    $_SESSION['pesan'] = "Data distributor tidak ditemukan.";
    $_SESSION['tipe']  = "danger";
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dist['nm_distributor'] = trim($_POST['nm_distributor']);
    $dist['alamat']         = trim($_POST['alamat']);
    $dist['no_telp']        = trim($_POST['no_telp']);
    $dist['email']          = trim($_POST['email']);
    $dist['nm_sales']       = trim($_POST['nm_sales']);
    $dist['hp_sales']       = trim($_POST['hp_sales']);

    if ($dist['nm_distributor'] === '') {
        $error = "Nama distributor wajib diisi.";
    } else {
        try {
            $stmt = mysqli_prepare($koneksi,
                "UPDATE distributor
                 SET nm_distributor = ?, alamat = ?, no_telp = ?, email = ?, nm_sales = ?, hp_sales = ?
                 WHERE kd_distributor = ?");
            mysqli_stmt_bind_param($stmt, "ssssssi",
                $dist['nm_distributor'], $dist['alamat'], $dist['no_telp'],
                $dist['email'], $dist['nm_sales'], $dist['hp_sales'], $id);
            mysqli_stmt_execute($stmt);

            $_SESSION['pesan'] = "Distributor berhasil diubah.";
            header("Location: index.php");
            exit;
        } catch (mysqli_sql_exception $ex) {
            $error = "Gagal menyimpan distributor. Coba lagi.";
        }
    }
}

include '../partials/header.php';
?>

<div class="app-content-header">
  <div class="container-fluid">
    <h3 class="mb-0">Edit Distributor</h3>
  </div>
</div>

<div class="app-content">
  <div class="container-fluid">

    <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>

    <div class="card">
      <form method="post">
        <div class="card-body">
          <div class="row">
            <div class="col-md-12 mb-3">
              <label class="form-label">Nama Distributor</label>
              <input type="text" name="nm_distributor" class="form-control"
                     value="<?= e($dist['nm_distributor']) ?>" required>
            </div>
            <div class="col-md-12 mb-3">
              <label class="form-label">Alamat</label>
              <textarea name="alamat" class="form-control" rows="3"><?= e($dist['alamat']) ?></textarea>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">No. Telp</label>
              <input type="text" name="no_telp" class="form-control"
                     value="<?= e($dist['no_telp']) ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control"
                     value="<?= e($dist['email']) ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Nama Sales</label>
              <input type="text" name="nm_sales" class="form-control"
                     value="<?= e($dist['nm_sales']) ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">HP Sales</label>
              <input type="text" name="hp_sales" class="form-control"
                     value="<?= e($dist['hp_sales']) ?>">
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
          <a href="index.php" class="btn btn-secondary">Batal</a>
        </div>
      </form>
    </div>

  </div>
</div>

<?php include '../partials/footer.php'; ?>