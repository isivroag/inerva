<?php

include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$nombre = (isset($_POST['nombre'])) ? $_POST['nombre'] : '';
$costo = (isset($_POST['costo'])) ? $_POST['costo'] : '';
$id = (isset($_POST['id'])) ? $_POST['id'] : '';
$opcion = (isset($_POST['opcion'])) ? $_POST['opcion'] : '';

function mayusculasEspanol($texto) {
    return mb_strtoupper($texto, 'UTF-8');
}

$nombre = mayusculasEspanol($nombre);

switch($opcion){
    case 1: // alta
        $consulta = "INSERT INTO servicio (nom_serv, costo_serv, edo_serv) VALUES(:nombre, :costo, 1)";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $resultado->bindParam(':costo', $costo, PDO::PARAM_STR);
        $resultado->execute();

        $consulta = "SELECT * FROM servicio ORDER BY id_serv DESC LIMIT 1";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 2: // modificación
        $consulta = "UPDATE servicio SET nom_serv=:nombre, costo_serv=:costo WHERE id_serv=:id";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $resultado->bindParam(':costo', $costo, PDO::PARAM_STR);
        $resultado->bindParam(':id', $id, PDO::PARAM_INT);
        $resultado->execute();

        $consulta = "SELECT * FROM servicio WHERE id_serv=:id";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':id', $id, PDO::PARAM_INT);
        $resultado->execute();
        $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 3: // baja
        $consulta = "UPDATE servicio SET edo_serv=0 WHERE id_serv=:id";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':id', $id, PDO::PARAM_INT);
        $resultado->execute();
        $data = 1;
        break;
}

print json_encode($data, JSON_UNESCAPED_UNICODE);
$conexion = NULL;