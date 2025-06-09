<?php
require 'auth.php';
require 'koneksi.php';

// Pastikan hanya user yang bisa akses
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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-5xl mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-medium text-slate-800">Data Barang</h1>
                    <p class="text-sm text-slate-500 mt-1">Selamat datang, <?= $_SESSION['username'] ?></p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="dashboard_user.php" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        Dashboard
                    </a>
                    <a href="logout.php" 
                       class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-6 py-8">
        <!-- Breadcrumb -->
        <div class="flex items-center space-x-2 text-sm text-slate-500 mb-6">
            <a href="dashboard_user.php" class="hover:text-slate-700">Dashboard</a>
            <span>›</span>
            <span class="text-slate-700 font-medium">Data Barang</span>
        </div>

        <!-- Search Section -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-slate-800">Pencarian Barang</h3>
            </div>
            
            <form method="get" class="flex gap-3">
                <input type="text" name="cari"
                       placeholder="Masukkan nama barang yang ingin dicari..."
                       value="<?= htmlspecialchars($cari) ?>"
                       class="flex-1 border border-slate-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Cari
                </button>
                <?php if ($cari !== ''): ?>
                    <a href="list.php" 
                       class="bg-slate-500 hover:bg-slate-600 text-white px-6 py-3 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Reset
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <!-- Table Header -->
            <div class="bg-gradient-to-r from-slate-50 to-slate-100 px-6 py-4 border-b border-slate-200">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-slate-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-slate-800">
                        <?= $cari !== '' ? "Hasil Pencarian: \"$cari\"" : "Daftar Semua Barang" ?>
                    </h3>
                </div>
            </div>

            <!-- Table Content -->
            <div class="overflow-x-auto">
                <?php if ($result->num_rows > 0): ?>
                    <table class="min-w-full">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    No
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Nama Barang
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Kategori
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Stok
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                    Harga
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors duration-150">
                                    <td class="px-6 py-4 text-sm text-slate-700">
                                        <?= $no++ ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-slate-800"><?= htmlspecialchars($row['nama_barang']) ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?= htmlspecialchars($row['nama_kategori'] ?? 'Tanpa Kategori') ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium 
                                                <?= $row['stok'] <= 10 ? 'text-red-600' : ($row['stok'] <= 20 ? 'text-amber-600' : 'text-emerald-600') ?>">
                                                <?= htmlspecialchars($row['stok']) ?>
                                            </span>
                                            <?php if ($row['stok'] <= 10): ?>
                                                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Stok Rendah
                                                </span>
                                            <?php elseif ($row['stok'] <= 20): ?>
                                                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                    Stok Terbatas
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-slate-800">
                                        Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <!-- Empty State -->
                    <div class="text-center py-16">
                        <div class="w-24 h-24 mx-auto mb-6 bg-slate-100 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-slate-800 mb-2">
                            <?= $cari !== '' ? 'Tidak ada hasil pencarian' : 'Belum ada data barang' ?>
                        </h3>
                        <p class="text-slate-600 mb-6 max-w-md mx-auto">
                            <?= $cari !== '' ? "Tidak ditemukan barang dengan kata kunci \"$cari\". Coba gunakan kata kunci lain." : 'Saat ini belum ada barang yang terdaftar dalam sistem.' ?>
                        </p>
                        <?php if ($cari !== ''): ?>
                            <a href="list.php" 
                               class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg text-sm font-medium transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                                </svg>
                                Lihat Semua Barang
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Info Card -->
        <?php if ($result->num_rows > 0): ?>
            <div class="mt-6 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-slate-800">Total Data Ditampilkan</h4>
                            <p class="text-xs text-slate-500">Jumlah barang yang sesuai dengan pencarian</p>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-slate-700">
                        <?= $result->num_rows ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="mt-16 py-6 border-t bg-white">
        <div class="max-w-5xl mx-auto px-6 text-center">
            <p class="text-slate-500 text-sm">All Rights Reserved | © App Inventaris Barang - 2025</p>
        </div>
    </footer>
</body>
</html>