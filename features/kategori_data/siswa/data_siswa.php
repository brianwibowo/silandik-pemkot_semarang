<?php
session_start();
include '../../../config.php';
include '../../../partials/head.php';
include '../../../koneksi.php';
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php include '../../../partials/sidebar.php'; ?>
<body>
<div id="layoutSidenav">
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <div class="page-header">
                    <h1 class="page-title">Data Siswa</h1>
                    <p class="page-subtitle">Data Siswa Sekolah Inklusi</p>
                </div>

                <div class="search-section">
                    <div class="row align-items-center g-3 flex-wrap">
                        <div class="col-md-3">
                            <div class="search-input-group">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" class="search-input form-control" placeholder="Cari nama sekolah..." id="searchInput">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="filter-select form-select" id="jenjangFilter">
                                <option value="">Semua Jenjang</option>
                                <option value="PAUD">PAUD</option>
                                <option value="TK">TK (TK A dan TK B)</option>
                                <option value="SD">SD (Kelas 1-6)</option>
                                <option value="SMP">SMP (Kelas 1-3)</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="filter-select form-select" id="kelasFilter">
                                <option value="">Semua Kelas</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="filter-select form-select" id="jkFilter">
                                <option value="">Semua JK</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="filter-select form-select" id="inklusiFilter">
                                <option value="">Semua Jenis Inklusi</option>
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
                                <table id="dataTable" class="table table-striped table-hover table-bordered align-middle">
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
                                                    <span class="badge bg-secondary ms-1 jenjang"><?= $row['jenjang_sekolah']; ?></span>
                                                </td>
                                                <td class="text-center"><?= htmlspecialchars($row['kelas']); ?></td>
                                                <td class="text-center" data-kode="<?= $jenis_kode ?>"><?= $jenis_label ?></td>
                                                <td><?= htmlspecialchars($row['alamat']); ?></td>
                                                <td class="text-center">
                                                    <div class="btn-group action-buttons" role="group">
                                                        <a href="edit_siswa.php?id=<?= $row['id']; ?>" 
                                                           class="btn btn-warning btn-sm" 
                                                           data-bs-toggle="tooltip" 
                                                           data-bs-placement="top" 
                                                           title="Edit Data Siswa">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="hapus_siswa.php?id=<?= $row['id']; ?>" 
                                                           class="btn btn-danger btn-sm" 
                                                           onclick="return confirmDelete(event)"
                                                           data-bs-toggle="tooltip" 
                                                           data-bs-placement="top" 
                                                           title="Hapus Data Siswa">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <!-- TABEL REKAP UNTUK UMUM -->
                                <table id="rekapTable" class="table table-striped table-hover table-bordered align-middle">
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
<?php include '../../../partials/footer.php'; ?>
</body>
</html>

<script>
function applyFilters() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const jenjangValue = document.getElementById('jenjangFilter').value.toUpperCase().trim();
    const kelasValue = document.getElementById('kelasFilter').value.toLowerCase();
    const jkValue = document.getElementById('jkFilter').value;
    const inklusiValue = document.getElementById('inklusiFilter').value;

    const tableId = document.getElementById('dataTable') ? 'dataTable' : 'rekapTable';
    const rows = document.querySelectorAll(`#${tableId} tbody tr`);

    rows.forEach(row => {
        const sekolah = row.querySelector('.school-name')?.textContent.toLowerCase() || row.cells[4]?.textContent.toLowerCase();
        const jenjang = (row.querySelector('.jenjang')?.textContent || '').toUpperCase().trim();
        const kelas = row.querySelector('.kelas')?.textContent.toLowerCase() || row.cells[5]?.textContent.toLowerCase();
        let match = true;

        // Untuk tabel detail siswa
        if (tableId === 'dataTable') {
            const jk = row.cells[3]?.textContent.trim();
            const inklusi = row.cells[6]?.getAttribute('data-kode');
            
            if (jkValue && jk !== jkValue) match = false;
            if (inklusiValue && inklusi !== inklusiValue) match = false;
        }
        // Untuk tabel rekap
        else {
            const jkL = row.querySelector('.jk-l')?.textContent || '';
            const jkP = row.querySelector('.jk-p')?.textContent || '';
            if (jkValue) {
                const totalJK = parseInt(jkValue === 'L' ? jkL : jkP);
                if (isNaN(totalJK) || totalJK === 0) match = false;
            }
        }

        if (searchTerm && !sekolah.includes(searchTerm)) match = false;
        if (jenjangValue && jenjang !== jenjangValue && !(jenjangValue === 'TK' && jenjang.startsWith('TK'))) match = false;
        if (kelasValue && !kelas.includes(kelasValue)) match = false;

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

// Load default kelas option sesuai jenjang
window.addEventListener('DOMContentLoaded', () => {
    updateKelasOptions(document.getElementById('jenjangFilter').value);
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Sweet Alert confirmation for delete
function confirmDelete(event) {
    event.preventDefault();
    const link = event.currentTarget.href;

    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Apakah Anda yakin ingin menghapus data siswa ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = link;
        }
    });
    
    return false;
}
</script>

<style>
/* Modern Table Styling */
.table-responsive {
    background: white;
    border-radius: 12px;
    box-shadow: 0 0 25px rgba(0,0,0,.08);
    overflow: hidden;
    margin: 0;
}

.table {
    margin-bottom: 0;
    background: white;
}

/* Header Styling */
.table > thead {
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

.table > thead th {
    border: none !important;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    padding: 1.2rem 1rem;
    color: #495057;
    letter-spacing: 0.5px;
    background: linear-gradient(to bottom, #f8f9fa, #f1f3f5);
    vertical-align: middle;
}

/* Body Styling */
.table > tbody > tr {
    border-bottom: 1px solid #dee2e6;
    transition: all 0.2s ease;
}

.table > tbody > tr:hover {
    background-color: rgba(70, 128, 255, 0.05) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,.02);
}

.table > tbody > tr > td {
    padding: 1rem;
    vertical-align: middle;
    border: none;
    color: #495057;
}

/* Striped Rows */
.table-striped > tbody > tr:nth-of-type(odd) {
    background-color: rgba(0,0,0,.01);
}

/* Card Styling */
.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 0 25px rgba(0,0,0,.08);
    overflow: hidden;
    background: white;
}

.card-header {
    background: white;
    border-bottom: 1px solid rgba(0,0,0,.05);
    padding: 1.2rem 1.5rem;
}

.card-header i {
    color: #4680ff;
}

.card-body {
    padding: 0;
}

/* Header Section */
.header-section {
    margin-bottom: 2rem;
}

.page-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: #6c757d;
    margin-bottom: 0;
}

/* Search Section */
.search-section {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 0 25px rgba(0,0,0,.08);
    margin-bottom: 1.5rem;
}

.search-input-group {
    position: relative;
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    z-index: 2;
}

.search-input, .filter-select {
    height: 2.8rem;
    border: 1px solid #dee2e6;
    padding: 0.6rem 1rem;
    border-radius: 8px;
    transition: all 0.2s;
    font-size: 0.9rem;
}

.search-input {
    padding-left: 2.5rem;
}

.search-input:focus, .filter-select:focus {
    border-color: #4680ff;
    box-shadow: 0 0 0 0.2rem rgba(70,128,255,.15);
}

/* Button Styling */
.btn-success {
    background: #10b981;
    border-color: #10b981;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-success:hover {
    background: #059669;
    border-color: #059669;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(16,185,129,.15);
}

.action-buttons .btn {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 6px;
    margin: 0 0.15rem;
    transition: all 0.2s ease;
    position: relative;
    border: none;
}

.action-buttons .btn:hover {
    transform: translateY(-2px);
}

.action-buttons .btn-warning {
    background: #fbbf24;
    color: #fff;
}

.action-buttons .btn-warning:hover {
    background: #f59e0b;
    box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3);
}

.action-buttons .btn-danger {
    background: #ef4444;
    color: #fff;
}

.action-buttons .btn-danger:hover {
    background: #dc2626;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,.1);
}

/* Badge Styling */
.badge {
    padding: 0.5em 0.8em;
    font-weight: 500;
    font-size: 0.75rem;
    border-radius: 6px;
}

/* Background */
body {
    background-color: #f8f9fa;
    font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

/* Responsive Design */
@media (max-width: 768px) {
    .search-section {
        padding: 1rem;
    }
    
    .table {
        white-space: nowrap;
    }
    
    .table > thead th {
        padding: 0.8rem;
        font-size: 0.8rem;
    }
    
    .table > tbody > tr > td {
        padding: 0.8rem;
    }
    
    .btn-sm {
        padding: 0.3rem 0.6rem;
        font-size: 0.8rem;
    }
}
</style>
</html>
