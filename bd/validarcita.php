<?php
header('Content-Type: application/json');
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$data="";


$inicio = (isset($_POST['inicio'])) ? $_POST['inicio'] : '';
$duracion = (isset($_POST['duracion'])) ? $_POST['duracion'] : '';
$cabina = (isset($_POST['cabina'])) ? $_POST['cabina'] : '';
$colaborador = (isset($_POST['colaborador'])) ? $_POST['colaborador'] : '';
$cita = (isset($_POST['cita'])) ? $_POST['cita'] : '';

$data=0;
$consulta = " call spvalidardisp2('$inicio','$duracion','$colaborador','$cabina','$cita')";

$resultado = $conexion->prepare($consulta);
$resultado->execute();
if($resultado->rowCount()>0){
    $data=1;
}



echo json_encode($data); //enviar el array final en formato json a JS
$conexion = NULL;
?>