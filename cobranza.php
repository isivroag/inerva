<?php
$pagina = "cobranza";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";

include_once "bd/conexion.php";
$objeto = new conn();
$conexion = $objeto->connect();
$fecha = date("Y-m-d");
$folio_cob = 0;
$folio_cita = "";
$id_col = "";
$colaborador = "";
$id_px = "";
$paciente = "";
$fecha = date("Y-m-d");
$id_serv = "";
$costo = "";
$descuento = 0;
$total = 0;
$metodo_pago = "";


if (isset($_GET['folio_cob'])) {
    $folio_cob = $_GET['folio_cob'];
    $consulta = "SELECT * FROM vcobro WHERE folio_cob=:folio_cob";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':folio_cob', $folio_cob, PDO::PARAM_STR);
    $resultado->execute();
    $cobranza = $resultado->fetch(PDO::FETCH_ASSOC);
    $folio_cob = $cobranza['folio_cob'] ?? '';
    $folio_cita = $cobranza['id_cita'] ?? '';
    $id_col = $cobranza['id_col'] ?? '';
    $colaborador = $cobranza['colaborador'] ?? '';
    $id_px = $cobranza['id_px'] ?? '';
    $paciente = $cobranza['paciente'] ?? '';
    $fecha = $cobranza['fecha'] ?? date("Y-m-d");
    $id_serv = $cobranza['id_serv'] ?? '';
    $costo = $cobranza['costo'] ?? 0;
    $descuento = $cobranza['descuento'] ?? 0;
    $total = $cobranza['total'] ?? 0;
    $metodo_pago = $cobranza['metodo'] ?? '';
} else {
    $cobranza = null;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $consulta = "SELECT id, id_px,id_col,nombre as colaborador, title as paciente, 
    descripcion,date(start) as fecha,time(start) as hora, id_con, nom_con as consultorio,
    estado_citap,color,estado FROM vcitap2 WHERE id=:id";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':id', $id, PDO::PARAM_INT);
    $resultado->execute();
    $cita = $resultado->fetch(PDO::FETCH_ASSOC);
    $folio_cita = $id;
    $id_col = $cita['id_col'];
    $colaborador = $cita['colaborador'];
    $id_px = $cita['id_px'];
    $paciente = $cita['paciente'];
    $costo = 0;
    $descuento = 0;
    $total = 0;
    $metodo_pago = "";
} else {
    $cita = null;
    $consulta = "SELECT id, id_px,id_col,nombre as colaborador, title as paciente, 
    descripcion,date(start) as fecha,time(start) as hora, id_con, nom_con as consultorio,
    estado_citap,color,estado FROM vcitap2 WHERE estado=5 ORDER BY start";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $datares = $resultado->fetchall(PDO::FETCH_ASSOC);
}


// Obtener servicios activos
$consulta = "SELECT id_serv, nom_serv, costo_serv FROM servicio WHERE edo_serv=1";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$servicios = $resultado->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<style>
    .selectinerva .hover {

        background-color: #7362a2 !important;
    }

    .selectinerva .dropdown-item:hover {
        background-color: #7362a2 !important;
        color: white !important;
    }

    select.selectinerva {
        width: 100% !important;
    }

    select.selectinerva .dropdown-toggle::after {
        display: none !important;
    }

    select.selectinerva .dropdown-toggle {
        background-color: #7362a2 !important;
        color: white !important;
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="card">
            <div class="card-header bg-green text-light">
                <h1 class="card-title mx-auto">COBRANZA</h1>
            </div>
            <div class="card-body">


                <div class="container-fluid">
                    <div class="row justify-content-start">
                        <div class="col-auto">
                            <?php if ($folio_cob == 0) { ?>
                                <button type="button" id="btnGuardar" class="btn btn-primary">Guardar</button>
                            <?php } else { ?>
                                <button type="button" id="btnImprimir" class="btn btn-secondary" style="<?php if ($folio_cob == 0) echo 'display: none;'; ?>">Imprimir</button>
                                <button type="button" id="btnHome" class="btn btn-info" style="<?php if ($folio_cob == 0) echo 'display: none;'; ?>">Home</button>
                            <?php } ?>
                        </div>
                    </div>
                    <form id="formDatos" action="" method="POST" <?php if($folio_cob != 0) echo 'data-disabled="true"'; ?>>

                        <div class="row justify-content-center">
                            <div class="col-sm-1">
                                <div class="form-group input-group-sm">
                                    <label for="folio_cob" class="col-form-label">*FOLIO :</label>
                                    <input type="text" class="form-control" name="folio_cob" id="folio_cob" value="<?php echo $folio_cob ? $folio_cob : ''; ?>" readonly>
                                </div>
                            </div>






                            <div class="col-sm-1">
                                <div class="form-group input-group-sm">
                                    <label for="id_cita" class="col-form-label">*ID CITA :</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" name="id_cita" id="id_cita" value="<?php echo $folio_cita; ?>" readonly>

                                        <input type="hidden" name="folio_cita" id="folio_cita" value="<?php echo $folio_cita; ?>">
                                        <?php if ($folio_cita == "") { ?>
                                            <span class="input-group-append">
                                                <button id="bcita" type="button" class="btn btn-primary "><i class="fas fa-search"></i></button>
                                            </span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>




                            <div class="col-sm-4">
                                <div class="form-group input-group-sm">
                                    <label for="colaborador" class="col-form-label">Psicólogo :</label>
                                    <input type="hidden" name="id_col" id="id_col" value="<?php echo $id_col; ?>">
                                    <input type="text" class="form-control" name="colaborador" id="colaborador" value="<?php echo $colaborador; ?>" readonly>
                                </div>
                            </div>

                            <div class="col-sm-1">
                                <div class="form-group input-group-sm">
                                    <label for="fecha" class="col-form-label">*FECHA :</label>
                                    <input type="date" class="form-control" name="fecha" id="fecha" value="<?php echo $fecha; ?>" required>
                                </div>
                            </div>



                        </div>

                        <div class=" row justify-content-center">
                            <div class="col-sm-7">
                                <div class="form-group input-group-sm">

                                    <input type="hidden" name="id_paciente" id="id_paciente" value="<?php echo $id_px; ?>">
                                    <label for="paciente" class="col-form-label">*PACIENTE :</label>
                                    <input type="text" class="form-control" name="paciente" id="paciente" value="<?php echo $paciente; ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-7">
                                <div class="form-group input-group-sm">
                                    <label for="id_serv" class="col-form-label form-control-sm">SERVICIO:</label>
                                    <select class="form-control form-control-sm selectpicker selectinerva" name="id_serv" id="id_serv" data-live-search="true" title="SELECCIONA SERVICIO" required>
                                        <?php foreach ($servicios as $servicio): ?>
                                            <option value="<?php echo $servicio['id_serv'] ?>"
                                                data-costo="<?php echo $servicio['costo_serv'] ?>"
                                                <?php if ($id_serv == $servicio['id_serv']) echo 'selected'; ?>>
                                                <?php echo $servicio['nom_serv'] ?> - $<?php echo number_format($servicio['costo_serv'], 2) ?>
                                            </option>
                                        <?php endforeach; ?>
                                       
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-sm-2">
                                <div class="form-group  input-group-sm">
                                    <label for="costo" class="col-form-label">*IMPORTE :</label>
                                    <input type="number" step="0.01" class="form-control text-right" name="costo" id="costo" value="<?php echo $costo; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="form-group input-group-sm">
                                    <label for="descuento" class="col-form-label">DESCUENTO :</label>
                                    <input type="number" step="0.01" class="form-control text-right" name="descuento" id="descuento" value="<?php echo $descuento; ?>" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group input-group-sm">
                                    <label for="total" class="col-form-label">TOTAL :</label>
                                    <input type="number" step="0.01" class="form-control text-right" name="total" id="total" value="<?php echo $total; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group input-group-sm">
                                    <label for="metodo_pago" class="col-form-label">*MÉTODO PAGO :</label>
                                    <select class="form-control form-control-sm selectpicker selectinerva" name="metodo_pago" id="metodo_pago" data-live-search="true" title="SELECCIONA MÉTODO PAGO" required>
                                        <option value="Efectivo" <?php if ($metodo_pago == "Efectivo") echo "selected"; ?>>Efectivo</option>
                                        <option value="Tarjeta Crédito" <?php if ($metodo_pago == "Tarjeta Crédito") echo "selected"; ?>>Tarjeta Crédito</option>
                                        <option value="Tarjeta Débito" <?php if ($metodo_pago == "Tarjeta Débito") echo "selected"; ?>>Tarjeta Débito</option>
                                        <option value="Transferencia" <?php if ($metodo_pago == "Transferencia") echo "selected"; ?>>Transferencia</option>
                                        <option value="Cortesía" <?php if ($metodo_pago == "Cortesía") echo "selected"; ?>>Cortesía</option>
                                    </select>
                                </div>
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
            <div class="modal fade" id="modalCitas" tabindex="-3" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl " role="document">
                    <div class="modal-content w-auto">
                        <div class="modal-header bg-green">
                            <h5 class="modal-title" id="exampleModalLabel">BUSCAR CITAS</h5>

                        </div>
                        <br>
                        <div class="table-hover table-responsive w-auto" style="padding:15px">
                            <table name="tablaCita" id="tablaCita" class=" tablaredonda table  table-sm table-striped table-bordered table-condensed" style="width:100%">
                                <thead class="text-center bg-green">
                                    <tr>
                                        <th>ID </th>
                                        <th>FECHA</th>
                                        <th>HORA</th>
                                        <th>COLOR</th>
                                        <th>PSICOLOGO</th>
                                        <th>PACIENTE</th>
                                        <th>CONSULTORIO</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($datares as $datcx) {
                                    ?>
                                        <tr>
                                            <td><?php echo $datcx['id'] ?></td>
                                            <td><?php echo $datcx['fecha'] ?></td>
                                            <td><?php echo $datcx['hora'] ?></td>
                                            <td><?php echo $datcx['color'] ?></td>
                                            <td><?php echo $datcx['colaborador'] ?></td>
                                            <td><?php echo $datcx['paciente'] ?></td>
                                            <td><?php echo $datcx['consultorio'] ?></td>

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



</div>

<?php include_once 'templates/footer.php'; ?>
<script src="fjs/cobranza.js?v=<?php echo (rand()); ?>"></script>
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