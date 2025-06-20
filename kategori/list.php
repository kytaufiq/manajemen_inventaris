<?php
require '../auth.php';
require '../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../dashboard_user.php");
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
    case 'hapus.php':
        // Untuk halaman tambah/edit/hapus, cek folder
        if (strpos($_SERVER['REQUEST_URI'], 'barang') !== false) {
            $active_menu = 'barang';
        } elseif (strpos($_SERVER['REQUEST_URI'], 'kategori') !== false) {
            $active_menu = 'kategori';
        }
        break;
}

$result = $conn->query("SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangku - Kelola Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 264px;
        }
        
        body {
            background-color: #f8f9fa;
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
            .main-content {
                margin-left: var(--sidebar-width);
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
        
        .logo-img {
            width: 32px;
            height: 32px;
            object-fit: contain;
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
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
                <img src="../img/logo.png" alt="Logo" class="logo-img" />
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
        <a href="../dashboard_admin.php" class="nav-link <?= $active_menu == 'dashboard' ? 'active dashboard' : '' ?>">
            <span style="font-size: 18px;">📊</span>
            <span>Dashboard</span>
        </a>

        <!-- Kelola Barang Menu -->
        <a href="../barang/list.php" class="nav-link <?= $active_menu == 'barang' ? 'active barang' : '' ?>">
            <span style="font-size: 18px;">📦</span>
            <span>Kelola Barang</span>
        </a>

        <!-- Kelola Kategori Menu -->
        <a href="list.php" class="nav-link <?= $active_menu == 'kategori' ? 'active kategori' : '' ?>">
            <span style="font-size: 18px;">🏷️</span>
            <span>Kelola Kategori</span>
        </a>

        <!-- Divider -->
        <hr class="my-3">

        <!-- Logout Menu -->
        <a href="../logout.php" class="nav-link logout">
            <span style="font-size: 18px;">🔓</span>
            <span>Logout</span>
        </a>
    </nav>
</aside>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Main Content -->
<main class="main-content">
    <div class="container-fluid px-3 px-sm-4 px-lg-5">
        <!-- Page Header -->
        <div class="mb-4 mb-sm-5">
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="../dashboard_admin.php" class="text-muted text-decoration-none">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active fw-medium text-dark" aria-current="page">
                        Kelola Kategori
                    </li>
                </ol>
            </nav>
            <h2 class="fs-5 fs-sm-4 fw-medium text-secondary mb-2">Data Kategori</h2>
            <p class="text-muted mb-0">Kelola kategori untuk mengorganisir barang Anda</p>
        </div>

        <!-- Content Card -->
        <div class="card border-0 shadow-sm">
            <!-- Card Header dengan Tombol Tambah -->
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="fs-6 fs-sm-5 fw-medium text-dark mb-0">Daftar Kategori</h3>
                    <a href="tambah.php" class="btn btn-success btn-sm d-inline-flex align-items-center">
                        <i class="bi bi-plus-lg me-2"></i>
                        Tambah Kategori
                    </a>
                </div>
            </div>

            <!-- Table Content -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="px-3 px-sm-4 py-3 text-uppercase small fw-medium text-muted">No</th>
                                <th scope="col" class="px-3 px-sm-4 py-3 text-uppercase small fw-medium text-muted">Nama Kategori</th>
                                <th scope="col" class="px-3 px-sm-4 py-3 text-uppercase small fw-medium text-muted">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="px-3 px-sm-4 py-3 small text-dark">
                                            <?= $no++ ?>
                                        </td>
                                        <td class="px-3 px-sm-4 py-3">
                                            <div class="small fw-medium text-dark"><?= htmlspecialchars($row['nama_kategori']) ?></div>
                                        </td>
                                        <td class="px-3 px-sm-4 py-3">
                                            <div class="d-flex gap-2">
                                                <a href="edit.php?id=<?= $row['id'] ?>" 
                                                   class="btn btn-warning btn-sm d-inline-flex align-items-center">
                                                    <i class="bi bi-pencil me-1"></i>
                                                    Edit
                                                </a>
                                                <a href="hapus.php?id=<?= $row['id'] ?>" 
                                                   onclick="return confirm('Yakin ingin menghapus kategori ini?')"
                                                   class="btn btn-danger btn-sm d-inline-flex align-items-center">
                                                    <i class="bi bi-trash me-1"></i>
                                                    Hapus
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="px-3 px-sm-4 py-5 text-center text-muted">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-tags display-4 text-muted mb-3"></i>
                                            <p class="small text-muted mb-2">Belum ada kategori</p>
                                            <a href="tambah.php" class="text-primary text-decoration-none small">
                                                Tambah Kategori Pertama
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>