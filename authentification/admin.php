<?php
session_start();
require '../koneksi.php';
include '../config.php';

// Hanya admin yang boleh akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$message = "";
$message_type = "";

// Proses approve/decline dengan prepared statement untuk keamanan
if (isset($_GET['action'], $_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    $action = $_GET['action'];
    
    if ($id && in_array($action, ['approve', 'decline'])) {
        if ($action === 'approve') {
            // Set role jadi pengurus, hapus request
            $stmt = mysqli_prepare($conn, "UPDATE users SET role='pengurus', request_pengurus=0 WHERE id=? AND request_pengurus=1");
            mysqli_stmt_bind_param($stmt, "i", $id);
            
            if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
                $message = "Permintaan berhasil disetujui!";
                $message_type = "success";
            } else {
                $message = "Gagal menyetujui permintaan atau permintaan tidak ditemukan.";
                $message_type = "error";
            }
            mysqli_stmt_close($stmt);
            
        } elseif ($action === 'decline') {
            // Tolak request, hapus request
            $stmt = mysqli_prepare($conn, "UPDATE users SET request_pengurus=0 WHERE id=? AND request_pengurus=1");
            mysqli_stmt_bind_param($stmt, "i", $id);
            
            if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
                $message = "Permintaan berhasil ditolak.";
                $message_type = "info";
            } else {
                $message = "Gagal menolak permintaan atau permintaan tidak ditemukan.";
                $message_type = "error";
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $message = "ID atau aksi tidak valid.";
        $message_type = "error";
    }
    
    // Redirect untuk prevent double action
    header("Location: admin.php" . (!empty($message) ? "?msg=" . urlencode($message) . "&type=" . $message_type : ""));
    exit;
}

// Tampilkan pesan jika ada
if (isset($_GET['msg']) && isset($_GET['type'])) {
    $message = htmlspecialchars($_GET['msg']);
    $message_type = htmlspecialchars($_GET['type']);
}

// Ambil semua request pengurus dengan prepared statement
$stmt = mysqli_prepare($conn, "SELECT id, email, request_pengurus, role, created_at FROM users WHERE request_pengurus=1 ORDER BY created_at DESC");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>Permintaan Pengurus Sekolah - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-user-cog"></i> Admin Panel - Permintaan Pengurus</h2>
            <div>
                <span class="badge bg-danger">Admin: <?= htmlspecialchars($_SESSION['email']) ?></span>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span><i class="fas fa-list"></i> Daftar Permintaan Pengurus Sekolah</span>
                <span class="badge bg-light text-dark"><?= mysqli_num_rows($result) ?> permintaan</span>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($result) === 0): ?>
                    <div class="alert alert-info text-center mb-0">
                        <i class="fas fa-info-circle"></i>
                        Tidak ada permintaan pengurus sekolah saat ini.
                    </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Email User</th>
                                <th>Role Saat Ini</th>
                                <th style="width: 150px;">Tanggal Request</th>
                                <th style="width: 200px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $no=1; while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <i class="fas fa-envelope text-muted"></i>
                                    <?= htmlspecialchars($row['email']) ?>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?= htmlspecialchars(ucfirst($row['role'])) ?></span>
                                </td>
                                <td class="text-muted small">
                                    <?= isset($row['created_at']) ? date('d/m/Y H:i', strtotime($row['created_at'])) : 'N/A' ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-success btn-sm" onclick="confirmAction('approve', <?= $row['id'] ?>, '<?= htmlspecialchars($row['email']) ?>')">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmAction('decline', <?= $row['id'] ?>, '<?= htmlspecialchars($row['email']) ?>')">
                                            <i class="fas fa-times"></i> Decline
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="../index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Beranda
            </a>
            <a href="logout.php" class="btn btn-outline-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <script>
        // Fungsi untuk konfirmasi aksi
        function confirmAction(action, id, email) {
            const actionText = action === 'approve' ? 'menyetujui' : 'menolak';
            const actionTitle = action === 'approve' ? 'Setujui Permintaan' : 'Tolak Permintaan';
            const actionIcon = action === 'approve' ? 'question' : 'warning';
            const confirmButtonColor = action === 'approve' ? '#198754' : '#dc3545';
            
            Swal.fire({
                title: actionTitle,
                text: `Yakin ingin ${actionText} permintaan dari ${email}?`,
                icon: actionIcon,
                showCancelButton: true,
                confirmButtonColor: confirmButtonColor,
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Ya, ${actionText.charAt(0).toUpperCase() + actionText.slice(1)}`,
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `?action=${action}&id=${id}`;
                }
            });
        }

        // Tampilkan pesan jika ada
        <?php if (!empty($message)): ?>
        Swal.fire({
            icon: '<?= $message_type === "success" ? "success" : ($message_type === "info" ? "info" : "error") ?>',
            title: '<?= $message_type === "success" ? "Berhasil!" : ($message_type === "info" ? "Informasi" : "Error!") ?>',
            text: '<?= addslashes($message) ?>',
            confirmButtonColor: '#198754'
        });
        <?php endif; ?>
    </script>
</body>
</html>

<?php mysqli_stmt_close($stmt); ?>