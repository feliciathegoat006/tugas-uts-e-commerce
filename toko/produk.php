<?php
$judul = "Semua Produk";
require_once __DIR__ . '/../config/koneksi.php';

// ---- Ambil filter dari URL ----
$q        = trim($_GET['q'] ?? '');
$kdBrand  = (int) ($_GET['brand'] ?? 0);
$kdKat    = (int) ($_GET['kategori'] ?? 0);
$kdUkuran = (int) ($_GET['ukuran'] ?? 0);
$hargaMin = ($_GET['harga_min'] ?? '') !== '' ? (float) $_GET['harga_min'] : null;
$hargaMax = ($_GET['harga_max'] ?? '') !== '' ? (float) $_GET['harga_max'] : null;
$urut     = $_GET['urut'] ?? 'terbaru';
$halaman  = max(1, (int) ($_GET['halaman'] ?? 1));
$perHalaman = 12;

// ---- Susun kondisi WHERE ----
$where = [];
$tipe  = '';
$param = [];

if ($q !== '') {
    $where[] = "(p.nama_produk LIKE ? OR b.nm_brand LIKE ?)";
    $tipe   .= 'ss';
    $param[] = "%$q%";
    $param[] = "%$q%";
}
if ($kdBrand)  { $where[] = "p.kd_brand = ?";    $tipe .= 'i'; $param[] = $kdBrand; }
if ($kdKat)    { $where[] = "p.kd_kategori = ?"; $tipe .= 'i'; $param[] = $kdKat; }
if ($kdUkuran) { $where[] = "p.kd_ukuran = ?";   $tipe .= 'i'; $param[] = $kdUkuran; }
if ($hargaMin !== null) { $where[] = "p.harga_jual >= ?"; $tipe .= 'd'; $param[] = $hargaMin; }
if ($hargaMax !== null) { $where[] = "p.harga_jual <= ?"; $tipe .= 'd'; $param[] = $hargaMax; }

$sqlWhere = $where ? "WHERE " . implode(" AND ", $where) : "";

$urutan = [
    'terbaru'  => 'p.kd_produk DESC',
    'termurah' => 'p.harga_jual ASC',
    'termahal' => 'p.harga_jual DESC',
];
$orderBy = $urutan[$urut] ?? $urutan['terbaru'];

// Jalankan query dengan parameter dinamis
function jalankan($koneksi, $sql, $tipe, $param) {
    $stmt = mysqli_prepare($koneksi, $sql);
    if ($param) {
        mysqli_stmt_bind_param($stmt, $tipe, ...$param);
    }
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

// ---- Hitung total dan halaman ----
$hasilTotal = jalankan($koneksi,
    "SELECT COUNT(*) AS total
     FROM produk p JOIN brand b ON p.kd_brand = b.kd_brand $sqlWhere",
    $tipe, $param);
$total       = (int) mysqli_fetch_assoc($hasilTotal)['total'];
$totalHalaman = max(1, (int) ceil($total / $perHalaman));
$halaman     = min($halaman, $totalHalaman);
$offset      = ($halaman - 1) * $perHalaman;

// ---- Ambil produk ----
$data = jalankan($koneksi,
    "SELECT p.kd_produk, p.nama_produk, p.harga_jual, p.stok, b.nm_brand,
            (SELECT path FROM foto WHERE kd_produk = p.kd_produk ORDER BY kd_foto LIMIT 1) AS foto
     FROM produk p
     JOIN brand b ON p.kd_brand = b.kd_brand
     $sqlWhere
     ORDER BY $orderBy
     LIMIT $perHalaman OFFSET $offset",
    $tipe, $param);

// Query dasar untuk link pagination (semua filter, tanpa nomor halaman)
$queryDasar = $_GET;
unset($queryDasar['halaman']);

include __DIR__ . '/partials/header.php';
?>

<div class="container mt-4">
  <div class="row g-4">

    <!-- Filter -->
    <div class="col-lg-3">
      <form method="get" class="card card-body">
        <h2 class="h6 fw-bold mb-3">Filter</h2>

        <div class="mb-3">
          <label class="form-label small">Pencarian</label>
          <input type="text" name="q" class="form-control form-control-sm" value="<?= e($q) ?>">
        </div>
        <div class="mb-3">
          <label class="form-label small">Brand</label>
          <select name="brand" class="form-select form-select-sm">
            <?= opsi($koneksi, 'brand', 'kd_brand', 'nm_brand', $kdBrand) ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label small">Kategori</label>
          <select name="kategori" class="form-select form-select-sm">
            <?= opsi($koneksi, 'kategori', 'kd_kategori', 'nm_kategori', $kdKat) ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label small">Ukuran</label>
          <select name="ukuran" class="form-select form-select-sm">
            <?= opsi($koneksi, 'ukuran', 'kd_ukuran', 'nm_ukuran', $kdUkuran) ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label small">Harga (Rp)</label>
          <div class="d-flex gap-2">
            <input type="number" name="harga_min" class="form-control form-control-sm" placeholder="Min"
                   min="0" value="<?= $hargaMin !== null ? e($hargaMin) : '' ?>">
            <input type="number" name="harga_max" class="form-control form-control-sm" placeholder="Maks"
                   min="0" value="<?= $hargaMax !== null ? e($hargaMax) : '' ?>">
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label small">Urutkan</label>
          <select name="urut" class="form-select form-select-sm">
            <option value="terbaru"  <?= $urut === 'terbaru'  ? 'selected' : '' ?>>Terbaru</option>
            <option value="termurah" <?= $urut === 'termurah' ? 'selected' : '' ?>>Harga termurah</option>
            <option value="termahal" <?= $urut === 'termahal' ? 'selected' : '' ?>>Harga termahal</option>
          </select>
        </div>

        <button type="submit" class="btn btn-dark btn-sm w-100 mb-2">Terapkan</button>
        <a href="produk.php" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
      </form>
    </div>

    <!-- Hasil -->
    <div class="col-lg-9">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 judul-seksi mb-0">Semua Produk</h1>
        <span class="text-muted small"><?= $total ?> produk ditemukan</span>
      </div>

      <?php if ($total === 0): ?>
        <div class="alert alert-light border text-center py-5">
          Produk tidak ditemukan. Coba ubah atau reset filter.
        </div>
      <?php endif; ?>

      <div class="row g-3">
        <?php $kolom = 'col-6 col-md-4'; ?>
        <?php while ($row = mysqli_fetch_assoc($data)): ?>
          <?php include __DIR__ . '/partials/kartu.php'; ?>
        <?php endwhile; ?>
      </div>

      <?php if ($totalHalaman > 1): ?>
        <nav class="mt-4">
          <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalHalaman; $i++): ?>
              <li class="page-item <?= $i === $halaman ? 'active' : '' ?>">
                <a class="page-link" href="produk.php?<?= e(http_build_query($queryDasar + ['halaman' => $i])) ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>
          </ul>
        </nav>
      <?php endif; ?>
    </div>

  </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>