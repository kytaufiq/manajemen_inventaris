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
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    screens: {
                        'sidebar-lg': '1024px',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Header -->
<header class="bg-white shadow-sm border-b fixed top-0 left-0 right-0 z-50">
    <div class="px-4 sm:px-6 py-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">               
                <img src="img/logo.png" alt="Logo" class="w-8 h-8 sm:w-10 sm:h-10 object-contain rounded-full shadow" />
                <h1 class="text-xl sm:text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-yellow-500 to-pink-500 drop-shadow-sm tracking-wide">
                    Barangku
                </h1>
            </div>

            <div class="flex items-center space-x-2 sm:space-x-4">
                <span class="text-gray-600 text-sm sm:text-base hidden sm:block font-semibold">
                <?= $_SESSION['username'] ?>
                </span>
                <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-700 text-sm font-semibold">
                    <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?><?= strtoupper(explode(' ', $_SESSION['username'])[1][0] ?? '') ?>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Menu Button -->
<button id="mobile-menu-btn" class="fixed top-4 left-4 z-60 sidebar-lg:hidden bg-white p-2 rounded shadow-md">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</button>

<!-- Sidebar -->
<aside id="sidebar" class="fixed top-16 left-0 h-full w-64 bg-white border-r shadow-sm z-40 transform -translate-x-full sidebar-lg:translate-x-0 transition-transform duration-300">
    <nav class="mt-4 px-4 space-y-2">
        <!-- Dashboard Menu -->
        <a href="dashboard_admin.php" class="block py-3 px-4 rounded-lg transition-all duration-200 
            <?= $active_menu == 'dashboard' ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-500 font-semibold shadow-sm' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' ?>">
            <div class="flex items-center space-x-3">
                <span class="text-lg">📊</span>
                <span>Dashboard</span>
            </div>
        </a>
        
        <!-- Kelola Barang Menu -->
        <a href="barang/list.php" class="block py-3 px-4 rounded-lg transition-all duration-200 
            <?= $active_menu == 'barang' ? 'bg-green-50 text-green-700 border-l-4 border-green-500 font-semibold shadow-sm' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' ?>">
            <div class="flex items-center space-x-3">
                <span class="text-lg">📦</span>
                <span>Kelola Barang</span>
            </div>
        </a>
        
        <!-- Kelola Kategori Menu -->
        <a href="kategori/list.php" class="block py-3 px-4 rounded-lg transition-all duration-200 
            <?= $active_menu == 'kategori' ? 'bg-purple-50 text-purple-700 border-l-4 border-purple-500 font-semibold shadow-sm' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' ?>">
            <div class="flex items-center space-x-3">
                <span class="text-lg">🏷️</span>
                <span>Kelola Kategori</span>
            </div>
        </a>

        <!-- Divider -->
        <div class="border-t border-gray-200 my-2"></div>
        
        <!-- Logout Menu -->
        <a href="logout.php" class="block py-3 px-4 rounded-lg transition-all duration-200 text-red-600 hover:bg-red-50 hover:text-red-700">
            <div class="flex items-center space-x-3">
                <span class="text-lg">🔓</span>
                <span>Logout</span>
            </div>
        </a>
    </nav>
</aside>

<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden sidebar-lg:hidden"></div>

<!-- Main Content -->
<main class="pt-20 pb-8 px-4 sm:px-6 lg:px-8 sidebar-lg:ml-64 max-w-7xl mx-auto sidebar-lg:max-w-none">
    <div class="mb-6 sm:mb-8">
        <h2 class="text-lg sm:text-xl font-medium text-gray-700 mb-2">Dashboard Admin</h2>
        <p class="text-sm sm:text-base text-gray-500">Pantau inventori dan kelola sistem dengan mudah</p>
    </div>

    <!-- Ringkasan Sistem -->
    <div class="mb-8">
        <h3 class="text-base sm:text-lg font-medium text-gray-800 mb-4">Ringkasan Sistem</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <!-- Total Barang -->
            <div class="bg-white rounded-lg shadow-sm border p-4 sm:p-6 text-center hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div class="text-2xl sm:text-3xl font-bold text-blue-600 mb-2"><?= $total_barang ?></div>
                <div class="text-sm text-gray-600">Total Barang</div>
            </div>
            
            <!-- Total Kategori -->
            <div class="bg-white rounded-lg shadow-sm border p-4 sm:p-6 text-center hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
                <div class="text-2xl sm:text-3xl font-bold text-green-600 mb-2"><?= $total_kategori ?></div>
                <div class="text-sm text-gray-600">Total Kategori</div>
            </div>
            
            <!-- Total Stok -->
            <div class="bg-white rounded-lg shadow-sm border p-4 sm:p-6 text-center hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="text-2xl sm:text-3xl font-bold text-orange-600 mb-2"><?= number_format($total_stok) ?></div>
                <div class="text-sm text-gray-600">Total Stok</div>
            </div>
        </div>
    </div>

    <!-- Daftar Barang -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-base sm:text-lg font-medium text-gray-800">Daftar Barang</h3>
                <a href="barang/list.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Lihat Semua →
                </a>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            No
                        </th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama Barang
                        </th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kategori
                        </th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Stok
                        </th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Harga
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if ($barang_list_result->num_rows > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = $barang_list_result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $no++ ?>
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($row['nama_barang']) ?></div>
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <?= htmlspecialchars($row['nama_kategori'] ?? 'Tanpa Kategori') ?>
                                    </span>
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 
                                        <?= $row['stok'] <= 10 ? 'text-red-600 font-semibold' : ($row['stok'] <= 20 ? 'text-yellow-600 font-medium' : 'text-green-600') ?>">
                                        <?= $row['stok'] ?>
                                        <?php if ($row['stok'] <= 10): ?>
                                            <span class="text-xs text-red-500 ml-1">(Stok Rendah)</span>
                                        <?php elseif ($row['stok'] <= 20): ?>
                                            <span class="text-xs text-yellow-500 ml-1">(Stok Terbatas)</span>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-4 sm:px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    <p class="text-sm text-gray-500">Belum ada barang tersedia</p>
                                    <a href="barang/list.php" class="mt-2 text-blue-600 hover:text-blue-800 text-sm">
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
</main>


</body>
</html>