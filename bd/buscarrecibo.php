<?php
//filter.php  

include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

// Recepción de los datos enviados mediante POST desde el JS   


$id = (isset($_POST['folio_cita'])) ? $_POST['folio_cita'] : '';
$folio = 0;

$consulta = "SELECT folio_cob from vcobro where id_cita=:id ";
$resultado = $conexion->prepare($consulta);
$resultado->bindParam(':id', $id);
if ($resultado->execute()) {
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    $folio = $data[0]['folio_cob'] ?? '';
}
print json_encode($folio, JSON_UNESCAPED_UNICODE); //enviar el array final en formato json a JS
$conexion = NULL;
