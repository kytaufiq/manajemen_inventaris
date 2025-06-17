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
        body {
            background-image: url('img/background.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
        }

        .login-container {
            min-height: 100vh;
            padding: 20px 0;
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

        .main-card {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }

        .left-box, .right-box {
            padding: 40px;
        }

        .left-box {
            background-color: #ffffff;
            color: #000;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .right-box {
            background-color: #ffffff;
            color: #000;
        }

        .footer-section {
            background-color: #f8f9fa;
            color: #666;
            text-align: center;
            padding: 15px;
            font-size: 13px;
            border-top: 1px solid #dee2e6;
        }

        @media (max-width: 768px) {
            .login-container {
                padding: 10px;
            }
            
            .left-box, .right-box {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid login-container d-flex justify-content-center align-items-center">
    <div class="main-card">
        <div class="row g-0">
            <div class="col-md-6 left-box">
                <img src="img/logo.png" alt="Logo" style="width: 120px;">
                <div class="app-title mt-3">Barangku</div>
                <div class="app-desc mt-3">
                    Barangku merupakan software untuk mengelola, memantau, dan mencatat data barang secara efisien di lingkungan kantor, baik instansi pemerintah maupun swasta.
                </div>
            </div>
            <div class="col-md-6 right-box">
                <h4 class="mb-4 text-center">🔑 <strong>User Login</strong></h4>
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
            </div>
        </div>
        
        <!-- Footer terintegrasi dalam card utama -->
        <div class="footer-section">
            All Rights Reserved | © App Inventaris Barang - 2025
        </div>
    </div>
</div>

<script src="js/script.js"></script>
</body>
</html>