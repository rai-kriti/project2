<?php
require_once '../includes/config.php';

$pdo = getConnection();

// Pehle se existing admin ko hatao
$pdo->prepare("DELETE FROM admins WHERE username = ?")->execute(['admin']);

// Fir naya hashed password insert karo
$password = password_hash('admin123', PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
$stmt->execute(['admin', $password]);

echo "done";
?>
