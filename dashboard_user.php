<?php
require 'auth.php';
require 'koneksi.php'; 

if ($_SESSION['role'] != 'user') {
    header("Location: dashboard_admin.php");
    exit;
}

// Ambil total barang
$barang_result = $conn->query("SELECT COUNT(*) AS total_barang FROM barang");
$total_barang = $barang_result->fetch_assoc()['total_barang'];

// Ambil total kategori
$kategori_result = $conn->query("SELECT COUNT(*) AS total_kategori FROM kategori");
$total_kategori = $kategori_result->fetch_assoc()['total_kategori'];

// Ambil total stok
$stok_result = $conn->query("SELECT SUM(stok) AS total_stok FROM barang");
$total_stok = $stok_result->fetch_assoc()['total_stok'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
            min-height: 100vh;
        }
        
        .container-custom {
            max-width: 1024px;
        }
        
        /* Header Styles */
        .header-shadow {
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        /* Card Styles */
        .card-custom {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: box-shadow 0.2s ease;
        }
        
        .card-custom:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .card-welcome {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        /* Gradient Background */
        .bg-gradient-blue {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }
        
        /* Icon Containers */
        .icon-container {
            width: 48px;
            height: 48px;
            background-color: #eff6ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .icon-container-emerald {
            width: 40px;
            height: 40px;
            background-color: #ecfdf5;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Button Styles */
        .btn-blue {
            background-color: #3b82f6;
            border-color: #3b82f6;
            color: white;
            transition: all 0.2s ease;
        }
        
        .btn-blue:hover {
            background-color: #2563eb;
            border-color: #2563eb;
            color: white;
        }
        
        .btn-red {
            background-color: #ef4444;
            border-color: #ef4444;
            color: white;
            transition: all 0.2s ease;
        }
        
        .btn-red:hover {
            background-color: #dc2626;
            border-color: #dc2626;
            color: white;
        }
        
        /* Text Colors */
        .text-slate-800 {
            color: #1e293b;
        }
        
        .text-slate-600 {
            color: #475569;
        }
        
        .text-slate-500 {
            color: #64748b;
        }
        
        .text-slate-700 {
            color: #334155;
        }
        
        .text-blue-600 {
            color: #2563eb;
        }
        
        .text-emerald-600 {
            color: #059669;
        }
        
        /* Background Colors */
        .bg-slate-50 {
            background-color: #f8fafc;
        }
        
        .bg-blue-50 {
            background-color: #eff6ff;
        }
        
        .bg-emerald-50 {
            background-color: #ecfdf5;
        }
        
        /* List Styles */
        .feature-list {
            list-style: none;
            padding: 0;
        }
        
        .feature-list li {
            display: flex;
            align-items: center;
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 8px;
        }
        
        .feature-dot {
            width: 6px;
            height: 6px;
            background-color: #60a5fa;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        /* Border Styles */
        .border-slate-200 {
            border-color: #e2e8f0;
        }
        
        .border-slate-100 {
            border-color: #f1f5f9;
        }
        
        /* Footer */
        .footer-border {
            border-top: 1px solid #e2e8f0;
            margin-top: 64px;
        }
        
        /* Stats Cards */
        .stats-card {
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
        }
        
        .stats-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #334155;
            margin-bottom: 4px;
        }
        
        .stats-label {
            font-size: 0.875rem;
            color: #64748b;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="bg-white header-shadow">
        <div class="container container-custom py-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="fs-5 fw-medium text-slate-800 mb-1">Dashboard User</h1>
                    <p class="small text-slate-500 mb-0">Selamat datang, <?= $_SESSION['username'] ?></p>
                </div>
                <a href="logout.php" 
                   class="btn btn-red btn-sm fw-medium px-3">
                    Logout
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container container-custom py-5">
        <!-- Welcome Card -->
        <div class="card card-welcome mb-5">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="icon-container me-3">
                        <i class="bi bi-person text-primary" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <h2 class="fs-6 fw-medium text-slate-800 mb-1">Halo, <?= $_SESSION['username'] ?>!</h2>
                        <p class="text-slate-600 mb-0">Anda dapat melihat dan mencari data barang melalui sistem ini</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Actions -->
        <div class="row g-4 mb-5">
            <!-- Data Barang Card -->
            <div class="col-12 col-md-6">
                <div class="card card-custom h-100 overflow-hidden">
                    <div class="bg-gradient-blue p-4">
                        <div class="d-flex align-items-center text-white">
                            <i class="bi bi-box me-3" style="font-size: 32px;"></i>
                            <h3 class="fs-6 fw-medium mb-0">Data Barang</h3>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-slate-600 mb-4">Jelajahi koleksi barang yang tersedia dalam sistem</p>
                        <ul class="feature-list mb-4">
                            <li>
                                <span class="feature-dot"></span>
                                Lihat semua barang
                            </li>
                            <li>
                                <span class="feature-dot"></span>
                                Cari berdasarkan nama
                            </li>
                            <li>
                                <span class="feature-dot"></span>
                                Filter berdasarkan kategori
                            </li>
                        </ul>
                        <a href="list.php" 
                           class="btn btn-blue w-100 py-2 fw-medium d-flex align-items-center justify-content-center">
                            <i class="bi bi-eye me-2"></i>
                            Lihat Data Barang
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Info Card -->
            <div class="col-12 col-md-6">
                <div class="card card-custom h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-container-emerald me-3">
                                <i class="bi bi-info-circle text-emerald-600" style="font-size: 20px;"></i>
                            </div>
                            <h3 class="fs-6 fw-medium text-slate-800 mb-0">Informasi</h3>
                        </div>
                        
                        <div>
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom border-slate-100">
                                <span class="text-slate-600">Status Akun</span>
                                <span class="text-emerald-600 fw-medium">Aktif</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom border-slate-100">
                                <span class="text-slate-600">Akses Level</span>
                                <span class="text-blue-600 fw-medium">User</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between py-2">
                                <span class="text-slate-600">Last Login</span>
                                <span class="text-slate-500 small">Hari ini</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ringkasan Sistem -->
        <div class="card card-custom">
            <div class="card-body p-4">
                <h3 class="fs-6 fw-medium text-slate-800 mb-4">Ringkasan Sistem</h3>
                <div class="row g-3">
                    <div class="col-12 col-sm-4">
                        <div class="stats-card">
                            <div class="stats-number"><?= $total_barang ?></div>
                            <div class="stats-label">Total Barang</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="stats-card">
                            <div class="stats-number"><?= $total_kategori ?></div>
                            <div class="stats-label">Kategori</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="stats-card">
                            <div class="stats-number"><?= $total_stok ?></div>
                            <div class="stats-label">Barang Tersedia</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white footer-border py-4">
        <div class="container container-custom text-center">
            <p class="text-slate-500 small mb-0">All Rights Reserved | © App Inventaris Barang - 2025</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>