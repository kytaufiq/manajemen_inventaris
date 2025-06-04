<?php
require '../auth.php';
require '../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../dashboard_user.php");
    exit;
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM kategori WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    if (!empty($nama)) {
        $stmt = $conn->prepare("UPDATE kategori SET nama_kategori=? WHERE id=?");
        $stmt->bind_param("si", $nama, $id);
        $stmt->execute();
        header("Location: list.php");
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kategori</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
<div class="max-w-lg mx-auto mt-10 p-6 bg-white shadow-md rounded-lg">
    <h2 class="text-2xl font-bold mb-6 text-center">Edit Kategori</h2>
    <form method="POST" class="space-y-4">
        <div>
            <label class="block font-medium mb-1">Nama Kategori</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($data['nama_kategori']) ?>"
                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        <div class="flex justify-between items-center pt-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Update
            </button>
            <a href="list.php" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition">
                Kembali
            </a>
        </div>
    </form>
</div>
</body>
</html>
