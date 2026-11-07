  </main>

  <footer class="app-footer">
    <strong>Footstep</strong> - Tugas Kuliah
  </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= $base ?>/js/adminlte.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
<script>
// Semua tabel yang punya class "datatable" otomatis dijadikan DataTables
document.querySelectorAll('table.datatable').forEach(function (tabel) {
  var kepala    = Array.from(tabel.querySelectorAll('thead th'));
  var tanpaUrut = [];

  // Kolom No, Foto, Aksi, dan kolom tanpa judul tidak perlu bisa diurutkan
  kepala.forEach(function (th, i) {
    var teks = th.textContent.trim();
    if (teks === '' || teks === 'No' || teks === 'Aksi' || teks === 'Foto') {
      tanpaUrut.push(i);
    }
  });
  var adaNo = kepala.length > 0 && kepala[0].textContent.trim() === 'No';

  var dt = new DataTable(tabel, {
    order: [],          // urutan awal tetap sesuai query PHP
    pageLength: 10,
    columnDefs: [{ orderable: false, targets: tanpaUrut }],
    language: {
      search: 'Cari:',
      lengthMenu: 'Tampilkan _MENU_ data',
      info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
      infoEmpty: 'Tidak ada data',
      infoFiltered: '(disaring dari _MAX_ data)',
      zeroRecords: 'Data tidak ditemukan',
      emptyTable: 'Belum ada data',
      paginate: { first: 'Awal', last: 'Akhir', next: 'Berikutnya', previous: 'Sebelumnya' }
    }
  });

  // Nomor urut dihitung ulang setiap kali tabel dicari, diurutkan, atau pindah halaman
  if (adaNo) {
    var renomor = function () {
      dt.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (sel, i) {
        sel.textContent = i + 1;
      });
    };
    dt.on('draw', renomor);
    renomor();
  }
});
</script>
</body>
</html>