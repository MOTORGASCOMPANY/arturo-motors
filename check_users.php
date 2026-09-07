<?php
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("USE laravel");
    
    // Check users table
    $stmt = $pdo->query("SELECT id, name, email FROM users LIMIT 5");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Usuarios disponibles:\n";
    foreach ($users as $user) {
        echo "ID: " . $user['id'] . " | Name: " . $user['name'] . " | Email: " . $user['email'] . "\n";
    }
    
    // Check structure of users
    $stmt2 = $pdo->query("SHOW COLUMNS FROM users");
    echo "\nEstructura de users:\n";
    foreach ($stmt2 as $column) {
        echo " - " . $column->Field . " (" . $column->Type . ")\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}