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
$id_medio = (isset($_POST['medio'])) ? $_POST['medio'] : '';
$otro_medio = (isset($_POST['otro_medio'])) ? $_POST['otro_medio'] : '';

$opcion = (isset($_POST['opcion'])) ? $_POST['opcion'] : '';

function mayusculasEspanol($texto) {
    return mb_strtoupper($texto, 'UTF-8');
}

$nombre = mayusculasEspanol($nombre);

switch($opcion){
    case 1: //alta
        $consulta = "INSERT INTO paciente (nombre_px,tel_px,correo_px,fechanac_px,id_medio,otro_medio) VALUES(:nombre,:tel,:correo,:fechanac,:id_medio,:otro_medio) ";			
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $resultado->bindParam(':tel', $tel, PDO::PARAM_STR);
        $resultado->bindParam(':correo', $correo, PDO::PARAM_STR);
        $resultado->bindParam(':fechanac', $fechanac, PDO::PARAM_STR);
        $resultado->bindParam(':id_medio', $id_medio, PDO::PARAM_INT);
        $resultado->bindParam(':otro_medio', $otro_medio, PDO::PARAM_STR);
        $resultado->execute(); 

        $consulta = "SELECT * FROM vpaciente ORDER BY id_px DESC LIMIT 1";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 2: //modificación
        $consulta = "UPDATE paciente SET nombre_px=:nombre, tel_px=:tel, correo_px=:correo, fechanac_px=:fechanac, id_medio=:id_medio, otro_medio=:otro_medio WHERE id_px=:id ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $resultado->bindParam(':tel', $tel, PDO::PARAM_STR);
        $resultado->bindParam(':correo', $correo, PDO::PARAM_STR);
        $resultado->bindParam(':fechanac', $fechanac, PDO::PARAM_STR);
        $resultado->bindParam(':id_medio', $id_medio, PDO::PARAM_INT);
        $resultado->bindParam(':otro_medio', $otro_medio, PDO::PARAM_STR);
        $resultado->bindParam(':id', $id, PDO::PARAM_INT);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        $consulta = "SELECT * FROM vpaciente WHERE id_px=:id ";
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
