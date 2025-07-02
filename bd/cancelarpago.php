<?php
// filepath: c:\xampp\htdocs\inerva\bd\cancelarpago.php
include_once '../bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$folio_cxc = $_POST['folio_cxc'] ?? '';
$importe= $_POST['importe'] ?? 0;
$id_pago = $_POST['id_pago'] ?? '';

// Obtener el último pago
$stmt = $conexion->prepare("SELECT  importe FROM pago WHERE id_pago=:id_pago");
$stmt->bindParam(':id_pago', $id_pago   );
$stmt->execute();
$pago = $stmt->fetch(PDO::FETCH_ASSOC);
$data = 0;
if ($pago && $pago['importe'] == $importe) {
    // Eliminar el pago
    $stmt = $conexion->prepare("UPDATE pago set edo_pago=0 WHERE id_pago=:id_pago");
    $stmt->bindParam(':id_pago', $id_pago);
    $stmt->execute();

    // Sumar el importe al saldo de la cxc
    $stmt = $conexion->prepare("UPDATE cxc SET saldo = saldo + :importe WHERE folio_cxc=:folio");
    $stmt->bindParam(':importe', $pago['importe']);
    $stmt->bindParam(':folio', $folio_cxc);
    $stmt->execute();
    $data=1;    
}
else {
    $data = json_encode(['status' => 'error', 'mensaje' => 'El importe no coincide con el pago']);
}
print json_encode($data, JSON_UNESCAPED_UNICODE); //enviar el array final en formato json a JS
// Cerrar la conexión


$conexion = null;