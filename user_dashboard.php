<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username']) || $_SESSION['level'] != 'User') {
    header("location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$perangkat_list = mysqli_query($conn, "SELECT * FROM ht_perangkat");

// LOGIKA AI REKOMENDASI OTOMATIS
$ai_rekomendasi = null;
if (isset($_POST['hitung_ai'])) {
    $skala_acara = $_POST['skala_acara']; // kecil, sedang, besar
    $durasi_hari = intval($_POST['durasi_hari']);
    $budget      = intval($_POST['budget']);

    // Algoritma Penentuan AI
    if ($skala_acara == 'kecil') {
        $rekomendasi_unit = 5;
        $tipe_saran = 'Baofeng UV-5R';
    } elseif ($skala_acara == 'sedang') {
        $rekomendasi_unit = 15;
        $tipe_saran = 'Firstcom FC-27';
    } else {
        $rekomendasi_unit = 35;
        $tipe_saran = 'ICOM V80-T';
    }
    $ai_rekomendasi = [
        'unit' => $rekomendasi_unit,
        'tipe' => $tipe_saran,
        'pesan' => "Berdasarkan analisis skala acara ($skala_acara) dan durasi $durasi_hari hari, sistem AI merekomendasikan penggunaan $rekomendasi_unit unit jenis $tipe_saran untuk cakupan sinyal optimal."
    ];
}

// Proses Pemesanan
if (isset($_POST['pesan_ht'])) {
    $id_perangkat = intval($_POST['id_perangkat']);
    $jumlah_unit  = intval($_POST['jumlah_unit']);
    $tgl_mulai    = $_POST['tgl_mulai'];
    $tgl_selesai  = $_POST['tgl_selesai'];

    $d1 = new DateTime($tgl_mulai);
    $d2 = new DateTime($tgl_selesai);
    $diff = $d1->diff($d2);
    $total_hari = $diff->days > 0 ? $diff->days : 1;

    $q_p = mysqli_fetch_assoc(mysqli_query($conn, "SELECT harga_sewa_per_hari FROM ht_perangkat WHERE id_perangkat = '$id_perangkat'"));
    $total_biaya = $total_hari * $jumlah_unit * $q_p['harga_sewa_per_hari'];

    $q_ins = "INSERT INTO ht_rental (id_user, id_perangkat, jumlah_unit, tgl_mulai, tgl_selesai, total_hari, total_biaya, status_rental) 
              VALUES ('$id_user', '$id_perangkat', '$jumlah_unit', '$tgl_mulai', '$tgl_selesai', '$total_hari', '$total_biaya', 'Menunggu Pembayaran')";
    
    if (mysqli_query($conn, $q_ins)) {
        $id_rental_baru = mysqli_insert_id($conn);
        $invoice = "INV-HT-" . date('Ymd') . "-" . $id_rental_baru;
        mysqli_query($conn, "INSERT INTO ht_pembayaran (id_rental, invoice, nama_rekening, metode_pembayaran, tgl_bayar, status) VALUES ('$id_rental_baru', '$invoice', '" . $_SESSION['nama'] . "', 'Transfer Bank', NOW(), 'Verifikasi')");
        
        echo "<script>alert('Pesanan HT berhasil dibuat! Silakan lakukan pembayaran.'); window.location='user_dashboard.php';</script>";
        exit();
    }
}

$riwayat = mysqli_query($conn, "SELECT r.*, p.merk_tipe, pay.invoice, pay.status AS status_bayar 
    FROM ht_rental r 
    JOIN ht_perangkat p ON r.id_perangkat = p.id_perangkat 
    LEFT JOIN ht_pembayaran pay ON r.id_rental = pay.id_rental 
    WHERE r.id_user = '$id_user' ORDER BY r.id_rental DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pelanggan Dashboard - RENT-HT AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <nav class="navbar navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="bi bi-broadcast me-2"></i>RENT-HT AI <span class="fs-6 fw-normal text-white-50">| Pelanggan</span></a>
            <div class="navbar-nav ms-auto flex-row gap-3">
                <a class="nav-link text-white fw-bold" href="login.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-4 flex-grow-1">
        <!-- AI RECOMMENDATION WIDGET -->
        <div class="card border-0 shadow-sm p-4 bg-white mb-4 border-start border-primary border-4">
            <h5 class="fw-bold text-primary mb-2"><i class="bi bi-robot me-2"></i>AI Recommendation Engine</h5>
            <p class="text-muted small mb-3">Bingung menghitung jumlah Handie Talkie yang dibutuhkan untuk acara Anda? Biarkan AI yang menghitungnya!</p>
            
            <form method="POST" action="" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Skala Acara / Kegiatan</label>
                    <select name="skala_acara" class="form-select form-select-sm" required>
                        <option value="kecil">Kecil (Indoor / Panitia &lt; 10 orang)</option>
                        <option value="sedang">Sedang (Outdoor / Event Kampus / 10-30 orang)</option>
                        <option value="besar">Besar (Konser / Pengamanan / Festival &gt; 30 orang)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Durasi Sewa (Hari)</label>
                    <input type="number" name="durasi_hari" class="form-control form-control-sm" value="1" min="1" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Estimasi Budget (Rp)</label>
                    <input type="number" name="budget" class="form-control form-control-sm" placeholder="Contoh: 500000" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" name="hitung_ai" class="btn btn-primary btn-sm w-100 fw-bold"><i class="bi bi-cpu me-1"></i> Analisis AI</button>
                </div>
            </form>

            <?php if ($ai_rekomendasi) { ?>
                <div class="alert alert-success mt-3 mb-0 small">
                    <i class="bi bi-lightbulb-fill me-1"></i> <strong>Hasil Rekomendasi AI:</strong> <?php echo $ai_rekomendasi['pesan']; ?>
                </div>
            <?php } ?>
        </div>

        <div class="row g-4">
            <!-- FORM PEMESANAN -->
            <div class="col-md-5">
                <div class="card border-0 shadow-sm p-4 bg-white">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-cart-plus me-2 text-primary"></i>Form Pemesanan HT</h5>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Pilih Tipe Perangkat HT</label>
                            <select name="id_perangkat" class="form-select" required>
                                <option value="">-- Pilih Model HT --</option>
                                <?php while($p = mysqli_fetch_assoc($perangkat_list)) { ?>
                                    <option value="<?php echo $p['id_perangkat']; ?>"><?php echo $p['merk_tipe']; ?> (Rp <?php echo number_format($p['harga_sewa_per_hari'], 0, ',', '.'); ?>/hari)</option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Jumlah Unit</label>
                            <input type="number" name="jumlah_unit" class="form-control" min="1" value="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Tanggal Mulai Sewa</label>
                            <input type="date" name="tgl_mulai" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Tanggal Selesai Sewa</label>
                            <input type="date" name="tgl_selesai" class="form-control" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="pesan_ht" class="btn btn-primary fw-bold">Pesan Sekarang</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- RIWAYAT PEMESANAN -->
            <div class="col-md-7">
                <div class="card border-0 shadow-sm p-4 bg-white h-100">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history me-2 text-info"></i>Riwayat Pemesanan Anda</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle small table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Perangkat</th>
                                    <th>Jumlah</th>
                                    <th>Durasi</th>
                                    <th>Total Biaya</th>
                                    <th>Status Bayar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($riwayat) > 0) { while($r = mysqli_fetch_assoc($riwayat)) { ?>
                                <tr>
                                    <td class="fw-bold"><?php echo $r['merk_tipe']; ?></td>
                                    <td><?php echo $r['jumlah_unit']; ?> Unit</td>
                                    <td><?php echo $r['tgl_mulai']; ?> s/d <?php echo $r['tgl_selesai']; ?></td>
                                    <td class="fw-bold text-success">Rp <?php echo number_format($r['total_biaya'], 0, ',', '.'); ?></td>
                                    <td><span class="badge bg-warning text-dark"><?php echo $r['status_bayar']; ?></span></td>
                                </tr>
                                <?php }} else { ?>
                                <tr><td colspan="5" class="text-center text-muted py-3">Belum ada riwayat pesanan.</td></tr>
                                <?php } ?>
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