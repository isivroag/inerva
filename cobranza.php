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
$saldo = 0;


if (isset($_GET['folio_cob'])) {
    $folio_cob = $_GET['folio_cob'];
    $consulta = "SELECT * FROM vcxc WHERE folio_cxc=:folio_cob";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':folio_cob', $folio_cob, PDO::PARAM_STR);
    $resultado->execute();
    $cobranza = $resultado->fetch(PDO::FETCH_ASSOC);

    $folio_cob = $cobranza['folio_cxc'] ?? '';
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
    $saldo = $cobranza['saldo'] ?? 0;
} else {
    $cobranza = null;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Verificar si ya existe una CXC para esta cita
    $consultaCXC = "SELECT * FROM vcxc WHERE id_cita=:id and edo_cxc=1";
    $resultadoCXC = $conexion->prepare($consultaCXC);
    $resultadoCXC->bindParam(':id', $id, PDO::PARAM_INT);
    $resultadoCXC->execute();
    $cxcExistente = $resultadoCXC->fetch(PDO::FETCH_ASSOC);

    if ($cxcExistente) {
        // Si ya existe, mostrar mensaje y deshabilitar todo
        $cxcYaExiste = true;

        $folio_cob = $cxcExistente['folio_cxc'];
        $folio_cita = $cxcExistente['id_cita'];
        $saldo = $cxcExistente['saldo'];
        $id_col = $cxcExistente['id_col'];
        $colaborador = $cxcExistente['colaborador'];
        $id_px = $cxcExistente['id_px'];
        $paciente = $cxcExistente['paciente'];
        $fecha = $cxcExistente['fecha_cob'];
        $id_serv = $cxcExistente['id_serv'];
        $costo = $cxcExistente['costo'];
        $descuento = $cxcExistente['descuento'];
        $total = $cxcExistente['total'];
    } else {
        $cxcYaExiste = false;
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
    }
} else {
    $cxcYaExiste = false;
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

                                <button type="button" class="btn btn-info" id="btnVerPagos" data-folio="<?php echo $folio_cob; ?>">Ver Pagos</button>
                                <button type="button" class="btn btn-success" id="btnPagar" data-folio="<?php echo $folio_cob; ?>">Registrar Pago</button>

                                <!-- <button type="button" id="btnImprimir" class="btn btn-secondary" style="<?php if ($folio_cob == 0) echo 'display: none;'; ?>">Imprimir</button>
                                <button type="button" id="btnHome" class="btn btn-info" style="<?php if ($folio_cob == 0) echo 'display: none;'; ?>">Home</button>-->
                            <?php } ?>
                        </div>
                    </div>
                    <form id="formDatos" action="" method="POST" <?php if ($folio_cob != 0 || $cxcYaExiste) echo 'data-disabled="true"'; ?>>

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

                            <div class="col-sm-2">
                                <div class="form-group input-group-sm">
                                    <label for="fecha" class="col-form-label">*FECHA :</label>
                                    <input type="date" class="form-control" name="fecha" id="fecha" value="<?php echo $fecha; ?>" required>
                                </div>
                            </div>



                        </div>

                        <div class=" row justify-content-center">
                            <div class="col-sm-8">
                                <div class="form-group input-group-sm">

                                    <input type="hidden" name="id_paciente" id="id_paciente" value="<?php echo $id_px; ?>">
                                    <label for="paciente" class="col-form-label">*PACIENTE :</label>
                                    <input type="text" class="form-control" name="paciente" id="paciente" value="<?php echo $paciente; ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-8">
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
                            <div class="col-sm-2">
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
                                    <label for="saldo" class="col-form-label">SALDO :</label>
                                    <input type="number" step="0.01" class="form-control text-right" name="saldo" id="saldo" value="<?php echo $saldo; ?>" readonly>
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
    <section>
        <div class="modal fade" id="modalPago" tabindex="-1" role="dialog" aria-labelledby="modalPagoLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form id="formPago">
                    <div class="modal-content">
                        <div class="modal-header bg-green text-white">
                            <h5 class="modal-title" id="modalPagoLabel">Registrar Pago</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="folio_cxc_pago" name="folio_cxc">
                            <div class="form-group form-group-sm">
                                <label for="fecha_pago">Fecha de Pago:</label>
                                <input type="date" class="form-control form-control-sm" id="fecha_pago" name="fecha_pago" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="form-group form-group-sm">
                                <label for="saldo_ini">Saldo Inicial:</label>
                                <input type="text" class="form-control form-control-sm" id="saldo_ini" name="saldo_ini" readonly>
                            </div>
                            <div class="form-group form-group-sm">
                                <label for="importe_pago">Importe:</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" id="importe_pago" name="importe_pago" required>
                            </div>
                            <div class="form-group form-group-sm">
                                <label for="saldo_fin">Saldo Final:</label>
                                <input type="text" class="form-control form-control-sm" id="saldo_fin" name="saldo_fin" readonly>
                            </div>
                            <div class="form-group form-group-sm">
                                <label for="metodo_pago_real">Método de Pago:</label>
                                <select class="form-control form-control-sm" id="metodo_pago_real" name="metodo_pago_real" required>
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Tarjeta Crédito">Tarjeta Crédito</option>
                                    <option value="Tarjeta Débito">Tarjeta Débito</option>
                                    <option value="Transferencia">Transferencia</option>
                                    <option value="Cortesía">Cortesía</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Registrar Pago</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- Modal Registrar Pago -->

    <section>
        <!-- Modal Ver Pagos -->
        <div class="modal fade" id="modalVerPagos" tabindex="-1" role="dialog" aria-labelledby="modalVerPagosLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-green text-white">
                        <h5 class="modal-title" id="modalVerPagosLabel">Pagos Realizados</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="pagosBody">
                        <div class="row justify-content-center">

                            <div class="col-sm-12">
                                <?php
                                if (isset($folio_cob) && $folio_cob != 0) {
                                    // Consulta los pagos realizados para este folio de cobranza
                                    $consultaPagos = "SELECT id_pago,fecha_pago, importe, metodo, saldoini, saldofin FROM pago WHERE folio_cxc = :folio_cxc ORDER BY id_pago,fecha_pago ASC";
                                    $stmtPagos = $conexion->prepare($consultaPagos);
                                    $stmtPagos->bindParam(':folio_cxc', $folio_cob, PDO::PARAM_STR);
                                    $stmtPagos->execute();
                                    $pagos = $stmtPagos->fetchAll(PDO::FETCH_ASSOC);

                                    if ($pagos && count($pagos) > 0) {
                                        echo '<div class="table-responsive">';
                                        echo '<table id="tablaPagos" class="table table-bordered table-sm table-striped mx-auto tabla-condensed tablaredonda" style="width:100% !important; font-size:14px">';
                                        echo '<thead class="bg-green text-white"><tr>
                                        <th>Folio Pago</th>
                                        <th>Fecha</th>
                                        <th>Método</th>
                                        <th>Saldo Inicial</th>
                                        <th>Importe</th>
                                        <th>Saldo Final</th>
                                        <th>Acciones</th>
                                        </tr></thead><tbody>';
                                        foreach ($pagos as $pago) {
                                            echo '<tr>';
                                            echo '<td>' . htmlspecialchars($pago['id_pago']) . '</td>';
                                            echo '<td>' . htmlspecialchars($pago['fecha_pago']) . '</td>';
                                           
                                            echo '<td>' . htmlspecialchars($pago['metodo']) . '</td>';
                                            echo '<td class="text-right">$' . number_format($pago['saldoini'], 2) . '</td>';
                                             echo '<td class="text-right">$' . number_format($pago['importe'], 2) . '</td>';
                                            echo '<td class="text-right">$' . number_format($pago['saldofin'], 2) . '</td>';
                                            echo '<td></td>'; // Placeholder for actions if needed
                                            echo '</tr>';
                                        }
                                        echo '</tbody></table></div>';
                                    } else {
                                        echo '<div class="alert alert-info">No hay pagos registrados para este folio.</div>';
                                    }
                                } else {
                                    echo '<div class="alert alert-warning">No se ha seleccionado un folio de cobranza.</div>';
                                }
                                ?>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
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