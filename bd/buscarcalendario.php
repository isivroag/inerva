<?php  
 //filter.php  

 include_once 'conexion.php';
 $objeto = new conn();
 $conexion = $objeto->connect();
 
 // Recepción de los datos enviados mediante POST desde el JS   
 
 
 $fechad = (isset($_POST['fechad'])) ? $_POST['fechad'] : '';

 
 
 $consulta = "SELECT id, id_px,id_col,nombre as colaborador, title as paciente, 
 descripcion,date(start) as fecha,time(start) as hora, id_con, nom_con as consultorio,
 estado_citap,color,estado FROM vcitap2 WHERE date(start)='$fechad' and  estado<>4 and estado<>5 and estado <>10
  ORDER BY start";
 $resultado = $conexion->prepare($consulta);
 $resultado->execute();
 $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
 
 
 print json_encode($data, JSON_UNESCAPED_UNICODE);
 $conexion = NULL;
