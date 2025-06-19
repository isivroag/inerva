<?php

include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$opcion = isset($_POST['opcion']) ? $_POST['opcion'] : '';
$fecha_cob = isset($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d');
$id_cita = isset($_POST['id_cita']) ? $_POST['id_cita'] : '';
$id_px = isset($_POST['id_paciente']) ? $_POST['id_paciente'] : '';
$id_serv = isset($_POST['id_serv']) ? $_POST['id_serv'] : '';
$costo = isset($_POST['costo']) ? $_POST['costo'] : 0;
$descuento = isset($_POST['descuento']) ? $_POST['descuento'] : 0;
$total = isset($_POST['total']) ? $_POST['total'] : 0;
$metodo = isset($_POST['metodo_pago']) ? $_POST['metodo_pago'] : '';

if($opcion == 1){
    try {
        $consulta = "INSERT INTO cobro (fecha_cob, id_cita, id_px, id_serv, costo, descuento, total, metodo) 
                     VALUES (:fecha_cob, :id_cita, :id_px, :id_serv, :costo, :descuento, :total, :metodo)";
        $stmt = $conexion->prepare($consulta);
        $stmt->bindParam(':fecha_cob', $fecha_cob);
        $stmt->bindParam(':id_cita', $id_cita);
        $stmt->bindParam(':id_px', $id_px);
        $stmt->bindParam(':id_serv', $id_serv);
        $stmt->bindParam(':costo', $costo);
        $stmt->bindParam(':descuento', $descuento);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':metodo', $metodo);
        $stmt->execute();
        echo json_encode(['status' => 'ok']);

        $update = "UPDATE citap SET estado = '10' WHERE folio_citap = :id_cita";
        $stmt_update = $conexion->prepare($update);
        $stmt_update->bindParam(':id_cita', $id_cita);
        $stmt_update->execute();

    } catch(Exception $e) {
        echo json_encode(['status' => 'error', 'mensaje' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'mensaje' => 'Opción no válida']);
}
$conexion = null;