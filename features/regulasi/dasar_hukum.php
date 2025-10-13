<?php
session_start();
include '../../config.php';
include '../../partials/head.php';
include '../../koneksi.php';

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
                    <h1 class="mt-4">Dasar Hukum</h1>

                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-gavel me-1"></i> Daftar Regulasi / Dasar Hukum</span>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <a href="tambah_dasar_hukum.php" class="btn btn-sm btn-success">
                                    <i class="fas fa-plus"></i> Tambah Regulasi
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-primary text-center">
                                        <tr>
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
                                                    <a href="../../pdfs/<?= rawurlencode($namaFile) ?>" class="btn btn-sm btn-primary mb-1" target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="../../pdfs/<?= rawurlencode($namaFile) ?>" class="btn btn-sm btn-success mb-1" download>
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-danger">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                                    <a href="edit_dasar_hukum.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning mb-1" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger mb-1" 
                                                            onclick="konfirmasiHapus(<?= $row['id'] ?>, '<?= htmlspecialchars($row['nomor_regulasi'], ENT_QUOTES) ?>')" 
                                                            title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if ($namaFile): ?>
                                                    <a href="../../pdfs/<?= rawurlencode($namaFile) ?>" class="btn btn-sm btn-info mb-1" target="_blank">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                <?php endif; ?>
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
        function konfirmasiHapus(id, nomor) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Yakin ingin menghapus regulasi "${nomor}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `hapus_dasar_hukum.php?id=${id}`;
                }
            });
        }
    </script>
</body>
</html>