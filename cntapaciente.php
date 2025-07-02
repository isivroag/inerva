<?php
$pagina = "paciente";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";




include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$consulta = "SELECT * FROM vpaciente WHERE edo_px=1 ORDER BY id_px";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);


$cntam = "SELECT * FROM wmedios WHERE estado_medio=1 ORDER BY id_medio";
$res = $conexion->prepare($cntam);
$res->execute();
$datamedio = $res->fetchAll(PDO::FETCH_ASSOC);


$message = "";



?>

<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<style>
    .starchecked {
        color: rgba(255, 195, 0, 100)
    }

    .multi-line {
        white-space: normal;
        width: 250px;
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->


    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="card">
            <div class="card-header bg-green text-light">
                <h1 class="card-title mx-auto">PACIENTES</h1>
            </div>

            <div class="card-body">

                <div class="row">
                    <div class="col-lg-12">
                        <button id="btnNuevo" type="button" class="btn bg-green btn-ms" data-toggle="modal"><i class="fas fa-plus-square text-light"></i><span class="text-light"> Nuevo</span></button>
                    </div>
                </div>
                <br>
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table name="tablaV" id="tablaV" class="tablaredonda table table-sm table-striped table-bordered table-condensed text-nowrap w-auto mx-auto" style="width:100%; font-size:14px">
                                    <thead class="text-center bg-green">
                                        <tr>
                                            <th>ID</th>
                                            <th>NOMBRE</th>
                                            <th>TEL</th>
                                            <th>CORREO</th>
                                            <th>FECHA NAC</th>
                                            <th>ID MEDIO</th>
                                            <th>MEDIO</th>
                                            <th>OTRO MEDIO</th>
                                            <th>ACCIONES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($data as $dat) {
                                        ?>
                                            <tr>
                                                <td><?php echo $dat['id_px'] ?></td>
                                                <td><?php echo $dat['nombre_px'] ?></td>
                                                <td><?php echo $dat['tel_px'] ?></td>
                                                <td><?php echo $dat['correo_px'] ?></td>
                                                <td><?php echo $dat['fechanac_px'] ?></td>
                                                <td><?php echo $dat['id_medio'] ?></td>
                                                <td><?php echo $dat['nom_medio'] ?></td>
                                                <td><?php echo $dat['otro_medio'] ?></td>
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
                </div>

            </div>
            <!-- /.card-body -->

            <!-- /.card-footer-->
        </div>
        <!-- /.card -->

    </section>

    <!-- PROVEEDOR -->
    <section>
        <div class="modal fade" id="modalCRUD" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-green">
                        <h5 class="modal-title" id="exampleModalLabel">NUEVO PACIENTE</h5>

                    </div>
                    <div class="card card-widget" style="margin: 10px;">
                        <form id="formDatos" action="" method="POST">
                            <div class="modal-body row">

                                <input type="hidden" name="id" id="id">

                                <div class="col-sm-8">
                                    <div class="form-group input-group-sm">
                                        <label for="nombre" class="col-form-label">*NOMBRE :</label>
                                        <input type="text" class="form-control" name="nombre" id="nombre" autocomplete="off" placeholder="NOMBRE" require>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group input-group-sm">
                                        <label for="fechanac" class="col-form-label">*FECHA NACIMIENTO:</label>
                                        <input type="date" class="form-control" name="fechanac" id="fechanac" autocomplete="off" placeholder="Fecha de Nacimiento" required>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group input-group-sm">
                                        <label for="tel" class="col-form-label">TELEFONO:</label>
                                        <input type="text" class="form-control" name="tel" id="tel" autocomplete="off" placeholder="Teléfono" required maxlength="10" minlength="10" pattern="\d{10}">
                                    </div>
                                </div>


                                <div class="col-sm-4">
                                    <div class="form-group input-group-sm">
                                        <label for="correo" class="col-form-label">*CORREO:</label>
                                        <input type="mail" class="form-control" name="correo" id="correo" autocomplete="off" placeholder="Correo">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group input-group-sm auto">
                                        <label for="medio" class="col-form-label">Medio por el que nos conocio:</label>
                                        <select class="form-control" name="medio" id="medio">
                                            <?php foreach ($datamedio as $dtt) { ?>
                                                <option
                                                    id="<?php echo $dtt['id_medio'] ?>"
                                                    value="<?php echo $dtt['id_medio'] ?>"
                                                    data-mas-medio="<?php echo $dtt['mas_medio'] ?>">
                                                    <?php echo $dtt['nom_medio'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12" id="otro_medio_div" style="display: none;">
                                    <div class="form-group input-group-sm">
                                        <label for="otro medio" class="col-form-label">ESPECIFICAR:</label>
                                        <input type="text" class="form-control" name="otro_medio" id="otro_medio" autocomplete="off" placeholder="Especificar otro medio o convenio si es necesario">
                                    </div>

                                </div>
                            </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fas fa-ban"></i> Cancelar</button>
                        <button type="submit" id="btnGuardar" name="btnGuardar" class="btn bg-green" value="btnGuardar"><i class="far fa-save"></i> Guardar</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

</div>






<?php include_once 'templates/footer.php'; ?>
<script src="fjs/cntapaciente.js?v=<?php echo (rand()); ?>"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
<script src="http://cdn.datatables.net/plug-ins/1.10.21/sorting/formatted-numbers.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>