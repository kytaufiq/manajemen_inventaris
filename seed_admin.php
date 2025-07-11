<?php
require 'koneksi.php';

$username = 'zaky rahman';
$password = 'admin123'; 
$role = 'admin';


$hashedPassword = password_hash($password, PASSWORD_DEFAULT);


$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "User sudah ada!";
} else {
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashedPassword, $role);
    if ($stmt->execute()) {
        echo "User berhasil ditambahkan dengan password ter-hash.";
    } else {
        echo "Gagal menambahkan user.";
    }
}
?>
