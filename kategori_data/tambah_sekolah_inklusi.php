<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Login - SB Admin</title>
    <link href="/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

</head>

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
                                        <form action="proses_tambah.php" method="POST" enctype="multipart/form-data">
                                            <div class="mb-3">
                                                <label for="nama" class="form-label">Nama Sekolah</label>
                                                <input type="text" name="nama" class="form-control" id="nama" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="logo" class="form-label">Logo Sekolah</label>
                                                <input type="file" name="logo" class="form-control" id="logo" accept="image/*">
                                            </div>

                                            <div class="mb-3">
                                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                                <textarea name="deskripsi" class="form-control" id="deskripsi" rows="4" required></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label for="kecamatan" class="form-label">Kecamatan</label>
                                                <input type="text" name="kecamatan" class="form-control" id="kecamatan">
                                            </div>

                                            <div class="mb-3">
                                                <label for="jenjang" class="form-label">Jenjang Sekolah</label>
                                                <select name="jenjang" id="jenjang" class="form-select">
                                                    <option value="">-- Pilih Jenjang --</option>
                                                    <option value="TK">TK</option>
                                                    <option value="SD">SD</option>
                                                    <option value="SMP">SMP</option>
                                                    <option value="SMA">SMA</option>
                                                    <option value="SMK">SMK</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status</label>
                                                <select name="status" id="status" class="form-select">
                                                    <option value="">-- Pilih Status --</option>
                                                    <option value="Negeri">Negeri</option>
                                                    <option value="Swasta">Swasta</option>
                                                </select>
                                            </div>

                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-save"></i> Simpan Data
                                            </button>
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
</body>
</html>