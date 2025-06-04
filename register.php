<?php
session_start();
require 'koneksi.php';

if (isset($_SESSION['login'])) {
    if ($_SESSION['role'] == 'admin') header("Location: dashboard_admin.php");
    else header("Location: dashboard_user.php");
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'user';

    if (!empty($username) && !empty($password) && !empty($confirm_password)) {
        if ($password !== $confirm_password) {
            $error = "Konfirmasi password tidak sesuai!";
        } else {
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = "Username sudah terdaftar!";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $hashed, $role);
                if ($stmt->execute()) {
                    $success = "Akun berhasil dibuat. Silakan login.";
                } else {
                    $error = "Terjadi kesalahan saat mendaftarkan pengguna.";
                }
            }
        }
    } else {
        $error = "Lengkapi semua data dengan benar!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - Inventaris</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light d-flex justify-content-center align-items-center" style="height: 100vh;">
<div class="card p-4 shadow" style="width: 400px;">
    <h3 class="text-center mb-3">Register Akun</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Konfirmasi Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button class="btn btn-primary w-100">Daftar</button>

        <!-- Tombol seperti pada gambar kiri: text biasa dengan link -->
        <div class="text-center mt-2">
            Sudah punya akun? <a href="index.php" class="text-primary">Masuk</a>
        </div>
    </form>
</div>
</body>
</html>
