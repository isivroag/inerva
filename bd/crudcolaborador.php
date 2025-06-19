<?php
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

// Recepción de los datos enviados mediante POST desde el JS   
$nombre = (isset($_POST['nombre'])) ? $_POST['nombre'] : '';

$tel = (isset($_POST['tel'])) ? $_POST['tel'] : '';

$correo = (isset($_POST['correo'])) ? $_POST['correo'] : '';

$id = (isset($_POST['id'])) ? $_POST['id'] : '';

$opcion = (isset($_POST['opcion'])) ? $_POST['opcion'] : '';
$color = (isset($_POST['color'])) ? $_POST['color'] : '';

function mayusculasEspanol($texto) {
    return mb_strtoupper($texto, 'UTF-8');
}

$nombre = mayusculasEspanol($nombre);

switch($opcion){
    case 1: //alta
        $consulta = "INSERT INTO colaborador (nombre_col,tel_col,correo_col,color_col) VALUES(:nombre,:tel,:correo,:color) ";			
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $resultado->bindParam(':tel', $tel, PDO::PARAM_STR);
        $resultado->bindParam(':correo', $correo, PDO::PARAM_STR);
        $resultado->bindParam(':color', $color, PDO::PARAM_STR);
        $resultado->execute(); 

        $consulta = "SELECT * FROM colaborador ORDER BY id_col DESC LIMIT 1";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 2: //modificación
        $consulta = "UPDATE colaborador SET nombre_col=:nombre, tel_col=:tel, correo_col=:correo, color_col=:color WHERE id_col=:id ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $resultado->bindParam(':tel', $tel, PDO::PARAM_STR);
        $resultado->bindParam(':correo', $correo, PDO::PARAM_STR);
        $resultado->bindParam(':color', $color, PDO::PARAM_STR);
        $resultado->bindParam(':id', $id, PDO::PARAM_INT);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        $consulta = "SELECT * FROM colaborador WHERE id_col=:id ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':id', $id, PDO::PARAM_INT);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        
        break;        
    case 3://baja
        $consulta = "UPDATE colaborador SET edo_col=0 WHERE id_col=:id ";
        $resultado = $conexion->prepare($consulta);
        $resultado->bindParam(':id', $id, PDO::PARAM_INT);
        $resultado->execute();
        $data=1;
        break;
}

print json_encode($data, JSON_UNESCAPED_UNICODE); //enviar el array final en formato json a JS
$conexion = NULL;
