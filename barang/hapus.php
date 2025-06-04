<?php
require '../auth.php';
require '../koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../dashboard_user.php");
    exit;
}

$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM barang WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
header("Location: list.php");
