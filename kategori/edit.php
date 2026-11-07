<?php
$judul = "Edit Kategori";
$menu  = "kategori";
require_once '../config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = mysqli_prepare($koneksi, "SELECT * FROM kategori WHERE kd_kategori = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$kategori = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$kategori) {
    $_SESSION['pesan'] = "Data kategori tidak ditemukan.";
    $_SESSION['tipe']  = "danger";
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nm_kategori']);
    $ket  = trim($_POST['keterangan']);
    $kategori['nm_kategori'] = $nama;
    $kategori['keterangan']  = $ket;

    if ($nama === '') {
        $error = "Nama kategori wajib diisi.";
    } else {
        try {
            $stmt = mysqli_prepare($koneksi, "UPDATE kategori SET nm_kategori = ?, keterangan = ? WHERE kd_kategori = ?");
            mysqli_stmt_bind_param($stmt, "ssi", $nama, $ket, $id);
            mysqli_stmt_execute($stmt);

            $_SESSION['pesan'] = "Kategori berhasil diubah.";
            header("Location: index.php");
            exit;
        } catch (mysqli_sql_exception $ex) {
            $error = "Gagal menyimpan. Mungkin nama kategori sudah ada.";
        }
    }
}

include '../partials/header.php';
?>

<div class="app-content-header">
  <div class="container-fluid">
    <h3 class="mb-0">Edit Kategori</h3>
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
            <label class="form-label">Nama Kategori</label>
            <input type="text" name="nm_kategori" class="form-control"
                   value="<?= e($kategori['nm_kategori']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="3"><?= e($kategori['keterangan']) ?></textarea>
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