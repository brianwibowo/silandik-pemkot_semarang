<?php
include '../koneksi.php';
include '../partials/head.php';

$id = $_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM data_sekolah_inklusi WHERE no = '$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data tidak ditemukan.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_sekolah = $_POST['nama_sekolah'];
    $deskripsi = $_POST['deskripsi'];
    $logo_lama = $_POST['logo_lama'];

    if (!empty($_FILES['logo']['name'])) {
        $fileName = basename($_FILES["logo"]["name"]);
        $targetDir = __DIR__ . '/../upload/';
        $targetFilePath = $targetDir . $fileName;

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $targetFilePath)) {
            $logo = $fileName;
        } else {
            echo "Gagal mengunggah logo.";
            exit();
        }
    } else {
        $logo = $logo_lama;
    }

    $update = mysqli_query($conn, "UPDATE data_sekolah_inklusi 
        SET nama_sekolah='$nama_sekolah', logo_sekolah='$logo', deskripsi='$deskripsi' 
        WHERE no='$id'");

    if ($update) {
        header("Location: data_sekolah_inklusi.php");
        exit();
    } else {
        echo "Gagal mengupdate data.";
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
                                        <h4>Edit Data Sekolah Inklusi</h4>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST" enctype="multipart/form-data">
                                            <div class="mb-3">
                                                <label for="nama_sekolah" class="form-label">Nama Sekolah</label>
                                                <input type="text" name="nama_sekolah" class="form-control" id="nama_sekolah" value="<?= htmlspecialchars($data['nama_sekolah']); ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="logo" class="form-label">Logo Sekolah</label>
                                                <input type="file" name="logo" class="form-control" id="logo" accept="image/*" onchange="previewImage(event)">
                                                <input type="hidden" name="logo_lama" value="<?= htmlspecialchars($data['logo_sekolah']); ?>">
                                                
                                                <div id="logoPreviewContainer" class="mt-3" style="<?= $data['logo_sekolah'] ? '' : 'display: none;' ?>">
                                                    <h5>Preview Logo:</h5>
                                                    <img id="logoPreview" src="../upload/<?= $data['logo_sekolah']; ?>" alt="Logo Preview" style="width: 150px; height: auto; border-radius: 5px;">
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                                <textarea name="deskripsi" class="form-control" id="deskripsi" rows="4" required><?= htmlspecialchars($data['deskripsi']); ?></textarea>
                                            </div>

                                            <button type="submit" class="btn btn-success">Update</button>
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

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
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
