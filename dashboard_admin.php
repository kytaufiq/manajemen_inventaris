<?php
require 'auth.php';
require 'koneksi.php'; // pastikan file koneksi sudah benar

if ($_SESSION['role'] != 'admin') {
    header("Location: dashboard_user.php");
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
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-6xl mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-800">Dashboard Admin</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Halo, <?= $_SESSION['username'] ?></span>
                    <a href="logout.php" 
                       class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm transition-colors">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-6 py-8">
        <!-- Welcome Message -->
        <div class="mb-8">
            <h2 class="text-xl font-medium text-gray-700 mb-2">Selamat datang di Panel Admin</h2>
            <p class="text-gray-500">Kelola sistem Anda dengan mudah melalui menu di bawah ini</p>
        </div>

        <!-- Management Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Card Manajemen Barang -->
            <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800">Manajemen Barang</h3>
                            <p class="text-gray-500 text-sm">Kelola data barang dan inventori</p>
                        </div>
                    </div>
                    
                    <div class="space-y-2 mb-6">
                        <div class="flex items-center text-sm text-gray-600">
                            <span class="w-1.5 h-1.5 bg-blue-400 rounded-full mr-2"></span>
                            Tambah dan edit barang
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <span class="w-1.5 h-1.5 bg-blue-400 rounded-full mr-2"></span>
                            Monitor stok barang
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <span class="w-1.5 h-1.5 bg-blue-400 rounded-full mr-2"></span>
                            Laporan inventori
                        </div>
                    </div>
                    
                    <a href="barang/list.php" 
                       class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded text-center block transition-colors">
                        Kelola Barang
                    </a>
                </div>
            </div>

            <!-- Card Manajemen Kategori -->
            <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-800">Manajemen Kategori</h3>
                            <p class="text-gray-500 text-sm">Kelola kategori dan klasifikasi</p>
                        </div>
                    </div>
                    
                    <div class="space-y-2 mb-6">
                        <div class="flex items-center text-sm text-gray-600">
                            <span class="w-1.5 h-1.5 bg-green-400 rounded-full mr-2"></span>
                            Buat kategori baru
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <span class="w-1.5 h-1.5 bg-green-400 rounded-full mr-2"></span>
                            Organisasi kategori
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <span class="w-1.5 h-1.5 bg-green-400 rounded-full mr-2"></span>
                            Filter dan pencarian
                        </div>
                    </div>
                    
                    <a href="kategori/list.php" 
                       class="w-full bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded text-center block transition-colors">
                        Kelola Kategori
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="mt-12">
            <h3 class="text-lg font-medium text-gray-800 mb-4">Ringkasan Sistem</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total Barang -->
                <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
                    <div class="text-2xl font-bold text-blue-600 mb-2"><?= $total_barang ?></div>
                    <div class="text-sm text-gray-600">Total Barang</div>
                </div>
                <!-- Total Kategori -->
                <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
                    <div class="text-2xl font-bold text-green-600 mb-2"><?= $total_kategori ?></div>
                    <div class="text-sm text-gray-600">Total Kategori</div>
                </div>
                <!-- Total Stok -->
                <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
                    <div class="text-2xl font-bold text-orange-600 mb-2"><?= $total_stok ?></div>
                    <div class="text-sm text-gray-600">Total Stok</div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="mt-16 py-6 border-t bg-white">
        <div class="max-w-6xl mx-auto px-6 text-center text-gray-500 text-sm">
            All Rights Reserved | © App Inventaris Barang - 2025
        </div>
    </footer>
</body>
</html>
