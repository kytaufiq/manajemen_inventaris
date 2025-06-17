<?php
session_start();
require 'koneksi.php';

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if (isset($_SESSION['login'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: dashboard_admin.php");
        exit;
    } else {
        header("Location: dashboard_user.php");
        exit;
    }
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $inputPassword = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($inputPassword, $user['password'])) {
            $_SESSION['login'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: dashboard_admin.php");
            } else {
                header("Location: dashboard_user.php");
            }
            exit;
        } else {
            $error = "Username atau Password salah!";
        }
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>App Inventaris Barang</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('img/background.png');
            background-size: cover;
            background-position: center;
            filter: blur(2px);
            z-index: -1; 
        }

        .login-container {
            min-height: 100vh;
        }

        .left-box, .right-box {
            background-color: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 30px;
            padding: 40px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .app-title {
            font-size: 28px;
            font-weight: bold;
        }

        .app-desc {
            margin-top: 20px;
            font-size: 15px;
            color: #555;
        }

        footer {
            font-size: 13px;
            color: #888;
        }
    </style>
</head>
<body>
<div class="container-fluid login-container d-flex justify-content-center align-items-center">
    <div class="row shadow rounded" style="max-width: 900px; width: 100%;">
        <div class="col-md-6 left-box d-flex flex-column justify-content-center align-items-center text-center">
            <img src="img/logo.png" alt="Logo" style="width: 120px;">
            <div class="app-title mt-3">Barangku</div>
            <div class="app-desc mt-3">
                Barangku ini merupakan software sebagai alat bantu untuk mengelola, memantau, dan mencatat data barang secara efisien di lingkungan kantor, baik instansi pemerintah maupun swasta.
            </div>
        </div>
        <div class="col-md-6 right-box">
            <h4 class="mb-4 text-center">🔑 User Login</h4>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST" class="needs-validation" novalidate>
                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Username Or Email" required>
                </div>
                <div class="mb-3 input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button class="btn btn-primary w-100">Login 🔐</button>
            </form>
           <div class="text-center mt-3">
                    <small>Belum punya akun? <a href="register.php">Daftar di sini</a></small>
                </div>
                <footer class="text-center mt-4 w-100">
                    All Rights Reserved | © App Inventaris Barang - 2025
                </footer>

        </div>
    </div>
</div>

<script src="js/script.js"></script>
</body>
</html>
