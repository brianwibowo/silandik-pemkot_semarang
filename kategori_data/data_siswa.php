<?php
session_start();
include '../config.php';
include '../partials/head.php';
include '../koneksi.php';
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php include '../sidebar.php'; ?>

<div id="layoutSidenav">
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <div class="header-section">
                    <div class="header-container">
                        <div class="header-title">
                            <h1 class="page-title">Data Siswa</h1>
                            <p class="page-subtitle">Data Siswa Sekolah Inklusi</p>
                        </div>
                    </div>
                </div>

                <div class="search-section">
                    <div class="row align-items-center g-3 flex-wrap">
                        <div class="col-md-3">
                            <div class="search-input-group">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" class="search-input" placeholder="Cari nama sekolah..." id="searchInput">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="filter-section">
                                <select class="filter-select" id="jenjangFilter">
                                    <option value="">Semua Jenjang</option>
                                    <option value="PAUD">PAUD</option>
                                    <option value="TK">TK (TK A dan TK B)</option>
                                    <option value="SD">SD (Kelas 1-6)</option>
                                    <option value="SMP">SMP (Kelas 1-3)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="filter-section">
                                <select class="filter-select" id="kelasFilter">
                                    <option value="">Semua Kelas</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="filter-section">
                                <select class="filter-select" id="jkFilter">
                                    <option value="">Semua JK</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="filter-section">
                                <select class="filter-select" id="inklusiFilter">
                                    <option value="">Semua Inklusi</option>
                                    <option value="A">Tunanetra</option>
                                    <option value="B">Tunarungu</option>
                                    <option value="C">Tunagrahita Ringan</option>
                                    <option value="C1">Tunagrahita Sedang</option>
                                    <option value="D">Tunadaksa Ringan</option>
                                    <option value="D1">Tunadaksa Sedang</option>
                                    <option value="E">Tunalaras</option>
                                    <option value="F">Tunawicara</option>
                                    <option value="H">Hiperaktif</option>
                                    <option value="I">Cerdas Istimewa</option>
                                    <option value="J">Bakat Istimewa</option>
                                    <option value="K">Kesulitan Belajar</option>
                                    <option value="N">Narkoba</option>
                                    <option value="O">Indigo</option>
                                    <option value="P">Down Syndrome</option>
                                    <option value="Q">Autis</option>
                                    <option value="Lain">Lainnya</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-table me-1"></i>
                            Daftar Data Siswa
                        </div>
                        <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'pengurus'])) : ?>
                            <a href="tambah_siswa.php" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Tambah Data Siswa
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'pengurus'])) : ?>
                                <table id="datatablesSimple" class="table table-bordered table-hover align-middle">
                                    <thead class="table-primary text-center">
                                        <tr>
                                            <th style="width:40px;">No.</th>
                                            <th style="width:120px;">NISN</th>
                                            <th>Nama</th>
                                            <th style="width:60px;">JK</th>
                                            <th>Asal Sekolah</th>
                                            <th style="width:80px;">Kelas</th>
                                            <th style="width:120px;">Jenis Inklusi</th>
                                            <th>Alamat Sekolah</th>
                                            <th style="width:90px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        $query = mysqli_query($conn, "SELECT s.*, ds.nama_sekolah, ds.alamat, ds.jenjang_sekolah 
                                            FROM data_siswa s 
                                            LEFT JOIN data_sekolah_inklusi ds ON s.sekolah_id = ds.id
                                            ORDER BY s.id DESC");

                                        $deskripsi = [
                                            'A' => 'Tunanetra',
                                            'B' => 'Tunarungu',
                                            'C' => 'Tunagrahita Ringan',
                                            'C1' => 'Tunagrahita Sedang',
                                            'D' => 'Tunadaksa Ringan',
                                            'D1' => 'Tunadaksa Sedang',
                                            'E' => 'Tunalaras',
                                            'F' => 'Tunawicara',
                                            'H' => 'Hiperaktif',
                                            'I' => 'Cerdas Istimewa',
                                            'J' => 'Bakat Istimewa',
                                            'K' => 'Kesulitan Belajar',
                                            'N' => 'Narkoba',
                                            'O' => 'Indigo',
                                            'P' => 'Down Syndrome',
                                            'Q' => 'Autis',
                                            'Lain' => 'Lainnya'
                                        ];

                                        while ($row = mysqli_fetch_assoc($query)) :
                                            $jenis_kode = $row['jenis_inklusi'];
                                            $jenis_label = isset($deskripsi[$jenis_kode]) ? $deskripsi[$jenis_kode] : '-';
                                        ?>
                                            <tr>
                                                <td class="text-center"><?= $no++; ?></td>
                                                <td class="text-center"><?= htmlspecialchars($row['nisn']); ?></td>
                                                <td><?= htmlspecialchars($row['nama_siswa']); ?></td>
                                                <td class="text-center">
                                                    <?php if ($row['jenis_kelamin'] == 'L'): ?>
                                                        <span class="badge bg-info">L</span>
                                                    <?php elseif ($row['jenis_kelamin'] == 'P'): ?>
                                                        <span class="badge bg-pink text-white" style="background:#e83e8c;">P</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?= htmlspecialchars($row['nama_sekolah']); ?>
                                                    <span class="badge bg-secondary ms-1"><?= htmlspecialchars($row['jenjang_sekolah']); ?></span>
                                                </td>
                                                <td class="text-center"><?= htmlspecialchars($row['kelas']); ?></td>
                                                <td class="text-center" data-kode="<?= $jenis_kode ?>"><?= $jenis_label ?></td>
                                                <td><?= htmlspecialchars($row['alamat']); ?></td>
                                                <td class="text-center">
                                                    <a href="edit_siswa.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="hapus_siswa.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                        <tr id="noResults" class="d-none">
                                            <td colspan="9" class="text-center text-danger">Tidak ada data ditemukan.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <!-- Tabel untuk non-admin tetap -->
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<?php include '../partials/footer.php'; ?>
</body>

<script>
function applyFilters() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const jenjangValue = document.getElementById('jenjangFilter').value;
    const kelasValue = document.getElementById('kelasFilter').value;
    const jkValue = document.getElementById('jkFilter').value;
    const inklusiValue = document.getElementById('inklusiFilter').value.toLowerCase();

    const rows = document.querySelectorAll('table tbody tr');
    let visibleCount = 0;

    rows.forEach(row => {
        if (row.id === 'noResults') return;

        const asalSekolahCell = row.cells[4];
        const kelasCell = row.cells[5];
        const inklusiCell = row.cells[6];
        const jkCell = row.cells[3];

        let jenjang = '';
        if (asalSekolahCell) {
            const badge = asalSekolahCell.querySelector('span.badge.bg-secondary');
            jenjang = badge ? badge.textContent.trim() : '';
        }

        const kelas = kelasCell ? kelasCell.textContent.trim() : '';
        const jkBadge = jkCell ? jkCell.textContent.trim() : '';
        const kodeInklusi = inklusiCell ? inklusiCell.getAttribute('data-kode').toLowerCase() : '';

        const rowText = row.textContent.toLowerCase();

        const matchesSearch = rowText.includes(searchTerm);
        const matchesJenjang = jenjangValue === '' || jenjang === jenjangValue || (jenjangValue === 'TK' && jenjang.startsWith('TK'));
        const matchesKelas = kelasValue === '' || kelas.toLowerCase().includes(kelasValue.toLowerCase());
        const matchesJK = jkValue === '' || jkBadge === jkValue;
        const matchesInklusi = inklusiValue === '' || kodeInklusi === inklusiValue;

        if (matchesSearch && matchesJenjang && matchesKelas && matchesJK && matchesInklusi) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    const noResults = document.getElementById('noResults');
    if (noResults) {
        if (visibleCount === 0) {
            noResults.classList.remove('d-none');
        } else {
            noResults.classList.add('d-none');
        }
    }
}

document.getElementById('searchInput').addEventListener('input', applyFilters);
document.getElementById('jenjangFilter').addEventListener('change', function () {
    updateKelasOptions(this.value);
    applyFilters();
});
document.getElementById('kelasFilter').addEventListener('change', applyFilters);
document.getElementById('jkFilter').addEventListener('change', applyFilters);
document.getElementById('inklusiFilter').addEventListener('change', applyFilters);

function updateKelasOptions(jenjang) {
    const kelasFilter = document.getElementById('kelasFilter');
    kelasFilter.innerHTML = '<option value="">Semua Kelas</option>';

    if (jenjang === 'PAUD') {
        kelasFilter.innerHTML += '<option value="PAUD">PAUD</option>';
    } else if (jenjang === 'TK') {
        kelasFilter.innerHTML += '<option value="TK A">TK A</option>';
        kelasFilter.innerHTML += '<option value="TK B">TK B</option>';
    } else if (jenjang === 'SD') {
        for (let i = 1; i <= 6; i++) {
            kelasFilter.innerHTML += `<option value="${i}">${i}</option>`;
        }
    } else if (jenjang === 'SMP') {
        for (let i = 1; i <= 3; i++) {
            kelasFilter.innerHTML += `<option value="${i}">${i}</option>`;
        }
    }
}

setTimeout(() => {
    document.querySelectorAll('.alert.show').forEach(alert => {
        alert.classList.remove('show');
        alert.classList.add('fade');
    });
}, 5000);

// Inisialisasi kelas filter saat load
window.addEventListener('DOMContentLoaded', function() {
    updateKelasOptions(document.getElementById('jenjangFilter').value);
});
</script>
</html>