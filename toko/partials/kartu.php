<?php $foto = foto_produk($row['foto']); $kolom = $kolom ?? 'col-6 col-md-4 col-lg-3'; ?>
<div class="<?= $kolom ?>">
  <a href="<?= $tokoUrl ?>/detail.php?id=<?= $row['kd_produk'] ?>" class="card-produk d-block text-decoration-none text-dark">
    <div class="foto-box">
      <?php if ($foto): ?>
        <img src="<?= e($foto) ?>" alt="<?= e($row['nama_produk']) ?>" loading="lazy">
      <?php else: ?>
        <i class="bi bi-image"></i>
      <?php endif; ?>
      <?php if ($row['stok'] <= 0): ?>
        <span class="badge text-bg-dark habis">Habis</span>
      <?php endif; ?>
    </div>
    <div class="pt-2">
      <small class="text-muted text-uppercase"><?= e($row['nm_brand']) ?></small>
      <div class="nama"><?= e($row['nama_produk']) ?></div>
      <div class="harga"><?= rupiah($row['harga_jual']) ?></div>
    </div>
  </a>
</div>