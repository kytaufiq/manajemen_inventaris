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
        // Untuk halaman tambah/edit, cek folder
        if (strpos($_SERVER['REQUEST_URI'], 'barang') !== false) {
            $active_menu = 'barang';
        } elseif (strpos($_SERVER['REQUEST_URI'], 'kategori') !== false) {
            $active_menu = 'kategori';
        }
        break;
}

$cari = isset($_GET['cari']) ? trim($_GET['cari']) : '';

if ($cari !== '') {
    $stmt = $conn->prepare("SELECT barang.*, kategori.nama_kategori 
                            FROM barang 
                            LEFT JOIN kategori ON barang.kategori_id = kategori.id
                            WHERE barang.nama_barang LIKE ?");
    $searchTerm = "%{$cari}%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT barang.*, kategori.nama_kategori 
                            FROM barang 
                            LEFT JOIN kategori ON barang.kategori_id = kategori.id");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangku - Kelola Barang</title>
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
        
        /* Breadcrumb */
        .breadcrumb-nav {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 8px;
        }
        
        .breadcrumb-nav a {
            color: #6b7280;
            text-decoration: none;
        }
        
        .breadcrumb-nav a:hover {
            color: #374151;
        }
        
        .breadcrumb-nav .active {
            color: #374151;
            font-weight: 500;
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
        
        /* Button Styles */
        .btn-search {
            background-color: #3b82f6;
            border-color: #3b82f6;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .btn-search:hover {
            background-color: #2563eb;
            border-color: #2563eb;
            color: white;
        }
        
        .btn-add {
            background-color: #22c55e;
            border-color: #22c55e;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .btn-add:hover {
            background-color: #16a34a;
            border-color: #16a34a;
            color: white;
        }
        
        .btn-edit {
            background-color: #eab308;
            border-color: #eab308;
            color: white;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s ease;
        }
        
        .btn-edit:hover {
            background-color: #ca8a04;
            border-color: #ca8a04;
            color: white;
        }
        
        .btn-delete {
            background-color: #ef4444;
            border-color: #ef4444;
            color: white;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s ease;
        }
        
        .btn-delete:hover {
            background-color: #dc2626;
            border-color: #dc2626;
            color: white;
        }
        
        /* Empty State */
        .empty-state {
            padding: 48px 16px;
            text-align: center;
        }
        
        .empty-state i {
            font-size: 48px;
            color: #d1d5db;
            margin-bottom: 16px;
        }
        
        .empty-state h5 {
            font-size: 0.875rem;
            font-weight: 500;
            color: #111827;
            margin-bottom: 4px;
        }
        
        .empty-state p {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 16px;
        }
        
        /* Search Input */
        .search-input {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 0.875rem;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        /* Table Styles */
        .table th {
            font-size: 0.75rem;
            font-weight: 500;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 12px 16px;
        }
        
        .table td {
            padding: 16px;
            font-size: 0.875rem;
        }
        
        .table tbody tr:hover {
            background-color: #f9fafb;
        }
        
        /* Category Badge */
        .category-badge {
            background-color: #dcfce7;
            color: #166534;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
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
                <img src="../img/logo.png" alt="Logo" class="rounded-circle shadow" style="width: 32px; height: 32px; object-fit: contain;" />
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
        <a href="list.php" class="nav-link <?= $active_menu == 'barang' ? 'active barang' : '' ?>">
            <span style="font-size: 18px;">📦</span>
            <span>Kelola Barang</span>
        </a>
        
        <!-- Kelola Kategori Menu -->
        <a href="../kategori/list.php" class="nav-link <?= $active_menu == 'kategori' ? 'active kategori' : '' ?>">
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
    <div class="container-fluid px-3 px-sm-4 px-lg-5" style="max-width: none;">
        <!-- Page Header -->
        <div class="mb-4 mb-sm-5">
            <div class="breadcrumb-nav mb-2">
                <a href="../dashboard_admin.php">Dashboard</a>
                <span class="mx-2">›</span>
                <span class="active">Kelola Barang</span>
            </div>
            <h2 class="fs-4 fs-sm-3 fw-bold text-dark mb-2">Data Barang</h2>
            <p class="text-muted mb-0">Kelola dan pantau inventori barang Anda</p>
        </div>

        <!-- Content Card -->
        <div class="card border-0 shadow-sm">
            <!-- Card Header dengan Search dan Tombol Tambah -->
            <div class="card-header bg-white border-bottom py-3">
                <div class="row align-items-center g-3">
                    <!-- Form Pencarian -->
                    <div class="col-12 col-sm-8 col-md-6">
                        <form method="get" class="d-flex gap-2">
                            <input type="text" name="cari" 
                                   placeholder="Cari nama barang..." 
                                   value="<?= htmlspecialchars($cari) ?>" 
                                   class="form-control search-input">
                            <button type="submit" class="btn btn-search">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Tombol Tambah -->
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <div class="col-12 col-sm-4 col-md-6 text-end">
                            <a href="tambah.php" class="btn btn-add d-inline-flex align-items-center gap-2">
                                <i class="bi bi-plus"></i>
                                Tambah Barang
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Table Content -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Harga</th>
                                <?php if ($_SESSION['role'] == 'admin'): ?>
                                    <th>Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <div class="fw-medium text-dark"><?= htmlspecialchars($row['nama_barang']) ?></div>
                                        </td>
                                        <td>
                                            <span class="category-badge">
                                                <?= htmlspecialchars($row['nama_kategori'] ?? 'Tanpa Kategori') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="<?= $row['stok'] <= 10 ? 'stock-low' : ($row['stok'] <= 20 ? 'stock-limited' : 'stock-good') ?>">
                                                <?= htmlspecialchars($row['stok']) ?>
                                                <?php if ($row['stok'] <= 10): ?>
                                                    <span class="stock-label text-danger">(Rendah)</span>
                                                <?php elseif ($row['stok'] <= 20): ?>
                                                    <span class="stock-label text-warning">(Terbatas)</span>
                                                <?php endif; ?>
                                            </span>
                                        </td>
                                        <td>
                                            Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                                        </td>
                                        <?php if ($_SESSION['role'] == 'admin'): ?>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit">
                                                        <i class="bi bi-pencil" style="font-size: 12px;"></i>
                                                        Edit
                                                    </a>
                                                    <a href="hapus.php?id=<?= $row['id'] ?>" 
                                                       onclick="return confirm('Yakin ingin menghapus barang ini?')" 
                                                       class="btn-delete">
                                                        <i class="bi bi-trash" style="font-size: 12px;"></i>
                                                        Hapus
                                                    </a>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?= $_SESSION['role'] == 'admin' ? '6' : '5' ?>" class="empty-state">
                                        <i class="bi bi-box"></i>
                                        <h5><?= $cari !== '' ? 'Tidak ada hasil pencarian' : 'Belum ada barang' ?></h5>
                                        <p><?= $cari !== '' ? "Tidak ditemukan barang dengan kata kunci '{$cari}'" : 'Mulai dengan menambahkan barang pertama Anda' ?></p>
                                        <?php if ($_SESSION['role'] == 'admin'): ?>
                                            <?php if ($cari !== ''): ?>
                                                <a href="list.php" class="text-primary text-decoration-none fw-medium">
                                                    ← Kembali ke daftar barang
                                                </a>
                                            <?php else: ?>
                                                <a href="tambah.php" class="btn btn-add d-inline-flex align-items-center gap-2">
                                                    <i class="bi bi-plus"></i>
                                                    Tambah Barang Pertama
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
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