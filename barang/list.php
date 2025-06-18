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
                <img src="../img/logo.png" alt="Logo" class="w-8 h-8 sm:w-10 sm:h-10 object-contain rounded-full shadow" />
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
        <a href="../dashboard_admin.php" class="block py-3 px-4 rounded-lg transition-all duration-200 
            <?= $active_menu == 'dashboard' ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-500 font-semibold shadow-sm' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' ?>">
            <div class="flex items-center space-x-3">
                <span class="text-lg">📊</span>
                <span>Dashboard</span>
            </div>
        </a>
        
        <!-- Kelola Barang Menu -->
        <a href="list.php" class="block py-3 px-4 rounded-lg transition-all duration-200 
            <?= $active_menu == 'barang' ? 'bg-green-50 text-green-700 border-l-4 border-green-500 font-semibold shadow-sm' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' ?>">
            <div class="flex items-center space-x-3">
                <span class="text-lg">📦</span>
                <span>Kelola Barang</span>
            </div>
        </a>
        
        <!-- Kelola Kategori Menu -->
        <a href="../kategori/list.php" class="block py-3 px-4 rounded-lg transition-all duration-200 
            <?= $active_menu == 'kategori' ? 'bg-purple-50 text-purple-700 border-l-4 border-purple-500 font-semibold shadow-sm' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' ?>">
            <div class="flex items-center space-x-3">
                <span class="text-lg">🏷️</span>
                <span>Kelola Kategori</span>
            </div>
        </a>
        
        <!-- Divider -->
        <div class="border-t border-gray-200 my-2"></div>
        
        <!-- Logout Menu -->
        <a href="../logout.php" class="block py-3 px-4 rounded-lg transition-all duration-200 text-red-600 hover:bg-red-50 hover:text-red-700">
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
    <!-- Page Header -->
    <div class="mb-6 sm:mb-8">
        <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
            <a href="../dashboard_admin.php" class="hover:text-gray-700">Dashboard</a>
            <span>›</span>
            <span class="text-gray-700 font-medium">Kelola Barang</span>
        </div>
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">Data Barang</h2>
        <p class="text-sm sm:text-base text-gray-500">Kelola dan pantau inventori barang Anda</p>
    </div>

    <!-- Content Card -->
    <div class="bg-white rounded-lg shadow-sm border">
        <!-- Card Header dengan Search dan Tombol Tambah -->
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <!-- Form Pencarian -->
                <form method="get" class="flex-1 max-w-md">
                    <div class="flex gap-2">
                        <input type="text" name="cari"
                               placeholder="Cari nama barang..."
                               value="<?= htmlspecialchars($cari) ?>"
                               class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Tombol Tambah  -->
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <a href="tambah.php"
                       class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Barang
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Table Content -->
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
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if ($result->num_rows > 0): ?>
                        <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
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
                                    <span class="text-sm 
                                        <?= $row['stok'] <= 10 ? 'text-red-600 font-semibold' : ($row['stok'] <= 20 ? 'text-yellow-600 font-medium' : 'text-green-600') ?>">
                                        <?= htmlspecialchars($row['stok']) ?>
                                        <?php if ($row['stok'] <= 10): ?>
                                            <span class="text-xs text-red-500 ml-1">(Rendah)</span>
                                        <?php elseif ($row['stok'] <= 20): ?>
                                            <span class="text-xs text-yellow-500 ml-1">(Terbatas)</span>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                                </td>
                                <?php if ($_SESSION['role'] == 'admin'): ?>
                                    <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex items-center space-x-2">
                                            <a href="edit.php?id=<?= $row['id'] ?>"
                                               class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </a>
                                            <a href="hapus.php?id=<?= $row['id'] ?>"
                                               onclick="return confirm('Yakin ingin menghapus barang ini?')"
                                               class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Hapus
                                            </a>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= $_SESSION['role'] == 'admin' ? '6' : '5' ?>" class="px-4 sm:px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    <h3 class="text-sm font-medium text-gray-900 mb-1">
                                        <?= $cari !== '' ? 'Tidak ada hasil pencarian' : 'Belum ada barang' ?>
                                    </h3>
                                    <p class="text-sm text-gray-500 mb-4">
                                        <?= $cari !== '' ? "Tidak ditemukan barang dengan kata kunci '{$cari}'" : 'Mulai dengan menambahkan barang pertama Anda' ?>
                                    </p>
                                    <?php if ($_SESSION['role'] == 'admin'): ?>
                                        <?php if ($cari !== ''): ?>
                                            <a href="list.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                ← Kembali ke daftar barang
                                            </a>
                                        <?php else: ?>
                                            <a href="tambah.php" class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                                Tambah Barang Pertama
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
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