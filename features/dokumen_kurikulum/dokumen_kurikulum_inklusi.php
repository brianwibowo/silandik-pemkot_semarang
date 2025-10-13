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
    <title>Dokumen Kurikulum Inklusi</title>

    <?php
    if (isset($_SESSION['flash_message'])) {
        $message = '';
        $icon = '';
        
        switch ($_SESSION['flash_message']) {
            case 'success_edit':
                $message = 'Dokumen kurikulum berhasil diperbarui!';
                $icon = 'success';
                break;
            case 'error_edit':
                $message = 'Gagal memperbarui dokumen kurikulum!';
                $icon = 'error';
                break;
        }
        
        if ($message && $icon) {
            echo "
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Notifikasi',
                        text: '$message',
                        icon: '$icon',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                });
            </script>";
        }
        
        unset($_SESSION['flash_message']);
    }

    $data = mysqli_query($conn, "SELECT * FROM dokumen_kurikulum_inklusi WHERE id = 1");
    $draft = mysqli_fetch_assoc($data);
    $namaFile = isset($draft['draft_kurikulum']) ? $draft['draft_kurikulum'] : null;
    ?>

    <style>
        /* Modern Card Styling */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 0 25px rgba(0,0,0,.08);
            overflow: hidden;
            background: white;
            margin-bottom: 2rem;
        }

        .card-header {
            background: white;
            border-bottom: 1px solid rgba(0,0,0,.05);
            padding: 1.2rem 1.5rem;
        }

        .card-header i {
            color: #4680ff;
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

        .page-subtitle {
            color: #6c757d;
            margin-bottom: 0;
        }

        /* PDF Container */
        .pdf-container {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            position: relative;
            width: 100%;
            height: 600px;
            overflow: hidden;
        }

        .pdf-container iframe {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: white;
        }

        /* Button Styling */
        .btn {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,.1);
        }

        .btn i {
            margin-right: 0.4rem;
        }

        .btn-warning {
            background: #fbbf24;
            border: none;
            color: #fff;
        }

        .btn-warning:hover {
            background: #f59e0b;
            box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3);
        }

        .btn-primary {
            background: #4680ff;
            border: none;
        }

        .btn-primary:hover {
            background: #3b6def;
            box-shadow: 0 4px 12px rgba(70, 128, 255, 0.3);
        }

        /* No Document Message */
        .no-document {
            text-align: center;
            padding: 3rem 1.5rem;
            background: #f8f9fa;
            border-radius: 8px;
            color: #6c757d;
        }

        .no-document i {
            font-size: 3rem;
            color: #cbd5e0;
            margin-bottom: 1rem;
        }

        /* Spacing */
        main {
            min-height: calc(100vh - 60px);
            padding-bottom: 2rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
                gap: 1rem;
            }

            .btn-group {
                display: flex;
                width: 100%;
            }

            .btn-group .btn {
                flex: 1;
            }

            .pdf-container {
                height: 400px;
            }

            .page-title {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .container-fluid {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.8rem;
            }
        }
    </style>

<body class="bg-light">
    <?php include '../../partials/sidebar.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <div class="page-header">
                        <h1 class="page-title">Dokumen Kurikulum Inklusi</h1>
                        <p class="page-subtitle">Dokumen dan panduan kurikulum untuk sekolah inklusi</p>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-book-reader me-2"></i>
                                <span class="fw-semibold">Draft Kurikulum Inklusi</span>
                            </div>
                            <div class="btn-group">
                                <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'pengurus'])) : ?>
                                    <a href="edit_kurikulum.php" 
                                       class="btn btn-warning"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       title="Edit dokumen kurikulum">
                                        <i class="fas fa-edit"></i>
                                        <span class="d-none d-sm-inline">Edit Kurikulum</span>
                                    </a>
                                <?php endif; ?>
                                <?php if ($namaFile) : ?>
                                    <a href="../../pdfs/<?= rawurlencode($namaFile) ?>" 
                                       class="btn btn-primary ms-2"
                                       download
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       title="Download dokumen PDF">
                                        <i class="fas fa-download"></i>
                                        <span class="d-none d-sm-inline">Download PDF</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if ($namaFile) : ?>
                                <div class="pdf-container">
                                    <iframe src="../../pdfs/<?= rawurlencode($namaFile) ?>" 
                                            type="application/pdf" 
                                            width="100%" 
                                            height="100%"
                                            style="border: none;">
                                    </iframe>
                                </div>
                                <div class="p-4 text-center">
                                    <a href="../../pdfs/<?= rawurlencode($namaFile) ?>" 
                                       class="btn btn-primary"
                                       target="_blank"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       title="Buka dokumen di tab baru">
                                        <i class="fas fa-external-link-alt"></i> Buka di Tab Baru
                                    </a>
                                </div>
                            <?php else : ?>
                                <div class="no-document">
                                    <i class="fas fa-file-alt"></i>
                                    <h4>Tidak Ada Dokumen</h4>
                                    <p class="text-muted">Belum ada dokumen kurikulum yang tersedia saat ini.</p>
                                    <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'pengurus'])) : ?>
                                        <a href="edit_kurikulum.php" class="btn btn-primary mt-2">
                                            <i class="fas fa-plus"></i> Tambah Dokumen
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
            <?php include '../../partials/footer.php'; ?>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
    </script>
</body>
</html>