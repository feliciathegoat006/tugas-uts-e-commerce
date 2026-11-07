<?php
$judul = "Edit Warna";
$menu  = "warna";
require_once '../config/admin.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = mysqli_prepare($koneksi, "SELECT * FROM warna WHERE kd_warna = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$warna = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$warna) {
    $_SESSION['pesan'] = "Data warna tidak ditemukan.";
    $_SESSION['tipe']  = "danger";
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nm_warna']);
    $warna['nm_warna'] = $nama;

    if ($nama === '') {
        $error = "Nama warna wajib diisi.";
    } else {
        try {
            $stmt = mysqli_prepare($koneksi, "UPDATE warna SET nm_warna = ? WHERE kd_warna = ?");
            mysqli_stmt_bind_param($stmt, "si", $nama, $id);
            mysqli_stmt_execute($stmt);

            $_SESSION['pesan'] = "Warna berhasil diubah.";
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
    <h3 class="mb-0">Edit Warna</h3>
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
                   value="<?= e($warna['nm_warna']) ?>" required>
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