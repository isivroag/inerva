<?php
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

// Recepción de los datos enviados mediante POST desde el JS   

$nombre = (isset($_POST['nombre'])) ? $_POST['nombre'] : '';
$username = (isset($_POST['username'])) ? $_POST['username'] : '';
$email = (isset($_POST['email'])) ? $_POST['email'] : '';
$password = (isset($_POST['password'])) ? $_POST['password'] : '';
$rol_usuario = (isset($_POST['rol_usuario'])) ? $_POST['rol_usuario'] : '';
$nombre = $nombre;

$id_usuario = (isset($_POST['id_usuario'])) ? $_POST['id_usuario'] : '';

$opcion = (isset($_POST['opcion'])) ? $_POST['opcion'] : '';

switch($opcion){
    case 1: //alta

        $passwordh=md5($password);
        $consulta = "INSERT INTO w_usuario (username,nombre,email,password,rol_usuario) VALUES('$username','$nombre', '$email', '$passwordh','$rol_usuario') ";			
        $resultado = $conexion->prepare($consulta);
        $resultado->execute(); 

        $consulta = "SELECT * FROM w_usuario JOIN rol ON w_usuario.rol_usuario=rol.id WHERE w_usuario.id_usuario<>'".$_SESSION['s_id_usuario']."' ORDER BY w_usuario.id_usuario DESC LIMIT 1";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 2: //modificación
        $passwordh=md5($password);
        $consulta = "UPDATE w_usuario SET nombre='$nombre',email='$email', username='$username', rol_usuario='$rol_usuario', password='$passwordh' WHERE id_usuario='$id_usuario' ";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();        
        
        $consulta = "SELECT * FROM w_usuario JOIN rol ON w_usuario.rol_usuario=rol.id WHERE id_usuario='$id_usuario' ";       
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;        
    case 3://baja
        $consulta = "DELETE FROM usuario WHERE id_usuario='$id_usuario' ";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute(); 
        $data=1;                          
        break;        
}

print json_encode($data, JSON_UNESCAPED_UNICODE); //enviar el array final en formato json a JS
$conexion = NULL;
