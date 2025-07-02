<?php
// filepath: c:\xampp\htdocs\inerva\bd\getcxc.php
include_once '../bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$cliente = $_POST['cliente'] ?? '';
$fecha = $_POST['fecha'] ?? '';
$colaborador = $_POST['colaborador'] ?? '';

$where = "WHERE  edo_cxc = 1";
$params = [];

if ($cliente != '') {
    $where .= " AND (paciente LIKE :cliente OR id_px = :cliente_id)";
    $params[':cliente'] = "%$cliente%";
    $params[':cliente_id'] = $cliente;
}
if ($fecha != '') {
    $where .= " AND DATE(fecha_cob) = :fecha";
    $params[':fecha'] = $fecha;
}
if ($colaborador != '') {
    $where .= " AND id_col = :colaborador";
    $params[':colaborador'] = $colaborador;
}

$sql = "SELECT folio_cxc, fecha_cob, id_px, paciente, id_col, colaborador, id_cita, fecha_cita, hora_cita, servicio, total, saldo FROM vcxc $where ORDER BY folio_cxc,fecha_cob DESC";
$stmt = $conexion->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
$conexion = null;