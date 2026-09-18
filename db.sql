-- Database setup untuk LPM UMB Bandung
CREATE DATABASE IF NOT EXISTS `lpm_umbandung` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `lpm_umbandung`;

-- Tabel users (admin)
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `nama` VARCHAR(100) NOT NULL,
    `role` ENUM('admin', 'editor') DEFAULT 'editor',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin: username=admin, password=admin123 (hash di bawah)
INSERT INTO `users` (`username`, `password`, `nama`, `role`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin');

-- Tabel contoh data website (artikel/berita)
CREATE TABLE IF NOT EXISTS `artikel` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `judul` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `konten` TEXT,
    `gambar` VARCHAR(255),
    `status` ENUM('draft', 'published') DEFAULT 'draft',
    `user_id` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
);

-- Tabel pengaturan website
CREATE TABLE IF NOT EXISTS `pengaturan` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `kunci` VARCHAR(100) NOT NULL UNIQUE,
    `nilai` TEXT,
    `deskripsi` VARCHAR(255)
);

INSERT INTO `pengaturan` (`kunci`, `nilai`, `deskripsi`) VALUES
('site_title', 'LPM UMB Bandung', 'Judul website'),
('site_description', 'Lembaga Pers Mahasiswa Universitas Mercu Buana Bandung', 'Deskripsi website'),
('site_logo', '', 'Path logo website');