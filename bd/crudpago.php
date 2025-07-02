<?php
// filepath: c:\xampp\htdocs\inerva\bd\crudpago.php
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$folio_cxc = $_POST['folio_cxc'] ?? '';
$fecha_pago = $_POST['fecha_pago'] ?? date('Y-m-d');
$importe_pago = $_POST['importe_pago'] ?? 0;
$metodo_pago = $_POST['metodo_pago'] ?? '';

try {
    // Obtener saldo actual
    $stmt = $conexion->prepare("SELECT saldo FROM cxc WHERE folio_cxc=:folio");
    $stmt->bindParam(':folio', $folio_cxc);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $saldo_actual = $row ? $row['saldo'] : 0;

    // Calcular nuevo saldo
    $nuevo_saldo = $saldo_actual - $importe_pago;
    if ($nuevo_saldo < 0) $nuevo_saldo = 0;

    // Insertar pago
    $stmt = $conexion->prepare("INSERT INTO pago (folio_cxc, fecha_pago, importe, metodo, saldoini,saldofin) VALUES (:folio, :fecha, :importe, :metodo, :saldoini, :saldofin)");
    $stmt->bindParam(':folio', $folio_cxc);
    $stmt->bindParam(':fecha', $fecha_pago);
    $stmt->bindParam(':importe', $importe_pago);
    $stmt->bindParam(':metodo', $metodo_pago);
    $stmt->bindParam(':saldofin', $nuevo_saldo);
    $stmt->bindParam(':saldoini', $saldo_actual);
    $stmt->execute();

    // Actualizar saldo en cxc
    $stmt = $conexion->prepare("UPDATE cxc SET saldo=:saldo WHERE folio_cxc=:folio");
    $stmt->bindParam(':saldo', $nuevo_saldo);
    $stmt->bindParam(':folio', $folio_cxc);
    $stmt->execute();

    echo json_encode(['status' => 'ok']);
} catch(Exception $e) {
    echo json_encode(['status' => 'error', 'mensaje' => $e->getMessage()]);
}
$conexion = null;