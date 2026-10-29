<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username']) || $_SESSION['level'] != 'User') {
    header("location: login.php");
    exit();
}

$id_user   = $_SESSION['id_user'];
$nama_user = $_SESSION['nama'];

// Ambil riwayat sewa aktif user untuk pilihan laporan
$q_rental_aktif = mysqli_query($conn, "SELECT r.*, ht.merk_tipe 
    FROM ht_rental r 
    JOIN ht_perangkat ht ON r.id_perangkat = ht.id_perangkat 
    WHERE r.id_user = '$id_user' AND r.status_rental = 'Aktif'");

// Proses Kirim Laporan Kendala
if (isset($_POST['kirim_laporan'])) {
    $judul_kendala = mysqli_real_escape_string($conn, $_POST['judul_kendala']);
    $deskripsi     = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $tgl_laporan   = date('Y-m-d H:i:s');

    $q_insert = "INSERT INTO ht_laporan (id_user, judul_kendala, deskripsi, tgl_laporan, status) 
                 VALUES ('$id_user', '$judul_kendala', '$deskripsi', '$tgl_laporan', 'Pending')";
    
    if (mysqli_query($conn, $q_insert)) {
        echo "<script>alert('Laporan kendala berhasil dikirim ke Admin!'); window.location='laporan_ht.php';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal mengirim laporan: " . mysqli_error($conn) . "');</script>";
    }
}

// Riwayat Laporan User
$q_riwayat = mysqli_query($conn, "SELECT * FROM ht_laporan WHERE id_user = '$id_user' ORDER BY id_laporan DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lapor Kendala HT - RENT-HT AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <nav class="navbar navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="user_dashboard.php"><i class="bi bi-exclamation-triangle-fill me-2"></i>Lapor Kendala Perangkat HT</a>
            <a href="user_dashboard.php" class="btn btn-outline-light btn-sm fw-bold"><i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard</a>
        </div>
    </nav>

    <div class="container my-4 flex-grow-1">
        <div class="row g-4">
            <div class="col-md-5">
                <div class="card border-0 shadow-sm p-4 bg-white">
                    <h5 class="fw-bold text-primary mb-3"><i class="bi bi-pencil-square me-2"></i>Form Laporan Masalah</h5>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Judul / Jenis Kendala</label>
                            <input type="text" name="judul_kendala" class="form-control" placeholder="Contoh: Baterai Cepat Drop / Speaker Sember" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Deskripsi Detail Kendala</label>
                            <textarea name="deskripsi" class="form-control" rows="4" placeholder="Jelaskan kendala pada unit HT yang Anda sewa..." required></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="kirim_laporan" class="btn btn-primary fw-bold">Kirim Laporan</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card border-0 shadow-sm p-4 bg-white h-100">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history me-2 text-info"></i>Riwayat Laporan Anda</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle small table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Kendala</th>
                                    <th>Deskripsi</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                if (mysqli_num_rows($q_riwayat) > 0) {
                                    while ($l = mysqli_fetch_assoc($q_riwayat)) {
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td class="fw-bold"><?php echo htmlspecialchars($l['judul_kendala']); ?></td>
                                    <td><?php echo htmlspecialchars($l['deskripsi']); ?></td>
                                    <td><?php echo date('d M Y H:i', strtotime($l['tgl_laporan'])); ?></td>
                                    <td>
                                        <?php if ($l['status'] == 'Selesai') { ?>
                                            <span class="badge bg-success">Selesai</span>
                                        <?php } elseif ($l['status'] == 'Proses') { ?>
                                            <span class="badge bg-warning text-dark">Diproses</span>
                                        <?php } else { ?>
                                            <span class="badge bg-secondary">Pending</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center text-muted py-3'>Belum ada laporan kendala.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
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