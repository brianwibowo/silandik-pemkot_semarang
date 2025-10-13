<?php
session_start();
include '../../config.php'; // Pastikan $base_url didefinisikan di sini

// Autentikasi Admin
if (($_SESSION['role'] ?? null) !== 'admin') {
    header("Location: " . ($base_url ?? '/') . "index.php");
    exit;
}

include '../../partials/head.php';
include '../../koneksi.php';

// --- BAGIAN YANG DIPERBAIKI ---

$pesan_sukses = null;
$pesan_error = null;

// PERBAIKAN 1: Definisikan path upload secara absolut dan kuat
// Ini akan membuat path menjadi /home/u346430374/domains/silandik-semarang.com/public_html/pdfs/
define('PDF_DIR', $_SERVER['DOCUMENT_ROOT'] . '/pdfs/');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Pastikan folder tujuan ada, jika tidak, coba buat.
    if (!is_dir(PDF_DIR)) {
        // Coba buat folder dengan izin yang benar
        if (!mkdir(PDF_DIR, 0755, true)) {
            $pesan_error = "Kesalahan Kritis: Gagal membuat direktori upload.";
        }
    }

    // Pastikan ada file yang di-upload dan tidak ada error
    if (isset($_FILES["draft"]) && $_FILES["draft"]["error"] == 0) {
        $file_name = basename($_FILES["draft"]["name"]);
        $target_file = PDF_DIR . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validasi tipe file
        if ($file_type != "pdf") {
            $pesan_error = "Upload Gagal: Hanya file dengan format PDF yang diperbolehkan.";
        } else {
            // PERBAIKAN 2: Gunakan Prepared Statements untuk keamanan
            $stmt_select = $conn->prepare("SELECT draft_kurikulum FROM dokumen_kurikulum_inklusi WHERE id = 1");
            $stmt_select->execute();
            $result = $stmt_select->get_result();
            
            // Hapus file lama jika ada
            if ($row = $result->fetch_assoc()) {
                $old_file_path = PDF_DIR . $row['draft_kurikulum'];
                if (file_exists($old_file_path) && !empty($row['draft_kurikulum'])) {
                    unlink($old_file_path);
                }
            }
            $stmt_select->close();

            // Pindahkan file yang baru di-upload
            if (move_uploaded_file($_FILES["draft"]["tmp_name"], $target_file)) {
                
                // Update atau Insert ke database dengan aman
                if ($result->num_rows > 0) {
                    $stmt_update = $conn->prepare("UPDATE dokumen_kurikulum_inklusi SET draft_kurikulum = ? WHERE id = 1");
                    $stmt_update->bind_param("s", $file_name);
                    $stmt_update->execute();
                    $stmt_update->close();
                } else {
                    $stmt_insert = $conn->prepare("INSERT INTO dokumen_kurikulum_inklusi (id, draft_kurikulum) VALUES (1, ?)");
                    $stmt_insert->bind_param("s", $file_name);
                    $stmt_insert->execute();
                    $stmt_insert->close();
                }

                $pesan_sukses = "Draft kurikulum berhasil diperbarui.";

            } else {
                $pesan_error = "Terjadi kesalahan saat memindahkan file. Pastikan izin folder sudah benar.";
            }
        }
    } else {
        $pesan_error = "Tidak ada file yang di-upload atau terjadi error saat upload.";
    }
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<body>
    <?php include '../../partials/sidebar.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Ubah Draft Kurikulum Inklusi</h1>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-upload me-1"></i>
                            Upload Draft Kurikulum Terbaru
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label">Pilih File PDF</label>
                                    <input type="file" name="draft" class="form-control" accept="application/pdf" required>
                                    <div class="form-text">Pastikan file yang di-upload berformat .pdf</div>
                                </div>
                                <button type="submit" class="btn btn-warning"><i class="fas fa-upload"></i> Upload Draft Baru</button>
                                <a href="dokumen_kurikulum_inklusi.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include '../../partials/footer.php'; ?>

    <script>
    <?php if ($pesan_sukses): ?>
    Swal.fire({
        title: 'Berhasil!',
        text: '<?= addslashes($pesan_sukses); ?>',
        icon: 'success',
        confirmButtonColor: '#198754',
    }).then(() => {
        window.location.href = 'dokumen_kurikulum_inklusi.php';
    });
    <?php endif; ?>

    <?php if ($pesan_error): ?>
    Swal.fire({
        title: 'Gagal!',
        text: '<?= addslashes($pesan_error); ?>',
        icon: 'error',
        confirmButtonColor: '#dc3545',
    });
    <?php endif; ?>
    </script>
</body>
</html>