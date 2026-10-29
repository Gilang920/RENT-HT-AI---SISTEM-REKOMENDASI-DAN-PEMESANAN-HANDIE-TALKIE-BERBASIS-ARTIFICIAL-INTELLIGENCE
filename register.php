<?php
session_start();
include 'koneksi.php';

if (isset($_POST['register'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $nohp     = mysqli_real_escape_string($conn, $_POST['nohp']);

    $cek = mysqli_query($conn, "SELECT * FROM ht_user WHERE username = '$username'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Username sudah digunakan!";
    } else {
        mysqli_query($conn, "INSERT INTO ht_user (username, password, nama, nohp, level) VALUES ('$username', '$password', '$nama', '$nohp', 'User')");
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='login.php';</script>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - RENT-HT AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center min-vh-100">
    <div class="card border-0 shadow-lg p-4 bg-white" style="width: 100%; max-width: 400px;">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-primary mb-0"><i class="bi bi-broadcast me-2"></i>RENT-HT AI</h3>
            <p class="text-muted small">Daftar Akun Pelanggan Baru</p>
        </div>
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger py-2 small mb-3"><?php echo $error; ?></div>
        <?php } ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label small fw-bold">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">No. WhatsApp / HP</label>
                <input type="text" name="nohp" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="d-grid mb-3">
                <button type="submit" name="register" class="btn btn-primary fw-bold">Daftar Sekarang</button>
            </div>
            <div class="text-center small">
                Sudah punya akun? <a href="login.php" class="text-primary fw-bold text-decoration-none">Login di sini</a>
            </div>
        </form>
    </div>
</body>
</html>