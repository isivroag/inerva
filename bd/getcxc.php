<?php
// filepath: c:\xampp\htdocs\inerva\bd\getcxc.php
include_once '../bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$cliente = $_POST['cliente'] ?? '';
$fecha_inicio = $_POST['fecha_inicio'] ?? '';
$fecha_fin = $_POST['fecha_fin'] ?? '';
$colaborador = $_POST['colaborador'] ?? '';

$where = "WHERE  edo_cxc = 1";
$params = [];

if ($cliente != '') {
    $where .= " AND (paciente LIKE :cliente OR id_px = :cliente_id)";
    $params[':cliente'] = "%$cliente%";
    $params[':cliente_id'] = $cliente;
}
if ($fecha_inicio != '' && $fecha_fin != '') {
    $where .= " AND DATE(fecha_cob) BETWEEN :fecha_inicio AND :fecha_fin";
    $params[':fecha_inicio'] = $fecha_inicio;
    $params[':fecha_fin'] = $fecha_fin;
} elseif ($fecha_inicio != '') {
    $where .= " AND DATE(fecha_cob) >= :fecha_inicio";
    $params[':fecha_inicio'] = $fecha_inicio;
} elseif ($fecha_fin != '') {
    $where .= " AND DATE(fecha_cob) <= :fecha_fin";
    $params[':fecha_fin'] = $fecha_fin;
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