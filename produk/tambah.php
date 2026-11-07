<?php
$judul = "Tambah Produk";
$menu  = "produk";
require_once '../config/koneksi.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama        = trim($_POST['nama_produk']);
    $kd_brand    = (int) $_POST['kd_brand'];
    $kd_kategori = (int) $_POST['kd_kategori'];
    $kd_warna    = (int) $_POST['kd_warna'];
    $kd_ukuran   = (int) $_POST['kd_ukuran'];
    $harga_jual  = (float) $_POST['harga_jual'];
    $harga_beli  = (float) $_POST['harga_beli'];
    $stok        = (int) $_POST['stok'];
        $kd_distributor = ($_POST['kd_distributor'] ?? '') !== '' ? (int) $_POST['kd_distributor'] : null;
    $deskripsi   = trim($_POST['deskripsi']);

    // 1. Validasi isian
    if ($nama === '')                          $errors[] = "Nama produk wajib diisi.";
    if (!$kd_brand || !$kd_kategori || !$kd_warna || !$kd_ukuran)
                                               $errors[] = "Brand, kategori, warna, dan ukuran wajib dipilih.";
    if ($harga_jual <= 0)                      $errors[] = "Harga jual harus lebih dari 0.";
    if ($harga_beli < 0 || $stok < 0)          $errors[] = "Harga beli dan stok tidak boleh minus.";

    // 2. Validasi foto (boleh lebih dari satu, boleh kosong)
    $diizinkan = ['jpg', 'jpeg', 'png', 'webp'];
    $fotoValid = [];
    if (!empty($_FILES['foto']['name'][0])) {
        foreach ($_FILES['foto']['name'] as $i => $namaAsli) {
            if ($_FILES['foto']['error'][$i] !== UPLOAD_ERR_OK) {
                $errors[] = "Foto $namaAsli gagal diupload.";
                continue;
            }
            $ext = strtolower(pathinfo($namaAsli, PATHINFO_EXTENSION));
            if (!in_array($ext, $diizinkan)) {
                $errors[] = "Foto $namaAsli harus berformat JPG, PNG, atau WEBP.";
            } elseif ($_FILES['foto']['size'][$i] > 2 * 1024 * 1024) {
                $errors[] = "Foto $namaAsli lebih dari 2 MB.";
            } else {
                $fotoValid[] = ['asli' => $namaAsli, 'tmp' => $_FILES['foto']['tmp_name'][$i], 'ext' => $ext];
            }
        }
    }

    // 3. Kalau semua valid, simpan
    if (!$errors) {
        $fileTersimpan = [];
        try {
            mysqli_begin_transaction($koneksi);

            // Simpan produk
            $stmt = mysqli_prepare($koneksi,
                "INSERT INTO produk (kd_ukuran, kd_brand, kd_kategori, kd_warna, kd_distributor,
                                     nama_produk, harga_jual, harga_beli, deskripsi, stok)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "iiiiisddsi",
                $kd_ukuran, $kd_brand, $kd_kategori, $kd_warna, $kd_distributor,
                $nama, $harga_jual, $harga_beli, $deskripsi, $stok);
            mysqli_stmt_execute($stmt);
            $kd_produk = mysqli_insert_id($koneksi);

            // Simpan foto: pindahkan file, lalu catat di tabel foto
            $stmtFoto = mysqli_prepare($koneksi,
                "INSERT INTO foto (kd_produk, nm_foto, path) VALUES (?, ?, ?)");
            foreach ($fotoValid as $f) {
                $namaBaru = uniqid('produk_') . '.' . $f['ext'];
                $path     = 'uploads/produk/' . $namaBaru;
                move_uploaded_file($f['tmp'], __DIR__ . '/../' . $path);
                $fotoTersimpan[] = __DIR__ . '/../' . $path;

                mysqli_stmt_bind_param($stmtFoto, "iss", $kd_produk, $f['asli'], $path);
                mysqli_stmt_execute($stmtFoto);
            }

            // Catat riwayat stok masuk
            $stmtStok = mysqli_prepare($koneksi,
                "INSERT INTO stok_produk (tgl_masuk, kd_barang, harga_beli, harga_jual, qty)
                 VALUES (CURDATE(), ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmtStok, "iddi", $kd_produk, $harga_beli, $harga_jual, $stok);
            mysqli_stmt_execute($stmtStok);

            mysqli_commit($koneksi);

            $_SESSION['pesan'] = "Produk berhasil ditambahkan.";
            header("Location: index.php");
            exit;
        } catch (Exception $ex) {
            mysqli_rollback($koneksi);
            foreach ($fotoTersimpan ?? [] as $file) {
                if (file_exists($file)) unlink($file);
            }
            $errors[] = "Gagal menyimpan produk. Coba lagi.";
        }
    }
}

include '../partials/header.php';
?>

<div class="app-content-header">
  <div class="container-fluid">
    <h3 class="mb-0">Tambah Produk</h3>
  </div>
</div>

<div class="app-content">
  <div class="container-fluid">

    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="card">
      <form method="post" enctype="multipart/form-data">
        <div class="card-body">
          <div class="row">

            <div class="col-md-12 mb-3">
              <label class="form-label">Nama Produk</label>
              <input type="text" name="nama_produk" class="form-control"
                     value="<?= e($_POST['nama_produk'] ?? '') ?>" required>
            </div>

            <div class="col-md-3 mb-3">
              <label class="form-label">Brand</label>
              <select name="kd_brand" class="form-select" required>
                <?= opsi($koneksi, 'brand', 'kd_brand', 'nm_brand', $_POST['kd_brand'] ?? null) ?>
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Kategori</label>
              <select name="kd_kategori" class="form-select" required>
                <?= opsi($koneksi, 'kategori', 'kd_kategori', 'nm_kategori', $_POST['kd_kategori'] ?? null) ?>
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Warna</label>
              <select name="kd_warna" class="form-select" required>
                <?= opsi($koneksi, 'warna', 'kd_warna', 'nm_warna', $_POST['kd_warna'] ?? null) ?>
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Ukuran</label>
              <select name="kd_ukuran" class="form-select" required>
                <?= opsi($koneksi, 'ukuran', 'kd_ukuran', 'nm_ukuran', $_POST['kd_ukuran'] ?? null) ?>
              </select>
            </div>
            <div class="col-md-12 mb-3">
              <label class="form-label">Distributor (opsional)</label>
              <select name="kd_distributor" class="form-select">
                <?= opsi($koneksi, 'distributor', 'kd_distributor', 'nm_distributor', $_POST['kd_distributor'] ?? null) ?>
              </select>
            </div>

            <div class="col-md-4 mb-3">
              <label class="form-label">Harga Jual (Rp)</label>
              <input type="number" name="harga_jual" class="form-control" min="0"
                     value="<?= e($_POST['harga_jual'] ?? '') ?>" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Harga Beli (Rp)</label>
              <input type="number" name="harga_beli" class="form-control" min="0"
                     value="<?= e($_POST['harga_beli'] ?? '') ?>" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Stok</label>
              <input type="number" name="stok" class="form-control" min="0"
                     value="<?= e($_POST['stok'] ?? '0') ?>" required>
            </div>

            <div class="col-md-12 mb-3">
              <label class="form-label">Deskripsi</label>
              <textarea name="deskripsi" class="form-control" rows="3"><?= e($_POST['deskripsi'] ?? '') ?></textarea>
            </div>

            <div class="col-md-12 mb-3">
              <label class="form-label">foto (boleh lebih dari satu, maks 8 mb, minimal 600px, otomatis dirapikan jadi persegi)</label>
              <input type="file" name="foto[]" class="form-control" accept=".jpg,.jpeg,.png,.webp" multiple>
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