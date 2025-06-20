CREATE DATABASE inventaris;

USE inventaris;

-- Tabel user
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL
);

-- Admin default
INSERT INTO users (username, password, role)
VALUES ('admin', MD5('admin123'), 'admin');

-- Tabel kategori
CREATE TABLE kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL
);

-- Tabel barang
CREATE TABLE barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(100) NOT NULL,
    stok INT NOT NULL CHECK (stok >= 0),
    harga DECIMAL(10,2) NOT NULL CHECK (harga >= 0),
    kategori_id INT,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL
);