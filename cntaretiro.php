<?php
$date_default = date_default_timezone_set('America/Mexico_City');
$pagina = 'cntaretiro';

include_once 'templates/header.php';
include_once 'templates/barra.php';
include_once 'templates/navegacion.php';
include_once 'bd/conexion.php';

$objeto = new conn();
$conexion = $objeto->connect();

$_tz = new DateTimeZone('America/Mexico_City');
$_now = new DateTime('now', $_tz);
$rol_usuario = (int)($_SESSION['s_rol'] ?? 0);
$es_admin_o_root = in_array($rol_usuario, [2, 3], true);
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : $_now->format('Y-m-d');
if (!$es_admin_o_root) {
    $fecha = $_now->format('Y-m-d');
}
?>

<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

<div class="content-wrapper" id="ctxRetiro"
    data-rol="<?php echo $rol_usuario; ?>"
    data-es-admin="<?php echo $es_admin_o_root ? '1' : '0'; ?>"
    data-fecha="<?php echo $fecha; ?>">
    <section class="content">
        <div class="card">
            <div class="card-header bg-green text-light">
                <h1 class="card-title mx-auto">RETIROS</h1>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-auto">
                        <button id="btnNuevo" type="button" class="btn bg-green btn-ms"><i class="fas fa-plus-square text-light"></i><span class="text-light"> Nuevo</span></button>
                    </div>
                    <?php if ($es_admin_o_root): ?>
                        <div class="col-auto">
                            <label for="ctrlfecha" class="mr-2 mb-0">Fecha:</label>
                            <input type="date" id="ctrlfecha" name="ctrlfecha" value="<?php echo $fecha; ?>" class="form-control form-control-sm">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="table-responsive">
                    <table id="tablaRetiros" class="tablaredonda table table-sm table-striped table-bordered table-condensed text-nowrap w-auto mx-auto" style="width:100%; font-size:14px">
                        <thead class="text-center bg-green">
                            <tr>
                                <th>ID</th>
                                <th>FECHA</th>
                                <th>MONTO</th>
                                <th>ESTADO</th>
                                <th>FECHA OP</th>
                                <th>MONTO ANTES</th>
                                <th>MONTO DESPUES</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="modal fade" id="modalRetiro" tabindex="-1" role="dialog" aria-labelledby="modalRetiroLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <form id="formRetiro">
                    <div class="modal-content">
                        <div class="modal-header bg-green text-white">
                            <h5 class="modal-title" id="modalRetiroLabel">NUEVO RETIRO</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body ">
                            <input type="hidden" id="id_retiro" name="id_retiro">


                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <div class="form-group input-group-sm">
                                        <label for="fecha_retiro" class="col-form-label">*FECHA:</label>
                                        <input type="date" class="form-control" id="fecha_retiro" name="fecha_retiro" value="<?php echo $fecha; ?>" required>
                                    </div>
                                </div>
                            </div>


                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <div class="form-group input-group-sm">
                                        <label for="efectivo_caja" class="col-form-label">EFECTIVO EN CAJA:</label>
                                        <input type="text" class="form-control" id="efectivo_caja" name="efectivo_caja" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <div class="form-group input-group-sm">
                                        <label for="monto_retiro" class="col-form-label">*MONTO:</label>
                                        <input type="number" step="0.01" min="0.01" class="form-control" id="monto_retiro" name="monto_retiro" placeholder="0.00" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <div class="form-group input-group-sm">
                                        <label for="montoant" class="col-form-label">MONTO ANTES:</label>
                                        <input type="text" class="form-control" id="montoant" name="montoant" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <div class="form-group input-group-sm">
                                        <label for="montodesp" class="col-form-label">MONTO DESPUÉS:</label>
                                        <input type="text" class="form-control" id="montodesp" name="montodesp" readonly>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fas fa-ban"></i> Cerrar</button>
                            <button type="submit" id="btnGuardarRetiro" class="btn btn-success"><i class="far fa-save"></i> Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section>
        <div class="modal fade" id="modalCancelarRetiro" tabindex="-1" role="dialog" aria-labelledby="modalCancelarRetiroLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form id="formCancelarRetiro">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="modalCancelarRetiroLabel">CANCELAR RETIRO</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="id_retiro_cancelar" name="id_retiro_cancelar">
                            <div class="form-group input-group-sm">
                                <label for="motivo_cancelacion" class="col-form-label">*MOTIVO DE CANCELACIÓN:</label>
                                <textarea rows="3" class="form-control" id="motivo_cancelacion" name="motivo_cancelacion" placeholder="Describa el motivo" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fas fa-ban"></i> Cerrar</button>
                            <button type="submit" class="btn btn-danger"><i class="fas fa-times-circle"></i> Cancelar Retiro</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?php include_once 'templates/footer.php'; ?>
<script src="fjs/cntaretiro.js?v=<?php echo (rand()); ?>"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>