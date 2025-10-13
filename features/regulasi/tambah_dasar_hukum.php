<?php
session_start();
include '../config.php';
include '../partials/head.php';
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: dasar_hukum.php");
    exit;
}

$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor_regulasi = trim($_POST['nomor_regulasi']);
    $tentang = trim($_POST['tentang']);
    $draft_hukum = null;

    // Upload file PDF jika ada
    if (isset($_FILES['draft_hukum']) && $_FILES['draft_hukum']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['draft_hukum']['tmp_name'];
        $fileName = basename($_FILES['draft_hukum']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['pdf'];

        if (in_array($fileExt, $allowed)) {
            $newName = uniqid('regulasi_', true) . '.' . $fileExt;
            $dest = "../pdfs/" . $newName;
            if (move_uploaded_file($fileTmp, $dest)) {
                $draft_hukum = $newName;
            } else {
                $error = "Gagal upload file.";
            }
        } else {
            $error = "File harus berupa PDF.";
        }
    }

    if (!$error) {
        $stmt = mysqli_prepare($conn, "INSERT INTO dasar_hukum (nomor_regulasi, tentang, draft_hukum) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $nomor_regulasi, $tentang, $draft_hukum);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Regulasi berhasil ditambahkan.";
            header("Location: dasar_hukum.php?success=add");
            exit;
        } else {
            $error = "Gagal menambah regulasi.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<body>
    <?php include '../sidebar.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Tambah Regulasi / Dasar Hukum</h1>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-plus me-1"></i> Form Tambah Regulasi
                        </div>
                        <div class="card-body">
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label">Nomor Regulasi</label>
                                    <input type="text" name="nomor_regulasi" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tentang</label>
                                    <textarea name="tentang" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Draft Regulasi (PDF, opsional)</label>
                                    <input type="file" name="draft_hukum" class="form-control" accept="application/pdf">
                                    <small class="text-muted">Format file: PDF. Maksimal 2MB.</small>
                                </div>
                                <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i> Simpan</button>
                                <a href="dasar_hukum.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php include '../partials/footer.php'; ?>
</body>
</html>