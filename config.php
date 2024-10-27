<?php
$host = 'your_host_ip';
$dbname = 'db_name';
$user = 'database_name';
$password = 'password';
$port = 'port_or_3306';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;port=$port;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Bağlantı hatası: " . $e->getMessage();
    exit;
}
?>
