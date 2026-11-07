<?php
$judul = "Edit Brand";
$menu  = "brand";
require_once '../config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = mysqli_prepare($koneksi, "SELECT * FROM brand WHERE kd_brand = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$brand = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$brand) {
    $_SESSION['pesan'] = "Data brand tidak ditemukan.";
    $_SESSION['tipe']  = "danger";
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nm_brand']);
    $ket  = trim($_POST['keterangan']);
    $brand['nm_brand']   = $nama;
    $brand['keterangan'] = $ket;

    if ($nama === '') {
        $error = "Nama brand wajib diisi.";
    } else {
        try {
            $stmt = mysqli_prepare($koneksi, "UPDATE brand SET nm_brand = ?, keterangan = ? WHERE kd_brand = ?");
            mysqli_stmt_bind_param($stmt, "ssi", $nama, $ket, $id);
            mysqli_stmt_execute($stmt);

            $_SESSION['pesan'] = "Brand berhasil diubah.";
            header("Location: index.php");
            exit;
        } catch (mysqli_sql_exception $ex) {
            $error = "Gagal menyimpan. Mungkin nama brand sudah ada.";
        }
    }
}

include '../partials/header.php';
?>

<div class="app-content-header">
  <div class="container-fluid">
    <h3 class="mb-0">Edit Brand</h3>
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
            <label class="form-label">Nama Brand</label>
            <input type="text" name="nm_brand" class="form-control"
                   value="<?= e($brand['nm_brand']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="3"><?= e($brand['keterangan']) ?></textarea>
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