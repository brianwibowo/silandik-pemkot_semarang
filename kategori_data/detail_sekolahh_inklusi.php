<?php
session_start();
include '../config.php';
include '../partials/head.php';
include '../koneksi.php';
include '../sidebar.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "<script>alert('ID sekolah tidak valid'); window.location.href = '../kategori_data/data_sekolah_inklusi.php';</script>";
    exit;
}

// Ambil data sekolah
$sekolah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM data_sekolah_inklusi WHERE id = $id"));
if (!$sekolah) {
    echo "<script>alert('Data tidak ditemukan'); window.location.href = '../kategori_data/data_sekolah_inklusi.php';</script>";
    exit;
}

// Ambil rekap
$rekap = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rekap WHERE sekolah_id = $id"));

// Ambil prasarana
$prasarana = [];
$q_prasarana = mysqli_query($conn, "SELECT * FROM prasarana WHERE sekolah_id = $id ORDER BY jenis_prasarana");
while ($row = mysqli_fetch_assoc($q_prasarana)) {
    $prasarana[] = $row;
}

// Ambil galeri
$galeri = [];
$q_galeri = mysqli_query($conn, "SELECT * FROM galeri WHERE sekolah_id = $id ORDER BY id DESC");
while ($row = mysqli_fetch_assoc($q_galeri)) {
    $galeri[] = $row;
}

// Ambil data siswa
$siswa = mysqli_query($conn, "SELECT * FROM data_siswa WHERE sekolah_id = $id");

// Helper
function sanitizeOutput($value, $default = '-')
{
    return !empty($value) ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8') : $default;
}
function formatNumber($number)
{
    return is_numeric($number) ? number_format($number) : '0';
}
function getJenjangText($jenjang)
{
    switch ($jenjang) {
        case 'SD':
            return 'Sekolah Dasar';
        case 'SMP':
            return 'Sekolah Menengah Pertama';
        case 'SMA':
            return 'Sekolah Menengah Atas';
        case 'SMK':
            return 'Sekolah Menengah Kejuruan';
        default:
            return $jenjang;
    }
}

// Hitung total siswa
$siswa_l = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM data_siswa WHERE sekolah_id = $id AND jenis_kelamin = 'L'"));
$siswa_p = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM data_siswa WHERE sekolah_id = $id AND jenis_kelamin = 'P'"));
$totalSiswa = $siswa_l + $siswa_p;
$isAdminOrPengurus = isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'pengurus']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail <?= sanitizeOutput($sekolah['nama_sekolah']); ?> - Sekolah Inklusi</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #06b6d4;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #1f2937;
            --light-bg: #f8fafc;
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-secondary: linear-gradient(135deg, #667eea 0%, #f093fb 100%);
            --gradient-success: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            --shadow-light: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-medium: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-large: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .school-profile-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .school-logo {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 15px;
            border: 3px solid #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .school-name {
            color: #4285f4;
            font-size: 1.8rem;
            font-weight: 600;
            margin: 15px 0 10px 0;
        }

        .school-type-badge {
            background: #34a853;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-block;
            margin: 10px 0;
        }

        .section-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .section-header {
            background: #4285f4;
            color: white;
            padding: 15px 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .section-header i {
            margin-right: 10px;
        }

        .info-item {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-item i {
            width: 20px;
            margin-right: 12px;
            color: #4285f4;
        }

        .info-label {
            font-size: 0.85rem;
            color: #666;
            display: block;
        }

        .info-value {
            color: #333;
            font-weight: 500;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px auto;
            font-size: 1.5rem;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        .prasarana-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 4px solid #34a853;
        }

        .prasarana-count {
            background: #34a853;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        .total-prasarana {
            background: linear-gradient(135deg, #34a853, #0f9d58);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin-top: 20px;
        }

        .students-table {
            margin-top: 30px;
        }

        .table-header {
            background: #4285f4;
            color: white;
        }

        .gender-badge {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
        }

        .male {
            background: #4285f4;
        }

        .female {
            background: #ea4335;
        }

        .edit-btn {
            background: #fbbc04;
            border: none;
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .edit-btn:hover {
            background: #f9ab00;
            transform: translateY(-1px);
        }

        .back-btn {
            background: #6c757d;
            color: white;
            padding: 0.5rem 0.5rem;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
            font-size: small;
        }

        .back-btn:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
            transform: translateY(-1px);
        }

        .description-card {
            background: linear-gradient(135deg, #e8f4fd 0%, #f0f8ff 100%);
            border: 1px solid #e3f2fd;
            border-radius: 12px;
            padding: 25px;
            line-height: 1.8;
        }
    </style>
</head>

<body>
    <div class="container-fluid px-4 py-4">
        <!-- Header -->
        <div class="row align-items-center mb-4">
            <div class="col-md-4 text-start">
                <a href="../kategori_data/data_sekolah_inklusi.php" class="back-btn">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Data Sekolah
                </a>
                <?php if ($isAdminOrPengurus): ?>
                    <a href="edit-detail.php?id=<?= $sekolah['id']; ?>" class="edit-btn me-2">Edit Detail</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">
            <!-- Kolom Kiri -->
            <div class="col-lg-4">
                <!-- Profil Sekolah -->
                <div class="school-profile-card">
                    <div class="text-center p-4">
                        <?php
                        $logoPath = "../upload/" . $sekolah['logo_sekolah'];
                        if (!empty($sekolah['logo_sekolah']) && file_exists($logoPath)): ?>
                            <img src="<?= $logoPath; ?>" alt="Logo <?= sanitizeOutput($sekolah['nama_sekolah']); ?>" class="school-logo">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/120x120/4285f4/ffffff?text=LOGO" alt="Logo Sekolah" class="school-logo">
                        <?php endif; ?>
                        <h3 class="school-name"><?= sanitizeOutput($sekolah['nama_sekolah']); ?></h3>
                        <p class="text-muted mb-2">NPSN: <?= sanitizeOutput($sekolah['npsn']); ?></p>
                        <span class="school-type-badge"><?= getJenjangText($sekolah['jenjang_sekolah']); ?></span>
                        <?php if (!empty($sekolah['kepala_sekolah'])): ?>
                            <div class="mt-3 pt-3 border-top">
                                <small class="info-label">Kepala Sekolah</small>
                                <div class="info-value"><?= sanitizeOutput($sekolah['kepala_sekolah']); ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Informasi Kontak -->
                <div class="section-card">
                    <div class="section-header">
                        <span><i class="fas fa-address-book"></i>Informasi Kontak</span>
                    </div>
                    <div class="p-3">
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <div>
                                <span class="info-label">Telepon</span>
                                <div class="info-value"><?= sanitizeOutput($sekolah['telepon']); ?></div>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-globe"></i>
                            <div>
                                <span class="info-label">Website</span>
                                <div class="info-value">
                                    <?php if (!empty($sekolah['website'])): ?>
                                        <a href="<?= strpos($sekolah['website'], 'http') === 0 ? $sekolah['website'] : 'http://' . $sekolah['website']; ?>" target="_blank" style="color: #4285f4;">
                                            <?= sanitizeOutput($sekolah['website']); ?> <i class="fas fa-external-link-alt ms-1"></i>
                                        </a>
                                        <?php else: ?>- <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <span class="info-label">Alamat</span>
                                <div class="info-value"><?= sanitizeOutput($sekolah['alamat']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Prasarana Sekolah -->
                <div class="section-card">
                    <div class="section-header">
                        <span><i class="fas fa-building"></i>Prasarana Sekolah</span>
                    </div>
                    <div class="p-3">
                        <?php
                        $totalPrasarana = 0;
                        if ($prasarana && count($prasarana) > 0):
                            foreach ($prasarana as $item):
                                $totalPrasarana += (int)($item['jumlah'] ?? 0);
                        ?>
                                <div class="prasarana-item">
                                    <div>
                                        <div class="fw-bold"><?= sanitizeOutput($item['jenis_prasarana'], 'Prasarana'); ?></div>
                                        <small class="text-muted">Prasarana</small>
                                    </div>
                                    <span class="prasarana-count"><?= formatNumber($item['jumlah'] ?? 0); ?></span>
                                </div>
                            <?php endforeach; ?>
                            <div class="total-prasarana">
                                <div class="fw-bold">Total Prasarana</div>
                                <div class="h4 mb-0 mt-1"><?= formatNumber($totalPrasarana); ?> Unit</div>
                                <small>Jumlah keseluruhan unit prasarana</small>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted mb-2">Belum ada data prasarana</h6>
                                <p class="text-muted mb-0">Data prasarana sekolah belum tersedia</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="col-lg-8">
                <!-- Tentang Sekolah -->
                <?php if (!empty($sekolah['deskripsi'])): ?>
                    <div class="section-card mb-4">
                        <div class="section-header">
                            <span><i class="fas fa-info-circle"></i>Tentang Sekolah</span>
                        </div>
                        <div class="p-4">
                            <div class="description-card">
                                <p class="mb-0 text-dark"><?= nl2br(sanitizeOutput($sekolah['deskripsi'])); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Statistik Sekolah -->
                <div class="section-card">
                    <div class="section-header">
                        <span><i class="fas fa-chart-bar"></i>Statistik Sekolah</span>
                    </div>
                    <div class="p-4">
                        <div class="row text-center">
                            <div class="col-md-4 mb-3">
                                <div class="stat-card">
                                    <div class="stat-icon" style="background: rgba(66, 133, 244, 0.1);">
                                        <i class="fas fa-user-tie" style="color: #4285f4;"></i>
                                    </div>
                                    <div class="stat-number" style="color: #4285f4;"><?= formatNumber($rekap['jumlah_pegawai'] ?? 0); ?></div>
                                    <div class="stat-label">Total Pegawai</div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="stat-card">
                                    <div class="stat-icon" style="background: rgba(52, 168, 83, 0.1);">
                                        <i class="fas fa-user-graduate" style="color: #34a853;"></i>
                                    </div>
                                    <div class="stat-number" style="color: #34a853;"><?= formatNumber($totalSiswa); ?></div>
                                    <div class="stat-label">Total Siswa</div>
                                    <div class="mt-2">
                                        <small style="color: #4285f4;"><i class="fas fa-mars"></i> L: <?= formatNumber($siswa_l); ?></small>
                                        <small class="ms-2" style="color: #ea4335;"><i class="fas fa-venus"></i> P: <?= formatNumber($siswa_p); ?></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="stat-card">
                                    <div class="stat-icon" style="background: rgba(251, 188, 4, 0.1);">
                                        <i class="fas fa-users" style="color: #fbbc04;"></i>
                                    </div>
                                    <div class="stat-number" style="color: #fbbc04;"><?= formatNumber($rekap['jumlah_rombel'] ?? 0); ?></div>
                                    <div class="stat-label">Rombongan Belajar</div>
                                </div>
                            </div>
                        </div>
                        <?php if ($rekap): ?>
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    Terakhir diperbarui: <?= date('d M Y H:i', strtotime($rekap['updated_at'] ?? $rekap['created_at'] ?? 'now')); ?>
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Data Siswa (Hanya untuk admin/pengurus) -->
                <?php if ($isAdminOrPengurus): ?>
                <div class="students-table">
                    <div class="section-card">
                        <div class="section-header">
                            <span><i class="fas fa-users"></i>Data Siswa</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-header">
                                    <tr class="text-center">
                                        <th style="width: 60px;">No</th>
                                        <th style="width: 120px;">NISN</th>
                                        <th>Nama</th>
                                        <th style="width: 80px;">JK</th>
                                        <th style="width: 80px;">Kelas</th>
                                        <th style="width: 100px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    while ($row = mysqli_fetch_assoc($siswa)): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++; ?></td>
                                            <td class="text-center"><?= sanitizeOutput($row['nisn']); ?></td>
                                            <td><?= sanitizeOutput($row['nama_siswa']); ?></td>
                                            <td class="text-center">
                                                <?php if ($row['jenis_kelamin'] == 'L'): ?>
                                                    <span class="gender-badge male">L</span>
                                                <?php elseif ($row['jenis_kelamin'] == 'P'): ?>
                                                    <span class="gender-badge female">P</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center"><?= sanitizeOutput($row['kelas']); ?></td>
                                            <td class="text-center">
                                                <a href="edit_siswa.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning me-1" title="Edit"><i class="fas fa-edit"></i></a>
                                                <a href="hapus_siswa.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Hapus siswa ini?')"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Galeri Sekolah -->
                <div class="section-card mt-4">
                    <div class="section-header">
                        <span><i class="fas fa-images"></i>Galeri Sekolah</span>
                    </div>
                    <div class="p-3">
                        <?php if ($galeri && count($galeri) > 0): ?>
                            <div class="row g-2">
                                <?php foreach ($galeri as $index => $foto): ?>
                                    <div class="col-md-4 mb-2">
                                        <div class="gallery-item">
                                            <?php
                                            $galeriPath = "../upload/galeri/" . $foto['path_gambar'];
                                            if (file_exists($galeriPath)):
                                            ?>
                                                <img src="<?= $galeriPath; ?>" alt="<?= sanitizeOutput($foto['judul'], 'Galeri'); ?>" class="img-fluid w-100 img-gallery" style="height: 180px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 180px;">
                                                    <div class="text-center text-muted">
                                                        <i class="fas fa-image fa-2x mb-2"></i>
                                                        <p class="mb-0 small">Gambar tidak ditemukan</p>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($foto['judul'])): ?>
                                                <small class="text-muted d-block mt-2"><?= sanitizeOutput($foto['judul']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-images fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">Belum ada galeri tersedia</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scroll untuk anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Animasi untuk stat cards
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animation = 'fadeInUp 0.6s ease forwards';
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.stat-card').forEach(card => {
                observer.observe(card);
            });

            // Hover effect untuk prasarana items
            document.querySelectorAll('.prasarana-item').forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(5px)';
                    this.style.transition = 'transform 0.3s ease';
                });

                item.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateX(0)';
                });
            });
        });

        // CSS Animation keyframes
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>

</html>