<?php
require '../auth.php';
require '../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../dashboard_user.php");
    exit;
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM barang WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$kategori = $conn->query("SELECT * FROM kategori");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $kategori_id = $_POST['kategori_id'];
    $stok = $_POST['stok'];
    $harga = $_POST['harga'];

    if (!empty($nama) && is_numeric($stok) && is_numeric($harga)) {
        $stmt = $conn->prepare("UPDATE barang SET nama_barang=?, kategori_id=?, stok=?, harga=? WHERE id=?");
        $stmt->bind_param("siiii", $nama, $kategori_id, $stok, $harga, $id);
        $stmt->execute();
        header("Location: list.php");
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Barang</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
<div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-6">Edit Barang</h2>
    <form method="POST" class="space-y-4">
        <div>
            <label class="block mb-1 font-medium">Nama Barang</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($data['nama_barang']) ?>"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        <div>
            <label class="block mb-1 font-medium">Kategori</label>
            <select name="kategori_id"
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <?php while ($row = $kategori->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>" <?= $row['id'] == $data['kategori_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($row['nama_kategori']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <label class="block mb-1 font-medium">Stok</label>
            <input type="number" name="stok" value="<?= $data['stok'] ?>"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        <div>
            <label class="block mb-1 font-medium">Harga</label>
            <input type="number" name="harga" value="<?= $data['harga'] ?>"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        <div class="flex gap-3 pt-4">
            <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded transition">
                Update
            </button>
            <a href="list.php"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-4 py-2 rounded transition">
                Kembali
            </a>
        </div>
    </form>
</div>
</body>
</html>
