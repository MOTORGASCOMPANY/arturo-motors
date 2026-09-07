<?php
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("USE laravel");
    
    // Check the structure of movimientos_caja
    $stmt = $pdo->query("SHOW COLUMNS FROM movimientos_caja");
    echo "Estructura de movimientos_caja:\n";
    foreach ($stmt as $column) {
        echo " - " . $column->Field . " (" . $column->Type . ")\n";
    }
    
    echo "\nEstructura de sesiones_caja:\n";
    $stmt2 = $pdo->query("SHOW COLUMNS FROM sesiones_caja");
    foreach ($stmt2 as $column) {
        echo " - " . $column->Field . " (" . $column->Type . ")\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}