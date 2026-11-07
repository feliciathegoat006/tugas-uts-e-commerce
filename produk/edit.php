<?php
$judul = "Edit Produk";
$menu  = "produk";
require_once '../config/koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

// Ambil data produk
$stmt = mysqli_prepare($koneksi, "SELECT * FROM produk WHERE kd_produk = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$produk = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$produk) {
    $_SESSION['pesan'] = "Data produk tidak ditemukan.";
    $_SESSION['tipe']  = "danger";
    header("Location: index.php");
    exit;
}

$stokLama = (int) $produk['stok'];
$errors   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama           = trim($_POST['nama_produk']);
    $kd_brand       = (int) $_POST['kd_brand'];
    $kd_kategori    = (int) $_POST['kd_kategori'];
    $kd_warna       = (int) $_POST['kd_warna'];
    $kd_ukuran      = (int) $_POST['kd_ukuran'];
    $kd_distributor = ($_POST['kd_distributor'] ?? '') !== '' ? (int) $_POST['kd_distributor'] : null;
    $harga_jual     = (float) $_POST['harga_jual'];
    $harga_beli     = (float) $_POST['harga_beli'];
    $stok           = (int) $_POST['stok'];
    $deskripsi      = trim($_POST['deskripsi']);
    $hapusFoto      = array_map('intval', $_POST['hapus_foto'] ?? []);

    // Supaya form tetap terisi kalau ada error
    $produk['nama_produk']    = $nama;
    $produk['kd_brand']       = $kd_brand;
    $produk['kd_kategori']    = $kd_kategori;
    $produk['kd_warna']       = $kd_warna;
    $produk['kd_ukuran']      = $kd_ukuran;
    $produk['kd_distributor'] = $kd_distributor;
    $produk['harga_jual']     = $harga_jual;
    $produk['harga_beli']     = $harga_beli;
    $produk['stok']           = $stok;
    $produk['deskripsi']      = $deskripsi;

    // 1. Validasi isian
    if ($nama === '')                          $errors[] = "Nama produk wajib diisi.";
    if (!$kd_brand || !$kd_kategori || !$kd_warna || !$kd_ukuran)
                                               $errors[] = "Brand, kategori, warna, dan ukuran wajib dipilih.";
    if ($harga_jual <= 0)                      $errors[] = "Harga jual harus lebih dari 0.";
    if ($harga_beli < 0 || $stok < 0)          $errors[] = "Harga beli dan stok tidak boleh minus.";

    // 2. Validasi foto baru
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

    // 3. Simpan
    if (!$errors) {
        $fileBaru  = [];
        $fileHapus = [];
        try {
            mysqli_begin_transaction($koneksi);

            // Update data produk
            $stmt = mysqli_prepare($koneksi,
                "UPDATE produk
                 SET kd_ukuran = ?, kd_brand = ?, kd_kategori = ?, kd_warna = ?, kd_distributor = ?,
                     nama_produk = ?, harga_jual = ?, harga_beli = ?, deskripsi = ?, stok = ?
                 WHERE kd_produk = ?");
            mysqli_stmt_bind_param($stmt, "iiiiisddsii",
                $kd_ukuran, $kd_brand, $kd_kategori, $kd_warna, $kd_distributor,
                $nama, $harga_jual, $harga_beli, $deskripsi, $stok, $id);
            mysqli_stmt_execute($stmt);

            // Hapus foto yang dicentang
            $stmtCari = mysqli_prepare($koneksi, "SELECT path FROM foto WHERE kd_foto = ? AND kd_produk = ?");
            $stmtDel  = mysqli_prepare($koneksi, "DELETE FROM foto WHERE kd_foto = ? AND kd_produk = ?");
            foreach ($hapusFoto as $kd_foto) {
                mysqli_stmt_bind_param($stmtCari, "ii", $kd_foto, $id);
                mysqli_stmt_execute($stmtCari);
                $baris = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtCari));
                if ($baris) {
                    mysqli_stmt_bind_param($stmtDel, "ii", $kd_foto, $id);
                    mysqli_stmt_execute($stmtDel);
                    $fileHapus[] = __DIR__ . '/../' . $baris['path'];
                }
            }

            // Tambah foto baru
            $stmtFoto = mysqli_prepare($koneksi,
                "INSERT INTO foto (kd_produk, nm_foto, path) VALUES (?, ?, ?)");
            foreach ($fotoValid as $f) {
                $namaBaru = uniqid('produk_') . '.' . $f['ext'];
                $path     = 'uploads/produk/' . $namaBaru;
                move_uploaded_file($f['tmp'], __DIR__ . '/../' . $path);
                $fileBaru[] = __DIR__ . '/../' . $path;

                mysqli_stmt_bind_param($stmtFoto, "iss", $id, $f['asli'], $path);
                mysqli_stmt_execute($stmtFoto);
            }

            // Kalau stok naik, catat selisihnya sebagai stok masuk
            if ($stok > $stokLama) {
                $selisih = $stok - $stokLama;
                $stmtStok = mysqli_prepare($koneksi,
                    "INSERT INTO stok_produk (tgl_masuk, kd_barang, harga_beli, harga_jual, qty)
                     VALUES (CURDATE(), ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmtStok, "iddi", $id, $harga_beli, $harga_jual, $selisih);
                mysqli_stmt_execute($stmtStok);
            }

            mysqli_commit($koneksi);

            // File foto lama baru dihapus setelah database sukses
            foreach ($fileHapus as $file) {
                if (file_exists($file)) unlink($file);
            }

            $_SESSION['pesan'] = "Produk berhasil diubah.";
            header("Location: index.php");
            exit;
        } catch (Exception $ex) {
            mysqli_rollback($koneksi);
            foreach ($fileBaru as $file) {
                if (file_exists($file)) unlink($file);
            }
            $errors[] = "Gagal menyimpan perubahan. Coba lagi.";
        }
    }
}

// Ambil daftar foto produk ini
$stmt = mysqli_prepare($koneksi, "SELECT * FROM foto WHERE kd_produk = ? ORDER BY kd_foto");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$fotoList = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

include '../partials/header.php';
?>

<div class="app-content-header">
  <div class="container-fluid">
    <h3 class="mb-0">Edit Produk</h3>
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
                     value="<?= e($produk['nama_produk']) ?>" required>
            </div>

            <div class="col-md-3 mb-3">
              <label class="form-label">Brand</label>
              <select name="kd_brand" class="form-select" required>
                <?= opsi($koneksi, 'brand', 'kd_brand', 'nm_brand', $produk['kd_brand']) ?>
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Kategori</label>
              <select name="kd_kategori" class="form-select" required>
                <?= opsi($koneksi, 'kategori', 'kd_kategori', 'nm_kategori', $produk['kd_kategori']) ?>
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Warna</label>
              <select name="kd_warna" class="form-select" required>
                <?= opsi($koneksi, 'warna', 'kd_warna', 'nm_warna', $produk['kd_warna']) ?>
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Ukuran</label>
              <select name="kd_ukuran" class="form-select" required>
                <?= opsi($koneksi, 'ukuran', 'kd_ukuran', 'nm_ukuran', $produk['kd_ukuran']) ?>
              </select>
            </div>

            <div class="col-md-12 mb-3">
              <label class="form-label">Distributor (opsional)</label>
              <select name="kd_distributor" class="form-select">
                <?= opsi($koneksi, 'distributor', 'kd_distributor', 'nm_distributor', $produk['kd_distributor']) ?>
              </select>
            </div>

            <div class="col-md-4 mb-3">
              <label class="form-label">Harga Jual (Rp)</label>
              <input type="number" name="harga_jual" class="form-control" min="0"
                     value="<?= e($produk['harga_jual']) ?>" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Harga Beli (Rp)</label>
              <input type="number" name="harga_beli" class="form-control" min="0"
                     value="<?= e($produk['harga_beli']) ?>" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Stok</label>
              <input type="number" name="stok" class="form-control" min="0"
                     value="<?= e($produk['stok']) ?>" required>
            </div>

            <div class="col-md-12 mb-3">
              <label class="form-label">Deskripsi</label>
              <textarea name="deskripsi" class="form-control" rows="3"><?= e($produk['deskripsi']) ?></textarea>
            </div>

            <div class="col-md-12 mb-3">
              <label class="form-label">Foto saat ini (centang untuk menghapus)</label>
              <div class="d-flex flex-wrap gap-3">
                <?php if (!$fotoList): ?>
                  <span class="text-muted">Belum ada foto.</span>
                <?php endif; ?>
                <?php foreach ($fotoList as $f): ?>
                  <?php $ada = file_exists(__DIR__ . '/../' . $f['path']); ?>
                  <div class="text-center">
                    <?php if ($ada): ?>
                      <img src="<?= $base ?>/<?= e($f['path']) ?>" alt=""
                           style="width:100px;height:100px;object-fit:cover;border-radius:6px">
                    <?php else: ?>
                      <div class="bg-secondary-subtle small text-muted d-flex align-items-center justify-content-center"
                           style="width:100px;height:100px;border-radius:6px">File hilang</div>
                    <?php endif; ?>
                    <div class="form-check mt-1">
                      <input class="form-check-input" type="checkbox" name="hapus_foto[]"
                             value="<?= $f['kd_foto'] ?>" id="foto<?= $f['kd_foto'] ?>">
                      <label class="form-check-label small" for="foto<?= $f['kd_foto'] ?>">Hapus</label>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="col-md-12 mb-3">
              <label class="form-label">Tambah foto baru (boleh lebih dari satu, maks 8 MB, minimal 600 px)</label>
              <input type="file" name="foto[]" class="form-control" accept=".jpg,.jpeg,.png,.webp" multiple>
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