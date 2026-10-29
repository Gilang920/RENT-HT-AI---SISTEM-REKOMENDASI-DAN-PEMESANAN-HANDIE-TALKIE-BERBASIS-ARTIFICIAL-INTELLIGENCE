<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit();
}

$id_rental = intval($_GET['id'] ?? 0);
$q = mysqli_query($conn, "SELECT r.*, ht.merk_tipe, u.nama AS nama_user, u.nohp, p.invoice, p.status AS status_bayar 
    FROM ht_rental r 
    JOIN ht_perangkat ht ON r.id_perangkat = ht.id_perangkat 
    JOIN ht_user u ON r.id_user = u.id_user 
    LEFT JOIN ht_pembayaran p ON r.id_rental = p.id_rental 
    WHERE r.id_rental = '$id_rental' LIMIT 1");
$data = mysqli_fetch_assoc($q);

if (!$data) {
    die("Data transaksi tidak ditemukan!");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?php echo $data['invoice']; ?> - RENT-HT AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print { .no-print { display: none; } }
    </style>
</head>
<body class="bg-white py-5">
    <div class="container" style="max-width: 700px;">
        <div class="card border p-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                <div>
                    <h3 class="fw-bold text-primary mb-0"><i class="bi bi-broadcast me-2"></i>RENT-HT AI</h3>
                    <p class="text-muted small mb-0">Sistem Rekomendasi & Pemesanan Handie Talkie</p>
                </div>
                <div class="text-end">
                    <h5 class="fw-bold mb-0">INVOICE</h5>
                    <span class="text-muted small"><?php echo $data['invoice']; ?></span>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-sm-6">
                    <h6 class="fw-bold text-secondary">Kepada Pelanggan:</h6>
                    <p class="mb-0 fw-bold"><?php echo $data['nama_user']; ?></p>
                    <p class="mb-0 text-muted small">No. WhatsApp: <?php echo $data['nohp']; ?></p>
                </div>
                <div class="col-sm-6 text-sm-end">
                    <h6 class="fw-bold text-secondary">Status Pembayaran:</h6>
                    <span class="badge bg-success fs-6 px-3 py-1"><?php echo $data['status_bayar']; ?></span>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle small">
                    <thead class="table-dark">
                        <tr>
                            <th>Model Perangkat</th>
                            <th>Jumlah Unit</th>
                            <th>Durasi Sewa</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold"><?php echo $data['merk_tipe']; ?></td>
                            <td><?php echo $data['jumlah_unit']; ?> Unit</td>
                            <td><?php echo $data['tgl_mulai']; ?> s/d <?php echo $data['tgl_selesai']; ?> (<?php echo $data['total_hari']; ?> Hari)</td>
                            <td class="fw-bold">Rp <?php echo number_format($data['total_biya'], 0, ',', '.'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center border-top pt-3">
                <div>
                    <p class="text-muted small mb-0">Terima kasih telah menggunakan layanan RENT-HT AI.</p>
                    <p class="text-muted small mb-0">&copy; <?php echo date('Y'); ?> <strong>Farrel Septian Wiguna</strong></p>
                </div>
                <div class="no-print">
                    <button onclick="window.print()" class="btn btn-primary btn-sm fw-bold">Cetak Invoice</button>
                    <a href="user_dashboard.php" class="btn btn-outline-secondary btn-sm">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>