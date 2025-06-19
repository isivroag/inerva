<?php
$pagina = "confirmacion";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";




include_once 'bd/conexion.php';
$objeto = new conn();
$message = "";
$conexion = $objeto->connect();
if (isset($_GET['fecha'])) {
    $fecha = $_GET['fecha'];
} else {
    $fecha = date('Y-m-d');
}

$sql = "SELECT id, id_px,id_col,nombre as colaborador, title as paciente, descripcion,date(start) as fecha,time(start) as hora, id_con, nom_con as consultorio,estado_citap,color,estado 
from vcitap2 where estado_citap = '1' and estado in (0,1,2) and date(start) = :fecha order by time(start) ";

$resultado = $conexion->prepare($sql);
$resultado->bindParam(':fecha', $fecha);
$resultado->execute();
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);



?>

<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

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
                        <!--
                        <button id="btnNuevo" type="button" class="btn bg-info btn-ms" data-toggle="modal"><i class="fas fa-plus-square text-light"></i><span class="text-light"> Cita Prospecto</span></button>
                        <button id="btnNuevox" type="button" class="btn bg-green btn-ms" data-toggle="modal"><i class="fas fa-plus-square text-light"></i><span class="text-light"> Cita Cliente</span></button>
-->
                    </div>
                </div>
                <br>
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col-sm-1">
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
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table name="tablacal" id="tablacal" class="tablaredonda table table-sm  table-bordered  table-hover table-condensed text-nowrap w-auto mx-auto " style="font-size:12px;vertical-align: center!important;">
                                    <thead class="text-center  bg-green">
                                        <tr>
                                            <th>ID</th>
                                            <th>HORA</th>
                                            <th>COLOR</th>
                                            <th>PSICOLOGO</th>
                                            <th>PACIENTE</th>
                                            <th>CONSULTORIO</th>
                                            <th>DESCRIPCIÓN</th>
                                            <th>ESTADO</th>
                                            <th>ACCIONES</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data as $dat): ?>
                                            <tr>
                                                <td><?php echo $dat['id'] ?></td>
                                                <td><?php echo $dat['hora'] ?></td>
                                                <td><?php echo $dat['color'] ?></td>
                                                <td><?php echo $dat['colaborador'] ?></td>
                                                <td><?php echo $dat['paciente'] ?></td>
                                                <td><?php echo $dat['consultorio'] ?></td>
                                                <td><?php echo $dat['descripcion'] ?></td>
                                                <td class="text-center"><?php echo $dat['estado'] ?></td>
                                                <td></td>



                                            </tr>
                                        <?php endforeach; ?>
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
    <section>
        <div class="modal fade" id="modalcan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog " role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title" id="exampleModalLabel">CANCELAR REGISTRO</h5>
                    </div>
                    <div class="card card-widget" style="margin: 10px;">
                        <form id="formcan" action="" method="POST">
                            <div class="modal-body row">
                                <div class="col-sm-12">
                                    <div class="form-group input-group-sm">
                                        <label for="motivo" class="col-form-label">Motivo de Cancelación:</label>
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
                        <button type="button" id="btnGuardarc" name="btnGuardarc" class="btn btn-success" value="btnGuardarc"><i class="far fa-save"></i> Guardar</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </section>



</div>


<?php include_once 'templates/footer.php'; ?>
<script src="fjs/confirmacion.js?v=<?php echo (rand()); ?>"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>