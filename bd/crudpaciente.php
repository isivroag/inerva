<?php
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

// Recepción de los datos enviados mediante POST desde el JS   
$nombre = (isset($_POST['nombre'])) ? $_POST['nombre'] : '';

$tel = (isset($_POST['tel'])) ? $_POST['tel'] : '';

$correo = (isset($_POST['correo'])) ? $_POST['correo'] : '';

$id = (isset($_POST['id'])) ? $_POST['id'] : '';

$fechanac = (isset($_POST['fechanac'])) ? $_POST['fechanac'] : '';

$opcion = (isset($_POST['opcion'])) ? $_POST['opcion'] : '';

function mayusculasEspanol($texto) {
    return mb_strtoupper($texto, 'UTF-8');
}

$nombre = mayusculasEspanol($nombre);

switch($opcion){
    case 1: //alta
        $consulta = "INSERT INTO paciente (nombre_px,tel_px,correo_px,fechanac_px) VALUES(:nombre,:tel,:correo,:fechanac) ";			
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $resultado->bindParam(':tel', $tel, PDO::PARAM_STR);
        $resultado->bindParam(':correo', $correo, PDO::PARAM_STR);
        $resultado->bindParam(':fechanac', $fechanac, PDO::PARAM_STR);
        $resultado->execute(); 

        $consulta = "SELECT * FROM paciente ORDER BY id_px DESC LIMIT 1";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 2: //modificación
        $consulta = "UPDATE paciente SET nombre_px=:nombre, tel_px=:tel, correo_px=:correo, fechanac_px=:fechanac WHERE id_px=:id ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $resultado->bindParam(':tel', $tel, PDO::PARAM_STR);
        $resultado->bindParam(':correo', $correo, PDO::PARAM_STR);
        $resultado->bindParam(':fechanac', $fechanac, PDO::PARAM_STR);
        $resultado->bindParam(':id', $id, PDO::PARAM_INT);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        $consulta = "SELECT * FROM paciente WHERE id_px=:id ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':id', $id, PDO::PARAM_INT);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        
        break;        
    case 3://baja
        $consulta = "UPDATE paciente SET edo_px=0 WHERE id_px=:id ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':id', $id, PDO::PARAM_INT);
        $resultado->execute();
        $data=1;
        break;
}

print json_encode($data, JSON_UNESCAPED_UNICODE); //enviar el array final en formato json a JS
$conexion = NULL;
