<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username']) || $_SESSION['level'] != 'Admin') {
    header("location: login.php");
    exit();
}

$total_pendapatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(r.total_biaya), 0) AS total FROM ht_pembayaran p JOIN ht_rental r ON p.id_rental = r.id_rental WHERE p.status = 'Paid'"))['total'];
$total_user       = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM ht_user WHERE level='User'"))['total'];
$total_unit_sewa  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(jumlah_unit), 0) as total FROM ht_rental WHERE status_rental='Aktif'"))['total'];

// Query Daftar Pemesanan & Penyewa
$query_penyewa = mysqli_query($conn, "SELECT r.*, u.nama AS nama_penyewa, u.nohp, p.merk_tipe, pay.invoice, pay.status AS status_bayar 
    FROM ht_rental r 
    JOIN ht_user u ON r.id_user = u.id_user 
    JOIN ht_perangkat p ON r.id_perangkat = p.id_perangkat 
    LEFT JOIN ht_pembayaran pay ON r.id_rental = pay.id_rental 
    ORDER BY r.id_rental DESC");

// Query Daftar Perangkat HT
$query_perangkat = mysqli_query($conn, "SELECT * FROM ht_perangkat ORDER BY id_perangkat ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - RENT-HT AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    
    <!-- NAVBAR ADMIN -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="bi bi-broadcast me-2 text-warning"></i>RENT-HT AI <span class="fs-6 fw-normal text-muted">| Admin Panel</span></a>
            <div class="navbar-nav ms-auto gap-2">
                <a class="nav-link active" href="admin_dashboard.php">Dashboard</a>
                <a class="nav-link" href="verifikasi_pembayaran.php">Verifikasi Pembayaran</a>
                <a class="nav-link text-danger fw-bold" href="login.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-4 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">AI Rental Management & Analytics</h4>
                <p class="text-muted small mb-0">Administrator: <strong><?php echo $_SESSION['nama']; ?></strong></p>
            </div>
            <span class="badge bg-secondary px-3 py-2"><?php echo date('d M Y'); ?></span>
        </div>

        <!-- STATISTIK KARTU -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3 bg-success text-white">
                    <h6 class="text-white-50 mb-1">Total Pendapatan (Lunas)</h6>
                    <h3 class="fw-bold mb-0">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3 bg-primary text-white">
                    <h6 class="text-white-50 mb-1">Total Pelanggan Terdaftar</h6>
                    <h3 class="fw-bold mb-0"><?php echo $total_user; ?> User</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3 bg-warning text-dark">
                    <h6 class="text-dark mb-1">Unit HT Disewa Aktif</h6>
                    <h3 class="fw-bold mb-0"><?php echo $total_unit_sewa; ?> Unit</h3>
                </div>
            </div>
        </div>

        <!-- TABEL 1: DAFTAR PEMESANAN & PENYEWA -->
        <div class="card border-0 shadow-sm p-4 bg-white mb-4">
            <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-person-lines-fill me-2"></i>Daftar Pemesanan & Penyewa Handie Talkie</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle small table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Penyewa</th>
                            <th>No. WhatsApp</th>
                            <th>Perangkat HT</th>
                            <th>Jumlah</th>
                            <th>Periode</th>
                            <th>Total Biaya</th>
                            <th>Status Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; if(mysqli_num_rows($query_penyewa) > 0) { while($p = mysqli_fetch_assoc($query_penyewa)) { ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td class="fw-bold"><?php echo $p['nama_penyewa']; ?></td>
                            <td><a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $p['nohp']); ?>" target="_blank" class="text-success fw-bold"><i class="bi bi-whatsapp me-1"></i><?php echo $p['nohp']; ?></a></td>
                            <td><?php echo $p['merk_tipe']; ?></td>
                            <td><span class="badge bg-secondary"><?php echo $p['jumlah_unit']; ?> Unit</span></td>
                            <td><?php echo $p['tgl_mulai']; ?> s/d <?php echo $p['tgl_selesai']; ?></td>
                            <td class="fw-bold">Rp <?php echo number_format($p['total_biaya'], 0, ',', '.'); ?></td>
                            <td><span class="badge bg-success"><?php echo $p['status_bayar']; ?></span></td>
                        </tr>
                        <?php }} else { ?>
                        <tr><td colspan="8" class="text-center text-muted py-3">Belum ada data pemesanan.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TABEL 2: STOK & PERANGKAT HANDIE TALKIE -->
        <div class="card border-0 shadow-sm p-4 bg-white">
            <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-broadcast me-2 text-info"></i>Manajemen Stok Perangkat Handie Talkie</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle small table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Merk / Tipe</th>
                            <th>Spesifikasi</th>
                            <th>Harga Sewa / Hari</th>
                            <th>Stok Tersedia</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no2=1; while($ht = mysqli_fetch_assoc($query_perangkat)) { ?>
                        <tr>
                            <td><?php echo $no2++; ?></td>
                            <td class="fw-bold text-primary"><?php echo $ht['merk_tipe']; ?></td>
                            <td><?php echo $ht['spesifikasi']; ?></td>
                            <td>Rp <?php echo number_format($ht['harga_sewa_per_hari'], 0, ',', '.'); ?></td>
                            <td><span class="badge bg-success"><?php echo $ht['stok_tersedia']; ?> Unit</span></td>
                        </tr>
                        <?php } ?>
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