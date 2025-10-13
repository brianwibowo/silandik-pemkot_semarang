<?php
session_start();
include '../../config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include '../../partials/head.php';
include '../../koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: dasar_hukum.php");
    exit;
}

// Ambil data regulasi
$q = mysqli_query($conn, "SELECT * FROM dasar_hukum WHERE id = $id");
$data = mysqli_fetch_assoc($q);
if (!$data) {
    header("Location: dasar_hukum.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomor_regulasi = trim($_POST['nomor_regulasi']);
    $tentang = trim($_POST['tentang']);
    $draft_hukum = $data['draft_hukum'];

    // Jika ada file baru diupload
    if (isset($_FILES['draft_hukum']) && $_FILES['draft_hukum']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['draft_hukum']['tmp_name'];
        $fileName = basename($_FILES['draft_hukum']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['pdf'];

        if (in_array($fileExt, $allowed)) {
            $newName = uniqid('regulasi_', true) . '.' . $fileExt;
            $dest = "../../../pdfs/" . $newName;
            if (move_uploaded_file($fileTmp, $dest)) {
                // Hapus file lama jika ada
                if ($draft_hukum && file_exists("../../../pdfs/" . $draft_hukum)) {
                    unlink("../../../pdfs/" . $draft_hukum);
                }
                $draft_hukum = $newName;
            } else {
                $error = "Gagal upload file.";
            }
        } else {
            $error = "File harus berupa PDF.";
        }
    }

    if (!$error) {
        $stmt = mysqli_prepare($conn, "UPDATE dasar_hukum SET nomor_regulasi=?, tentang=?, draft_hukum=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssi", $nomor_regulasi, $tentang, $draft_hukum, $id);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Regulasi berhasil diperbarui.',
                    icon: 'success',
                    confirmButtonColor: '#198754',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'dasar_hukum.php';
                });
            </script>";
            exit;
        } else {
            $error = "Gagal memperbarui regulasi.";
        }
        mysqli_stmt_close($stmt);
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
                    <h1 class="mt-4">Edit Regulasi / Dasar Hukum</h1>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-edit me-1"></i> Form Edit Regulasi
                        </div>
                        <div class="card-body">
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label">Nomor Regulasi</label>
                                    <input type="text" name="nomor_regulasi" class="form-control" value="<?= htmlspecialchars($data['nomor_regulasi']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tentang</label>
                                    <textarea name="tentang" class="form-control" rows="3" required><?= htmlspecialchars($data['tentang']) ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Draft Regulasi (PDF, opsional)</label>
                                    <input type="file" name="draft_hukum" class="form-control" accept="application/pdf">
                                    <?php if ($data['draft_hukum']): ?>
                                        <div class="mt-2">
                                            <a href="../../../pdfs<?= htmlspecialchars($data['draft_hukum']) ?>" target="_blank" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> Lihat Draft Lama
                                            </a>
                                            <a href=" <?= htmlspecialchars($data['draft_hukum']) ?>" download class="btn btn-sm btn-success">
                                                <i class="fas fa-download"></i> Unduh Draft Lama
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <small class="text-muted d-block">Kosongkan jika tidak ingin mengubah file. Format file: PDF.</small>
                                </div>
                                <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
                                <a href="dasar_hukum.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php include '../../partials/footer.php'; ?>
</body>
</html>