<?php
require 'auth.php';
require 'koneksi.php';

if ($_SESSION['role'] != 'user') {
    header("Location: ../dashboard_admin.php");
    exit;
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
    <title>Data Barang - User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
            min-height: 100vh;
        }
        
        .header-shadow {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        
        .card-custom {
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        
        .text-slate-800 { color: #1e293b; }
        .text-slate-500 { color: #64748b; }
        .text-slate-600 { color: #475569; }
        .text-slate-700 { color: #334155; }
        .text-slate-400 { color: #94a3b8; }
        
        .bg-slate-50 { background-color: #f8fafc; }
        .bg-slate-100 { background-color: #f1f5f9; }
        
        .btn-blue {
            background-color: #3b82f6;
            border-color: #3b82f6;
            color: white;
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
        }
        
        .btn-red:hover {
            background-color: #dc2626;
            border-color: #dc2626;
            color: white;
        }
        
        .btn-slate {
            background-color: #64748b;
            border-color: #64748b;
            color: white;
        }
        
        .btn-slate:hover {
            background-color: #475569;
            border-color: #475569;
            color: white;
        }
        
        .icon-wrapper {
            width: 2.5rem;
            height: 2.5rem;
            background-color: #eff6ff;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .icon-wrapper-emerald {
            background-color: #ecfdf5;
        }
        
        .breadcrumb-item a {
            color: #64748b;
            text-decoration: none;
        }
        
        .breadcrumb-item a:hover {
            color: #334155;
        }
        
        .breadcrumb-item.active {
            color: #334155;
            font-weight: 500;
        }
        
        .table-hover tbody tr:hover {
            background-color: #f8fafc;
        }
        
        .badge-blue {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .badge-red {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .badge-amber {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .text-red-600 { color: #dc2626; }
        .text-amber-600 { color: #d97706; }
        .text-emerald-600 { color: #059669; }
        
        .gradient-header {
            background: linear-gradient(90deg, #f8fafc 0%, #f1f5f9 100%);
        }
        
        .empty-state-icon {
            width: 6rem;
            height: 6rem;
            background-color: #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="bg-white header-shadow">
        <div class="container" style="max-width: 80rem;">
            <div class="row align-items-center py-3">
                <div class="col">
                    <h1 class="h4 mb-1 text-slate-800">Data Barang</h1>
                    <p class="small text-slate-500 mb-0">Selamat datang, <?= $_SESSION['username'] ?></p>
                </div>
                <div class="col-auto">
                    <div class="d-flex gap-2">
                        <a href="dashboard_user.php" class="btn btn-blue btn-sm">
                            Dashboard
                        </a>
                        <a href="logout.php" class="btn btn-red btn-sm">
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container py-4" style="max-width: 80rem;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="dashboard_user.php">Dashboard</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Data Barang</li>
            </ol>
        </nav>

        <!-- Search Section -->
        <div class="card card-custom mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-wrapper me-3">
                        <i class="bi bi-search text-primary"></i>
                    </div>
                    <h5 class="card-title mb-0 text-slate-800">Pencarian Barang</h5>
                </div>
                
                <form method="get" class="row g-2">
                    <div class="col">
                        <input type="text" name="cari" class="form-control"
                               placeholder="Masukkan nama barang yang ingin dicari..."
                               value="<?= htmlspecialchars($cari) ?>">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-blue">
                            <i class="bi bi-search me-1"></i>
                            Cari
                        </button>
                    </div>
                    <?php if ($cari !== ''): ?>
                        <div class="col-auto">
                            <a href="list.php" class="btn btn-slate">
                                <i class="bi bi-x-lg me-1"></i>
                                Reset
                            </a>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card card-custom">
            <!-- Table Header -->
            <div class="gradient-header p-3 border-bottom">
                <div class="d-flex align-items-center">
                    <i class="bi bi-box-seam text-slate-600 me-3 fs-5"></i>
                    <h5 class="mb-0 text-slate-800">
                        <?= $cari !== '' ? "Hasil Pencarian: \"$cari\"" : "Daftar Semua Barang" ?>
                    </h5>
                </div>
            </div>

            <!-- Table Content -->
            <div class="table-responsive">
                <?php if ($result->num_rows > 0): ?>
                    <table class="table table-hover mb-0">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="text-slate-600 text-uppercase small fw-semibold">No</th>
                                <th class="text-slate-600 text-uppercase small fw-semibold">Nama Barang</th>
                                <th class="text-slate-600 text-uppercase small fw-semibold">Kategori</th>
                                <th class="text-slate-600 text-uppercase small fw-semibold">Stok</th>
                                <th class="text-slate-600 text-uppercase small fw-semibold">Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-slate-700 small">
                                        <?= $no++ ?>
                                    </td>
                                    <td>
                                        <div class="fw-medium text-slate-800 small"><?= htmlspecialchars($row['nama_barang']) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge badge-blue">
                                            <?= htmlspecialchars($row['nama_kategori'] ?? 'Tanpa Kategori') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="small fw-medium 
                                                <?= $row['stok'] <= 10 ? 'text-red-600' : ($row['stok'] <= 20 ? 'text-amber-600' : 'text-emerald-600') ?>">
                                                <?= htmlspecialchars($row['stok']) ?>
                                            </span>
                                            <?php if ($row['stok'] <= 10): ?>
                                                <span class="badge badge-red ms-2">
                                                    Stok Rendah
                                                </span>
                                            <?php elseif ($row['stok'] <= 20): ?>
                                                <span class="badge badge-amber ms-2">
                                                    Stok Terbatas
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="small fw-medium text-slate-800">
                                        Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <div class="empty-state-icon">
                            <i class="bi bi-box-seam text-slate-400" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="text-slate-800 mb-2">
                            <?= $cari !== '' ? 'Tidak ada hasil pencarian' : 'Belum ada data barang' ?>
                        </h5>
                        <p class="text-slate-600 mb-4" style="max-width: 28rem; margin: 0 auto;">
                            <?= $cari !== '' ? "Tidak ditemukan barang dengan kata kunci \"$cari\". Coba gunakan kata kunci lain." : 'Saat ini belum ada barang yang terdaftar dalam sistem.' ?>
                        </p>
                        <?php if ($cari !== ''): ?>
                            <a href="list.php" class="btn btn-blue">
                                <i class="bi bi-arrow-left me-2"></i>
                                Lihat Semua Barang
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Info Card -->
        <?php if ($result->num_rows > 0): ?>
            <div class="card card-custom mt-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper icon-wrapper-emerald me-3">
                                <i class="bi bi-check-circle text-emerald-600"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-slate-800 small">Total Data Ditampilkan</h6>
                                <p class="mb-0 text-slate-500" style="font-size: 0.75rem;">Jumlah barang yang sesuai dengan pencarian</p>
                            </div>
                        </div>
                        <div class="fs-2 fw-bold text-slate-700">
                            <?= $result->num_rows ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="mt-5 py-3 border-top bg-white">
        <div class="container text-center" style="max-width: 80rem;">
            <p class="text-slate-500 small mb-0">All Rights Reserved | © App Inventaris Barang - 2025</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>