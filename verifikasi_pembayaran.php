<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username']) || $_SESSION['level'] != 'Admin') {
    header("location: login.php");
    exit();
}

// Proses Aksi Konfirmasi (Terima / Tolak)
if (isset($_POST['aksi'])) {
    $id_pembayaran = intval($_POST['id_pembayaran']);
    $id_rental     = intval($_POST['id_rental']);
    $aksi          = $_POST['aksi'];

    if ($aksi == 'terima') {
        mysqli_query($conn, "UPDATE ht_pembayaran SET status = 'Paid' WHERE id_pembayaran = '$id_pembayaran'");
        mysqli_query($conn, "UPDATE ht_rental SET status_rental = 'Aktif' WHERE id_rental = '$id_rental'");
        echo "<script>alert('Pembayaran berhasil dikonfirmasi! Status rental aktif.'); window.location='verifikasi_pembayaran.php';</script>";
    } elseif ($aksi == 'tolak') {
        mysqli_query($conn, "UPDATE ht_pembayaran SET status = 'Rejected' WHERE id_pembayaran = '$id_pembayaran'");
        mysqli_query($conn, "UPDATE ht_rental SET status_rental = 'Batal' WHERE id_rental = '$id_rental'");
        echo "<script>alert('Pembayaran ditolak dan pesanan dibatalkan.'); window.location='verifikasi_pembayaran.php';</script>";
    }
    exit();
}

// DIPERBAIKI: Menggunakan total_biaya (sesuai database)
$query_bayar = mysqli_query($conn, "SELECT p.*, r.tgl_mulai, r.tgl_selesai, r.jumlah_unit, r.total_biaya, u.nama AS nama_user, ht.merk_tipe 
    FROM ht_pembayaran p
    JOIN ht_rental r ON p.id_rental = r.id_rental
    JOIN ht_user u ON r.id_user = u.id_user
    JOIN ht_perangkat ht ON r.id_perangkat = ht.id_perangkat
    ORDER BY p.id_pembayaran DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Pembayaran - RENT-HT AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="admin_dashboard.php"><i class="bi bi-broadcast me-2 text-warning"></i>RENT-HT AI <span class="fs-6 fw-normal text-muted">| Verifikasi Pembayaran</span></a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="admin_dashboard.php">Dashboard</a>
                <a class="nav-link active" href="verifikasi_pembayaran.php">Verifikasi Pembayaran</a>
                <a class="nav-link text-danger fw-bold" href="login.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-4 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="bi bi-receipt me-2 text-warning"></i>Verifikasi Pembayaran Pelanggan</h4>
                <p class="text-muted small mb-0">Periksa dan konfirmasi status pembayaran sewa Handie Talkie.</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm p-4 bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle small table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Invoice</th>
                            <th>Pelanggan</th>
                            <th>Perangkat</th>
                            <th>Total Biaya</th>
                            <th>Status Bayar</th>
                            <th class="text-center" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if (mysqli_num_rows($query_bayar) > 0) {
                            while ($b = mysqli_fetch_assoc($query_bayar)) {
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td class="fw-bold text-primary"><?php echo $b['invoice']; ?></td>
                            <td><?php echo htmlspecialchars($b['nama_user']); ?></td>
                            <td><?php echo $b['merk_tipe']; ?> (<?php echo $b['jumlah_unit']; ?> Unit)</td>
                            <td class="fw-bold">Rp <?php echo number_format($b['total_biaya'], 0, ',', '.'); ?></td>
                            <td>
                                <?php if ($b['status'] == 'Paid') { ?>
                                    <span class="badge bg-success">Lunas (Paid)</span>
                                <?php } elseif ($b['status'] == 'Rejected') { ?>
                                    <span class="badge bg-danger">Ditolak</span>
                                <?php } else { ?>
                                    <span class="badge bg-warning text-dark">Menunggu Verifikasi</span>
                                <?php } ?>
                            </td>
                            <td class="text-center">
                                <?php if ($b['status'] == 'Verifikasi') { ?>
                                    <form method="POST" action="" class="d-flex gap-1 justify-content-center">
                                        <input type="hidden" name="id_pembayaran" value="<?php echo $b['id_pembayaran']; ?>">
                                        <input type="hidden" name="id_rental" value="<?php echo $b['id_rental']; ?>">
                                        <button type="submit" name="aksi" value="terima" class="btn btn-success btn-sm px-2" title="Terima"><i class="bi bi-check-lg"></i></button>
                                        <button type="submit" name="aksi" value="tolak" class="btn btn-danger btn-sm px-2" title="Tolak" onclick="return confirm('Yakin ingin menolak?')"><i class="bi bi-x-lg"></i></button>
                                    </form>
                                <?php } else { ?>
                                    <span class="text-muted fst-italic small">Selesai</span>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center text-muted py-4'>Belum ada data pembayaran masuk.</td></tr>";
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