<?php
$judul = "Checkout";
require_once __DIR__ . '/../config/pelanggan.php';

$tokoUrl = '/toko-sepatu/toko';
$uid     = (int) $_SESSION['user']['kd_user'];
$errors  = [];

// ---- Proses pembuatan pesanan ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alamat    = trim($_POST['alamat'] ?? '');
    $no_telpon = trim($_POST['no_telpon'] ?? '');

    if ($alamat === '')    $errors[] = "Alamat pengiriman wajib diisi.";
    if ($no_telpon === '') $errors[] = "No. telpon wajib diisi.";

    if (!$errors) {
        try {
            mysqli_begin_transaction($koneksi);

            // Ambil isi keranjang sambil mengunci baris produknya
            $stmt = mysqli_prepare($koneksi,
                "SELECT kb.kd_produk, kb.qty, p.nama_produk, p.harga_jual, p.harga_beli, p.stok
                 FROM keranjang_belanja kb
                 JOIN produk p ON kb.kd_produk = p.kd_produk
                 WHERE kb.kd_user = ?
                 FOR UPDATE");
            mysqli_stmt_bind_param($stmt, "i", $uid);
            mysqli_stmt_execute($stmt);
            $isi = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

            if (!$isi) {
                throw new PesananException("Keranjang kamu kosong.");
            }
            foreach ($isi as $r) {
                if ($r['qty'] > $r['stok']) {
                    throw new PesananException(
                        "Stok " . $r['nama_produk'] . " tidak cukup (tersisa " . $r['stok'] . ").");
                }
            }

            // Simpan alamat dan telepon terbaru
            $stmt = mysqli_prepare($koneksi, "UPDATE `user` SET alamat = ?, no_telpon = ? WHERE kd_user = ?");
            mysqli_stmt_bind_param($stmt, "ssi", $alamat, $no_telpon, $uid);
            mysqli_stmt_execute($stmt);

            // Buat pesanan
            $invoice = 'INV-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
            $stmt = mysqli_prepare($koneksi,
                "INSERT INTO pembayaran (kd_user, invoice, status) VALUES (?, ?, 'menunggu')");
            mysqli_stmt_bind_param($stmt, "is", $uid, $invoice);
            mysqli_stmt_execute($stmt);
            $kdBayar = mysqli_insert_id($koneksi);

            // Isi pesanan + kurangi stok
            $stmtDetail = mysqli_prepare($koneksi,
                "INSERT INTO detail_pembayaran (kd_pembayaran, kd_produk, harga_jual, harga_beli, qty)
                 VALUES (?, ?, ?, ?, ?)");
            $stmtStok = mysqli_prepare($koneksi, "UPDATE produk SET stok = stok - ? WHERE kd_produk = ?");

            foreach ($isi as $r) {
                $kdp = (int) $r['kd_produk'];
                $hj  = (float) $r['harga_jual'];
                $hb  = (float) $r['harga_beli'];
                $q   = (int) $r['qty'];

                mysqli_stmt_bind_param($stmtDetail, "iiddi", $kdBayar, $kdp, $hj, $hb, $q);
                mysqli_stmt_execute($stmtDetail);

                mysqli_stmt_bind_param($stmtStok, "ii", $q, $kdp);
                mysqli_stmt_execute($stmtStok);
            }

            // Kosongkan keranjang
            $stmt = mysqli_prepare($koneksi, "DELETE FROM keranjang_belanja WHERE kd_user = ?");
            mysqli_stmt_bind_param($stmt, "i", $uid);
            mysqli_stmt_execute($stmt);

            mysqli_commit($koneksi);

            header("Location: sukses.php?inv=" . urlencode($invoice));
            exit;
        } catch (PesananException $ex) {
            mysqli_rollback($koneksi);
            $errors[] = $ex->getMessage();
        } catch (Exception $ex) {
            mysqli_rollback($koneksi);
            $errors[] = "Gagal membuat pesanan. Coba lagi.";
        }
    }
}

// ---- Data untuk tampilan ----
$stmt = mysqli_prepare($koneksi, "SELECT username, no_telpon, alamat FROM `user` WHERE kd_user = ?");
mysqli_stmt_bind_param($stmt, "i", $uid);
mysqli_stmt_execute($stmt);
$profil = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$stmt = mysqli_prepare($koneksi,
    "SELECT kb.qty, p.nama_produk, p.harga_jual, p.stok, u.nm_ukuran, w.nm_warna
     FROM keranjang_belanja kb
     JOIN produk p ON kb.kd_produk = p.kd_produk
     JOIN ukuran u ON p.kd_ukuran  = u.kd_ukuran
     JOIN warna w  ON p.kd_warna   = w.kd_warna
     WHERE kb.kd_user = ?
     ORDER BY kb.kd_keranjang_belanja DESC");
mysqli_stmt_bind_param($stmt, "i", $uid);
mysqli_stmt_execute($stmt);
$items = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

if (!$items && !$errors) {
    $_SESSION['pesan'] = "Keranjang kamu kosong.";
    $_SESSION['tipe']  = "danger";
    header("Location: keranjang.php");
    exit;
}

$total      = 0;
$bermasalah = false;
foreach ($items as $it) {
    $total += $it['harga_jual'] * $it['qty'];
    if ($it['qty'] > $it['stok']) $bermasalah = true;
}

include __DIR__ . '/partials/header.php';
?>

<div class="container mt-4">
  <h1 class="h4 judul-seksi mb-3">Checkout</h1>

  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post">
    <div class="row g-4">

      <div class="col-lg-7">
        <div class="card card-body">
          <h2 class="h6 fw-bold mb-3">Data Pengiriman</h2>
          <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" class="form-control" value="<?= e($profil['username']) ?>" disabled>
          </div>
          <div class="mb-3">
            <label class="form-label">No. Telpon</label>
            <input type="text" name="no_telpon" class="form-control"
                   value="<?= e($_POST['no_telpon'] ?? $profil['no_telpon']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Alamat Lengkap</label>
            <textarea name="alamat" rows="4" class="form-control" required><?= e($_POST['alamat'] ?? $profil['alamat']) ?></textarea>
          </div>
          <div class="alert alert-light border mb-0 small">
            Pembayaran lewat transfer bank. Petunjuk pembayaran muncul setelah pesanan dibuat.
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="card card-body">
          <h2 class="h6 fw-bold mb-3">Ringkasan Pesanan</h2>

          <?php foreach ($items as $it): ?>
            <div class="d-flex justify-content-between mb-2">
              <div>
                <div class="fw-semibold"><?= e($it['nama_produk']) ?></div>
                <div class="small text-muted">
                  Ukuran <?= e($it['nm_ukuran']) ?> &bull; <?= e($it['nm_warna']) ?> &bull; <?= $it['qty'] ?> x <?= rupiah($it['harga_jual']) ?>
                </div>
                <?php if ($it['qty'] > $it['stok']): ?>
                  <div class="small text-danger">Stok tersisa <?= $it['stok'] ?></div>
                <?php endif; ?>
              </div>
              <div class="text-nowrap"><?= rupiah($it['harga_jual'] * $it['qty']) ?></div>
            </div>
          <?php endforeach; ?>

          <hr>
          <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
            <span>Total</span>
            <span><?= rupiah($total) ?></span>
          </div>

          <?php if ($bermasalah): ?>
            <a href="keranjang.php" class="btn btn-secondary w-100">Sesuaikan keranjang dulu</a>
          <?php else: ?>
            <button type="submit" class="btn btn-dark w-100">Buat Pesanan</button>
          <?php endif; ?>
          <a href="keranjang.php" class="btn btn-link w-100 mt-2">Kembali ke keranjang</a>
        </div>
      </div>

    </div>
  </form>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>