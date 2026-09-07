<?php
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('USE laravel');
    
    // Describe table
    \$stmt = \$pdo->query('DESCRIBE sesiones_caja');
    echo "Estructura de sesiones_caja:\n";
    while (\$row = \$stmt->fetch(PDO::FETCH_ASSOC)) {
        echo \"  \" . \$row['Field'] . ' ' . \$row['Type'] . PHP_EOL;
    }
    
    // Check if there's data
    \$stmt2 = \$pdo->query('SELECT * FROM sesiones_caja LIMIT 1');
    \$row2 = \$stmt2->fetch(PDO::FETCH_ASSOC);
    echo PHP_EOL . 'Datos de la sesión 1:' . PHP_EOL;
    while (\$row2) {
        foreach (\$row2 as \$k => \$v) {
            echo \"  \" . \$k . ': ' . var_export(\$v, true) . PHP_EOL;
        }
        break; // Only first row
    }
    
} catch (PDOException \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"