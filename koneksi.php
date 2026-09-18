<?php
// Koneksi ke Database ct-rex via PDO
// Laragon default: host=localhost, user=root, pass=(empty), port=3306

$host   = 'localhost';
$dbname = 'ct-rex';
$user   = 'root';
$pass   = '';
$port   = 3306;

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die('Koneksi database gagal: ' . $e->getMessage());
}
