<?php
session_start();
include '../config.php';

// Ubah: pengurus juga boleh akses
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pengurus'])) {
    header("Location: ../index.php");
    exit;
}

include '../partials/head.php';
include '../koneksi.php';
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php include '../sidebar.php'; ?>

<div id="layoutSidenav">
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Edit Data Siswa</h1>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-user-edit me-1"></i>
                        Form Edit Data Siswa
                    </div>
                    <div class="card-body">

                        <?php
                        $id = (int)$_GET['id'];
                        $query = mysqli_query($conn, "SELECT * FROM data_siswa WHERE id=$id");
                        $data = mysqli_fetch_assoc($query);

                        // Ambil daftar sekolah untuk dropdown
                        $daftar_sekolah = mysqli_query($conn, "SELECT id, nama_sekolah, jenjang_sekolah FROM data_sekolah_inklusi ORDER BY nama_sekolah");

                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            $nisn = trim($_POST['nisn']);
                            $nama = trim($_POST['nama']);
                            $jenis_kelamin = $_POST['jenis_kelamin'];
                            $sekolah_id = (int)$_POST['sekolah_id'];
                            $kelas = trim($_POST['kelas']);
                            $jenis_inklusi = trim($_POST['jenis_inklusi']);

                            if ($nama && $kelas && in_array($jenis_kelamin, ['L', 'P']) && $sekolah_id > 0) {
                                // Gunakan prepared statement untuk keamanan
                                $stmt = $conn->prepare("UPDATE data_siswa SET 
                                    nisn=?, nama_siswa=?, jenis_kelamin=?, sekolah_id=?, kelas=?, jenis_inklusi=?
                                    WHERE id=?
                                ");
                                $stmt->bind_param("ssssssi", $nisn, $nama, $jenis_kelamin, $sekolah_id, $kelas, $jenis_inklusi, $id);
                                if ($stmt->execute()) {
                                    echo "
                                    <script>
                                        Swal.fire({
                                            title: 'Sukses!',
                                            text: 'Data berhasil diubah',
                                            icon: 'success',
                                            confirmButtonColor: '#198754',
                                            confirmButtonText: 'OK'
                                        }).then(() => {
                                            window.location.href = 'data_siswa.php';
                                        });
                                    </script>";
                                    exit;
                                } else {
                                    echo "<div class='alert alert-danger'>Gagal mengubah data: " . htmlspecialchars($conn->error) . "</div>";
                                }
                                $stmt->close();
                            } else {
                                echo "<div class='alert alert-danger'>Data tidak lengkap!</div>";
                            }
                            // Ambil data siswa lagi setelah update (agar form tetap terisi benar)
                            $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM data_siswa WHERE id=$id"));
                        }
                        ?>

                        <form method="POST" id="form-edit-siswa">
                            <div class="mb-3">
                                <label class="form-label">NISN</label>
                                <input type="text" name="nisn" class="form-control" maxlength="15" value="<?= htmlspecialchars($data['nisn']) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Siswa</label>
                                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama_siswa']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-control" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="L" <?= $data['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="P" <?= $data['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Asal Sekolah</label>
                                <select name="sekolah_id" id="sekolah_id" class="form-control" required onchange="updateKelasOptions()">
                                    <option value="">-- Pilih Sekolah --</option>
                                    <?php
                                    mysqli_data_seek($daftar_sekolah, 0);
                                    while ($row = mysqli_fetch_assoc($daftar_sekolah)): ?>
                                        <option value="<?= $row['id'] ?>"
                                            data-jenjang="<?= $row['jenjang_sekolah'] ?>"
                                            <?= ($row['id'] == $data['sekolah_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($row['nama_sekolah']) ?> (<?= htmlspecialchars($row['jenjang_sekolah']) ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kelas</label>
                                <select name="kelas" id="kelas" class="form-control" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    <!-- Opsi kelas akan diisi otomatis oleh JS -->
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jenis Inklusi</label>
                                <select name="jenis_inklusi" class="form-control" required>
                                    <option value="">-- Pilih Jenis Inklusi --</option>
                                    <?php
                                    $daftar_inklusi = [
                                        'A' => 'Tunanetra',
                                        'B' => 'Tunarungu',
                                        'C' => 'Tunagrahita Ringan',
                                        'C1' => 'Tunagrahita Sedang',
                                        'D' => 'Tunadaksa Ringan',
                                        'D1' => 'Tunadaksa Sedang',
                                        'E' => 'Tunalaras',
                                        'F' => 'Tunawicara',
                                        'H' => 'Hiperaktif',
                                        'I' => 'Cerdas Istimewa',
                                        'J' => 'Bakat Istimewa',
                                        'K' => 'Kesulitan Belajar',
                                        'N' => 'Narkoba',
                                        'O' => 'Indigo',
                                        'P' => 'Down Syndrome',
                                        'Q' => 'Autis',
                                        'Lain' => 'Lainnya'
                                    ];

                                    foreach ($daftar_inklusi as $kode => $label) {
                                        $selected = ($data['jenis_inklusi'] === $kode) ? 'selected' : '';
                                        echo "<option value=\"$kode\" $selected>$kode - $label</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                            <a href="data_siswa.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
                        </form>
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
                                // Set value jika sudah ada (untuk edit)
                                <?php if (!empty($data['kelas'])): ?>
                                    kelasSelect.value = "<?= htmlspecialchars($data['kelas']) ?>";
                                <?php endif; ?>
                            }
                            // Set kelas otomatis saat load
                            window.addEventListener('DOMContentLoaded', function() {
                                updateKelasOptions();
                            });
                        </script>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../partials/footer.php'; ?>
</body>
</html>