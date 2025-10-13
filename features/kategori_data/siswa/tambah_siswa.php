<?php
session_start();
include '../config.php';
include '../partials/head.php';
include '../koneksi.php';

// Ubah: pengurus juga boleh akses
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pengurus'])) {
    header("Location: ../index.php");
    exit;
}

// Ambil sekolah_id dari GET (jika ada)
$sekolah_id = isset($_GET['sekolah_id']) ? (int)$_GET['sekolah_id'] : 0;

// Ambil daftar sekolah untuk dropdown
$daftar_sekolah = mysqli_query($conn, "SELECT id, nama_sekolah, jenjang_sekolah FROM data_sekolah_inklusi ORDER BY nama_sekolah");
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php include '../sidebar.php'; ?>

<div id="layoutSidenav">
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Tambah Data Siswa</h1>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-user-plus me-1"></i>
                        Form Tambah Data Siswa
                    </div>
                    <div class="card-body">
                        <?php
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            $nisn = trim($_POST['nisn']);
                            $nama = trim($_POST['nama']);
                            $jenis_kelamin = $_POST['jenis_kelamin'];
                            $sekolah_id_post = (int)$_POST['sekolah_id'];
                            $kelas = trim($_POST['kelas']);
                            $jenis_inklusi = trim($_POST['jenis_inklusi']);

                            if ($nama && $kelas && in_array($jenis_kelamin, ['L', 'P']) && $sekolah_id_post > 0 && $jenis_inklusi) {
                                $query = "INSERT INTO data_siswa (sekolah_id, nisn, nama_siswa, jenis_kelamin, kelas, jenis_inklusi) 
                                          VALUES ($sekolah_id_post, '$nisn', '$nama', '$jenis_kelamin', '$kelas', '$jenis_inklusi')";
                                mysqli_query($conn, $query);

                                echo "
                                <script>
                                    Swal.fire({
                                        title: 'Sukses!',
                                        text: 'Data berhasil disimpan',
                                        icon: 'success',
                                        confirmButtonColor: '#198754',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        window.location.href = 'detail_sekolahh_inklusi.php?id=$sekolah_id_post';
                                    });
                                </script>";
                                exit;
                            } else {
                                echo "<div class='alert alert-danger'>Data tidak lengkap!</div>";
                            }
                        }
                        ?>

                        <form method="POST" id="form-tambah-siswa">
                            <div class="mb-3">
                                <label class="form-label">NISN</label>
                                <input type="text" name="nisn" class="form-control" maxlength="15">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Siswa</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-control" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Asal Sekolah</label>
                                <select name="sekolah_id" id="sekolah_id" class="form-control" required onchange="updateKelasOptions()">
                                    <option value="">-- Pilih Sekolah --</option>
                                    <?php while ($row = mysqli_fetch_assoc($daftar_sekolah)): ?>
                                        <option value="<?= $row['id'] ?>"
                                            data-jenjang="<?= $row['jenjang_sekolah'] ?>"
                                            <?= ($row['id'] == $sekolah_id) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($row['nama_sekolah']) ?> (<?= htmlspecialchars($row['jenjang_sekolah']) ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kelas</label>
                                <select name="kelas" id="kelas" class="form-control" required>
                                    <option value="">-- Pilih Kelas --</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis Inklusi</label>
                                <select name="jenis_inklusi" class="form-control" required>
                                    <option value="">-- Pilih Jenis Inklusi --</option>
                                    <option value="A">Tunanetra</option>
                                    <option value="B">Tunarungu</option>
                                    <option value="C">Tunagrahita Ringan</option>
                                    <option value="C1">Tunagrahita Sedang</option>
                                    <option value="D">Tunadaksa Ringan</option>
                                    <option value="D1">Tunadaksa Sedang</option>
                                    <option value="E">Tunalaras</option>
                                    <option value="F">Tunawicara</option>
                                    <option value="H">Hiperaktif</option>
                                    <option value="I">Cerdas Istimewa</option>
                                    <option value="J">Bakat Istimewa</option>
                                    <option value="K">Kesulitan Belajar</option>
                                    <option value="N">Narkoba</option>
                                    <option value="O">Indigo</option>
                                    <option value="P">Down Syndrome</option>
                                    <option value="Q">Autis</option>
                                    <option value="Lain">Lainnya</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Tambah</button>
                            <a href="detail_sekolahh_inklusi.php?id=<?= $sekolah_id ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<?php include '../partials/footer.php'; ?>
<script>
function updateKelasOptions() {
    const sekolahSelect = document.getElementById('sekolah_id');
    const kelasSelect = document.getElementById('kelas');
    const selected = sekolahSelect.options[sekolahSelect.selectedIndex];
    const jenjang = selected ? selected.getAttribute('data-jenjang') : '';
    let kelasOptions = '<option value="">-- Pilih Kelas --</option>';
    if (jenjang === 'PAUD') {
        kelasOptions += `<option value="PAUD">PAUD</option>`;
    } else if (jenjang === 'TK') {
        kelasOptions += `<option value="TK A">TK A</option>`;
        kelasOptions += `<option value="TK B">TK B</option>`;
    } else if (jenjang === 'SD') {
        for (let i = 1; i <= 6; i++) {
            kelasOptions += `<option value="${i}">${i}</option>`;
        }
    } else if (jenjang === 'SMP') {
        for (let i = 1; i <= 3; i++) {
            kelasOptions += `<option value="${i}">${i}</option>`;
        }
    }
    kelasSelect.innerHTML = kelasOptions;
}

window.addEventListener('DOMContentLoaded', function() {
    updateKelasOptions();
});
</script>
</body>
</html>