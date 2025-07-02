<?php
// filepath: c:\xampp\htdocs\inerva\bd\cancelarcxc.php
include_once '../bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$folio_cxc = $_POST['folio_cxc'] ?? '';
$id_cita = $_POST['id_cita'] ?? '';
$data = 0;

// Cancelar la CXC
$stmt = $conexion->prepare("UPDATE cxc SET edo_cxc=0 WHERE folio_cxc=:folio");
$stmt->bindParam(':folio', $folio_cxc);
if ($stmt->execute()) {
    $stmt = $conexion->prepare("UPDATE citap SET estado = 5 WHERE folio_citap = :id_cita");
    $stmt->bindParam(':id_cita', $id_cita);
    if ($stmt->execute()) {
        $data = 1; // Indica que la operación fue exitosa
    }
}

// Regresar la cita a estado 5

print json_encode($data, JSON_UNESCAPED_UNICODE);
$conexion = null;
