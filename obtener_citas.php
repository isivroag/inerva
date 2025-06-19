<?php
header('Content-Type: application/json');

try {
    include_once 'bd/conexion.php';
    $objeto = new conn();
    $conexion = $objeto->connect();

    $consulta = "SELECT * FROM vcitap2 where estado <> 3 and estado <> 4 ORDER BY id";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $citas = $resultado->fetchAll(PDO::FETCH_ASSOC);

    if ($citas === false) {
        throw new Exception("Error al obtener las citas");
    }

    $eventos = array();

    foreach ($citas as $cita) {
        $evento = array(
            'id' => $cita['id'] ?? null,
            'title' => $cita['title'] ?? 'Sin título',
            'start' => $cita['start'] ?? date('Y-m-d H:i:s'),
            'end' => $cita['end'] ?? date('Y-m-d H:i:s', strtotime('+1 hour')),
            'color' => $cita['color'] ?? '#3788d8',
            'textColor' => $cita['textcolor'] ?? '#FFFFFF',
            'extendedProps' => array(
                'nombre' => $cita['nombre'] ?? '',
                'descripcion' => $cita['descripcion'] ?? $cita['concepto'] ?? '',
                'obs' => $cita['obs'] ?? '',
                'tipo_p' => $cita['tipo_p'] ?? '',
                'estado' => $cita['estado'] ?? '',
                'confirmar' => $cita['confirmar'] ?? '',
                'duracion' => $cita['duracion'] ?? '',
                'id_con' => $cita['id_con'] ?? '',
                'nom_con' => $cita['nom_con'] ?? 'No asignado',
                'estado_citap' => $cita['estado_citap'] ?? ''
            )
        );
        $eventos[] = $evento;
    }

    echo json_encode($eventos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array(
        'error' => true,
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ));
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(array(
        'error' => true,
        'message' => $e->getMessage()
    ));
} finally {
    if (isset($conexion)) {
        $conexion = null;
    }
}
?>