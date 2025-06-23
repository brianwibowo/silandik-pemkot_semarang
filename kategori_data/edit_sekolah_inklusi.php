<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config.php';
include '../koneksi.php';
include '../partials/head.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Ambil data sekolah
$id = $_GET['id'] ?? 0;
$query = mysqli_query($conn, "SELECT * FROM data_sekolah_inklusi WHERE id = $id");
$sekolah = mysqli_fetch_assoc($query);

if (!$sekolah) {
    die("<div class='alert alert-danger'>Data sekolah tidak ditemukan!</div>");
}
?>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="<?= $base_url ?>index.php">
            <img src="<?= $base_url ?>assets/logo_dinas.png" alt="Logo" width="50" height="40"> SILANDIK
        </a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    </nav>

    <div id="layoutSidenav">
        <?php include '../sidebar.php'; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Edit Sekolah Inklusi</h1>

                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $npsn = $_POST['npsn'];
                        $nama_sekolah = $_POST['nama_sekolah'];
                        $alamat = $_POST['alamat'];
                        $kepala_sekolah = $_POST['kepala_sekolah'];
                        $telepon = $_POST['telepon'];
                        $tanggal_berdiri = $_POST['tanggal_berdiri'];
                        $website = $_POST['website'];
                        $deskripsi = $_POST['deskripsi'];

                        $logo_file = $sekolah['logo_sekolah'];
                        if (!empty($_FILES["logo"]["name"])) {
                            $targetDir = __DIR__ . '/../upload/';
                            $logo_file = basename($_FILES["logo"]["name"]);
                            $targetFilePath = $targetDir . $logo_file;

                            if (!is_dir($targetDir)) {
                                mkdir($targetDir, 0777, true);
                            }
                            move_uploaded_file($_FILES["logo"]["tmp_name"], $targetFilePath);
                        }

                        $sql = "UPDATE data_sekolah_inklusi 
                            SET npsn = '$npsn',
                                nama_sekolah = '$nama_sekolah',
                                alamat = '$alamat',
                                kepala_sekolah = '$kepala_sekolah',
                                telepon = '$telepon',
                                tanggal_berdiri = '$tanggal_berdiri',
                                website = '$website',
                                deskripsi = '$deskripsi',
                                logo_sekolah = '$logo_file'
                            WHERE id = $id";

                        if (mysqli_query($conn, $sql)) {
                            $success_message = "Data berhasil diupdate.";
                        } else {
                            $error_message = "Gagal mengupdate data: " . mysqli_error($conn);
                        }
                    }
                    ?>

                    <div class="card mt-3">
                        <div class="card-header"><i class="fas fa-school me-1"></i> Form Edit Sekolah Inklusi</div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3"><label for="npsn">NPSN</label><input type="text" name="npsn" class="form-control" value="<?= $sekolah['npsn']; ?>" required></div>
                                <div class="mb-3"><label for="nama_sekolah">Nama Sekolah</label><input type="text" name="nama_sekolah" class="form-control" value="<?= $sekolah['nama_sekolah']; ?>" required></div>
                                <div class="mb-3"><label for="alamat">Alamat</label><input type="text" name="alamat" class="form-control" value="<?= $sekolah['alamat']; ?>" required></div>
                                <div class="mb-3"><label for="kepala_sekolah">Kepala Sekolah</label><input type="text" name="kepala_sekolah" class="form-control" value="<?= $sekolah['kepala_sekolah']; ?>"></div>
                                <div class="mb-3"><label for="telepon">Telepon</label><input type="text" name="telepon" class="form-control" value="<?= $sekolah['telepon']; ?>"></div>
                                <div class="mb-3"><label for="tanggal_berdiri">Tanggal Berdiri</label><input type="date" name="tanggal_berdiri" class="form-control" value="<?= $sekolah['tanggal_berdiri']; ?>"></div>
                                <div class="mb-3"><label for="website">Website Sekolah</label><input type="url" name="website" class="form-control" value="<?= $sekolah['website']; ?>"></div>
                                <div class="mb-3">
                                    <label for="logo">Logo Sekolah</label>
                                    <input type="file" name="logo" class="form-control" id="logo" accept="image/*" onchange="previewImage(event)">
                                    <div id="logoPreviewContainer" class="mt-3">
                                        <h6>Logo Sekolah Saat Ini:</h6>
                                        <img src="../upload/<?= $sekolah['logo_sekolah']; ?>" alt="Logo Sekolah" style="width:150px;height:auto;">
                                    </div>
                                    <div id="newLogoContainer" class="mt-3" style="display:none;">
                                        <h6>Preview Logo Baru:</h6>
                                        <img id="logoPreview" src="" alt="Logo Baru" style="width:150px;height:auto;">
                                    </div>
                                </div>
                                <div class="mb-3"><label for="deskripsi">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="4" required><?= $sekolah['deskripsi']; ?></textarea></div>

                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                                <a href="data_sekolah_inklusi.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
                            </form>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>

    <?php include '../partials/footer.php'; ?>

    <!-- Tambahan Notifikasi SweetAlert-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if (!empty($success_message)): ?>
        <script>
            Swal.fire({
                title: 'Sukses!',
                text: '<?= $success_message ?>',
                icon: 'success',
                confirmButtonColor: '#198754',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'data_sekolah_inklusi.php';
            });
        </script>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <script>
            Swal.fire({
                title: 'Error!',
                text: '<?= $error_message ?>',
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        </script>
    <?php endif; ?>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('logoPreview');
                const container = document.getElementById('newLogoContainer');
                output.src = reader.result;
                container.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>

</html>