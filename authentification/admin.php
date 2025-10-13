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
    
    if ($id && in_array($action, ['approve', 'decline', 'change_role', 'delete'])) {
        if ($action === 'delete') {
            // Pastikan bukan menghapus diri sendiri
            if ($id != $_SESSION['user_id']) {
                $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id=? AND email != ?");
                mysqli_stmt_bind_param($stmt, "is", $id, $_SESSION['email']);
                if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
                    $message = "User berhasil dihapus!";
                    $message_type = "success";
                } else {
                    $message = "Gagal menghapus user.";
                    $message_type = "error";
                }
            } else {
                $message = "Tidak dapat menghapus akun sendiri!";
                $message_type = "error";
            }
        } elseif ($action === 'approve') {
            $stmt = mysqli_prepare($conn, "UPDATE users SET role='pengurus', request_pengurus=0 WHERE id=? AND request_pengurus=1");
            mysqli_stmt_bind_param($stmt, "i", $id);
            if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
                $message = "Permintaan berhasil disetujui!";
                $message_type = "success";
            } else {
                $message = "Gagal menyetujui permintaan atau permintaan tidak ditemukan.";
                $message_type = "error";
            }
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
    
    // Cek struktur tabel
    $check_column = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'sekolah_id'");
    
    if (mysqli_num_rows($check_column) == 0) {
        // Jika kolom belum ada, buat dulu
        mysqli_query($conn, "ALTER TABLE users ADD COLUMN sekolah_id INT DEFAULT NULL");
        mysqli_query($conn, "ALTER TABLE users ADD CONSTRAINT fk_sekolah FOREIGN KEY (sekolah_id) REFERENCES data_sekolah_inklusi(id) ON DELETE SET NULL");
    }
    
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

try {
    // Ambil semua request pengurus
    $result_requests = mysqli_query($conn, "SELECT id, email, request_pengurus, role, created_at FROM users WHERE request_pengurus=1 ORDER BY created_at DESC");
    if (!$result_requests) {
        throw new Exception(mysqli_error($conn));
    }

    // Ambil semua user
    $result_users = mysqli_query($conn, "SELECT id, email, role, request_pengurus, created_at FROM users ORDER BY created_at DESC");
    if (!$result_users) {
        throw new Exception(mysqli_error($conn));
    }
} catch (Exception $e) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '" . addslashes($e->getMessage()) . "'
            });
        });
    </script>";
}
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
    <?php
    // Set default active tab
    $activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'requests';
    ?>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-user-cog"></i> Admin Panel</h2>
            <div>
                <span class="badge bg-danger">Admin: <?= htmlspecialchars($_SESSION['email']) ?></span>
            </div>
        </div>
        
        <!-- Tab Navigation -->
        <div class="d-flex gap-2 mb-4">
            <a href="?tab=requests" class="btn <?= $activeTab === 'requests' ? 'btn-primary' : 'btn-outline-primary' ?>">
                <i class="fas fa-clipboard-list"></i> Permintaan Pengurus 
                <span class="badge <?= $activeTab === 'requests' ? 'bg-light text-dark' : 'bg-primary' ?> ms-1">
                    <?= mysqli_num_rows($result_requests) ?>
                </span>
            </a>
            <a href="?tab=users" class="btn <?= $activeTab === 'users' ? 'btn-primary' : 'btn-outline-primary' ?>">
                <i class="fas fa-users"></i> Kelola User 
                <span class="badge <?= $activeTab === 'users' ? 'bg-light text-dark' : 'bg-primary' ?> ms-1">
                    <?= mysqli_num_rows($result_users) ?>
                </span>
            </a>
        </div>

        <!-- Tab Content -->
        <div class="tab-content" id="adminTabsContent">
            <!-- Tab Permintaan Pengurus -->
            <div class="tab-pane <?= $activeTab === 'requests' ? 'd-block' : 'd-none' ?>" id="requests">
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
                                            <th>Email</th>
                                            <th>Role Saat Ini</th>
                                            <th style="width: 150px;">Tanggal Request</th>
                                            <th style="width: 200px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; while($row = mysqli_fetch_assoc($result_requests)): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td>
                                                    <i class="fas fa-user text-muted"></i>
                                                    <?= htmlspecialchars($row['email']) ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $current_role_color = match($row['role']) {
                                                        'admin' => 'bg-danger',
                                                        'pengurus' => 'bg-warning',
                                                        'umum' => 'bg-primary',
                                                        default => 'bg-secondary'
                                                    };
                                                    ?>
                                                    <span class="badge <?= $current_role_color ?>">
                                                        <?= htmlspecialchars(ucfirst($row['role'])) ?>
                                                    </span>
                                                </td>
                                                <td class="text-muted small">
                                                    <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-success btn-sm" 
                                                                onclick="confirmAction('approve', <?= $row['id'] ?>, '<?= htmlspecialchars($row['email']) ?>')">
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm" 
                                                                onclick="confirmAction('decline', <?= $row['id'] ?>, '<?= htmlspecialchars($row['email']) ?>')">
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
            <div class="tab-pane <?= $activeTab === 'users' ? 'd-block' : 'd-none' ?>" id="users">
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
                                <table class="table table-hover align-middle border-top">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="py-3" style="width: 50px;">No</th>
                                            <th class="py-3">Informasi User</th>
                                            <th class="py-3" style="width: 200px;">Status</th>
                                            <th class="py-3" style="width: 250px;">Sekolah Dikelola</th>
                                            <th class="py-3 text-center" style="width: 250px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="border-top-0">
                                        <?php $no = 1; while($row = mysqli_fetch_assoc($result_users)): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="rounded-circle bg-light p-2">
                                                                <i class="fas fa-user text-primary"></i>
                                                            </div>
                                                            <div>
                                                                <div class="fw-semibold"><?= htmlspecialchars($row['email']) ?></div>
                                                                <div class="text-muted small">
                                                                    <i class="fas fa-clock me-1"></i>
                                                                    Terdaftar: <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column gap-1">
                                                        <?php 
                                                        $role_color = match($row['role']) {
                                                            'admin' => 'bg-danger',
                                                            'pengurus' => 'bg-warning',
                                                            'umum' => 'bg-primary',
                                                            default => 'bg-secondary'
                                                        };
                                                        ?>
                                                        <span class="badge <?= $role_color ?> fw-normal">
                                                            <i class="fas <?= $row['role'] === 'admin' ? 'fa-user-shield' : ($row['role'] === 'pengurus' ? 'fa-user-tie' : 'fa-user') ?> me-1"></i>
                                                            <?= htmlspecialchars(ucfirst($row['role'])) ?>
                                                        </span>
                                                        <?php if ($row['request_pengurus'] == 1): ?>
                                                            <span class="badge bg-info fw-normal">
                                                                <i class="fas fa-clock me-1"></i>
                                                                Pending Request
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if ($row['role'] === 'pengurus'): 
                                                        // Cek apakah user sudah memiliki sekolah yang dikelola
                                                        $user_id = intval($row['id']);
                                                        try {
                                                            $qUserSekolah = mysqli_query($conn, "SELECT s.id, s.nama_sekolah 
                                                                FROM data_sekolah_inklusi s 
                                                                LEFT JOIN users u ON s.id = u.sekolah_id 
                                                                WHERE u.id = $user_id");
                                                            $dSekolah = mysqli_fetch_assoc($qUserSekolah);
                                                            
                                                            if ($dSekolah) {
                                                                $namaSekolah = htmlspecialchars($dSekolah['nama_sekolah']);
                                                                $sid = $dSekolah['id'];
                                                                ?>
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <i class="fas fa-school text-primary"></i>
                                                                    <span id="nama-sekolah-<?=$row['id']?>"><?=$namaSekolah?></span>
                                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                            onclick="showEditSekolahModal(<?=$row['id']?>, <?=$sid?>)"
                                                                            title="Ubah Sekolah">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                </div>
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <span class="text-warning">
                                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                                        Belum ditentukan
                                                                    </span>
                                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                            onclick="showEditSekolahModal(<?=$row['id']?>, 0)"
                                                                            title="Pilih Sekolah">
                                                                        <i class="fas fa-plus"></i>
                                                                    </button>
                                                                </div>
                                                                <?php
                                                            }
                                                        } catch (Exception $e) {
                                                            echo '<div class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>Error: ' . $e->getMessage() . '</div>';
                                                        }
                                                    ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">
                                                            <i class="fas fa-minus me-1"></i>
                                                            Tidak Relevan
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-2">
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
                                                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                                                    onclick="confirmDelete(<?= $row['id'] ?>, '<?= htmlspecialchars($row['email']) ?>')">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">
                                                                <i class="fas fa-user-check me-1"></i>
                                                                Admin Aktif
                                                            </span>
                                                        <?php endif; ?>
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

        // Preserve tab parameter when submitting forms
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const tabInput = document.createElement('input');
                tabInput.type = 'hidden';
                tabInput.name = 'tab';
                tabInput.value = '<?= $activeTab ?>';
                this.appendChild(tabInput);
            });
        });

        // Fungsi untuk konfirmasi hapus user
        function confirmDelete(id, email) {
            Swal.fire({
                title: 'Hapus User',
                text: `Yakin ingin menghapus user ${email}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `?action=delete&id=${id}&tab=users`;
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