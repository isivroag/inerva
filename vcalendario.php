<?php
$pagina = "vcalendario";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";




include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();
if (isset($_GET['fecha'])) {
    $fecha = $_GET['fecha'];
} else {
    $fecha = date('Y-m-d');
}


$consulta = "SELECT * FROM vcitap2 where estado<>3 and estado<>4 order by folio_citap";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);
;


$consultacx = "SELECT * FROM paciente where edo_px='1' order by id_px";
$resultadocx = $conexion->prepare($consultacx);
$resultadocx->execute();
$datacx = $resultadocx->fetchAll(PDO::FETCH_ASSOC);


$consultai = "SELECT * FROM colaborador WHERE edo_col ='1' ORDER BY id_col";
$resultadoi = $conexion->prepare($consultai);
$resultadoi->execute();
$datai = $resultadoi->fetchAll(PDO::FETCH_ASSOC);

$consultacab = "SELECT * FROM consultorio WHERE edo_con ='1' ORDER BY id_con";
$resultadocab = $conexion->prepare($consultacab);
$resultadocab->execute();
$datacab = $resultadocab->fetchAll(PDO::FETCH_ASSOC);

?>
<style>
    .tarjetacita {
        cursor: pointer;
        transition: transform 0.2s;
    }

    .tarjetacita:hover {
        transform: scale(1.05);
        color: black;
    }       
</style>

<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.css">
<!--Datetimepicker Bootstrap -->

<!--
<link rel="stylesheet" href="plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
-->
<!--tempusdominus-bootstrap-4 -->
<link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.css">



<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->


    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header bg-green text-light">
                <h1 class="card-title mx-auto">Vista de Calendario</h1>
            </div>

            <div class="card-body">

                <div class="row">
                    <div class="col-lg-12">

                       
                        <button id="btnNuevox" type="button" class="btn bg-green btn-ms" data-toggle="modal"><i class="fas fa-plus-square text-light"></i><span class="text-light"> Cita Cliente</span></button>
                    </div>
                </div>
                <br>
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-sm-2">
                            <div class="form-group input-group-sm">
                                <label for="fecha" class="col-form-label">Fecha:</label>
                                <input type="date" id="fecha" name="fecha" class="form-control" autocomplete="off" placeholder="Fecha" value=<?php echo $fecha ?>>

                                <!--
                                    <div class="input-group date form_datetime" data-date="" data-date-format="yyyy-mm-dd HH:ii:00" data-link-field="dtp_input1">
                                            <input class="form-control" type="text" value="" readonly>
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-th"></span></span>
                                        </div>
                                        <input type="hidden" id="dtp_input1" value="" /><br/>
                                        -->
                            </div>

                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-lg-12">
                            <div class="table-responsive w-100">
                                <table name="tablacal" id="tablacal" class="table  table-sm  table-bordered  table-hover table-condensed text-nowrap w-100 mx-auto " style="font-size:12px;vertical-align: center!important;">
                                    <thead class="text-center bg-green">
                                        <tr>
                                            <th>HR/CAB</th>
                                            <?php foreach ($datacab as $rowcab) { ?>
                                                <th class="font-weight-bold"><?php echo  $rowcab['nombre_con'] ?></th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $horaInicial = "09:00";


                                        $arreglo = array();
                                        do {
                                        ?>
                                            <tr>
                                                <td><?php echo $horaInicial ?></td>
                                                <?php


                                                $horatope =  date("H:i", strtotime($horaInicial) + 1800);


                                                foreach ($datacab as $rowcab) {
                                                    $cabina = $rowcab['id_con'];
                                                    $consulta = "SELECT * FROM vcitap2 where estado<>3 and estado<>4 and date(start)='$fecha' and time(start)='$horaInicial'  and id_con='$cabina'";
                                                    $resultado = $conexion->prepare($consulta);
                                                    $resultado->execute();
                                                    if ($resultado->rowCount() > 0) {
                                                        $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
                                                        foreach ($data as $rowcita) {
                                                            $icono = "";
                                                            /*if ($rowcita['tipo_p'] == 1) {
                                                                $icono = '<i class="fa-solid fa-star text-warning  text-center"></i>';
                                                            }*/
                                                            if ($rowcita['duracion'] == 30) {
                                                                echo
                                                                '<td>
                                                                    <div class="container text-center d-block">
                                                                        <div class="card tarjetacita" id=' . $rowcita['id'] . ' value=' . $rowcita['id'] . ' style:"font-size:12px!important">
                                                                            <div class="card-header m-0 p-1 text-light" style="background-color:' . $rowcita['color'] . '">
                                                                                <span>' . $rowcita['title'] . '</span>' . $icono . '
                                                                            </div>
                                                                            <div class="card-body p-1" style:"font-size:10px">
                                                                                <span>' . $rowcita['descripcion'] . '</span><br>
                                                                                <span>' . $rowcita['nombre'] . ' </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>';
                                                            } else {
                                                                $duracion = $rowcita['duracion'];
                                                                $duracion = intval($duracion) / 30;
                                                                echo    '<td rowspan="' . $duracion . '" style="vertical-align: middle!important;" >
                                                                            <div class="container text-center d-block">
                                                                                <div class="card tarjetacita d-block " id=' . $rowcita['id'] . ' value=' . $rowcita['id'] . ' style:"font-size:12px!important">
                                                                                    <div class="card-header m-0 p-1 text-light" style="background-color:' . $rowcita['color'] . '">
                                                                                        <span>' . $rowcita['title'] . '</span>' . $icono . '
                                                                                    </div>
                                                                                    <div class="card-body p-1" style:"font-size:10px">
                                                                                        <span>' . $rowcita['descripcion'] . '</span><br>
                                                                                        <span>' . $rowcita['nombre'] . ' </span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </td>';

                                                                $horaf = $horaInicial;

                                                                for ($i = 1; $i < intval($duracion); $i++) {

                                                                    $minutoAnadir = 30;
                                                                    $segundos_horaf = strtotime($horaf);
                                                                    $segundos_minutoAnadir = $minutoAnadir * 60;
                                                                    $horaf = date("H:i", $segundos_horaf + $segundos_minutoAnadir);


                                                                    $nuevoregistro = array("hora" =>  $horaf, "cabina" => $cabina);
                                                                    $registro = (object) $nuevoregistro;
                                                                    array_push($arreglo, $registro);
                                                                }
                                                            }
                                                        }
                                                    } else {
                                                        $encontrado = 0;
                                                        foreach ($arreglo as $info => $p) {
                                                            if ($p->hora == $horaInicial && $p->cabina == $cabina) {
                                                                $encontrado = 1;
                                                            }
                                                        }
                                                        if ($encontrado == 0) {
                                                            echo '<td></td>';
                                                        }
                                                    }
                                                }


                                                ?>


                                            </tr>
                                        <?php
                                            $minutoAnadir = 30;
                                            $segundos_horaInicial = strtotime($horaInicial);
                                            $segundos_minutoAnadir = $minutoAnadir * 60;
                                            $horaInicial = date("H:i", $segundos_horaInicial + $segundos_minutoAnadir);
                                        } while ($horaInicial <= "19:30");


                                        ?>

                                        <?php

                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- /.card-body -->

            <!-- /.card-footer-->
        </div>
        <!-- /.card -->

    </section>




    <!-- CITA DE PACIENTES -->

    <section>
        <div class="modal fade" id="modalpx" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-green">
                        <h5 class="modal-title" id="exampleModalLabel">Agendar Cita Cliente</h5>

                    </div>
                    <form id="formDatospx" action="" method="POST">
                        <div class="modal-body row">


                            <div class="col-sm-12">
                                <div class="form-group input-group-sm">
                                    <input type="hidden" class="form-control" name="tipopx" id="tipopx" value="1">
                                    <input type="hidden" class="form-control" name="foliox" id="foliox">
                                    <input type="hidden" class="form-control" name="opcionx" id="opcionx">
                                    <input type="hidden" class="form-control" name="id_prosx" id="id_prosx">
                                    <label for="nombrex" class="col-form-label">Cliente:</label>

                                    <div class="input-group">

                                        <input type="text" class="form-control" name="nom_prosx" id="nom_prosx" autocomplete="off" placeholder="Cliente" readonly>
                                        <span class="input-group-append">
                                            <button id="bclientex" type="button" class="btn btn-primary "><i class="fas fa-search"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group  input-group-sm">
                                    <label for="responsablex" class="col-form-label">Responsable:</label>
                                    <select class="form-control" name="responsablex" id="responsablex">
                                        <?php
                                        foreach ($datai as $dti) {
                                        ?>
                                            <option id="<?php echo $dti['id_col'] ?>" value="<?php echo $dti['id_col'] ?>"> <?php echo $dti['nombre_col'] ?></option>

                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>


                            <div class="col-sm-12">
                                <div class="form-group input-group-sm">
                                    <label for="conceptox" class="col-form-label">Concepto Cita</label>
                                    <input type="text" class="form-control" name="conceptox" id="conceptox" autocomplete="off" placeholder="Concepto de Cita">
                                </div>
                            </div>

                            <div class="col-sm-5">
                                <div class="form-group input-group-sm auto">
                                    <label for="cabinax" class="col-form-label">Consultorio:</label>
                                    <select class="form-control" name="cabinax" id="cabinax">
                                        <?php
                                        foreach ($datacab as $dtcab) {

                                        ?>
                                            <option id="cab<?php echo $dtcab['id_con'] ?>" value="<?php echo $dtcab['id_con'] ?>"> <?php echo $dtcab['nombre_con'] ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <div class="form-group input-group-sm auto">
                                    <label for="duracionx" class="col-form-label">Duración(min.):</label>
                                    <select class="form-control" name="duracionx" id="duracionx">
                                      
                                        <option id="t60x" value="60"> 60</option>

                                    </select>
                                </div>
                            </div>


                            <div class="col-sm-3">
                                <div class="form-group input-group-sm">
                                    <label for="fechax" class="col-form-label">Fecha:</label>

                                    <input type="date" id="fechax" name="fechax" class="form-control">


                                </div>

                            </div>
                            <div class="col-sm-2">
                                <div class="form-group input-group-sm auto">
                                    <label for="horax" class="col-form-label">Hora:</label>
                                    <select class="form-control" name="horax" id="horax">
                                        <!--        <?php
                                                    $horaI = "09:00:00";
                                                    do {
                                                    ?>
                                            <option value="<?php echo $horaI ?>"><?php echo $horaI ?></option>
                                        <?php
                                                        $minutoAnadir = 30;
                                                        $segundos_horaInicial = strtotime($horaI);
                                                        $segundos_minutoAnadir = $minutoAnadir * 60;
                                                        $horaI = date("H:i:s", $segundos_horaInicial + $segundos_minutoAnadir);
                                                    } while ($horaI <= "19:30:00");
                                        ?>-->

                                    </select>
                                </div>
                            </div>



                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="obsx" class="col-form-label">Observaciones:</label>
                                    <textarea class="form-control" name="obsx" id="obsx" rows="3" autocomplete="off" placeholder="Observaciones"></textarea>
                                </div>
                            </div>




                        </div>



                        <div class="modal-footer row d-flex justify-content-between">

                            <div class="col-sm-3 d-flex">
                                <button type="button" id="btnCancelarctax" class="btn btn-danger btn-block"><i class="fas fa-ban"></i> Cancelar Cita</button>
                            </div>
                            <div class="col-sm-3 d-flex">
                                <button type="button" id="btnreagendarx" name="btnreagendarx" class="btn btn-primary btn-block" value="btnreagendar"><i class="far fa-save"></i> Guardar Cita</button>
                            </div>
                            <div class="col-sm-3 d-flex">
                                <button type="button" id="btnGuardarx" name="btnGuardarx" class="btn btn-success btn-block" value="btnGuardarx"><i class="far fa-save"></i> Guardar Cita</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

   <section>
    <div class="container">

      <!-- Default box -->
      <div class="modal fade" id="modalProspectox" tabindex="-3" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl " role="document">
          <div class="modal-content w-auto">
            <div class="modal-header bg-green">
              <h5 class="modal-title" id="exampleModalLabel">BUSCAR PACIENTE</h5>

            </div>
            <br>
            <div class="table-hover table-responsive w-auto" style="padding:15px">
              <table name="tablaCx" id="tablaCx" class=" tablaredonda table  table-sm table-striped table-bordered table-condensed" style="width:100%">
                <thead class="text-center bg-green">
                  <tr>
                    <th>Id</th>
                    <th>Nombre</th>
                    <th>Telefono</th>
                    <th>Correo</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  foreach ($datacx as $datcx) {
                  ?>
                    <tr>
                      <td><?php echo $datcx['id_px'] ?></td>
                      <td><?php echo $datcx['nombre_px'] ?></td>
                      <td><?php echo $datcx['tel_px'] ?></td>
                      <td><?php echo $datcx['correo_px'] ?></td>

                      <td></td>
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>
              </table>
            </div>


          </div>

        </div>
        <!-- /.card-body -->

        <!-- /.card-footer-->
      </div>
      <!-- /.card -->

    </div>
  </section>

  <section>
    <div class="modal fade" id="modalcan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog " role="document">
        <div class="modal-content">
          <div class="modal-header bg-gradient-danger">
            <h5 class="modal-title" id="exampleModalLabel">CANCELAR REGISTRO</h5>
          </div>
          <div class="card card-widget" style="margin: 10px;">
            <form id="formcan" action="" method="POST">
              <div class="modal-body row">
                <div class="col-sm-12">
                  <div class="form-group input-group-sm">
                    <label for="motivo" class="col-form-label">Motivo de Cancelacioón:</label>
                    <textarea rows="3" class="form-control" name="motivo" id="motivo" placeholder="Motivo de Cancelación"></textarea>
                    <input type="hidden" id="fechac" name="fechac" value="<?php echo $fecha ?>">
                    <input type="hidden" id="foliocan" name="foliocan" value="">
                  </div>
                </div>
              </div>
          </div>
         
          <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fas fa-ban"></i> Cancelar</button>
            <button type="button" id="btnGuardarc" name="btnGuardarc" class="btn btn-primary" value="btnGuardarc"><i class="far fa-save"></i> Guardar</button>
          </div>
          </form>
        </div>
      </div>
    </div>
  </section>




</div>


<?php include_once 'templates/footer.php'; ?>
<script src="fjs/vcalendario.js?v=<?php echo (rand()); ?>"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- fullCalendar 2.2.5 -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/fullcalendar/main.min.js"></script>
<script src='plugins/fullcalendar/locales-all.js'></script>
<script src='plugins/fullcalendar/locales/es.js'></script>
<script src="plugins/fullcalendar-daygrid/main.min.js"></script>
<script src="plugins/fullcalendar-timegrid/main.min.js"></script>
<script src="plugins/fullcalendar-interaction/main.min.js"></script>
<script src="plugins/fullcalendar-bootstrap/main.js"></script>


<!--Datetimepicker Bootstrap -->
<!--
<script src="plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script src="plugins/bootstrap-datetimepicker/locales/bootstrap-datetimepicker.es.js" charset="UTF-8"></script>
-->
<!--tempusdominus-bootstrap-4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.js"></script>
<script src="plugins/tempusdominus-bootstrap-4/js/locale/es.js"></script>