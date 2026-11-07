<?php
$judul = "Detail Pesanan";
$menu  = "pesanan";
require_once '../config/admin.php';

$id = (int) ($_GET['id'] ?? 0);

// Ambil data pesanan dan pelanggannya
$stmt = mysqli_prepare($koneksi,
    "SELECT p.*, u.username, u.email, u.no_telpon, u.alamat
     FROM pembayaran p
     JOIN `user` u ON p.kd_user = u.kd_user
     WHERE p.kd_pembayaran = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$pesanan = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$pesanan) {
    $_SESSION['pesan'] = "Pesanan tidak ditemukan.";
    $_SESSION['tipe']  = "danger";
    header("Location: index.php");
    exit;
}

$daftarStatus = ['menunggu', 'diproses', 'dikirim', 'selesai', 'dibatalkan'];
$final        = in_array($pesanan['status'], ['selesai', 'dibatalkan']);

// Proses ubah status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$final) {
    $baru = $_POST['status'] ?? '';

    if (!in_array($baru, $daftarStatus)) {
        $error = "Status tidak valid.";
    } elseif ($baru !== $pesanan['status']) {
        try {
            mysqli_begin_transaction($koneksi);

            $stmt = mysqli_prepare($koneksi, "UPDATE pembayaran SET status = ? WHERE kd_pembayaran = ?");
            mysqli_stmt_bind_param($stmt, "si", $baru, $id);
            mysqli_stmt_execute($stmt);

            // Kalau dibatalkan, kembalikan stok produk
            if ($baru === 'dibatalkan') {
                $stmt = mysqli_prepare($koneksi,
                    "SELECT kd_produk, qty FROM detail_pembayaran WHERE kd_pembayaran = ?");
                mysqli_stmt_bind_param($stmt, "i", $id);
                mysqli_stmt_execute($stmt);
                $isi = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

                $stmtStok = mysqli_prepare($koneksi, "UPDATE produk SET stok = stok + ? WHERE kd_produk = ?");
                foreach ($isi as $barang) {
                    $qty = (int) $barang['qty'];
                    $kd  = (int) $barang['kd_produk'];
                    mysqli_stmt_bind_param($stmtStok, "ii", $qty, $kd);
                    mysqli_stmt_execute($stmtStok);
                }
            }

            mysqli_commit($koneksi);

            $_SESSION['pesan'] = "Status pesanan diubah menjadi " . $baru . ".";
            header("Location: detail.php?id=$id");
            exit;
        } catch (Exception $ex) {
            mysqli_rollback($koneksi);
            $error = "Gagal mengubah status. Coba lagi.";
        }
    }
}

// Ambil isi pesanan
$stmt = mysqli_prepare($koneksi,
    "SELECT d.*, pr.nama_produk
     FROM detail_pembayaran d
     JOIN produk pr ON d.kd_produk = pr.kd_produk
     WHERE d.kd_pembayaran = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$items = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

$total = 0;
foreach ($items as $it) {
    $total += $it['harga_jual'] * $it['qty'];
}

include '../partials/header.php';
?>

<div class="app-content-header">
  <div class="container-fluid">
    <h3 class="mb-0">Detail Pesanan <?= e($pesanan['invoice']) ?></h3>
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

    <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>

    <div class="row">

      <div class="col-lg-8">
        <div class="card">
          <div class="card-header"><strong>Isi Pesanan</strong></div>
          <div class="card-body p-0 table-responsive">
            <table class="table mb-0">
              <thead>
                <tr>
                  <th>Produk</th>
                  <th>Harga</th>
                  <th>Qty</th>
                  <th class="text-end">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($items as $it): ?>
                  <tr>
                    <td><?= e($it['nama_produk']) ?></td>
                    <td><?= rupiah($it['harga_jual']) ?></td>
                    <td><?= $it['qty'] ?></td>
                    <td class="text-end"><?= rupiah($it['harga_jual'] * $it['qty']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot>
                <tr>
                  <th colspan="3" class="text-end">Total</th>
                  <th class="text-end"><?= rupiah($total) ?></th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

      <div class="col-lg-4">

        <div class="card mb-3">
          <div class="card-header"><strong>Pelanggan</strong></div>
          <div class="card-body">
            <p class="mb-1"><b><?= e($pesanan['username']) ?></b></p>
            <p class="mb-1"><?= e($pesanan['email']) ?></p>
            <p class="mb-1"><?= e($pesanan['no_telpon']) ?></p>
            <p class="mb-0"><?= nl2br(e($pesanan['alamat'])) ?></p>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><strong>Status Pesanan</strong></div>
          <div class="card-body">
            <p>
              Saat ini:
              <span class="badge text-bg-<?= badge_status($pesanan['status']) ?>">
                <?= e(ucfirst($pesanan['status'])) ?>
              </span>
              <br>
              <small class="text-muted"><?= date('d/m/Y H:i', strtotime($pesanan['tgl_bayar'])) ?></small>
            </p>

            <?php if ($final): ?>
              <p class="text-muted mb-0">
                Pesanan sudah <?= e($pesanan['status']) ?>, status tidak bisa diubah lagi.
              </p>
            <?php else: ?>
              <form method="post">
                <select name="status" class="form-select mb-2">
                  <?php foreach ($daftarStatus as $s): ?>
                    <option value="<?= $s ?>" <?= $pesanan['status'] === $s ? 'selected' : '' ?>>
                      <?= ucfirst($s) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary w-100">Simpan Status</button>
                <small class="text-muted">Kalau dibatalkan, stok produk dikembalikan otomatis.</small>
              </form>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>

    <a href="index.php" class="btn btn-secondary mt-3">Kembali</a>

  </div>
</div>

<?php include '../partials/footer.php'; ?>