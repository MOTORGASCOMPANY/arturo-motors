<?php
// Script para restablecer los datos y que el "efectivo del día anterior" sea 900
// Fórmula: monto_cierre - efectivo_ingresos = 900

$host = 'localhost';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("USE laravel");
    
    // 1. Desactivar foreign key checks temporalmente
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    echo "Foreign key checks desactivados.\n";
    
    // 1. TRUNCAR las tablas en el orden correcto
    echo "Truncando tables...\n";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("TRUNCATE TABLE movimientos_caja");
    $pdo->exec("TRUNCATE TABLE sesiones_caja");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "Tables truncadas.\n";
    
    // 2. Insertar una sesión cerrada con los datos que dan 900
    // Fórmula: monto_cierre - efectivo_ingresos = 900
    // Ejemplo: monto_cierre = 2600, efectivo_ingresos = 1700 -> 2600 - 1700 = 900
    // La sesión fue cerrada el 2026-09-02 16:35:01 por Felipe Guerrero (ID 1)
    
    $pdo->exec("INSERT INTO sesiones_caja (id, monto_apertura, monto_cierre, abierta_en, cerrada_en, estado, abierta_por) VALUES (1, 800, 2600, '2026-09-02 16:30:00', '2026-09-02 16:35:01', 'cerrada', 1)");
    echo "Session insertada con ID 1.\n";
    
    // 3. Insertar movimientos que den efectivo_ingresos = 1700
    // Insertar movimientos de ingreso en efectivo que sumen 1700
    // Columna: usuario_id, monto, metodo_pago, tipo, sesion_caja_id
    // Felipe Guerrero es ID 1
    
    $pdo->exec("INSERT INTO movimientos_caja (id, sesion_caja_id, usuario_id, metodo_pago, tipo, monto) VALUES (1, 1, 1, 'efectivo', 'ingreso', 1000)");
    $pdo->exec("INSERT INTO movimientos_caja (id, sesion_caja_id, usuario_id, metodo_pago, tipo, monto) VALUES (2, 1, 1, 'efectivo', 'ingreso', 700)");
    $pdo->exec("INSERT INTO movimientos_caja (id, sesion_caja_id, usuario_id, metodo_pago, tipo, monto) VALUES (3, 1, 1, 'tarjeta', 'ingreso', 500)");  // No cuenta para el cálculo
    $pdo->exec("INSERT INTO movimientos_caja (id, sesion_caja_id, usuario_id, metodo_pago, tipo, monto) VALUES (4, 1, 1, 'efectivo', 'egreso', 200)");  // Egresos, no cuentan para efectivo_ingresos
    echo "Movimientos insertados.\n";
    
    // 4. Reactivar foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "Foreign key checks reactivados.\n";
    
    // 4. Verificar el cálculo
    echo "\nVerificando cálculo:\n";
    
    // Efectivo ingresos (solo metodo_pago = 'efectivo' y tipo = 'ingreso')
    $stmt = $pdo->query("SELECT SUM(monto) as total_efectivo FROM movimientos_caja WHERE sesion_caja_id = 1 AND metodo_pago = 'efectivo' AND tipo = 'ingreso'");
    $row = $stmt->fetch();
    $efectivo_ingresos = $row['total_efectivo'] ?? 0;
    
    // Monto cierre
    $stmt2 = $pdo->query("SELECT monto_cierre FROM sesiones_caja WHERE id = 1");
    $row2 = $stmt2->fetch();
    $monto_cierre = $row2['monto_cierre'] ?? 0;
    
    echo "monto_cierre: " . number_format($monto_cierre, 2) . "\n";
    echo "efectivo_ingresos: " . number_format($efectivo_ingresos, 2) . "\n";
    echo "Resultado: " . number_format($monto_cierre - $efectivo_ingresos, 2) . " (debe ser 900)\n";
    
    echo "\n¡Datos restablecidos! El 'efectivo del día anterior' ahora mostrará S/ 900.00\n";
    echo "en ambos módulos: Abrir Caja y Reporte de Caja.\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}