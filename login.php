<?php
session_start();
include 'koneksi.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $q = mysqli_query($conn, "SELECT * FROM ht_user WHERE username = '$username' AND password = '$password' LIMIT 1");
    if (mysqli_num_rows($q) > 0) {
        $user = mysqli_fetch_assoc($q);
        $_SESSION['id_user']  = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama']     = $user['nama'];
        $_SESSION['level']    = $user['level'];

        if ($user['level'] == 'Admin') {
            header("location: admin_dashboard.php");
        } else {
            header("location: user_dashboard.php");
        }
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - RENT-HT AI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center min-vh-100 m-0">
    
    <div class="card border-0 shadow-lg p-4 bg-white rounded-4" style="width: 100%; max-width: 420px;">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-primary mb-0"><i class="bi bi-broadcast me-2"></i>RENT-HT AI</h3>
            <p class="text-muted small">Sistem Rekomendasi & Pemesanan Handie Talkie</p>
        </div>

        <?php if (isset($error)) { ?>
            <div class="alert alert-danger py-2 small mb-3"><?php echo $error; ?></div>
        <?php } ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label small fw-bold">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username..." required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password..." required>
            </div>
            <div class="d-grid mb-3">
                <button type="submit" name="login" class="btn btn-primary fw-bold py-2">Login Sistem</button>
            </div>
            <div class="text-center small mb-3">
                Belum punya akun? <a href="register.php" class="text-primary fw-bold text-decoration-none">Daftar Akun Baru</a>
            </div>
        </form>

        <footer class="text-center mt-2 pt-3 border-top text-muted small">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> <strong>RENT-HT AI</strong> &mdash; <strong>Farrel Septian Wiguna</strong></p>
        </footer>
    </div>

</body>
</html>