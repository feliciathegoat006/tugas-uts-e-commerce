<?php
$judul = "Tambah Distributor";
$menu  = "distributor";
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nm_distributor']);
    $alamat   = trim($_POST['alamat']);
    $no_telp  = trim($_POST['no_telp']);
    $email    = trim($_POST['email']);
    $nm_sales = trim($_POST['nm_sales']);
    $hp_sales = trim($_POST['hp_sales']);

    if ($nama === '') {
        $error = "Nama distributor wajib diisi.";
    } else {
        try {
            $stmt = mysqli_prepare($koneksi,
                "INSERT INTO distributor (nm_distributor, alamat, no_telp, email, nm_sales, hp_sales)
                 VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssssss", $nama, $alamat, $no_telp, $email, $nm_sales, $hp_sales);
            mysqli_stmt_execute($stmt);

            $_SESSION['pesan'] = "Distributor berhasil ditambahkan.";
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
    <h3 class="mb-0">Tambah Distributor</h3>
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
                     value="<?= e($_POST['nm_distributor'] ?? '') ?>" required>
            </div>
            <div class="col-md-12 mb-3">
              <label class="form-label">Alamat</label>
              <textarea name="alamat" class="form-control" rows="3"><?= e($_POST['alamat'] ?? '') ?></textarea>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">No. Telp</label>
              <input type="text" name="no_telp" class="form-control"
                     value="<?= e($_POST['no_telp'] ?? '') ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control"
                     value="<?= e($_POST['email'] ?? '') ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Nama Sales</label>
              <input type="text" name="nm_sales" class="form-control"
                     value="<?= e($_POST['nm_sales'] ?? '') ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">HP Sales</label>
              <input type="text" name="hp_sales" class="form-control"
                     value="<?= e($_POST['hp_sales'] ?? '') ?>">
            </div>
          </div>
        </div>
        <div class="card-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
          <a href="index.php" class="btn btn-secondary">Batal</a>
        </div>
      </form>
    </div>

  </div>
</div>

<?php include '../partials/footer.php'; ?>