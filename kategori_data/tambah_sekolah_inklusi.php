<?php
session_start();
include '../config.php';
include '../koneksi.php';
include '../partials/head.php';

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pengurus'])) {
    header("Location: index.php"); exit;
}
?>

<div id="background">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php include '../sidebar.php'; ?>

<div id="layoutSidenav">
    <div id="layoutSidenav_content">
        <main class="main-container">
            <div class="container-fluid px-4">
                <!-- Page Header -->
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fas fa-plus-circle"></i>
                        Tambah Data Sekolah Inklusi
                    </h1>
                    <p class="page-subtitle">Lengkapi formulir di bawah ini untuk menambahkan data sekolah inklusi baru</p>
                </div>

                <!-- Form Container -->
                <div class="form-container">
                    <div class="form-header">
                        <h5>
                            <i class="fas fa-school"></i>
                            Informasi Sekolah Inklusi
                        </h5>
                    </div>

                    <form method="POST" enctype="multipart/form-data" id="schoolForm">
                        <div class="form-body">
                            <!-- NPSN & Nama Sekolah -->
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label required-field" for="npsn">NPSN</label>
                                    <input type="text" name="npsn" id="npsn" class="form-control"
                                        placeholder="Contoh: 20123456" required>
                                    <div class="form-hint">Nomor Pokok Sekolah Nasional</div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label required-field" for="nama_sekolah">Nama Sekolah</label>
                                    <input type="text" name="nama_sekolah" id="nama_sekolah" class="form-control"
                                        placeholder="Contoh: SD Negeri 1 Jakarta" required>
                                </div>
                            </div>

                            <!-- Jenjang Sekolah -->
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label required-field" for="jenjang_sekolah">Jenjang Sekolah</label>
                                    <select name="jenjang_sekolah" id="jenjang_sekolah" class="form-control" required>
                                        <option value="">Pilih Jenjang Sekolah</option>
                                        <option value="PAUD">PAUD</option>
                                        <option value="TK">TK </option>
                                        <option value="SD">SD </option>
                                        <option value="SMP">SMP </option>
                                    </select>
                                    <div class="form-hint">Pilih jenjang pendidikan sekolah</div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label required-field" for="kepala_sekolah">Kepala Sekolah</label>
                                    <input type="text" name="kepala_sekolah" id="kepala_sekolah" class="form-control"
                                        placeholder="Nama lengkap kepala sekolah" required>
                                </div>
                            </div>

                            <!-- Alamat -->
                            <div class="form-row-full">
                                <div class="form-group">
                                    <label class="form-label required-field" for="alamat">Alamat Lengkap</label>
                                    <input type="text" name="alamat" id="alamat" class="form-control"
                                        placeholder="Jl. Pendidikan No. 123, Kelurahan, Kecamatan, Kota" required>
                                </div>
                            </div>

                            <!-- Telepon & Website -->
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label" for="telepon">Nomor Telepon</label>
                                    <input type="tel" name="telepon" id="telepon" class="form-control"
                                        placeholder="(021) 1234567 atau 08123456789">
                                    <div class="form-hint">Nomor telepon sekolah (opsional)</div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="website">Website Sekolah</label>
                                    <input type="url" name="website" id="website" class="form-control"
                                        placeholder="https://www.sekolah.sch.id">
                                    <div class="form-hint">URL lengkap website sekolah (opsional)</div>
                                </div>
                            </div>

                            <!-- Tanggal Berdiri -->
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label" for="tanggal_berdiri">Tanggal Berdiri</label>
                                    <input type="date" name="tanggal_berdiri" id="tanggal_berdiri" class="form-control">
                                    <div class="form-hint">Tanggal berdiri sekolah (opsional)</div>
                                </div>
                            </div>

                            <!-- Logo Upload -->
                            <div class="form-row-full">
                                <div class="form-group">
                                    <label class="form-label" for="logo_sekolah">Logo Sekolah</label>
                                    <div class="file-input-custom" id="fileInputCustom">
                                        <div class="file-input-icon">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                        </div>
                                        <div class="file-input-text">
                                            <strong>Klik untuk memilih file</strong> atau drag & drop
                                            <br><small>Format: JPG, JPEG, PNG (Maks. 2MB)</small>
                                        </div>
                                        <input type="file" name="logo_sekolah" id="logo_sekolah" accept="image/*" onchange="previewImage(event)">
                                    </div>
                                    <div class="preview-container" id="previewContainer" style="display: none;">
                                        <img id="logoPreview" class="preview-image" alt="Preview Logo">
                                        <button type="button" class="remove-preview" onclick="removePreview()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="form-hint">Upload logo sekolah (opsional)</div>
                                </div>
                            </div>

                            <!-- Deskripsi -->
                            <div class="form-row-full">
                                <div class="form-group">
                                    <label class="form-label" for="deskripsi">Deskripsi Sekolah</label>
                                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="5"
                                        placeholder="Jelaskan tentang sekolah, program inklusi, fasilitas, keunggulan, dan layanan khusus yang dimiliki untuk mendukung pendidikan inklusi..."></textarea>
                                    <div class="form-hint">Deskripsi sekolah dan program inklusi (opsional)</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="data_sekolah_inklusi.php" class="btn-custom btn-secondary">
                                <i class="fas fa-times"></i>
                                Batal
                            </a>
                            <button type="submit" class="btn-custom btn-primary">
                                <i class="fas fa-save"></i>
                                Simpan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner">
        <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
        <p>Menyimpan data...</p>
    </div>
</div>

<?php
// Logic INSERT - Disesuaikan dengan struktur tabel database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $npsn = mysqli_real_escape_string($conn, $_POST['npsn']);
    $nama_sekolah = mysqli_real_escape_string($conn, $_POST['nama_sekolah']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $kepala_sekolah = mysqli_real_escape_string($conn, $_POST['kepala_sekolah']);
    $telepon = !empty($_POST['telepon']) ? mysqli_real_escape_string($conn, $_POST['telepon']) : NULL;
    $tanggal_berdiri = !empty($_POST['tanggal_berdiri']) ? $_POST['tanggal_berdiri'] : NULL;
    $website = !empty($_POST['website']) ? mysqli_real_escape_string($conn, $_POST['website']) : NULL;
    $deskripsi = !empty($_POST['deskripsi']) ? mysqli_real_escape_string($conn, $_POST['deskripsi']) : NULL;
    $jenjang_sekolah = mysqli_real_escape_string($conn, $_POST['jenjang_sekolah']);

    $logo_sekolah = NULL;

    // Handle file upload jika ada
    if (isset($_FILES["logo_sekolah"]) && $_FILES["logo_sekolah"]["error"] == 0) {
        $targetDir = __DIR__ . '/../upload/';
        $logoFile = basename($_FILES["logo_sekolah"]["name"]);
        
        $allowTypes = ['jpg', 'jpeg', 'png'];

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileType = strtolower(pathinfo($logoFile, PATHINFO_EXTENSION));
        $fileSize = $_FILES["logo_sekolah"]["size"];

        // Validate file size (2MB = 2097152 bytes)
        if ($fileSize > 2097152) {
            echo "<script>
                Swal.fire({
                    title: 'File Terlalu Besar!',
                    text: 'Ukuran file maksimal 2MB',
                    icon: 'warning',
                    confirmButtonColor: '#f0ad4e',
                    confirmButtonText: 'OK'
                });
            </script>";
            exit;
        } elseif (in_array($fileType, $allowTypes)) {
            // Generate unique filename to prevent conflicts
            $uniqueFileName = time() . '_' . $logoFile;
            $targetFilePath = $targetDir . $uniqueFileName;
            
            if (move_uploaded_file($_FILES["logo_sekolah"]["tmp_name"], $targetFilePath)) {
                $logo_sekolah = $uniqueFileName;
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
                exit;
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
            exit;
        }
    }

    // Query INSERT sesuai dengan struktur tabel
    $query = "INSERT INTO data_sekolah_inklusi 
    (npsn, nama_sekolah, alamat, kepala_sekolah, telepon, tanggal_berdiri, website, logo_sekolah, deskripsi, jenjang_sekolah) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssssssssss", 
        $npsn, $nama_sekolah, $alamat, $kepala_sekolah, $telepon, 
        $tanggal_berdiri, $website, $logo_sekolah, $deskripsi, $jenjang_sekolah
    );
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
            Swal.fire({
                title: 'Berhasil!',
                text: 'Data sekolah inklusi berhasil ditambahkan',
                icon: 'success',
                confirmButtonColor: '#667eea',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'data_sekolah_inklusi.php?success=add';
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                title: 'Gagal!',
                text: 'Gagal menyimpan data ke database: " . mysqli_error($conn) . "',
                icon: 'error',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
        </script>";
    }
    
    mysqli_stmt_close($stmt);
}
?>

<?php include '../partials/footer.php'; ?>

<script>
    // Preview image function
    function previewImage(event) {
        const file = event.target.files[0];
        const fileInputCustom = document.getElementById('fileInputCustom');
        const previewContainer = document.getElementById('previewContainer');
        const logoPreview = document.getElementById('logoPreview');

        if (file) {
            // Validate file size (2MB)
            if (file.size > 2097152) {
                Swal.fire({
                    title: 'File Terlalu Besar!',
                    text: 'Ukuran file maksimal 2MB',
                    icon: 'warning',
                    confirmButtonColor: '#f0ad4e',
                    confirmButtonText: 'OK'
                });
                event.target.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                logoPreview.src = e.target.result;
                previewContainer.style.display = 'block';
                fileInputCustom.classList.add('has-file');
                fileInputCustom.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    }

    // Remove preview function
    function removePreview() {
        const fileInput = document.getElementById('logo_sekolah');
        const fileInputCustom = document.getElementById('fileInputCustom');
        const previewContainer = document.getElementById('previewContainer');
        
        fileInput.value = '';
        previewContainer.style.display = 'none';
        fileInputCustom.classList.remove('has-file');
        fileInputCustom.style.display = 'block';
        
        // Reset file input text
        const fileInputText = fileInputCustom.querySelector('.file-input-text');
        fileInputText.innerHTML = `<strong>Klik untuk memilih file</strong> atau drag & drop<br><small>Format: JPG, JPEG, PNG (Maks. 2MB)</small>`;
    }

    // Auto-update school name based on jenjang
    document.getElementById('jenjang_sekolah').addEventListener('change', function() {
        const jenjang = this.value;
        const namaSekolahInput = document.getElementById('nama_sekolah');
        if (jenjang === 'PAUD') {
            namaSekolahInput.placeholder = "Contoh: PAUD Mawar";
        } else if (jenjang === 'TK') {
            namaSekolahInput.placeholder = "Contoh: TK Bina Anak";
        } else if (jenjang === 'SD') {
            namaSekolahInput.placeholder = "Contoh: SD Negeri 1 Jakarta";
        } else if (jenjang === 'SMP') {
            namaSekolahInput.placeholder = "Contoh: SMP Negeri 1 Jakarta";
        } else {
            namaSekolahInput.placeholder = "Contoh: Nama Sekolah";
        }
    });

    // Form validation
    document.getElementById('schoolForm').addEventListener('submit', function(e) {
        // Show loading overlay
        document.getElementById('loadingOverlay').style.display = 'flex';
    });

    // Phone number formatting
    document.getElementById('telepon').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.startsWith('08')) {
            // Format: 0812-3456-7890
            value = value.replace(/(\d{4})(\d{4})(\d{4})/, '$1-$2-$3');
        } else if (value.startsWith('021') || value.startsWith('022')) {
            // Format: (021) 1234-5678
            value = value.replace(/(\d{3})(\d{4})(\d{4})/, '($1) $2-$3');
        }
        e.target.value = value;
    });

    // NPSN validation
    document.getElementById('npsn').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 8) {
            value = value.substring(0, 8);
        }
        e.target.value = value;
    });

    // Drag and drop functionality
    const fileInputCustom = document.getElementById('fileInputCustom');
    const fileInput = document.getElementById('logo_sekolah');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        fileInputCustom.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        fileInputCustom.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        fileInputCustom.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        fileInputCustom.style.borderColor = '#667eea';
        fileInputCustom.style.background = '#f0f4ff';
    }

    function unhighlight(e) {
        fileInputCustom.style.borderColor = '';
        fileInputCustom.style.background = '';
    }

    fileInputCustom.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files.length > 0) {
            fileInput.files = files;
            previewImage({
                target: {
                    files: files
                }
            });
        }
    }
</script>

<style>
/* Additional styles for new elements */
.preview-container {
    position: relative;
    margin-top: 10px;
}

.preview-image {
    max-width: 200px;
    max-height: 200px;
    border-radius: 8px;
    border: 2px solid #e9ecef;
    object-fit: cover;
}

.remove-preview {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 12px;
}

.remove-preview:hover {
    background: #c82333;
}

.file-input-custom.has-file {
    border-color: #28a745;
    background-color: #f8fff9;
}

.form-group select.form-control {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loading-spinner {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    text-align: center;
    color: #667eea;
}
</style>

</body>
</html>