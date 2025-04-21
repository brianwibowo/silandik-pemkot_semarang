<?php include '../partials/head.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php include '../koneksi.php'; ?>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="/silandik-semarang/index.php">
            <img src="/silandik-semarang/logo_dinas.png" alt="Logo" width="50" height="40">SILANDIK
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
                    <h1 class="mt-4">Ubah Draft Hukum</h1>

                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $targetDir = __DIR__ . '/../pdfs/';
                        $fileType = strtolower(pathinfo($_FILES["draft"]["name"], PATHINFO_EXTENSION));
                        $fileName = basename($_FILES["draft"]["name"]);
                        $targetFile = $targetDir . $fileName;
                        $uploadOk = 1;

                        if ($fileType != "pdf") {
                            echo "<script>
                                Swal.fire('Gagal', 'Hanya file PDF yang diperbolehkan!', 'error');
                            </script>";
                            $uploadOk = 0;
                        }

                        if ($uploadOk) {
                            // Hapus file lama
                            $cek = mysqli_query($conn, "SELECT draft_hukum FROM dasar_hukum WHERE id = 1");
                            if ($row = mysqli_fetch_assoc($cek)) {
                                $oldFile = $targetDir . $row['draft_hukum'];
                                if (file_exists($oldFile)) {
                                    unlink($oldFile);
                                }
                            }

                            // Upload file baru
                            if (move_uploaded_file($_FILES["draft"]["tmp_name"], $targetFile)) {
                                if (mysqli_num_rows($cek) > 0) {
                                    mysqli_query($conn, "UPDATE dasar_hukum SET draft_hukum = '$fileName' WHERE id = 1");
                                } else {
                                    mysqli_query($conn, "INSERT INTO dasar_hukum (id, draft_hukum) VALUES (1, '$fileName')");
                                }

                                echo "<script>
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Draft hukum berhasil diperbarui.',
                                        icon: 'success',
                                        confirmButtonColor: '#198754',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        window.location.href = '/silandik-semarang/regulasi/dasar_hukum.php';
                                    });
                                </script>";
                            } else {
                                echo "<script>
                                    Swal.fire('Gagal', 'Terjadi kesalahan saat mengunggah file.', 'error');
                                </script>";
                            }
                        }
                    }
                    ?>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-upload me-1"></i>
                            Upload Draft Hukum Terbaru
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label">Upload PDF</label>
                                    <input type="file" name="draft" class="form-control" accept="application/pdf" required>
                                </div>
                                <button type="submit" class="btn btn-warning"><i class="fas fa-upload"></i> Upload Draft Baru</button>
                                <a href="/silandik-semarang/regulasi/dasar_hukum.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
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
