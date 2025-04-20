<?php include '../koneksi.php'; ?>

<?php
$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM data_siswa WHERE id=$id");
$data = mysqli_fetch_assoc($query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $asal_sekolah = $_POST['asal_sekolah'];
    $kelas = $_POST['kelas'];
    $alamat = $_POST['alamat'];

    mysqli_query($koneksi, "UPDATE data_siswa SET nama='$nama', asal_sekolah='$asal_sekolah', kelas='$kelas', alamat='$alamat' WHERE id=$id");

    header("Location: data_siswa.php");
}
?>

<?php include '../partials/head.php'; ?>
<body>
    <div class="container mt-4">
        <h2>Edit Data Siswa</h2>
        <form method="POST">
            <div class="mb-3">
                <label>Nama</label>
                <input type="text" name="nama" class="form-control" value="<?= $data['nama'] ?>" required>
            </div>
            <div class="mb-3">
                <label>Asal Sekolah</label>
                <input type="text" name="asal_sekolah" class="form-control" value="<?= $data['asal_sekolah'] ?>" required>
            </div>
            <div class="mb-3">
                <label>Kelas</label>
                <input type="text" name="kelas" class="form-control" value="<?= $data['kelas'] ?>" required>
            </div>
            <div class="mb-3">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control" required><?= $data['alamat'] ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="data_siswa.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>
