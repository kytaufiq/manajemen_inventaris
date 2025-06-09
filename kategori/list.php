<?php
require '../auth.php';
require '../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../dashboard_user.php");
    exit;
}

$result = $conn->query("SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangku - Kelola Kategori</title>
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

<!-- Sidebar -->
<aside id="sidebar" class="fixed top-16 left-0 h-full w-64 bg-white border-r shadow-sm z-40 transform -translate-x-full sidebar-lg:translate-x-0 transition-transform duration-300">
    <nav class="mt-4 px-4 space-y-2">
        <a href="../dashboard_admin.php" class="block py-2 px-4 rounded hover:bg-gray-100 text-gray-800">📊 Dashboard</a>
        <a href="../barang/list.php" class="block py-2 px-4 rounded hover:bg-gray-100 text-green-600">📦 Kelola Barang</a>
        <a href="list.php" class="block py-2 px-4 rounded bg-gray-100 text-green-600">🏷️ Kelola Kategori</a>
        <a href="../logout.php" class="block py-2 px-4 rounded hover:bg-gray-100 text-red-600">🔓 Logout</a>
    </nav>
</aside>


<!-- Main Content -->
<main class="pt-20 pb-8 px-4 sm:px-6 lg:px-8 sidebar-lg:ml-64 max-w-7xl mx-auto sidebar-lg:max-w-none">
    <!-- Page Header -->
    <div class="mb-6 sm:mb-8">
        <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
            <a href="../dashboard_admin.php" class="hover:text-gray-700">Dashboard</a>
            <span>›</span>
            <span class="text-gray-700 font-medium">Kelola Kategori</span>
        </div>
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">Data Kategori</h2>
        <p class="text-sm sm:text-base text-gray-500">Kelola kategori untuk mengorganisir barang Anda</p>
    </div>

    <!-- Content Card -->
    <div class="bg-white rounded-lg shadow-sm border">
        <!-- Card Header dengan Tombol Tambah -->
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-gray-900">Daftar Kategori</h3>
                    <p class="text-sm text-gray-500">Kelola kategori barang untuk mempermudah organisasi inventori</p>
                </div>
                <!-- Tombol Tambah -->
                <a href="tambah.php"
                   class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Kategori
                </a>
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
                            Nama Kategori
                        </th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
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
                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($row['nama_kategori']) ?></div>
                                </td>
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
                                           onclick="return confirm('Yakin ingin menghapus kategori ini?')"
                                           class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="px-4 sm:px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    <h3 class="text-sm font-medium text-gray-900 mb-1">Belum ada kategori</h3>
                                    <p class="text-sm text-gray-500 mb-4">Mulai dengan menambahkan kategori pertama Anda</p>
                                    <a href="tambah.php" class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
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
</main>


</body>
</html>