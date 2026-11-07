<?php
$judul = "Tambah Warna";
$menu  = "warna";
require_once '../config/admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nm_warna']);

    if ($nama === '') {
        $error = "Nama warna wajib diisi.";
    } else {
        try {
            $stmt = mysqli_prepare($koneksi, "INSERT INTO warna (nm_warna) VALUES (?)");
            mysqli_stmt_bind_param($stmt, "s", $nama);
            mysqli_stmt_execute($stmt);

            $_SESSION['pesan'] = "Warna berhasil ditambahkan.";
            header("Location: index.php");
            exit;
        } catch (mysqli_sql_exception $ex) {
            $error = "Gagal menyimpan. Mungkin nama warna sudah ada.";
        }
    }
}

include '../partials/header.php';
?>

<div class="app-content-header">
  <div class="container-fluid">
    <h3 class="mb-0">Tambah Warna</h3>
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
          <div class="mb-3">
            <label class="form-label">Nama Warna</label>
            <input type="text" name="nm_warna" class="form-control"
                   value="<?= e($_POST['nm_warna'] ?? '') ?>" required>
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