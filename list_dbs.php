<?php
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // List databases
    $stmt = $pdo->query('SHOW DATABASES');
    $databases = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Bases de datos disponibles:\n";
    foreach ($databases as $db) {
        echo '- ' . $db['Database'] . "\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}