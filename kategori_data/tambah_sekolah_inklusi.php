<?php 
include '../koneksi.php';
include '../partials/head.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_sekolah = htmlspecialchars($_POST['nama_sekolah']);
    $deskripsi = htmlspecialchars($_POST['deskripsi']);

    // Handle upload logo
    $targetDir = __DIR__ . '/../upload/';
    $fileName = basename($_FILES["logo"]["name"]);
    $targetFilePath = $targetDir . $fileName;  
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // Validasi file gambar
    $allowTypes = ['jpg', 'jpeg', 'png',];
    if (in_array($fileType, $allowTypes)) {
        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $targetFilePath)) {
            // Simpan data ke database
            $query = "INSERT INTO data_sekolah_inklusi (nama_sekolah, logo_sekolah, deskripsi) 
                      VALUES ('$nama_sekolah', '$fileName', '$deskripsi')";
            mysqli_query($conn, $query);

            header("Location: data_sekolah_inklusi.php");
            exit();
        } else {
            $error = "Gagal mengunggah logo sekolah.";
        }
    } else {
        $error = "Format file logo tidak didukung. Gunakan JPG, JPEG, PNG, atau GIF.";
    }
}
?>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="container mt-5">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Form Tambah Data Sekolah Inklusi</h4>
                                    </div>
                                    <div class="card-body">
                                        <?php if (isset($error)): ?>
                                            <div class="alert alert-danger"><?= $error; ?></div>
                                        <?php endif; ?>
                                        <form method="POST" enctype="multipart/form-data">
                                            <div class="mb-3">
                                                <label for="nama_sekolah" class="form-label">Nama Sekolah</label>
                                                <input type="text" name="nama_sekolah" class="form-control" id="nama_sekolah" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="logo" class="form-label">Logo Sekolah</label>
                                                <input type="file" name="logo" class="form-control" id="logo" accept="image/*" onchange="previewImage(event)" required>
                                                <div id="logoPreviewContainer" class="mt-3" style="display: none;">
                                                    <h5>Preview Logo:</h5>
                                                    <img id="logoPreview" src="" alt="Logo Preview" style="width: 150px; height: auto; border-radius: 5px;">
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                                <textarea name="deskripsi" class="form-control" id="deskripsi" rows="4" required></textarea>
                                            </div>

                                            <button type="submit" class="btn btn-success">Simpan</button>
                                            <a href="data_sekolah_inklusi.php" class="btn btn-secondary">Kembali</a>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="/js/scripts.js"></script>
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
