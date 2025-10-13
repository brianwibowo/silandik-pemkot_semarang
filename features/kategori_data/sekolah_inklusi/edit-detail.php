<?php
session_start();
include '../config.php';
include '../partials/head.php';
include '../koneksi.php';
include '../sidebar.php';

// Ubah: pengurus juga boleh akses
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'pengurus'])) {
    header("Location: ../index.php");
    exit;
}

function escape($value) {
    return htmlspecialchars(trim($value));
}

$sekolah_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($sekolah_id <= 0) {
    echo "<script>alert('ID sekolah tidak valid'); window.location.href = 'data_sekolah_inklusi.php';</script>";
    exit;
}

// Ambil data untuk prainput
$dataRekap = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM rekap WHERE sekolah_id = $sekolah_id"));
$dataPrasarana = mysqli_query($conn, "SELECT * FROM prasarana WHERE sekolah_id = $sekolah_id");
$dataGaleri = mysqli_query($conn, "SELECT * FROM galeri WHERE sekolah_id = $sekolah_id ORDER BY id DESC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pegawai = (int)$_POST['jumlah_pegawai'];
    $rombel = (int)$_POST['jumlah_rombel'];
    $cekRekap = mysqli_query($conn, "SELECT id FROM rekap WHERE sekolah_id = $sekolah_id");
    if (mysqli_num_rows($cekRekap) > 0) {
        mysqli_query($conn, "UPDATE rekap SET jumlah_pegawai=$pegawai, jumlah_rombel=$rombel WHERE sekolah_id = $sekolah_id");
    } else {
        mysqli_query($conn, "INSERT INTO rekap (sekolah_id, jumlah_pegawai, jumlah_rombel) VALUES ($sekolah_id, $pegawai, $rombel)");
    }

    mysqli_query($conn, "DELETE FROM prasarana WHERE sekolah_id = $sekolah_id");
    if (!empty($_POST['jenis_prasarana'])) {
        foreach ($_POST['jenis_prasarana'] as $i => $jenis) {
            $jml = (int)$_POST['jumlah_prasarana'][$i];
            $jenis = escape($jenis);
            if ($jenis !== '' && $jml > 0) {
                mysqli_query($conn, "INSERT INTO prasarana (sekolah_id, jenis_prasarana, jumlah) VALUES ($sekolah_id, '$jenis', $jml)");
            }
        }
    }

    if (!empty($_POST['hapus_galeri'])) {
        foreach ($_POST['hapus_galeri'] as $galeri_id) {
            $galeri_id = (int)$galeri_id;
            $result = mysqli_query($conn, "SELECT path_gambar FROM galeri WHERE id = $galeri_id AND sekolah_id = $sekolah_id");
            if ($row = mysqli_fetch_assoc($result)) {
                $file_path = '../upload/galeri/' . $row['path_gambar'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            mysqli_query($conn, "DELETE FROM galeri WHERE id = $galeri_id AND sekolah_id = $sekolah_id");
        }
    }

    if (!empty($_FILES['gambar']['tmp_name'])) {
        foreach ($_FILES['gambar']['tmp_name'] as $i => $tmp) {
            if (!empty($tmp)) {
                $namaFile = time() . '_' . basename($_FILES['gambar']['name'][$i]);
                $targetPath = '../upload/galeri/' . $namaFile;
                if (!is_dir('../upload/galeri')) {
                    mkdir('../upload/galeri', 0777, true);
                }
                move_uploaded_file($tmp, $targetPath);
                $judul = escape($_POST['judul_gambar'][$i]);
                mysqli_query($conn, "INSERT INTO galeri (sekolah_id, path_gambar, judul) VALUES ($sekolah_id, '$namaFile', '$judul')");
            }
        }
    }

    echo "<script>alert('Data berhasil diperbarui'); window.location.href = 'detail_sekolahh_inklusi.php?id=$sekolah_id';</script>";
    exit;
}
?>

<div class="container mt-4">
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-edit"></i> Edit Rekap, Prasarana, dan Galeri</h1>
        <p class="page-subtitle">Perbarui data tambahan untuk sekolah ini.</p>
    </div>

    <a href="tambah_siswa.php?sekolah_id=<?= $sekolah_id ?>" class="btn btn-success mb-3">
        <i class="fas fa-plus"></i> Tambah Siswa
    </a>

    <div class="form-container">
        <div class="form-header">
            <h5><i class="fas fa-info-circle"></i> Form Edit Data</h5>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-body">
                <div class="form-row">
                    <div>
                        <label class="form-label">Jumlah Pegawai</label>
                        <input type="number" name="jumlah_pegawai" class="form-control" value="<?= $dataRekap['jumlah_pegawai'] ?? '' ?>">
                    </div>
                    <div>
                        <label class="form-label">Jumlah Rombel</label>
                        <input type="number" name="jumlah_rombel" class="form-control" value="<?= $dataRekap['jumlah_rombel'] ?? '' ?>">
                    </div>
                </div>

                <div class="form-row-full">
                    <label class="form-label">Prasarana <button type="button" onclick="addPrasarana()" class="btn-custom btn-secondary btn-sm">Tambah</button></label>
                    <div id="prasarana-container">
                        <?php if (mysqli_num_rows($dataPrasarana) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($dataPrasarana)): ?>
                                <div class="form-row">
                                    <div><input name="jenis_prasarana[]" class="form-control" value="<?= htmlspecialchars($row['jenis_prasarana']) ?>" placeholder="Jenis Prasarana"></div>
                                    <div><input name="jumlah_prasarana[]" class="form-control" value="<?= $row['jumlah'] ?>" placeholder="Jumlah" type="number"></div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="form-row">
                                <div><input name="jenis_prasarana[]" class="form-control" placeholder="Jenis Prasarana"></div>
                                <div><input name="jumlah_prasarana[]" class="form-control" placeholder="Jumlah" type="number"></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-row-full">
                    <label class="form-label">Galeri <button type="button" onclick="addGaleri()" class="btn-custom btn-secondary btn-sm">Tambah</button></label>
                    <div id="galeri-container">
                        <div class="form-row">
                            <div><input type="file" name="gambar[]" class="form-control"></div>
                            <div><input name="judul_gambar[]" class="form-control" placeholder="Judul"></div>
                        </div>
                    </div>
                    <div class="form-row-full">
                        <label class="form-label mt-3">Galeri Saat Ini</label>
                        <div class="galeri-preview-grid">
                            <?php mysqli_data_seek($dataGaleri, 0); while ($galeri = mysqli_fetch_assoc($dataGaleri)): ?>
                                <div class="galeri-preview-item">
                                    <img src="../upload/galeri/<?= htmlspecialchars($galeri['path_gambar']) ?>" alt="<?= htmlspecialchars($galeri['judul']) ?>" class="galeri-thumb">
                                    <div class="galeri-caption"><?= htmlspecialchars($galeri['judul']) ?></div>
                                    <div class="text-center mt-1">
                                        <label style="font-size:12px; color:red;">
                                            <input type="checkbox" name="hapus_galeri[]" value="<?= $galeri['id'] ?>"> Hapus
                                        </label>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button class="btn-custom btn-primary" type="submit"><i class="fas fa-save"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<style>
.galeri-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 10px;
    margin-top: 10px;
}
.galeri-preview-item {
    border: 1px solid #ddd;
    padding: 5px;
    text-align: center;
    background: #f8f9fa;
    border-radius: 6px;
}
.galeri-thumb {
    max-width: 100%;
    height: 80px;
    object-fit: cover;
}
.galeri-caption {
    font-size: 12px;
    color: #555;
    margin-top: 4px;
}
</style>

<script>
function addPrasarana() {
    const container = document.getElementById('prasarana-container');
    container.insertAdjacentHTML('beforeend', `
        <div class="form-row">
            <div><input name="jenis_prasarana[]" class="form-control" placeholder="Jenis Prasarana"></div>
            <div><input name="jumlah_prasarana[]" class="form-control" placeholder="Jumlah" type="number"></div>
        </div>
    `);
}

function addGaleri() {
    const container = document.getElementById('galeri-container');
    container.insertAdjacentHTML('beforeend', `
        <div class="form-row">
            <div><input type="file" name="gambar[]" class="form-control"></div>
            <div><input name="judul_gambar[]" class="form-control" placeholder="Judul"></div>
        </div>
    `);
}
</script>