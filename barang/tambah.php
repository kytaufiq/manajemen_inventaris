<?php
require '../auth.php';
require '../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../dashboard_user.php");
    exit;
}

$kategori = $conn->query("SELECT * FROM kategori");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $kategori_id = $_POST['kategori_id'];
    $stok = $_POST['stok'];
    $harga = $_POST['harga'];

    if (!empty($nama) && is_numeric($stok) && is_numeric($harga)) {
        $stmt = $conn->prepare("INSERT INTO barang (nama_barang, stok, harga, kategori_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siii", $nama, $stok, $harga, $kategori_id);
        $stmt->execute();
        header("Location: list.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Barang</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
<div class="max-w-xl mx-auto mt-10 bg-white shadow-md rounded-lg p-6">
    <h2 class="text-2xl font-bold mb-6 text-center">Tambah Barang</h2>
    
    <form method="POST" class="space-y-4">
        <div>
            <label class="block mb-1 font-semibold">Nama Barang</label>
            <input type="text" name="nama" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>

        <div>
            <label class="block mb-1 font-semibold">Kategori</label>
            <select name="kategori_id" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <?php while ($row = $kategori->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nama_kategori']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div>
            <label class="block mb-1 font-semibold">Stok</label>
            <input type="number" name="stok" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>

        <div>
            <label class="block mb-1 font-semibold">Harga</label>
            <input type="number" name="harga" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>

        <div class="flex justify-between items-center mt-6">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Simpan
            </button>
            <a href="list.php" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition">
                Kembali
            </a>
        </div>
    </form>
</div>
</body>
</html>
