<?php
require '../auth.php';
require '../koneksi.php';

// Batasi akses hanya untuk admin
if ($_SESSION['role'] != 'admin') {
    header("Location: ../dashboard_user.php");
    exit;
}

$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM kategori WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
header("Location: list.php");