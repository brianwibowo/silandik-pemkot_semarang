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

// Proses approve/decline/change_role
if (isset($_GET['action'], $_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    $action = $_GET['action'];
    
    if ($id && in_array($action, ['approve', 'decline', 'change_role'])) {
        if ($action === 'approve') {
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
        } elseif ($action === 'change_role' && isset($_GET['new_role'])) {
            $new_role = $_GET['new_role'];
            if (in_array($new_role, ['umum', 'pengurus', 'admin'])) {
                $stmt = mysqli_prepare($conn, "UPDATE users SET role=?, request_pengurus=0 WHERE id=?");
                mysqli_stmt_bind_param($stmt, "si", $new_role, $id);
                if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
                    $message = "Role user berhasil diubah menjadi " . ucfirst($new_role) . "!";
                    $message_type = "success";
                } else {
                    $message = "Gagal mengubah role user.";
                    $message_type = "error";
                }
                mysqli_stmt_close($stmt);
            }
        }
    } else {
        $message = "ID atau aksi tidak valid.";
        $message_type = "error";
    }
    header("Location: admin.php" . (!empty($message) ? "?msg=" . urlencode($message) . "&type=" . $message_type : ""));
    exit;
}

// Proses update sekolah dikelola pengurus
if (isset($_POST['edit_sekolah_user_id'], $_POST['edit_sekolah_id'])) {
    $edit_user_id = intval($_POST['edit_sekolah_user_id']);
    $edit_sekolah_id = intval($_POST['edit_sekolah_id']);
    // Pastikan usernya pengurus
    $cek = mysqli_query($conn, "SELECT id FROM users WHERE id=$edit_user_id AND role='pengurus'");
    if (mysqli_num_rows($cek) > 0) {
        $upd = mysqli_query($conn, "UPDATE users SET sekolah_id=$edit_sekolah_id WHERE id=$edit_user_id");
        if ($upd) {
            $message = "Sekolah yang dikelola berhasil diubah.";
            $message_type = "success";
        } else {
            $message = "Gagal mengubah sekolah yang dikelola.";
            $message_type = "error";
        }
    } else {
        $message = "User bukan pengurus!";
        $message_type = "error";
    }
}

// Tampilkan pesan jika ada
if (isset($_GET['msg']) && isset($_GET['type'])) {
    $message = htmlspecialchars($_GET['msg']);
    $message_type = htmlspecialchars($_GET['type']);
}

// Ambil semua request pengurus
$stmt_requests = mysqli_prepare($conn, "SELECT id, email, request_pengurus, role, created_at FROM users WHERE request_pengurus=1 ORDER BY created_at DESC");
mysqli_stmt_execute($stmt_requests);
$result_requests = mysqli_stmt_get_result($stmt_requests);

// Ambil semua user (tambahkan sekolah_id)
$stmt_users = mysqli_prepare($conn, "SELECT id, email, role, sekolah_id, request_pengurus, created_at FROM users ORDER BY created_at DESC");
mysqli_stmt_execute($stmt_users);
$result_users = mysqli_stmt_get_result($stmt_users);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>Admin Panel - Kelola User & Permintaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/styles.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-user-cog"></i> Admin Panel</h2>
            <div>
                <span class="badge bg-danger">Admin: <?= htmlspecialchars($_SESSION['email']) ?></span>
            </div>
        </div>
        
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="requests-tab" data-bs-toggle="tab" data-bs-target="#requests" type="button" role="tab">
                    <i class="fas fa-clipboard-list"></i> Permintaan Pengurus 
                    <span class="badge bg-danger ms-1"><?= mysqli_num_rows($result_requests) ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
                    <i class="fas fa-users"></i> Kelola User 
                    <span class="badge bg-primary ms-1"><?= mysqli_num_rows($result_users) ?></span>
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="adminTabsContent">
            <!-- Tab Permintaan Pengurus -->
            <div class="tab-pane fade show active" id="requests" role="tabpanel">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-list"></i> Daftar Permintaan Pengurus Sekolah</span>
                        <span class="badge bg-light text-dark"><?= mysqli_num_rows($result_requests) ?> permintaan</span>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($result_requests) === 0): ?>
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
                                <?php $no=1; mysqli_data_seek($result_requests, 0); while($row = mysqli_fetch_assoc($result_requests)): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <i class="fas fa-envelope text-muted"></i>
                                            <?= htmlspecialchars($row['email']) ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $current_role_color = '';
                                            switch($row['role']) {
                                                case 'admin': $current_role_color = 'bg-danger'; break;
                                                case 'pengurus': $current_role_color = 'bg-warning'; break;
                                                case 'umum': $current_role_color = 'bg-primary'; break;
                                                default: $current_role_color = 'bg-secondary';
                                            }
                                            ?>
                                            <span class="badge <?= $current_role_color ?>"><?= htmlspecialchars(ucfirst($row['role'])) ?></span>
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
            </div>

            <!-- Tab Kelola User -->
            <div class="tab-pane fade" id="users" role="tabpanel">
                <div class="card">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-users"></i> Daftar Semua User</span>
                        <span class="badge bg-light text-dark"><?= mysqli_num_rows($result_users) ?> user</span>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($result_users) === 0): ?>
                            <div class="alert alert-info text-center mb-0">
                                <i class="fas fa-info-circle"></i>
                                Tidak ada user yang terdaftar.
                            </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Email User</th>
                                        <th>Role</th>
                                        <th>Sekolah Dikelola</th>
                                        <th>Status Request</th>
                                        <th style="width: 150px;">Tanggal Daftar</th>
                                        <th style="width: 200px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $no=1; mysqli_data_seek($result_users, 0); while($row = mysqli_fetch_assoc($result_users)): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <i class="fas fa-envelope text-muted"></i>
                                            <?= htmlspecialchars($row['email']) ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $role_color = '';
                                            switch($row['role']) {
                                                case 'admin': $role_color = 'bg-danger'; break;
                                                case 'pengurus': $role_color = 'bg-warning'; break;
                                                case 'umum': $role_color = 'bg-primary'; break;
                                                default: $role_color = 'bg-secondary';
                                            }
                                            ?>
                                            <span class="badge <?= $role_color ?>"><?= htmlspecialchars(ucfirst($row['role'])) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($row['role'] === 'pengurus'): 
                                                $sid = intval($row['sekolah_id']);
                                                $qSekolah = mysqli_query($conn, "SELECT nama_sekolah FROM data_sekolah_inklusi WHERE id=$sid");
                                                $dSekolah = mysqli_fetch_assoc($qSekolah);
                                                $namaSekolah = $dSekolah ? htmlspecialchars($dSekolah['nama_sekolah']) : '<span class="text-danger">Tidak ditemukan</span>';
                                            ?>
                                                <span id="nama-sekolah-<?= $row['id'] ?>"><?= $namaSekolah ?></span>
                                                <button type="button" class="btn btn-sm btn-outline-primary ms-1" onclick="showEditSekolahModal(<?= $row['id'] ?>, <?= $sid ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['request_pengurus'] == 1): ?>
                                                <span class="badge bg-info">Pending Request</span>
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-muted small">
                                            <?= isset($row['created_at']) ? date('d/m/Y H:i', strtotime($row['created_at'])) : 'N/A' ?>
                                        </td>
                                        <td>
                                            <?php if ($row['email'] !== $_SESSION['email']): ?>
                                            <div class="dropdown">
                                                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-user-edit"></i> Ubah Role
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <?php if ($row['role'] !== 'umum'): ?>
                                                    <li><a class="dropdown-item" href="#" onclick="changeRole(<?= $row['id'] ?>, 'umum', '<?= htmlspecialchars($row['email']) ?>')">
                                                        <i class="fas fa-user text-primary"></i> Umum
                                                    </a></li>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($row['role'] !== 'pengurus'): ?>
                                                    <li><a class="dropdown-item" href="#" onclick="changeRole(<?= $row['id'] ?>, 'pengurus', '<?= htmlspecialchars($row['email']) ?>')">
                                                        <i class="fas fa-user-tie text-warning"></i> Pengurus
                                                    </a></li>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($row['role'] !== 'admin'): ?>
                                                    <li><a class="dropdown-item" href="#" onclick="changeRole(<?= $row['id'] ?>, 'admin', '<?= htmlspecialchars($row['email']) ?>')">
                                                        <i class="fas fa-user-shield text-danger"></i> Admin
                                                    </a></li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                            <?php else: ?>
                                            <span class="badge bg-secondary">Admin Aktif</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
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

    <!-- Modal Pilih Sekolah -->
    <div class="modal fade" id="modalPilihSekolah" tabindex="-1" aria-labelledby="modalPilihSekolahLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form method="post" id="formPilihSekolah">
          <input type="hidden" name="edit_sekolah_user_id" id="edit_sekolah_user_id">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalPilihSekolahLabel">Ubah Sekolah Dikelola</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label for="edit_sekolah_id" class="form-label">Pilih Sekolah</label>
                <select class="form-select" name="edit_sekolah_id" id="edit_sekolah_id" required>
                  <option value="">-- Pilih Sekolah --</option>
                  <?php
                  $qAllSekolah = mysqli_query($conn, "SELECT id, nama_sekolah FROM data_sekolah_inklusi ORDER BY nama_sekolah ASC");
                  while ($s = mysqli_fetch_assoc($qAllSekolah)) {
                      echo '<option value="'.$s['id'].'">'.htmlspecialchars($s['nama_sekolah']).'</option>';
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Simpan</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fungsi untuk konfirmasi aksi approve/decline
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

        // Fungsi untuk mengubah role user
        function changeRole(id, newRole, email) {
            const roleText = newRole === 'admin' ? 'Admin' : (newRole === 'pengurus' ? 'Pengurus' : 'Umum');
            
            Swal.fire({
                title: 'Ubah Role User',
                text: `Yakin ingin mengubah role ${email} menjadi ${roleText}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Ubah',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `?action=change_role&id=${id}&new_role=${newRole}`;
                }
            });
        }

        // Modal edit sekolah dikelola
        function showEditSekolahModal(userId, sekolahId) {
            document.getElementById('edit_sekolah_user_id').value = userId;
            document.getElementById('edit_sekolah_id').value = sekolahId;
            var modal = new bootstrap.Modal(document.getElementById('modalPilihSekolah'));
            modal.show();
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

<?php 
mysqli_stmt_close($stmt_requests); 
mysqli_stmt_close($stmt_users);
?>