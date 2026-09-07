<?php
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('USE laravel');
    
    $stmt = $pdo->query("SELECT id, monto_apertura, monto_cierre, abierta_en, cerrada_en, estado FROM sesiones_caja WHERE id = 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Session ID 1:" . PHP_EOL;
    echo "  abierta_en: " . var_export($row['abierta_en'], true) . PHP_EOL;
    echo "  cerrada_en: " . var_export($row['cerrada_en'], true) . PHP_EOL;
    echo "  monto_apertura: " . $row['monto_apertura'] . PHP_EOL;
    echo "  monto_cierre: " . $row['monto_cierre'] . PHP_EOL;
    echo "  estado: " . $row['estado'] . PHP_EOL;
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}