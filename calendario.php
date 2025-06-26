<?php
$pagina = "calendario";
include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";




include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();
$fecha = date('Y-m-d');

$consulta = "SELECT * FROM vcitap2 where estado<>3 and estado<>4 order by folio_citap";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);


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

$message = "";



?>
<!-- fullCalendar -->
<link rel="stylesheet" href="plugins/fullcalendar/main.css">
<link rel="stylesheet" href="plugins/fullcalendar-daygrid/main.min.css">
<link rel="stylesheet" href="plugins/fullcalendar-timegrid/main.min.css">
<link rel="stylesheet" href="plugins/fullcalendar-bootstrap/main.css">
<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.css">
<!--Datetimepicker Bootstrap -->

<!--
<link rel="stylesheet" href="plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
-->
<!--tempusdominus-bootstrap-4 -->
<link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.css">





<style>
  .fc-bootstrap .fc-today.alert {
    border-radius: 0 !important;
    /*background: #B5F2E3 !important;*/
    background: #C8EFBE !important;
  }

  .punto {
    height: 20px !important;
    width: 20px !important;

    border-radius: 50% !important;
    display: inline-block !important;
    text-align: center;
    font-size: 15px;
  }

  #div_carga {
    position: absolute;
    /*top: 50%;
    left: 50%;
    */

    width: 100%;
    height: 100%;
    background-color: rgba(60, 60, 60, 0.5);
    display: none;

    justify-content: center;
    align-items: center;
    z-index: 3;
  }

  #cargador {
    position: absolute;
    top: 50%;
    left: 50%;
    margin-top: -25px;
    margin-left: -25px;
  }

  #textoc {
    position: absolute;
    top: 50%;
    left: 50%;
    margin-top: 120px;
    margin-left: 20px;
  }

  .fc-event-custom {
    border: none;
    border-left: 3px solid #6c757d;
    background-color: #f8f9fa;
    color: #212529;
    margin: 2px;
    padding: 6px;
    transition: all 0.2s;
    overflow: visible !important;
    min-height: 40px;
    /* Altura mínima */
  }

  .fc-event {
    overflow: visible !important;
    white-space: normal !important;
    text-overflow: clip !important;
    font-size: 12px !important;
    text-align: center !important;
    cursor: pointer;
  }


  .fc-time-grid-event .fc-content{
    height: 100px !important;
  }

  .fc-event-custom:hover {
    z-index: 9999;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    transform: scale(1.02);
    position: relative;
    min-width: calc(100% + 20px);
    /* Asegura espacio extra */
    white-space: normal !important;
  }

  .fc-event-title,
  .fc-event-desc {
    white-space: normal !important;
    word-wrap: break-word;
    overflow: visible !important;
    text-overflow: clip !important;
    font-size: 10px !important;

  }


  /* Asegura que el contenedor padre no recorte el contenido */
  .fc-timegrid-event-harness {
    overflow: visible !important;
  }

  .fc-timegrid-event {
    overflow: visible !important;
  }

  #calendar {
  
    /* Altura mínima del calendario height: 70vh !important; */
   height: 50% !important;
    /* Usa el 70% de la altura de la ventana */
   
    
  }
  .fc-dayGridMonth-view .fc-event {
  font-size: 0.8em;
  margin: 1px;
  padding: 0 2px;
}
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->


  <!-- Main content -->
  <section class="card">
    <div class="card-header bg-green text-light">
      <h2 class="card-title mx-auto">Calendario de Citas</h2>
    </div>
    <div class="card-body">

      <div class="row" style="display:none" id="divbtns">
        <div class="col-lg-12">
          <button id="btnNuevox" type="button" class="btn bg-green btn-ms" data-toggle="modal"><i class="fas fa-plus-square text-light"></i><span class="text-light"> Cita Px</span></button>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-2">
          <div class="table-responsive">
            <table name="tablaV" id="tablaV" class="tablaredonda table table-sm table-striped table-bordered table-condensed text-nowrap w-auto mx-auto" style="width:100%; font-size:10px">
              <thead class="text-center bg-green">
                <tr>

                  <th>Psicologo</th>
                  <th>Color</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($datai as $dat) { ?>
                  <tr>
                    <td><?php echo $dat['nombre_col']; ?></td>
                    <td class=" text-center"><span class="badge" style="background-color: <?php echo $dat['color_col'] ?>">&nbsp;&nbsp;&nbsp;</span></td>

                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-md-8 ">
          <div class="card card-primary">

            <div class="card-body p-0">

              <div id="div_carga">

                <img id="cargador" src="img/loader.gif" />
                <span class=" " id="textoc"><strong>Cargando...</strong></span>

              </div>
              <!-- THE CALENDAR -->
              <div id="calendar-container" class="container-fluid" >
                <div class="row">
                  <div class="col-md-12">
                    <div id="calendar" class="calendar" style="max-height: 50%;"></div>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
  </section>




  <!-- CITA DE PACIENTES -->

  <section>
    <div class="modal fade" id="modalpx" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header bg-green">
            <h5 class="modal-title" id="exampleModalLabel">Agendar Cita Paciente</h5>

          </div>
          <form id="formDatospx" action="" method="POST">
            <div class="modal-body row">


              <div class="col-sm-12">
                <div class="form-group input-group-sm">
                  <input type="hidden" class="form-control" name="tipopx" id="tipopx" value="1">
                  <input type="hidden" class="form-control" name="foliox" id="foliox">
                  <input type="hidden" class="form-control" name="opcionx" id="opcionx">
                  <input type="hidden" class="form-control" name="id_px" id="id_px">
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
                  <label for="cabinax" class="col-form-label">consultorio:</label>
                  <select class="form-control" name="cabinax" id="cabinax">
                    <?php
                    foreach ($datacab as $dtcab) {

                    ?>
                      <option id="con<?php echo $dtcab['id_con'] ?>" value="<?php echo $dtcab['id_con'] ?>"> <?php echo $dtcab['nombre_con'] ?></option>
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


            <?php
            if ($message != "") {
            ?>
              <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <span class="badge "><?php echo ($message); ?></span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>

              </div>

            <?php
            }
            ?>
            <div class="modal-footer row d-flex justify-content-between">

              <div class="col-sm-3 d-flex">
                <button type="button" id="btnCancelarctax" class="btn btn-danger btn-block"><i class="fas fa-ban"></i> Cancelar Cita</button>
              </div>
              <div class="col-sm-3 d-flex">
                <button type="button" id="btnreagendarx" name="btnreagendarx" class="btn btn-primary btn-block" value="btnreagendar"><i class="far fa-save"></i> Guardar Cita</button>
              </div>
              <div class="col-sm-3 d-flex">
                <button type="button" id="btnGuardarx" name="btnGuardarx" class="btn btn-primary btn-block" value="btnGuardarx"><i class="far fa-save"></i> Guardar Cita</button>
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
          <?php
          if ($message != "") {
          ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
              <span class="badge "><?php echo ($message); ?></span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </div>
          <?php
          }
          ?>
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

<script src="fjs/calendario.js?v=<?php echo (rand()); ?>"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>
<!-- jQuery UI -->
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




<!-- FullCalendar CSS -->

<!-- FullCalendar JS -->


<!--Datetimepicker Bootstrap -->
<!--
<script src="plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script src="plugins/bootstrap-datetimepicker/locales/bootstrap-datetimepicker.es.js" charset="UTF-8"></script>
-->
<!--tempusdominus-bootstrap-4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.js"></script>
<script src="plugins/tempusdominus-bootstrap-4/js/locale/es.js"></script>