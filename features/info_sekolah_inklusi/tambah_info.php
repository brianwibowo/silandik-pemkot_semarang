<?php
session_start();
include '../../config.php';
include '../../partials/head.php';
include '../../koneksi.php';

// Akses khusus admin atau pengurus
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pengurus'])) {
    header('Location: ../../authentification/login.php');
    exit;
}

$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sekolah_id = $_POST['sekolah_id'];
    $nama_kegiatan = trim($_POST['nama_kegiatan']);
    $tanggal = $_POST['tanggal'];
    $foto = '';

    if (!$sekolah_id || !$nama_kegiatan || !$tanggal) {
        $error = "Semua field wajib diisi.";
    } else {
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['foto']['tmp_name'];
            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $namaFile = uniqid() . '_' . time() . '.' . $ext;
            $target = '../../upload/info_sekolah/' . $namaFile;

            if (move_uploaded_file($tmp, $target)) {
                $foto = $namaFile;
            } else {
                $error = "Gagal mengupload gambar.";
            }
        }

        if (empty($error)) {
            $stmt = $conn->prepare("INSERT INTO info_sekolah_inklusi (sekolah_id, nama_kegiatan, foto, tanggal) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $sekolah_id, $nama_kegiatan, $foto, $tanggal);
            if ($stmt->execute()) {
                $_SESSION['flash_message'] = 'success_add';
                header('Location: informasi_sekolah_inklusi.php');
                exit;
            } else {
                $_SESSION['flash_message'] = 'error_add';
                header('Location: informasi_sekolah_inklusi.php');
                exit;
            }
        }
    }
}

// Ambil daftar sekolah untuk dropdown
$sekolahList = mysqli_query($conn, "SELECT id, nama_sekolah FROM data_sekolah_inklusi ORDER BY nama_sekolah ASC");
?>

<body>
    <?php include '../../partials/sidebar.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Tambah Informasi Sekolah Inklusi</h1>

                    <?php if ($success): ?>
                        <div class="alert alert-success">✅ <?= $success ?></div>
                    <?php elseif ($error): ?>
                        <div class="alert alert-danger">❌ <?= $error ?></div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="sekolah_id" class="form-label">Nama Sekolah</label>
                                    <select name="sekolah_id" id="sekolah_id" class="form-select" required>
                                        <option value="">-- Pilih Sekolah --</option>
                                        <?php while ($s = mysqli_fetch_assoc($sekolahList)) : ?>
                                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama_sekolah']) ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="nama_kegiatan" class="form-label">Nama Kegiatan</label>
                                    <input type="text" name="nama_kegiatan" id="nama_kegiatan" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="tanggal" class="form-label">Tanggal</label>
                                    <input type="date" name="tanggal" id="tanggal" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="foto" class="form-label">Foto Dokumentasi</label>
                                    <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
                                </div>

                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                                <a href="informasi_sekolah_inklusi.php" class="btn btn-secondary">Batal</a>
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











