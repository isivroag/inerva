<?php
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

// Recepción de los datos enviados mediante POST desde el JS   



$data = 0;
$fecha = (isset($_POST['fecha'])) ? $_POST['fecha'] : '';
$colaborador = (isset($_POST['colaborador'])) ? $_POST['colaborador'] : '';
$cabina = (isset($_POST['cabina'])) ? $_POST['cabina'] : '';
$cita = (isset($_POST['cita'])) ? $_POST['cita'] : '';




//$consulta = "SELECT nhora FROM horas";
$consulta="call spdisponibilidad2('$fecha','$colaborador','$cabina','$cita')";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);

print json_encode($data, JSON_UNESCAPED_UNICODE); //enviar el array final en formato json a JS
$conexion = NULL;
