<?php
header('Content-Type: application/json');
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$data="";


$consulta = "SELECT * FROM vcitap2 where estado <> 3 and estado <> 4 ORDER BY id";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);



echo json_encode($data); //enviar el array final en formato json a JS
$conexion = NULL;
?>