<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username']) || $_SESSION['level'] != 'Admin') {
    header("location: login.php");
    exit();
}

// Proses Update Status Laporan
if (isset($_POST['update_status'])) {
    $id_laporan  = intval($_POST['id_laporan']);
    $status_baru = mysqli_real_escape_string($conn, $_POST['status']);

    mysqli_query($conn, "UPDATE ht_laporan SET status = '$status_baru' WHERE id_laporan = '$id_laporan'");
    echo "<script>alert('Status laporan berhasil diperbarui!'); window.location='laporan_admin.php';</script>";
    exit();
}

$query_laporan = mysqli_query($conn, "SELECT l.*, u.nama AS nama_user, u.nohp 
    FROM ht_laporan l
    JOIN ht_user u ON l.id_user = u.id_user
    ORDER BY l.id_laporan DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Kendala - RENT-HT AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="admin_dashboard.php"><i class="bi bi-broadcast me-2 text-warning"></i>RENT-HT AI <span class="fs-6 fw-normal text-muted">| Kelola Kendala</span></a>
            <div class="navbar-nav ms-auto gap-2">
                <a class="nav-link" href="admin_dashboard.php">Dashboard</a>
                <a class="nav-link" href="verifikasi_pembayaran.php">Verifikasi Pembayaran</a>
                <a class="nav-link active" href="laporan_admin.php">Kendala Masuk</a>
                <a class="nav-link text-danger fw-bold" href="login.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-4 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>Daftar Kendala & Laporan Perangkat HT</h4>
                <p class="text-muted small mb-0">Tinjau dan perbarui penanganan keluhan perangkat dari pelanggan.</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm p-4 bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle small table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Pelanggan</th>
                            <th>No. WhatsApp</th>
                            <th>Judul Kendala</th>
                            <th>Deskripsi Detail</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th class="text-center" style="width: 170px;">Aksi Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if (mysqli_num_rows($query_laporan) > 0) {
                            while ($l = mysqli_fetch_assoc($query_laporan)) {
                                $st = $l['status'];
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($l['nama_user']); ?></td>
                            <td><a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $l['nohp']); ?>" target="_blank" class="text-success fw-bold"><i class="bi bi-whatsapp me-1"></i><?php echo $l['nohp']; ?></a></td>
                            <td class="fw-bold text-danger"><?php echo htmlspecialchars($l['judul_kendala']); ?></td>
                            <td><?php echo htmlspecialchars($l['deskripsi']); ?></td>
                            <td><?php echo date('d M Y H:i', strtotime($l['tgl_laporan'])); ?></td>
                            <td>
                                <?php if($st=='Selesai'){ echo '<span class="badge bg-success">Selesai</span>'; } elseif($st=='Proses'){ echo '<span class="badge bg-warning text-dark">Diproses</span>'; } else { echo '<span class="badge bg-secondary">Pending</span>'; } ?>
                            </td>
                            <td>
                                <form method="POST" action="" class="d-flex gap-1">
                                    <input type="hidden" name="id_laporan" value="<?php echo $l['id_laporan']; ?>">
                                    <select name="status" class="form-select form-select-sm" style="font-size: 11px;">
                                        <option value="Pending" <?php if($st=='Pending') echo 'selected'; ?>>Pending</option>
                                        <option value="Proses" <?php if($st=='Proses') echo 'selected'; ?>>Proses</option>
                                        <option value="Selesai" <?php if($st=='Selesai') echo 'selected'; ?>>Selesai</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-primary btn-sm px-2"><i class="bi bi-save"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center text-muted py-4'>Belum ada laporan kendala masuk.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer class="text-center py-3 my-4 border-top text-muted small">
        <div class="container">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> <strong>RENT-HT AI</strong> &mdash; <strong>Farrel Septian Wiguna</strong>. All Rights Reserved.</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>