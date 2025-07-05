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
                            <select class="filter-select" id="jenjangFilter">
                                <option value="">Semua Jenjang</option>
                                <option value="PAUD">PAUD</option>
                                <option value="TK">TK (TK A dan TK B)</option>
                                <option value="SD">SD (Kelas 1-6)</option>
                                <option value="SMP">SMP (Kelas 1-3)</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="filter-select" id="kelasFilter">
                                <option value="">Semua Kelas</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="filter-select" id="jkFilter">
                                <option value="">Semua JK</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div><i class="fas fa-table me-1"></i>Daftar Data Siswa</div>
                        <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'pengurus'])) : ?>
                            <a href="tambah_siswa.php" class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Tambah Data Siswa
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'pengurus'])) : ?>
                                <!-- TABEL DETAIL UNTUK ADMIN/PENGURUS -->
                                <table id="dataTable" class="table table-bordered table-hover align-middle">
                                    <thead class="table-primary text-center">
                                        <tr>
                                            <th>No</th>
                                            <th>NISN</th>
                                            <th>Nama</th>
                                            <th>JK</th>
                                            <th>Asal Sekolah</th>
                                            <th>Kelas</th>
                                            <th>Jenis Inklusi</th>
                                            <th>Alamat</th>
                                            <th>Aksi</th>
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
                                            'A' => 'Tunanetra', 'B' => 'Tunarungu', 'C' => 'Tunagrahita Ringan', 'C1' => 'Tunagrahita Sedang',
                                            'D' => 'Tunadaksa Ringan', 'D1' => 'Tunadaksa Sedang', 'E' => 'Tunalaras', 'F' => 'Tunawicara',
                                            'H' => 'Hiperaktif', 'I' => 'Cerdas Istimewa', 'J' => 'Bakat Istimewa', 'K' => 'Kesulitan Belajar',
                                            'N' => 'Narkoba', 'O' => 'Indigo', 'P' => 'Down Syndrome', 'Q' => 'Autis', 'Lain' => 'Lainnya'
                                        ];

                                        while ($row = mysqli_fetch_assoc($query)) :
                                            $jenis_kode = $row['jenis_inklusi'];
                                            $jenis_label = $deskripsi[$jenis_kode] ?? '-';
                                        ?>
                                            <tr>
                                                <td class="text-center"><?= $no++; ?></td>
                                                <td class="text-center"><?= htmlspecialchars($row['nisn']); ?></td>
                                                <td><?= htmlspecialchars($row['nama_siswa']); ?></td>
                                                <td class="text-center"><?= $row['jenis_kelamin']; ?></td>
                                                <td>
                                                    <?= htmlspecialchars($row['nama_sekolah']); ?>
                                                    <span class="badge bg-secondary ms-1"><?= $row['jenjang_sekolah']; ?></span>
                                                </td>
                                                <td class="text-center"><?= htmlspecialchars($row['kelas']); ?></td>
                                                <td class="text-center" data-kode="<?= $jenis_kode ?>"><?= $jenis_label ?></td>
                                                <td><?= htmlspecialchars($row['alamat']); ?></td>
                                                <td class="text-center">
                                                    <a href="edit_siswa.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                                    <a href="hapus_siswa.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="fas fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <!-- TABEL REKAP UNTUK UMUM -->
                                <table id="rekapTable" class="table table-bordered table-hover align-middle">
                                    <thead class="table-primary text-center">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Sekolah</th>
                                            <th>Jenjang</th>
                                            <th>Laki-laki</th>
                                            <th>Perempuan</th>
                                            <th>Kelas</th>
                                            <th>Alamat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        $query = mysqli_query($conn, "
                                            SELECT 
                                                ds.nama_sekolah,
                                                ds.jenjang_sekolah,
                                                ds.alamat,
                                                GROUP_CONCAT(DISTINCT s.kelas ORDER BY s.kelas ASC SEPARATOR ', ') AS kelas_terdata,
                                                SUM(CASE WHEN s.jenis_kelamin = 'L' THEN 1 ELSE 0 END) AS jumlah_laki,
                                                SUM(CASE WHEN s.jenis_kelamin = 'P' THEN 1 ELSE 0 END) AS jumlah_perempuan
                                            FROM data_siswa s
                                            LEFT JOIN data_sekolah_inklusi ds ON s.sekolah_id = ds.id
                                            GROUP BY s.sekolah_id
                                            ORDER BY ds.nama_sekolah ASC
                                        ");

                                        while ($row = mysqli_fetch_assoc($query)) :
                                        ?>
                                            <tr>
                                                <td class="text-center"><?= $no++; ?></td>
                                                <td class="school-name"><?= htmlspecialchars($row['nama_sekolah']); ?></td>
                                                <td class="jenjang"><?= $row['jenjang_sekolah']; ?></td>
                                                <td class="jk-l"><?= $row['jumlah_laki']; ?></td>
                                                <td class="jk-p"><?= $row['jumlah_perempuan']; ?></td>
                                                <td class="kelas"><?= $row['kelas_terdata']; ?></td>
                                                <td><?= htmlspecialchars($row['alamat']); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
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
    const kelasValue = document.getElementById('kelasFilter').value.toLowerCase();
    const jkValue = document.getElementById('jkFilter').value;

    const tableId = document.getElementById('dataTable') ? 'dataTable' : 'rekapTable';
    const rows = document.querySelectorAll(`#${tableId} tbody tr`);

    rows.forEach(row => {
        const sekolah = row.querySelector('.school-name')?.textContent.toLowerCase() || row.cells[4]?.textContent.toLowerCase();
        const jenjang = row.querySelector('.jenjang')?.textContent || '';
        const kelas = row.querySelector('.kelas')?.textContent.toLowerCase() || row.cells[5]?.textContent.toLowerCase();
        const jkL = row.querySelector('.jk-l')?.textContent || '';
        const jkP = row.querySelector('.jk-p')?.textContent || '';
        let match = true;

        if (searchTerm && !sekolah.includes(searchTerm)) match = false;
        if (jenjangValue && jenjang !== jenjangValue && !(jenjangValue === 'TK' && jenjang.startsWith('TK'))) match = false;
        if (kelasValue && !kelas.includes(kelasValue)) match = false;
        if (jkValue) {
            const totalJK = parseInt(jkValue === 'L' ? jkL : jkP);
            if (isNaN(totalJK) || totalJK === 0) match = false;
        }

        row.style.display = match ? '' : 'none';
    });
}

document.getElementById('searchInput').addEventListener('input', applyFilters);
document.getElementById('jenjangFilter').addEventListener('change', () => {
    updateKelasOptions(document.getElementById('jenjangFilter').value);
    applyFilters();
});
document.getElementById('kelasFilter').addEventListener('change', applyFilters);
document.getElementById('jkFilter').addEventListener('change', applyFilters);

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

// Load default kelas option sesuai jenjang
window.addEventListener('DOMContentLoaded', () => {
    updateKelasOptions(document.getElementById('jenjangFilter').value);
});
</script>
</html>
