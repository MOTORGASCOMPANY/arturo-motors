<?php
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('USE laravel');
    
    // Check session 1
    $stmt = $pdo->query("SELECT id, monto_apertura, monto_cierre, abierta_en, cerrada_en, estado FROM sesiones_caja WHERE id = 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $monto_cierre = $row['monto_cierre'];
    
    $stmt2 = $pdo->query("SELECT SUM(monto) as total_efectivo FROM movimientos_caja WHERE sesion_caja_id = 1 AND metodo_pago = 'efectivo' AND tipo = 'ingreso'");
    $row2 = $stmt2->fetch();
    $efectivo_ingresos = $row2['total_efectivo'] ?? 0;
    
    $resultado = $monto_cierre - $efectivo_ingresos;
    
    echo "monto_cierre: " . number_format($monto_cierre, 2) . "\n";
    echo "efectivo_ingresos: " . number_format($efectivo_ingresos, 2) . "\n";
    echo "Resultado: " . number_format($resultado, 2) . " (debe ser 900)\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}