<?php
include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

// Recibir parámetros del POST
$fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin = $_POST['fecha_fin'] ?? date('Y-m-d');
$estado = $_POST['estado'] ?? 'todos';
$colaborador = $_POST['colaborador'] ?? 'todos';

// Construir la consulta SQL
$consulta = "SELECT * FROM vcitap2 WHERE start BETWEEN :fecha_inicio AND :fecha_fin";
$parametros = [
    ':fecha_inicio' => $fecha_inicio . ' 00:00:00',
    ':fecha_fin' => $fecha_fin . ' 23:59:59'
];

if ($estado !== 'todos') {
    $consulta .= " AND estado = :estado";
    $parametros[':estado'] = $estado;
}

if ($colaborador !== 'todos') {
    $consulta .= " AND id_col = :colaborador";
    $parametros[':colaborador'] = $colaborador;
}

$consulta .= " ORDER BY start";

$resultado = $conexion->prepare($consulta);
$resultado->execute($parametros);
$citas = $resultado->fetchAll(PDO::FETCH_ASSOC);

// Procesar datos para gráficos
$citas_por_dia = [];
$citas_por_colaborador = [];
$citas_por_estado = [];
$contadores_estado = [
    'pendiente' => 0,
    'confirmada' => 0,
    'noconfirmada' => 0,
    'cancelada' => 0,
    'asistio' => 0,
    'noasistio' => 0,
    'pagada' => 0
];

foreach ($citas as $cita) {
    $fecha = date('Y-m-d', strtotime($cita['start']));

    // Conteo por día
    if (!isset($citas_por_dia[$fecha])) {
        $citas_por_dia[$fecha] = 0;
    }
    $citas_por_dia[$fecha]++;

    // Conteo por colaborador
    $colab_nombre = $cita['nombre'];
    if (!isset($citas_por_colaborador[$colab_nombre])) {
        $citas_por_colaborador[$colab_nombre] = 0;
    }
    $citas_por_colaborador[$colab_nombre]++;

    // Conteo por estado y actualización de contadores
    $estado_nombre = '';
    switch ($cita['estado']) {
        case 0:
            $estado_nombre = 'Pendiente';
            $contadores_estado['pendiente']++;
            break;
        case 1:
            $estado_nombre = 'Confirmada';
            $contadores_estado['confirmada']++;
            break;
        case 2:
            $estado_nombre = 'No Confirmada';
            $contadores_estado['noconfirmada']++;
        case 5:
            $estado_nombre = 'Asistió';
            $contadores_estado['asistio']++;
            break;
            
        case 4:
            $estado_nombre = 'Cancelada';
            $contadores_estado['cancelada']++;
            break;
        case 6:
            $estado_nombre = 'No Asistió';
            $contadores_estado['noasistio']++;
            break;
        case 10:
            $estado_nombre = 'Pagada';
            $contadores_estado['pagada']++;
            break;
    }

    if (!isset($citas_por_estado[$estado_nombre])) {
        $citas_por_estado[$estado_nombre] = 0;
    }
    $citas_por_estado[$estado_nombre]++;
}

// Preparar respuesta JSON
$response = [
    'labels_dias' => array_keys($citas_por_dia),
    'data_dias' => array_values($citas_por_dia),
    'labels_colab' => array_keys($citas_por_colaborador),
    'data_colab' => array_values($citas_por_colaborador),
    'labels_estado' => array_keys($citas_por_estado),
    'data_estado' => array_values($citas_por_estado),
    'total_citas' => count($citas),
    'pendiente' => $contadores_estado['pendiente'],
    'confirmada' => $contadores_estado['confirmada'],
    'noconfirmada' => $contadores_estado['noconfirmada'],
     'cancelada' => $contadores_estado['cancelada'],
    'asistio' => $contadores_estado['asistio'],
    'noasistio' => $contadores_estado['noasistio'],
    'pagada' => $contadores_estado['pagada']
];

header('Content-Type: application/json');
echo json_encode($response);
