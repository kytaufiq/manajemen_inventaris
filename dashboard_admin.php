<?php
require 'auth.php';
require 'koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: dashboard_user.php");
    exit;
}

// Deteksi halaman aktif berdasarkan nama file atau parameter
$current_page = basename($_SERVER['PHP_SELF']);
$active_menu = '';

// Tentukan menu aktif berdasarkan halaman
switch($current_page) {
    case 'dashboard_admin.php':
        $active_menu = 'dashboard';
        break;
    case 'list.php':
        // Cek apakah ini dari folder barang atau kategori
        if (strpos($_SERVER['REQUEST_URI'], 'barang') !== false) {
            $active_menu = 'barang';
        } elseif (strpos($_SERVER['REQUEST_URI'], 'kategori') !== false) {
            $active_menu = 'kategori';
        }
        break;
    case 'tambah.php':
    case 'edit.php':
        // Untuk halaman tambah/edit, cek folder
        if (strpos($_SERVER['REQUEST_URI'], 'barang') !== false) {
            $active_menu = 'barang';
        } elseif (strpos($_SERVER['REQUEST_URI'], 'kategori') !== false) {
            $active_menu = 'kategori';
        }
        break;
}

$barang_result = $conn->query("SELECT COUNT(*) AS total_barang FROM barang");
$total_barang = $barang_result->fetch_assoc()['total_barang'];

$kategori_result = $conn->query("SELECT COUNT(*) AS total_kategori FROM kategori");
$total_kategori = $kategori_result->fetch_assoc()['total_kategori'];

$stok_result = $conn->query("SELECT SUM(stok) AS total_stok FROM barang");
$total_stok = $stok_result->fetch_assoc()['total_stok'] ?? 0;

$barang_list_query = "
    SELECT b.id, b.nama_barang, b.stok, b.harga, k.nama_kategori 
    FROM barang b 
    LEFT JOIN kategori k ON b.kategori_id = k.id 
    ORDER BY b.nama_barang ASC
";
$barang_list_result = $conn->query($barang_list_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangku - Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* membuat lebar sidebar */
        :root {
            --sidebar-width: 264px;
        }
        
        body {
            background-color:#f8f9fa;
            min-height: 100vh;
        }
        
        /* Header Styles */
        .header-gradient {
            background: linear-gradient(135deg, #fbbf24 0%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: bold;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .header-fixed {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1050;
            background: white;
            border-bottom: 1px solid #e9ecef;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 64px;
            left: 0;
            height: calc(100vh - 64px);
            width: var(--sidebar-width);
            background: white;
            border-right: 1px solid #e9ecef;
            box-shadow: 1px 0 3px rgba(0,0,0,0.1);
            z-index: 1040;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        
        .sidebar.show {
            transform: translateX(0);
        }
        
        @media (min-width: 1024px) {
            .sidebar {
                transform: translateX(0);
            }
        }
        
        .sidebar-nav .nav-link {
            padding: 12px 16px;
            margin: 4px 0;
            border-radius: 8px;
            color: #374151;
            text-decoration: none;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .sidebar-nav .nav-link:hover {
            background-color: #f3f4f6;
            color: #111827;
        }
        
        .sidebar-nav .nav-link.active.dashboard {
            background-color: #eff6ff;
            color: #1d4ed8;
            border-left: 4px solid #3b82f6;
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .sidebar-nav .nav-link.active.barang {
            background-color: #f0fdf4;
            color: #166534;
            border-left: 4px solid #22c55e;
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .sidebar-nav .nav-link.active.kategori {
            background-color: #faf5ff;
            color: #7c2d12;
            border-left: 4px solid #a855f7;
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .sidebar-nav .nav-link.logout {
            color: #dc2626;
        }
        
        .sidebar-nav .nav-link.logout:hover {
            background-color: #fef2f2;
            color: #b91c1c;
        }
        
        /* Main Content */
        .main-content {
            padding-top: 80px;
            padding-bottom: 32px;
            transition: margin-left 0.3s ease;
        }
        
        @media (min-width: 1024px) {
            .main-content {
                margin-left: var(--sidebar-width);
            }
        }
        
        /* Overlay */
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1030;
            display: none;
        }
        
        .sidebar-overlay.show {
            display: block;
        }
        
        @media (min-width: 1024px) {
            .sidebar-overlay {
                display: none !important;
            }
        }
        
        /* Card Styles */
        .stats-card {
            transition: box-shadow 0.3s ease;
        }
        
        .stats-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .stats-icon {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
        }
        
        .stats-icon.blue {
            background-color: #dbeafe;
            color: #2563eb;
        }
        
        .stats-icon.green {
            background-color: #dcfce7;
            color: #16a34a;
        }
        
        .stats-icon.orange {
            background-color: #fed7aa;
            color: #ea580c;
        }
        
        /* Stock Status */
        .stock-low {
            color: #dc2626 !important;
            font-weight: 600;
        }
        
        .stock-limited {
            color: #d97706 !important;
            font-weight: 500;
        }
        
        .stock-good {
            color: #16a34a;
        }
        
        .stock-label {
            font-size: 0.75rem;
            margin-left: 4px;
        }
        
        /* Avatar */
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #d1d5db;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #374151;
            font-size: 14px;
            font-weight: 600;
        }
        
        /* Responsive adjustments */
        @media (max-width: 576px) {
            .stats-card {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>

<!-- Header -->
<header class="header-fixed">
    <div class="container-fluid px-3 px-sm-4 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="btn btn-link d-lg-none p-0 me-3" id="sidebarToggle" type="button">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <img src="img/logo.png" alt="Logo" class="rounded-circle shadow" style="width: 32px; height: 32px; object-fit: contain;" />
                <h1 class="header-gradient fs-4 fs-sm-3 mb-0 ms-3">Barangku</h1>
            </div>

            <div class="d-flex align-items-center">
                <span class="text-muted fw-semibold d-none d-sm-block me-3">
                    <?= $_SESSION['username'] ?>
                </span>
                <div class="user-avatar">
                    <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?><?= strtoupper(explode(' ', $_SESSION['username'])[1][0] ?? '') ?>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <nav class="sidebar-nav p-3">
        <!-- Dashboard Menu -->
        <a href="dashboard_admin.php" class="nav-link <?= $active_menu == 'dashboard' ? 'active dashboard' : '' ?>">
            <span style="font-size: 18px;">📊</span>
            <span>Dashboard</span>
        </a>
        
        <!-- Kelola Barang Menu -->
        <a href="barang/list.php" class="nav-link <?= $active_menu == 'barang' ? 'active barang' : '' ?>">
            <span style="font-size: 18px;">📦</span>
            <span>Kelola Barang</span>
        </a>
        
        <!-- Kelola Kategori Menu -->
        <a href="kategori/list.php" class="nav-link <?= $active_menu == 'kategori' ? 'active kategori' : '' ?>">
            <span style="font-size: 18px;">🏷️</span>
            <span>Kelola Kategori</span>
        </a>

        <!-- Divider -->
        <hr class="my-3">
        
        <!-- Logout Menu -->
        <a href="logout.php" class="nav-link logout">
            <span style="font-size: 18px;">🔓</span>
            <span>Logout</span>
        </a>
    </nav>
</aside>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Main Content -->
<main class="main-content">
    <div class="container-fluid px-3 px-sm-4 px-lg-5">
        <div class="mb-4 mb-sm-5">
            <h2 class="fs-5 fs-sm-4 fw-medium text-secondary mb-2">Dashboard Admin</h2>
            <p class="text-muted mb-0">Pantau inventori dan kelola sistem dengan mudah</p>
        </div>

        <!-- Ringkasan Sistem -->
        <div class="mb-5">
            <h3 class="fs-6 fs-sm-5 fw-medium text-dark mb-4">Ringkasan Sistem</h3>
            <div class="row g-3 g-sm-4">
                <!-- Total Barang -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card stats-card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4 p-sm-5">
                            <div class="stats-icon blue">
                                <i class="bi bi-box" style="font-size: 24px;"></i>
                            </div>
                            <div class="display-6 display-sm-5 fw-bold text-primary mb-2"><?= $total_barang ?></div>
                            <div class="small text-muted">Total Barang</div>
                        </div>
                    </div>
                </div>
                
                <!-- Total Kategori -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card stats-card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4 p-sm-5">
                            <div class="stats-icon green">
                                <i class="bi bi-tags" style="font-size: 24px;"></i>
                            </div>
                            <div class="display-6 display-sm-5 fw-bold text-success mb-2"><?= $total_kategori ?></div>
                            <div class="small text-muted">Total Kategori</div>
                        </div>
                    </div>
                </div>
                
                <!-- Total Stok -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card stats-card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4 p-sm-5">
                            <div class="stats-icon orange">
                                <i class="bi bi-bar-chart" style="font-size: 24px;"></i>
                            </div>
                            <div class="display-6 display-sm-5 fw-bold text-warning mb-2"><?= number_format($total_stok) ?></div>
                            <div class="small text-muted">Total Stok</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Barang -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="fs-6 fs-sm-5 fw-medium text-dark mb-0">Daftar Barang</h3>
                    <a href="barang/list.php" class="text-primary text-decoration-none fw-medium small">
                        Lihat Semua →
                    </a>
                </div>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="px-3 px-sm-4 py-3 text-uppercase small fw-medium text-muted">No</th>
                                <th scope="col" class="px-3 px-sm-4 py-3 text-uppercase small fw-medium text-muted">Nama Barang</th>
                                <th scope="col" class="px-3 px-sm-4 py-3 text-uppercase small fw-medium text-muted">Kategori</th>
                                <th scope="col" class="px-3 px-sm-4 py-3 text-uppercase small fw-medium text-muted">Stok</th>
                                <th scope="col" class="px-3 px-sm-4 py-3 text-uppercase small fw-medium text-muted">Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($barang_list_result->num_rows > 0): ?>
                                <?php $no = 1; ?>
                                <?php while ($row = $barang_list_result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="px-3 px-sm-4 py-3 small text-dark">
                                            <?= $no++ ?>
                                        </td>
                                        <td class="px-3 px-sm-4 py-3">
                                            <div class="small fw-medium text-dark"><?= htmlspecialchars($row['nama_barang']) ?></div>
                                        </td>
                                        <td class="px-3 px-sm-4 py-3">
                                            <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-3 py-1">
                                                <?= htmlspecialchars($row['nama_kategori'] ?? 'Tanpa Kategori') ?>
                                            </span>
                                        </td>
                                        <td class="px-3 px-sm-4 py-3">
                                            <span class="small <?= $row['stok'] <= 10 ? 'stock-low' : ($row['stok'] <= 20 ? 'stock-limited' : 'stock-good') ?>">
                                                <?= $row['stok'] ?>
                                                <?php if ($row['stok'] <= 10): ?>
                                                    <span class="stock-label text-danger">(Stok Rendah)</span>
                                                <?php elseif ($row['stok'] <= 20): ?>
                                                    <span class="stock-label text-warning">(Stok Terbatas)</span>
                                                <?php endif; ?>
                                            </span>
                                        </td>
                                        <td class="px-3 px-sm-4 py-3 small text-dark">
                                            Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-3 px-sm-4 py-5 text-center text-muted">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-box display-4 text-muted mb-3"></i>
                                            <p class="small text-muted mb-2">Belum ada barang tersedia</p>
                                            <a href="barang/list.php" class="text-primary text-decoration-none small">
                                                Tambah Barang Pertama
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

</body>
</html>