<?php

$id_usuario = (isset($_COOKIE["usuario"])) ? $_COOKIE["usuario"] : '';
$pass = (isset($_COOKIE["pass"])) ? $_COOKIE["pass"] : '';
if ($id_usuario != '' && $pass!='') {
    session_start();
    include_once 'conexion.php';
    $objeto = new conn();



    $conexion = $objeto->connect();
    if ($conexion != null) {
        //recepcion de los datos en el medodo post desde ajax code 
        $id_usuario = (isset($_COOKIE['usuario'])) ? $_COOKIE['usuario'] : '';
        $pass = (isset($_COOKIE['pass'])) ? $_COOKIE['pass'] : '';



        $consulta = "SELECT * FROM w_usuario WHERE username='$id_usuario' AND password='$pass'";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();

        if ($resultado->rowCount() >= 1) {
            $data = $resultado->fetchAll(PDO::FETCH_ASSOC);

            foreach ($data as $row) {
                $_SESSION['s_usuario'] = $row['username'];
                $_SESSION['s_id_usuario'] = $row['id_usuario'];
                $_SESSION['s_nombre'] = $row['nombre'];
                $_SESSION['s_rol'] = $row['rol_usuario'];
             
            }
        } else {
            $_SESSION['s_id_usuario'] = null;
            $_SESSION['s_usuario'] = null;
            $_SESSION['s_nombre'] = null;
            $_SESSION['s_rol'] =  null;
         
        }

        $conexion = null;
    } else {


        $conexion = null;
    }
} else {
    echo $pass . " " . $id_usuario;
}
