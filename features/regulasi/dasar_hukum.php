<?php
session_start();
include '../../config.php';
include '../../koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include '../../partials/head.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Dasar Hukum</title>

    <style>

        body{
            background-color: whitesmoke;
        }
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

        /* Card Styling */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 0 25px rgba(0,0,0,.08);
            overflow: hidden;
            background: white;
            margin-bottom: 2rem;
        }

        .section-header {
            background: white;
            border-bottom: 1px solid rgba(0,0,0,.05);
            padding: 1.2rem 1.5rem;
        }

        /* Button Groups Styling */
        .draft-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }

        /* Action Buttons */
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

        /* Button Variants */
        .btn-success {
            background: #10b981 !important;
            color: #fff !important;
            border: none !important;
        }

        .btn-success:hover {
            background: #059669 !important;
            box-shadow: 0 4px 12px rgba(16,185,129,.15) !important;
        }

        .btn-warning {
            background: #fbbf24 !important;
            color: #fff !important;
            border: none !important;
        }

        .btn-warning:hover {
            background: #f59e0b !important;
            box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3) !important;
        }

        .btn-danger {
            background: #ef4444 !important;
            color: #fff !important;
            border: none !important;
        }

        .btn-danger:hover {
            background: #dc2626 !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3) !important;
        }

        .btn-info {
            background: #4680ff !important;
            color: #fff !important;
            border: none !important;
        }

        .btn-info:hover {
            background: #3b6def !important;
            box-shadow: 0 4px 12px rgba(70, 128, 255, 0.3) !important;
        }

        .btn-primary {
            background: #6366f1 !important;
            color: #fff !important;
            border: none !important;
        }

        .btn-primary:hover {
            background: #4f46e5 !important;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3) !important;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        /* Spacing */
        main {
            min-height: calc(100vh - 60px);
            padding-bottom: 2rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .table {
                font-size: 0.9rem;
            }
            
            .btn {
                padding: 0.4rem 0.7rem;
                font-size: 0.8rem;
            }
            
            .action-buttons {
                flex-wrap: wrap;
                gap: 0.25rem;
            }
        }
    </style>
</head>

<body>
<?php
// Ambil semua regulasi
$data = mysqli_query($conn, "SELECT * FROM dasar_hukum ORDER BY id ASC");

// Tampilkan pesan sukses atau error jika ada
if (isset($_SESSION['success_message']) || isset($_SESSION['error_message'])) {
    $message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : $_SESSION['error_message'];
    $icon = isset($_SESSION['success_message']) ? 'success' : 'error';
    $title = isset($_SESSION['success_message']) ? 'Berhasil!' : 'Error!';
    $buttonColor = isset($_SESSION['success_message']) ? '#198754' : '#dc3545';
    
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: '" . $title . "',
                text: '" . str_replace("'", "\'", $message) . "',
                icon: '" . $icon . "',
                confirmButtonColor: '" . $buttonColor . "'
            });
        });
    </script>";
    
    // Clear session messages
    unset($_SESSION['success_message']);
    unset($_SESSION['error_message']);
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<body>
    <?php include '../../partials/sidebar.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <div class="page-header">
                        <h1 class="page-title">Dasar Hukum</h1>
                        <p class="page-subtitle text-muted">Daftar regulasi dan dasar hukum sekolah inklusi</p>
                    </div>

                    <div class="card">
                        <div class="section-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <i class="fas fa-gavel me-2" style="color: #4680ff;"></i> 
                                Daftar Regulasi / Dasar Hukum
                            </h4>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <a href="tambah_dasar_hukum.php" class="btn btn-success">
                                    <i class="fas fa-plus me-1"></i> Tambah Regulasi
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle admin-table mb-0">
                                    <thead>
                                        <tr class="text-center">
                                            <th style="width:40px;">No</th>
                                            <th style="width:160px;">Nomor Regulasi</th>
                                            <th>Tentang</th>
                                            <th style="width:120px;">Draft</th>
                                            <th style="width:160px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        while ($row = mysqli_fetch_assoc($data)):
                                            $namaFile = $row['draft_hukum'] ?? null;
                                        ?>
                                        <tr>
                                            <td class="text-center"><?= $no++; ?></td>
                                            <td><?= htmlspecialchars($row['nomor_regulasi'] ?? '-'); ?></td>
                                            <td><?= htmlspecialchars($row['tentang'] ?? '-'); ?></td>
                                            <td class="text-center">
                                                <?php if ($namaFile): ?>
                                                    <div class="draft-buttons">
                                                        <a href="../../pdfs/<?= rawurlencode($namaFile) ?>" 
                                                           class="btn btn-primary btn-sm"
                                                           target="_blank"
                                                           data-bs-toggle="tooltip" 
                                                           data-bs-placement="top" 
                                                           title="Lihat Dokumen">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="../../pdfs/<?= rawurlencode($namaFile) ?>" 
                                                           class="btn btn-success btn-sm"
                                                           download
                                                           data-bs-toggle="tooltip" 
                                                           data-bs-placement="top" 
                                                           title="Download Dokumen">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Tidak ada file</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="action-buttons d-flex justify-content-center gap-1">
                                                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                                        <a href="edit_dasar_hukum.php?id=<?= $row['id'] ?>" 
                                                           class="btn btn-warning btn-sm"
                                                           data-bs-toggle="tooltip" 
                                                           data-bs-placement="top" 
                                                           title="Edit Regulasi">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-danger btn-sm"
                                                                onclick="konfirmasiHapus(<?= $row['id'] ?>, '<?= htmlspecialchars($row['nomor_regulasi'], ENT_QUOTES) ?>')"
                                                                data-bs-toggle="tooltip" 
                                                                data-bs-placement="top" 
                                                                title="Hapus Regulasi">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php if ($namaFile): ?>
                                                        <a href="../../pdfs/<?= rawurlencode($namaFile) ?>" 
                                                           class="btn btn-info btn-sm"
                                                           target="_blank"
                                                           data-bs-toggle="tooltip" 
                                                           data-bs-placement="top" 
                                                           title="Buka Dokumen">
                                                            <i class="fas fa-external-link-alt"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php include '../../partials/footer.php'; ?>
    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        function konfirmasiHapus(id, nomor) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Yakin ingin menghapus regulasi "${nomor}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `hapus_dasar_hukum.php?id=${id}`;
                }
            });
        }
    </script>
</body>
</html>