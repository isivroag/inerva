<?php
// filepath: c:\xampp\htdocs\inerva\cntacxc.php
$pagina = "cntacxc";
include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";
include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

// Obtener colaboradores para el filtro
$colaboradores = $conexion->query("SELECT DISTINCT id_col, colaborador FROM vcxc ORDER BY colaborador")->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

<div class="content-wrapper">
    <section class="content">
        <div class="card">
            <div class="card-header bg-green text-light">
                <h1 class="card-title mx-auto">Cuentas por Cobrar Pendientes</h1>
            </div>
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-sm-12">
                        <form id="formFiltros" class="mb-3">
                            <div class="form-row justify-content-center align-items-end">
                                <div class="col-3">
                                    <label for="filtro_cliente">Cliente:</label>
                                    <input type="text" id="filtro_cliente" name="filtro_cliente" class="form-control" placeholder="Nombre o ID">
                                </div>
                                <div class="col-2">
                                    <label for="filtro_fecha">Fecha CXC:</label>
                                    <input type="date" id="filtro_fecha" name="filtro_fecha" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-2">
                                    <label for="filtro_colaborador">Colaborador:</label>
                                    <select id="filtro_colaborador" name="filtro_colaborador" class="form-control">
                                        <option value="">Todos</option>
                                        <?php foreach ($colaboradores as $col): ?>
                                            <option value="<?= $col['id_col'] ?>"><?= htmlspecialchars($col['colaborador']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-1  align-items-end">
                                    <button type="button" id="btnBuscar" class="btn btn-success">Buscar</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>

                <div class="table-responsive">
                    <table id="tablaCXC" class="table table-sm table-bordered table-striped table-condensed tablaredonda mx-auto" style="width: 100% !important;">
                        <thead class="bg-green text-light text-center">
                            <tr>
                                <th>Folio CXC</th>
                                <th>Fecha </th>
                                <th>ID Paciente</th>
                                <th>Paciente</th>
                                <th>ID Colaborador</th>
                                <th>Colaborador</th>
                                <th>ID Cita</th>
                                <th>Fecha Cita</th>
                                <th>Hora Cita</th>
                                <th>Servicio</th>
                                <th>Total</th>
                                <th>Saldo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Se llena por AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
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
    <!-- Modal Ver Pagos -->
    <section>
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
                        <!-- Aquí se cargan los pagos por AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include_once 'templates/footer.php'; ?>
<script src="fjs/cntacxc.js?v=<?php echo (rand()); ?>"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>