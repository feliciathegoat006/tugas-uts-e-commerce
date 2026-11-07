<?php
$judul = "Detail Pesanan";
require_once __DIR__ . '/../config/pelanggan.php';

$tokoUrl = '/toko-sepatu/toko';
$uid     = (int) $_SESSION['user']['kd_user'];
$id      = (int) ($_GET['id'] ?? 0);

// ---- Proses pembatalan ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'batal') {
    try {
        mysqli_begin_transaction($koneksi);

        // Kunci pesanan, pastikan milik user ini dan masih "menunggu"
        $stmt = mysqli_prepare($koneksi,
            "SELECT status FROM pembayaran WHERE kd_pembayaran = ? AND kd_user = ? FOR UPDATE");
        mysqli_stmt_bind_param($stmt, "ii", $id, $uid);
        mysqli_stmt_execute($stmt);
        $cur = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if (!$cur || $cur['status'] !== 'menunggu') {
            throw new PesananException("Pesanan ini tidak bisa dibatalkan lagi.");
        }

        $stmt = mysqli_prepare($koneksi, "UPDATE pembayaran SET status = 'dibatalkan' WHERE kd_pembayaran = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        // Kembalikan stok
        $stmt = mysqli_prepare($koneksi,
            "SELECT kd_produk, qty FROM detail_pembayaran WHERE kd_pembayaran = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $isi = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

        $stmtStok = mysqli_prepare($koneksi, "UPDATE produk SET stok = stok + ? WHERE kd_produk = ?");
        foreach ($isi as $r) {
            $q   = (int) $r['qty'];
            $kdp = (int) $r['kd_produk'];
            mysqli_stmt_bind_param($stmtStok, "ii", $q, $kdp);
            mysqli_stmt_execute($stmtStok);
        }

        mysqli_commit($koneksi);
        $_SESSION['pesan'] = "Pesanan berhasil dibatalkan.";
        $_SESSION['tipe']  = "success";
    } catch (PesananException $ex) {
        mysqli_rollback($koneksi);
        $_SESSION['pesan'] = $ex->getMessage();
        $_SESSION['tipe']  = "danger";
    } catch (Exception $ex) {
        mysqli_rollback($koneksi);
        $_SESSION['pesan'] = "Gagal membatalkan pesanan. Coba lagi.";
        $_SESSION['tipe']  = "danger";
    }
    header("Location: riwayat_detail.php?id=$id");
    exit;
}

// ---- Data pesanan ----
$stmt = mysqli_prepare($koneksi, "SELECT * FROM pembayaran WHERE kd_pembayaran = ? AND kd_user = ?");
mysqli_stmt_bind_param($stmt, "ii", $id, $uid);
mysqli_stmt_execute($stmt);
$p = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$p) {
    $_SESSION['pesan'] = "Pesanan tidak ditemukan.";
    $_SESSION['tipe']  = "danger";
    header("Location: riwayat.php");
    exit;
}

$stmt = mysqli_prepare($koneksi,
    "SELECT d.harga_jual, d.qty, pr.kd_produk, pr.nama_produk, u.nm_ukuran, w.nm_warna
     FROM detail_pembayaran d
     JOIN produk pr ON d.kd_produk  = pr.kd_produk
     JOIN ukuran u  ON pr.kd_ukuran = u.kd_ukuran
     JOIN warna w   ON pr.kd_warna  = w.kd_warna
     WHERE d.kd_pembayaran = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$items = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

$total = 0;
foreach ($items as $it) $total += $it['harga_jual'] * $it['qty'];

$keterangan = [
    'menunggu'   => 'Menunggu pembayaran. Transfer sesuai total, lalu tunggu konfirmasi admin.',
    'diproses'   => 'Pembayaran sudah dikonfirmasi. Pesanan sedang disiapkan.',
    'dikirim'    => 'Pesanan sedang dalam pengiriman.',
    'selesai'    => 'Pesanan selesai. Terima kasih sudah berbelanja!',
    'dibatalkan' => 'Pesanan ini dibatalkan.',
];

include __DIR__ . '/partials/header.php';
?>

<div class="container mt-4" style="max-width:820px">
  <a href="riwayat.php" class="small text-decoration-none">&larr; Kembali ke pesanan saya</a>
  <h1 class="h4 judul-seksi my-3">Pesanan <?= e($p['invoice']) ?></h1>

  <?php if (isset($_SESSION['pesan'])): ?>
    <div class="alert alert-<?= $_SESSION['tipe'] ?? 'success' ?> alert-dismissible fade show">
      <?= e($_SESSION['pesan']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['pesan'], $_SESSION['tipe']); ?>
  <?php endif; ?>

  <div class="card card-body mb-3">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <span class="badge text-bg-<?= badge_status($p['status']) ?> fs-6"><?= e(ucfirst($p['status'])) ?></span>
        <div class="small text-muted mt-2"><?= e($keterangan[$p['status']] ?? '') ?></div>
      </div>
      <div class="small text-muted text-end"><?= date('d/m/Y H:i', strtotime($p['tgl_bayar'])) ?></div>
    </div>
  </div>

  <div class="card mb-3">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th>Produk</th>
            <th>Harga</th>
            <th>Jumlah</th>
            <th class="text-end">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $it): ?>
            <tr>
              <td>
                <a href="detail.php?id=<?= $it['kd_produk'] ?>" class="text-decoration-none text-dark fw-semibold">
                  <?= e($it['nama_produk']) ?></a>
                <div class="small text-muted">Ukuran <?= e($it['nm_ukuran']) ?> &bull; <?= e($it['nm_warna']) ?></div>
              </td>
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

  <?php if ($p['status'] === 'menunggu'): ?>
    <form method="post" onsubmit="return confirm('Yakin batalkan pesanan ini?')">
      <input type="hidden" name="aksi" value="batal">
      <button type="submit" class="btn btn-outline-danger">Batalkan pesanan</button>
    </form>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>