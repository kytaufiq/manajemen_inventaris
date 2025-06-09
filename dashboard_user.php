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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-5xl mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-medium text-slate-800">Dashboard User</h1>
                    <p class="text-sm text-slate-500 mt-1">Selamat datang, <?= $_SESSION['username'] ?></p>
                </div>
                <a href="logout.php" 
                   class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    Logout
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-6 py-8">
        <!-- Welcome Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-8">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-medium text-slate-800">Halo, <?= $_SESSION['username'] ?>!</h2>
                    <p class="text-slate-600">Anda dapat melihat dan mencari data barang melalui sistem ini</p>
                </div>
            </div>
        </div>

        <!-- Main Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Data Barang Card -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4">
                    <div class="flex items-center text-white">
                        <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <h3 class="text-lg font-medium">Data Barang</h3>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-slate-600 mb-4">Jelajahi koleksi barang yang tersedia dalam sistem</p>
                    <div class="space-y-2 mb-6">
                        <div class="flex items-center text-sm text-slate-500">
                            <span class="w-1.5 h-1.5 bg-blue-400 rounded-full mr-2"></span>
                            Lihat semua barang
                        </div>
                        <div class="flex items-center text-sm text-slate-500">
                            <span class="w-1.5 h-1.5 bg-blue-400 rounded-full mr-2"></span>
                            Cari berdasarkan nama
                        </div>
                        <div class="flex items-center text-sm text-slate-500">
                            <span class="w-1.5 h-1.5 bg-blue-400 rounded-full mr-2"></span>
                            Filter berdasarkan kategori
                        </div>
                    </div>
                    <a href="list.php" 
                       class="w-full bg-blue-500 hover:bg-blue-600 text-white py-3 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Lihat Data Barang
                    </a>
                </div>
            </div>

            <!-- Quick Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-slate-800">Informasi</h3>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-600">Status Akun</span>
                        <span class="text-emerald-600 font-medium">Aktif</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-600">Akses Level</span>
                        <span class="text-blue-600 font-medium">User</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-slate-600">Last Login</span>
                        <span class="text-slate-500 text-sm">Hari ini</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ringkasan Sistem -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-lg font-medium text-slate-800 mb-4">Ringkasan Sistem</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-slate-50 rounded-lg">
                    <div class="text-2xl font-bold text-slate-700 mb-1"><?= $total_barang ?></div>
                    <div class="text-sm text-slate-500">Total Barang</div>
                </div>
                <div class="text-center p-4 bg-slate-50 rounded-lg">
                    <div class="text-2xl font-bold text-slate-700 mb-1"><?= $total_kategori ?></div>
                    <div class="text-sm text-slate-500">Kategori</div>
                </div>
                <div class="text-center p-4 bg-slate-50 rounded-lg">
                    <div class="text-2xl font-bold text-slate-700 mb-1"><?= $total_stok ?></div>
                    <div class="text-sm text-slate-500">Barang Tersedia</div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="mt-16 py-6 border-t bg-white">
        <div class="max-w-5xl mx-auto px-6 text-center">
            <p class="text-slate-500 text-sm">All Rights Reserved | © App Inventaris Barang - 2025</p>
        </div>
    </footer>
</body>
</html>
