<?php
session_start();
include '../config.php';
include '../koneksi.php';
include '../partials/head.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<body class="sb-nav-fixed">
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand ps-3" href="<?= $base_url ?>index.php">
        <img src="<?= $base_url ?>assets/logo_dinas.png" alt="Logo" width="50" height="40"> SILANDIK
    </a>
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
</nav>

<div id="layoutSidenav">
    <?php include '../sidebar.php'; ?>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">

                <!-- JUDUL UTAMA -->
                <h1 class="mt-4">Tambah Data Sekolah Inklusi</h1>

                <!-- LOGIC INSERT -->
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

                    $targetDir = __DIR__ . '/../upload/';
                    $logoFile = basename($_FILES["logo"]["name"]);
                    $targetFilePath = $targetDir . $logoFile;

                    $allowTypes = ['jpg', 'jpeg', 'png'];

                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0777, true);
                    }

                    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

                    if (in_array($fileType, $allowTypes)) {
                        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $targetFilePath)) {
                            $query = "INSERT INTO data_sekolah_inklusi 
                            (npsn, nama_sekolah, alamat, kepala_sekolah, telepon, tanggal_berdiri, website, logo_sekolah, deskripsi) 
                            VALUES ('$npsn', '$nama_sekolah', '$alamat', '$kepala_sekolah', '$telepon', '$tanggal_berdiri', '$website','$logoFile', '$deskripsi')";
                            $insert = mysqli_query($conn, $query);

                            if ($insert) {
                                echo "<script>
                                    Swal.fire({
                                        title: 'Sukses!',
                                        text: 'Data berhasil disimpan',
                                        icon: 'success',
                                        confirmButtonColor: '#198754',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        window.location.href = 'data_sekolah_inklusi.php';
                                    });
                                </script>";
                            } else {
                                echo "<script>
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: 'Gagal menyimpan data ke database',
                                        icon: 'error',
                                        confirmButtonColor: '#d33',
                                        confirmButtonText: 'OK'
                                    });
                                </script>";
                            }
                        } else {
                            echo "<script>
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Gagal mengunggah logo sekolah',
                                    icon: 'error',
                                    confirmButtonColor: '#d33',
                                    confirmButtonText: 'OK'
                                });
                            </script>";
                        }
                    } else {
                        echo "<script>
                            Swal.fire({
                                title: 'Format Tidak Didukung!',
                                text: 'Gunakan format JPG, JPEG, atau PNG',
                                icon: 'warning',
                                confirmButtonColor: '#f0ad4e',
                                confirmButtonText: 'OK'
                            });
                        </script>";
                    }
                }
                ?>

                <!-- FORM TAMBAH SEKOLAH INKLUSI -->
                <div class="card mt-3">
                    <div class="card-header">
                        <i class="fas fa-school me-1"></i> Form Sekolah Inklusi
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3"><label for="npsn">NPSN</label><input type="text" name="npsn" class="form-control" required></div>
                            <div class="mb-3"><label for="nama_sekolah">Nama Sekolah</label><input type="text" name="nama_sekolah" class="form-control" required></div>
                            <div class="mb-3"><label for="alamat">Alamat</label><input type="text" name="alamat" class="form-control" required></div>
                            <div class="mb-3"><label for="kepala_sekolah">Kepala Sekolah</label><input type="text" name="kepala_sekolah" class="form-control" required></div>
                            <div class="mb-3"><label for="telepon">Telepon</label><input type="text" name="telepon" class="form-control"></div>
                            <div class="mb-3"><label for="tanggal_berdiri">Tanggal Berdiri</label><input type="date" name="tanggal_berdiri" class="form-control"></div>
                            <div class="mb-3"><label for="website">Website Sekolah</label><input type="url" name="website" class="form-control"></div>
                            <div class="mb-3">
                                <label for="logo">Logo Sekolah</label>
                                <input type="file" name="logo" class="form-control" id="logo" accept="image/*" required onchange="previewImage(event)">
                                <div id="logoPreviewContainer" class="mt-3" style="display: none;">
                                    <h6>Preview Logo:</h6>
                                    <img id="logoPreview" src="" alt="Logo Preview" style="width: 150px; height: auto; border-radius: 5px;">
                                </div>
                            </div>
                            <div class="mb-3"><label for="deskripsi">Deskripsi</label><textarea name="deskripsi" class="form-control" id="deskripsi" rows="4" required></textarea></div>
                            <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Tambah</button>
                            <a href="data_sekolah_inklusi.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
                        </form>
                    </div>
                </div>

            </div>
        </main>
    </div>
</div>

<?php include '../partials/footer.php'; ?>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('logoPreview');
            const container = document.getElementById('logoPreviewContainer');
            output.src = reader.result;
            container.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
</body>
</html>
