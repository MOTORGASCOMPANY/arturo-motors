<?php
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check the laravel database
    $pdo->exec("USE laravel");
    
    // Check existing sessions
    $stmt = $pdo->query("SELECT id, monto_cierre, monto_apertura, estado, cerrada_en FROM sesiones_caja ORDER BY cerrada_en DESC LIMIT 5");
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Últimas 5 sesiones cerradas:\n";
    foreach ($sessions as $session) {
        echo "ID: " . $session['id'] . " | monto_cierre: " . $session['monto_cierre'] . " | monto_apertura: " . $session['monto_apertura'] . " | estado: " . $session['estado'] . " | cerrada_en: " . $session['cerrada_en'] . "\n";
    }
    
    // Check movements
    $stmt2 = $pdo->query("SELECT id, sesion_caja_id, metodo_pago, tipo, monto FROM movimientos_caja ORDER BY id DESC LIMIT 10");
    $movements = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nÚltimos 10 movimientos:\n";
    foreach ($movements as $mov) {
        echo "ID: " . $mov['id'] . " | sesion: " . $mov['sesion_caja_id'] . " | metodo: " . $mov['metodo_pago'] . " | tipo: " . $mov['tipo'] . " | monto: " . $mov['monto'] . "\n";
    }
    
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}