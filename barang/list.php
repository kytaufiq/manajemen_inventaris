<?php
require '../auth.php';
require '../koneksi.php';

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
    <title>Daftar Barang</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
<div class="max-w-6xl mx-auto mt-10 px-4">
    <h2 class="text-3xl font-bold mb-6">Data Barang</h2>

    <!-- Form Pencarian -->
    <form method="get" class="mb-6">
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-4">
            <input type="text" name="cari"
                   placeholder="Cari nama barang..."
                   value="<?= htmlspecialchars($cari) ?>"
                   class="w-full sm:w-1/2 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit"
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
                Cari
            </button>
        </div>
    </form>

    <?php if ($_SESSION['role'] == 'admin'): ?>
        <a href="tambah.php"
            class="inline-flex items-center gap-2 mb-4 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
        Tambah Barang
        </a>

    <?php endif; ?>

        

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white shadow-md rounded">
            <thead class="bg-gray-200 text-gray-700">
                <tr>
                    <th class="py-3 px-4 text-left">No</th>
                    <th class="py-3 px-4 text-left">Nama Barang</th>
                    <th class="py-3 px-4 text-left">Kategori</th>
                    <th class="py-3 px-4 text-left">Stok</th>
                    <th class="py-3 px-4 text-left">Harga</th>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <th class="py-3 px-4 text-left">Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4"><?= $no++ ?></td>
                        <td class="py-3 px-4"><?= htmlspecialchars($row['nama_barang']) ?></td>
                        <td class="py-3 px-4"><?= htmlspecialchars($row['nama_kategori'] ?? 'Tanpa Kategori') ?></td>
                        <td class="py-3 px-4"><?= htmlspecialchars($row['stok']) ?></td>
                        <td class="py-3 px-4">Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <td class="py-3 px-4 space-x-2">
                                <a href="edit.php?id=<?= $row['id'] ?>"
                                   class="inline-block bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-sm">
                                    Edit
                                </a>
                                <a href="hapus.php?id=<?= $row['id'] ?>"
                                   onclick="return confirm('Yakin hapus?')"
                                   class="inline-block bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                    Hapus
                                </a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
