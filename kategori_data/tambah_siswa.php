<?php include '../koneksi.php'; ?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $asal_sekolah = $_POST['asal_sekolah'];
    $kelas = $_POST['kelas'];
    $alamat = $_POST['alamat'];

    $query = "INSERT INTO data_siswa (nama, asal_sekolah, kelas, alamat) VALUES ('$nama', '$asal_sekolah', '$kelas', '$alamat')";
    mysqli_query($koneksi, $query);

    header("Location: data_siswa.php");
}
?>

<?php include '../partials/head.php'; ?>
<body>
    <div class="container mt-4">
        <h2>Tambah Data Siswa</h2>
        <form method="POST">
            <div class="mb-3">
                <label>Nama</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Asal Sekolah</label>
                <input type="text" name="asal_sekolah" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Kelas</label>
                <input type="text" name="kelas" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="data_siswa.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html>
