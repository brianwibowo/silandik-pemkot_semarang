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
                <!-- Header Section -->
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
                                    <option value="SD">SD</option>
                                    <option value="SMP">SMP</option>
                                    <option value="SMA">SMA</option>
                                    <option value="SMK">SMK</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="filter-section">
                                <select class="filter-select" id="kelasFilter">
                                    <option value="">Semua Kelas</option>
                                    <!-- Isi akan dinamis tergantung jenjang -->
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
                                        while ($row = mysqli_fetch_assoc($query)) :
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
                                            <td colspan="8" class="text-center text-danger">Tidak ada data ditemukan.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-primary text-center">
                                        <tr>
                                            <th style="width:40px;">No.</th>
                                            <th>Nama Sekolah</th>
                                            <th style="width:200px;">Jumlah Siswa</th>
                                            <th>Alamat Sekolah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        $qSekolah = mysqli_query($conn, "SELECT id, nama_sekolah, alamat FROM data_sekolah_inklusi ORDER BY nama_sekolah");
                                        while ($row = mysqli_fetch_assoc($qSekolah)):
                                            $id = $row['id'];
                                            $l = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM data_siswa WHERE sekolah_id=$id AND jenis_kelamin='L'"))['total'];
                                            $p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM data_siswa WHERE sekolah_id=$id AND jenis_kelamin='P'"))['total'];
                                            $total = $l + $p;
                                        ?>
                                            <tr>
                                                <td class="text-center"><?= $no++; ?></td>
                                                <td><?= htmlspecialchars($row['nama_sekolah']); ?></td>
                                                <td class="text-center">
                                                    <span class="badge bg-info">L: <?= $l ?></span>
                                                    <span class="badge bg-pink text-white" style="background:#e83e8c;">P: <?= $p ?></span>
                                                    <span class="badge bg-success ms-1">Total: <?= $total ?></span>
                                                </td>
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
        const kelasValue = document.getElementById('kelasFilter').value;
        const jkValue = document.getElementById('jkFilter').value;

        const rows = document.querySelectorAll('table tbody tr');
        let visibleCount = 0;

        rows.forEach(row => {
            if (row.id === 'noResults') return;

            const rowText = row.textContent.toLowerCase();
            const asalSekolahCell = row.cells[4];
            const kelasCell = row.cells[5];
            const jkCell = row.cells[3];

            let jenjang = '';
            if (asalSekolahCell) {
                const badge = asalSekolahCell.querySelector('span.badge.bg-secondary');
                jenjang = badge ? badge.textContent.trim() : '';
            }

            const kelas = kelasCell ? kelasCell.textContent.trim() : '';
            const jkBadge = jkCell ? jkCell.textContent.trim() : '';

            const matchesSearch = rowText.includes(searchTerm);
            const matchesJenjang = jenjangValue === '' || jenjang === jenjangValue;
            const matchesKelas = kelasValue === '' || kelas.toLowerCase().includes(kelasValue.toLowerCase());
            const matchesJK = jkValue === '' || jkBadge === jkValue;

            if (matchesSearch && matchesJenjang && matchesKelas && matchesJK) {
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

    // Event listeners untuk semua filter
    document.getElementById('searchInput').addEventListener('input', applyFilters);
    document.getElementById('jenjangFilter').addEventListener('change', function () {
        updateKelasOptions(this.value);
        applyFilters();
    });
    document.getElementById('kelasFilter').addEventListener('change', applyFilters);
    document.getElementById('jkFilter').addEventListener('change', applyFilters);

    // Fungsi untuk memperbarui isi dropdown kelas secara dinamis berdasarkan jenjang
    function updateKelasOptions(jenjang) {
        const kelasFilter = document.getElementById('kelasFilter');
        kelasFilter.innerHTML = '<option value="">Semua Kelas</option>';

        let maxKelas = 0;
        if (jenjang === 'SD') maxKelas = 6;
        else if (['SMP', 'SMA', 'SMK'].includes(jenjang)) maxKelas = 3;

        for (let i = 1; i <= maxKelas; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = `Kelas ${i}`;
            kelasFilter.appendChild(option);
        }
    }

    // Auto-hide alerts after 5 seconds
    setTimeout(function () {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert.classList.contains('show')) {
                alert.classList.remove('show');
                alert.classList.add('fade');
            }
        });
    }, 5000);
</script>


</html>