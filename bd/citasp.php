<?php
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

// Recepción de los datos enviados mediante POST desde el JS   

$nombre = (isset($_POST['nombre'])) ? $_POST['nombre'] : '';
$id_px = (isset($_POST['id_px'])) ? $_POST['id_px'] : '';
$fecha = (isset($_POST['fecha'])) ? $_POST['fecha'] : '';
$obs = (isset($_POST['obs'])) ? $_POST['obs'] : '';
$concepto = (isset($_POST['concepto'])) ? $_POST['concepto'] : '';
$opcion = (isset($_POST['opcion'])) ? $_POST['opcion'] : '';
$id = (isset($_POST['id'])) ? $_POST['id'] : '';
$tipop = (isset($_POST['tipop'])) ? $_POST['tipop'] : '';
$responsable = (isset($_POST['responsable'])) ? $_POST['responsable'] : '';
$duracion = (isset($_POST['duracion'])) ? $_POST['duracion'] : '';
$cabina = (isset($_POST['cabina'])) ? $_POST['cabina'] : '';

$concepto = ucfirst(strtolower($concepto));
$obs = ucfirst(strtolower($obs));


switch ($opcion) {
        case 1: //alta
                $consulta = "SELECT * FROM citap where (id_col='$responsable' and fecha='$fecha' and estado<> 3 and estado <> 4 ) or (id_con='$cabina' and fecha='$fecha' and estado<> 3 and estado <> 4 ) ";
                $resultado = $conexion->prepare($consulta);
                $resultado->execute();
                if ($resultado->rowCount() == 0) {
                        if ($tipop == 0) {
                                $consulta = "SELECT * FROM citap where (id_px='$id_px' and fecha='$fecha') and estado<> 3 and estado <> 4";
                                $resultado = $conexion->prepare($consulta);
                                $resultado->execute();
                                if ($resultado->rowCount() == 0) {
                                        $consulta = "INSERT INTO citap (id_px,fecha,concepto,obs,tipo_p,id_col,duracion,id_con) VALUES('$id_px', '$fecha', '$concepto','$obs','$tipop','$responsable','$duracion','$cabina') ";
                                } else {
                                        $data = 0;
                                        break;
                                }
                        } else {
                                $consulta = "SELECT * FROM citap where (id_px='$id_px' and fecha='$fecha') and estado<> 3 and estado <> 4";
                                $resultado = $conexion->prepare($consulta);
                                $resultado->execute();
                                if ($resultado->rowCount() == 0) {
                                        $consulta = "INSERT INTO citap (id_px,fecha,concepto,obs,tipo_p,id_col,duracion,id_con) VALUES('$id_px', '$fecha', '$concepto','$obs','$tipop','$responsable','$duracion','$cabina') ";
                                } else {
                                        $data = 0;
                                        break;
                                }
                        }

                        $resultado = $conexion->prepare($consulta);
                        if ($resultado->execute()) {
                                $data = 1;
                        } else {
                                $data = 0;
                        }
                } else {
                        $data = 0;
                }


                break;
        case 2:
                $consulta = "SELECT * FROM citap where folio_citap <> '$id' and ((id_col='$responsable' and fecha='$fecha' and estado<> 3 and estado <> 4 ) or (id_con='$cabina' and fecha='$fecha' and estado<> 3 and estado <> 4 ) )";
                $resultado = $conexion->prepare($consulta);
                $resultado->execute();
                if ($resultado->rowCount() == 0) {
                        if ($tipop == 0) {
                                $consulta = "SELECT * FROM citap where (id_px='$id_px' and fecha='$fecha') and estado<> 3 and estado <> 4 and folio_citap<>'$id'";
                                $resultado = $conexion->prepare($consulta);
                                $resultado->execute();
                                if ($resultado->rowCount() == 0) {
                                        $consulta = "UPDATE citap SET fecha='$fecha',concepto='$concepto',obs='$obs',id_col='$responsable',duracion='$duracion',id_con='$cabina' WHERE folio_citap='$id' ";
                                } else {
                                        $data = 0;
                                        break;
                                }
                        } else {
                                $consulta = "SELECT id,id_px,id_col,title,descripcion,tipo_p,
                date(start) as fecha,time(start) as hora,obs,id_con,duracion,estado FROM citap where (id_px='$id_px' and fecha='$fecha') and estado<> 3 and estado <> 4 and folio_citap<>'$id'";
                                $resultado = $conexion->prepare($consulta);
                                $resultado->execute();
                                if ($resultado->rowCount() == 0) {
                                        $consulta = "UPDATE citap SET fecha='$fecha',concepto='$concepto',obs='$obs',id_col='$responsable',duracion='$duracion',id_con='$cabina', estado='0', confirmar=0 WHERE folio_citap='$id' ";
                                } else {
                                        $data = 0;
                                        break;
                                }
                        }

                        $resultado = $conexion->prepare($consulta);
                        if ($resultado->execute()) {
                                $data = 1;
                        } else {
                                $data = 0;
                        }
                } else {
                        $data = 0;
                }
                break;


        case 3:
                $consulta = "SELECT * FROM vcitap2 WHERE id='$id'";
                $resultado = $conexion->prepare($consulta);
                $resultado->execute();
                $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
                break;
        case 4:
                $consulta = "SELECT id,id_px,id_col,title,descripcion,tipo_p,
                date(start) as fecha,time(start) as hora,obs,id_con,duracion,estado
                FROM vcitap2 WHERE id='$id'";
                $resultado = $conexion->prepare($consulta);
                $resultado->execute();
                $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
                break;
}

print json_encode($data, JSON_UNESCAPED_UNICODE); //enviar el array final en formato json a JS
$conexion = NULL;
