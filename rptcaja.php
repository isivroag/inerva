<?php
date_default_timezone_set('America/Mexico_City');
$pagina = "rptcaja";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";

include_once "bd/conexion.php";
$objeto = new conn();
$conexion = $objeto->connect();



$esconsulta = isset($_GET['esconsulta']) ? $_GET['esconsulta'] : 0;

// Fuerza la fecha en hora de CDMX sin depender del php.ini
$_tz   = new DateTimeZone('America/Mexico_City');
$_now  = new DateTime('now', $_tz);
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : $_now->format('Y-m-d');
$rol_usuario = (int)($_SESSION['s_rol'] ?? 0);
$es_admin_o_root = in_array($rol_usuario, [2, 3], true);
if (!$es_admin_o_root && $fecha !== $_now->format('Y-m-d')) {
    $fecha = $_now->format('Y-m-d');
}
$es_fecha_actual = ($fecha === $_now->format('Y-m-d'));
$puede_actualizar_ingresos = $es_fecha_actual || in_array($rol_usuario, [2, 3], true);
$puede_actualizar_gastos = $puede_actualizar_ingresos;
$puede_actualizar_retiros = $puede_actualizar_ingresos;
$puede_actualizar_retiros = $puede_actualizar_ingresos;
$caja_no_existe = false;






$consulta = "SELECT * FROM caja WHERE fecha=:fecha LIMIT 1";
$resultado = $conexion->prepare($consulta);
$resultado->execute([':fecha' => $fecha]);
if ($resultado->rowCount() > 0) {
    $caja = $resultado->fetch(PDO::FETCH_ASSOC);
    $id_caja = $caja['id_caja'];
    $fecha_caja = $caja['fecha'];
    $inicial = $caja['inicial'];
    $efectivo = $caja['efectivo'];
    $tcredito = $caja['tcredito'];
    $tdebito = $caja['tdebito'];
    $cortesia = $caja['cortesia'];
    $transferencias = $caja['transferencias'];

    $gastos = $caja['gastos'];
    $retiros = $caja['retiros'];
    $final = $caja['final'];

    // Comparar con ingresos reales de la vista vcaja_ingresos
    $stmtV = $conexion->prepare(
        "SELECT COALESCE(v.efectivo,0) AS v_efectivo,
                COALESCE(v.tcredito,0) AS v_tcredito,
                COALESCE(v.tdebito,0) AS v_tdebito,
                COALESCE(v.transferencias,0) AS v_transferencias,
                COALESCE(v.cortesia,0) AS v_cortesia
         FROM vcaja_ingresos v WHERE v.fecha=:fecha LIMIT 1"
    );
    $stmtV->execute([':fecha' => $fecha]);
    $vista = $stmtV->fetch(PDO::FETCH_ASSOC);
    if ($stmtV->rowCount() === 0) {
        $vista = [
            'v_efectivo' => 0,
            'v_tcredito' => 0,
            'v_tdebito' => 0,
            'v_transferencias' => 0,
            'v_cortesia' => 0,
        ];
    }
    $v_efectivo      = $vista ? (float)$vista['v_efectivo']      : 0;
    $v_tcredito      = $vista ? (float)$vista['v_tcredito']      : 0;
    $v_tdebito       = $vista ? (float)$vista['v_tdebito']       : 0;
    $v_transferencias = $vista ? (float)$vista['v_transferencias'] : 0;
    $v_cortesia      = $vista ? (float)$vista['v_cortesia']      : 0;

        $stmtGastos = $conexion->prepare(
                "SELECT COALESCE(SUM(g.monto),0) AS v_gastos
                 FROM gasto g
                 WHERE DATE(g.fecha)=:fecha
                AND COALESCE(g.estado_gasto,0)=1"
        );
        $stmtGastos->execute([':fecha' => $fecha]);
        $gastosVista = $stmtGastos->fetch(PDO::FETCH_ASSOC);
        $v_gastos = $gastosVista ? (float)$gastosVista['v_gastos'] : 0;

        $stmtRetiros = $conexion->prepare(
                "SELECT COALESCE(SUM(r.monto),0) AS v_retiros
                 FROM retiro r
                 WHERE DATE(r.fecha)=:fecha
                     AND COALESCE(r.estado_retiro,0)=1"
        );
        $stmtRetiros->execute([':fecha' => $fecha]);
        $retirosVista = $stmtRetiros->fetch(PDO::FETCH_ASSOC);
        $v_retiros = $retirosVista ? (float)$retirosVista['v_retiros'] : 0;



    $caja_sin_ingresos = (
        abs((float)$efectivo) <= 0.001 &&
        abs((float)$tcredito) <= 0.001 &&
        abs((float)$tdebito) <= 0.001 &&
        abs((float)$transferencias) <= 0.001 &&
        abs((float)$cortesia) <= 0.001
    );
    $vista_sin_ingresos = (
        abs($v_efectivo) <= 0.001 &&
        abs($v_tcredito) <= 0.001 &&
        abs($v_tdebito) <= 0.001 &&
        abs($v_transferencias) <= 0.001 &&
        abs($v_cortesia) <= 0.001
    );

    if ($caja_sin_ingresos && $vista_sin_ingresos) {
        $diff_efectivo = false;
        $diff_tcredito = false;
        $diff_tdebito = false;
        $diff_transferencias = false;
        $diff_cortesia = false;
    } else {
        $diff_efectivo = abs((float)$efectivo - $v_efectivo) > 0.001;
        $diff_tcredito = abs((float)$tcredito - $v_tcredito) > 0.001;
        $diff_tdebito = abs((float)$tdebito - $v_tdebito) > 0.001;
        $diff_transferencias = abs((float)$transferencias - $v_transferencias) > 0.001;
        $diff_cortesia = abs((float)$cortesia - $v_cortesia) > 0.001;
    }

    $diff_gastos = abs((float)$gastos - $v_gastos) > 0.001;
    $diff_retiros = abs((float)$retiros - $v_retiros) > 0.001;

    $hay_diferencias = (
        $diff_efectivo ||
        $diff_tcredito ||
        $diff_tdebito ||
        $diff_transferencias ||
        $diff_cortesia ||
        $diff_gastos ||
        $diff_retiros
    );
} else {
    $id_caja = null;
    $fecha_caja = $fecha;
    $inicial = 0;
    $efectivo = 0;
    $tcredito = 0;
    $tdebito = 0;
    $cortesia = 0;
    $transferencias = 0;
    $gastos = 0;
    $retiros = 0;
    $final = 0;
    $v_efectivo = $v_tcredito = $v_tdebito = $v_transferencias = $v_cortesia = $v_gastos = $v_retiros = 0;
    $diff_efectivo = $diff_tcredito = $diff_tdebito = $diff_transferencias = $diff_cortesia = $diff_gastos = $diff_retiros = false;
    $hay_diferencias = false;
    $caja_no_existe = true;
}

$efectivo_real = ((float)($inicial ?? 0) + (float)($efectivo ?? 0));
$ingresos_totales = ((float)($efectivo ?? 0) + (float)($tcredito ?? 0) + (float)($tdebito ?? 0) +  (float)($transferencias ?? 0));
$efectivo_disponible = ((float)($inicial ?? 0) + (float)($efectivo ?? 0)) - ((float)($gastos ?? 0) + (float)($retiros ?? 0));
$final = $ingresos_totales;

if (!empty($fecha_caja)) {
    $fecha_obj = new DateTime($fecha_caja);
    $dias_semana = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
    $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    $fecha_larga = ucfirst($dias_semana[(int)$fecha_obj->format('w')]) . ' ' . $fecha_obj->format('d') . ' de ' . $meses[(int)$fecha_obj->format('n') - 1] . ' de ' . $fecha_obj->format('Y');
} else {
    $fecha_larga = '';
}







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

    .ticket-caja {
        /*background: linear-gradient(135deg, #f8f7fb 0%, #f0eef8 100%);*/
        border: 2px solid #7262a1;
        box-shadow: 0 8px 20px rgba(114, 98, 161, 0.15);
        border-radius: 14px;
        color: #1f2937;
    }

    .ticket-caja .ticket-header {
        background: linear-gradient(90deg, #7262a1 0%, #8e7dba 100%);
        color: white;
        border-radius: 10px;
        padding: 10px 12px;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .ticket-caja .ticket-line {
        border-top: 1px dashed #b0a3ce;
        margin: 8px 0;
    }

    .ticket-caja .ticket-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 2px 0;
        font-size: 0.95rem;
    }

    .ticket-caja .ticket-total {
        background: #ede9f5;
        border-radius: 8px;
        padding: 6px 8px;
        font-weight: 700;
        color: #7262a1;
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="card">
            <div class="card-header bg-green text-light">
                <h1 class="card-title mx-auto">REPORTE DE CAJA</h1>
            </div>
            <div class="card-body">


                <div class="container-fluid">
                    <div class="row justify-content-start">
                        <div class="col-auto">

                            <button type="button" id="btnGuardar" class="btn btn-primary"
                                data-caja-existe="<?php echo $id_caja ? '1' : '0'; ?>"
                                data-caja-fecha="<?php echo $fecha; ?>"
                                data-caja-no-existe="<?php echo $caja_no_existe ? '1' : '0'; ?>"
                                data-s-rol="<?php echo $_SESSION['s_rol'] ?? 0; ?>">Abrir Caja</button>


                            <button type="button" class="btn btn-info" id="btnVerPagos">Gastos</button>
                            <button type="button" class="btn btn-success" id="btnPagar">Retiros</button>
                            <button type="button" class="btn btn-warning" id="btnCerrarCaja">Cerrar Caja</button>

                        </div>
                    </div>
                    <?php if ($es_admin_o_root): ?>
                        <div class="row justify-content-center mt-3 mb-3">
                            <div class="col-auto">
                                <label for="ctrlfecha" class="mr-2">Fecha:</label>
                                <input type="date" id="ctrlfecha" name="ctrlfecha" value="<?php echo $fecha; ?>" class="form-control form-control-sm">
                            </div>
                        </div>
                    <?php endif; ?>
                    <form id="formDatos" action="" method="POST" <?php if ($esconsulta == 1) echo 'data-disabled="true"'; ?>>

                        <div class="ticket-caja p-3 mb-3" style="font-family: 'Segoe UI', Arial, sans-serif; max-width: 480px; margin: 0 auto;"
                            id="ticketCaja"
                            data-hay-diff="<?php echo $hay_diferencias ? '1' : '0'; ?>"
                            data-v-efectivo="<?php echo $v_efectivo; ?>"
                            data-v-tcredito="<?php echo $v_tcredito; ?>"
                            data-v-tdebito="<?php echo $v_tdebito; ?>"
                            data-v-transferencias="<?php echo $v_transferencias; ?>"
                            data-v-cortesia="<?php echo $v_cortesia; ?>"
                            data-v-gastos="<?php echo $v_gastos; ?>"
                            data-v-retiros="<?php echo $v_retiros; ?>">
                            <div class="ticket-header text-center mb-2">REPORTE DE CAJA</div>
                            <div class="ticket-row">
                                <span>ID:</span>
                                <span><?php echo $id_caja ? $id_caja : ''; ?></span>
                            </div>
                            <div class="ticket-row">
                                <span>FECHA:</span>
                                <span><?php echo $fecha_larga ? $fecha_larga : ($fecha_caja ? $fecha_caja : ''); ?></span>
                            </div>
                            <div class="ticket-line"></div>

                            <div class="ticket-row">
                                <span>INICIAL:</span>
                                <span><?php echo number_format((float)$inicial, 2, '.', ','); ?></span>
                            </div>
                            <div class="ticket-row" id="row-efectivo">
                                <span>EFECTIVO:
                                    <?php if ($diff_efectivo): ?>
                                        <i class="fas fa-exclamation-triangle text-warning ml-1 icono-diff" title="Difiere con pagos registrados" data-campo="efectivo" data-caja="<?php echo $efectivo; ?>" data-real="<?php echo $v_efectivo; ?>" style="cursor:pointer"></i>
                                    <?php endif; ?>
                                </span>
                                <span><?php echo number_format((float)$efectivo, 2, '.', ','); ?></span>
                            </div>
                            <div class="ticket-row" id="row-tcredito">
                                <span>T. CREDITO:
                                    <?php if ($diff_tcredito): ?>
                                        <i class="fas fa-exclamation-triangle text-warning ml-1 icono-diff" title="Difiere con pagos registrados" data-campo="tcredito" data-caja="<?php echo $tcredito; ?>" data-real="<?php echo $v_tcredito; ?>" style="cursor:pointer"></i>
                                    <?php endif; ?>
                                </span>
                                <span><?php echo number_format((float)$tcredito, 2, '.', ','); ?></span>
                            </div>
                            <div class="ticket-row" id="row-tdebito">
                                <span>T. DEBITO:
                                    <?php if ($diff_tdebito): ?>
                                        <i class="fas fa-exclamation-triangle text-warning ml-1 icono-diff" title="Difiere con pagos registrados" data-campo="tdebito" data-caja="<?php echo $tdebito; ?>" data-real="<?php echo $v_tdebito; ?>" style="cursor:pointer"></i>
                                    <?php endif; ?>
                                </span>
                                <span><?php echo number_format((float)$tdebito, 2, '.', ','); ?></span>
                            </div>
                            <div class="ticket-row" id="row-transferencias">
                                <span>TRANSFERENCIAS:
                                    <?php if ($diff_transferencias): ?>
                                        <i class="fas fa-exclamation-triangle text-warning ml-1 icono-diff" title="Difiere con pagos registrados" data-campo="transferencias" data-caja="<?php echo $transferencias; ?>" data-real="<?php echo $v_transferencias; ?>" style="cursor:pointer"></i>
                                    <?php endif; ?>
                                </span>
                                <span><?php echo number_format((float)$transferencias, 2, '.', ','); ?></span>
                            </div>
                            <div class="ticket-row" id="row-cortesia">
                                <span>CORTESÍA:
                                    <?php if ($diff_cortesia): ?>
                                        <i class="fas fa-exclamation-triangle text-warning ml-1 icono-diff" title="Difiere con pagos registrados" data-campo="cortesia" data-caja="<?php echo $cortesia; ?>" data-real="<?php echo $v_cortesia; ?>" style="cursor:pointer"></i>
                                    <?php endif; ?>
                                </span>
                                <span><?php echo number_format((float)$cortesia, 2, '.', ','); ?></span>
                            </div>
                            <div class="ticket-line"></div>

                            <div class="ticket-row ticket-total">
                                <span>INICIAL + EFECTIVO:</span>
                                <span><?php echo number_format((float)$efectivo_real, 2, '.', ','); ?></span>
                            </div>
                            <div class="ticket-line"></div>

                            <div class="ticket-row">
                                <span>GASTOS:
                                    <?php if ($diff_gastos): ?>
                                        <i class="fas fa-exclamation-triangle text-warning ml-1 icono-diff" title="Difiere con gastos registrados" data-campo="gastos" data-caja="<?php echo $gastos; ?>" data-real="<?php echo $v_gastos; ?>" style="cursor:pointer"></i>
                                    <?php endif; ?>
                                </span>
                                <span><?php echo number_format((float)$gastos, 2, '.', ','); ?></span>
                            </div>
                            <div class="ticket-row">
                                <span>RETIROS:
                                    <?php if ($diff_retiros): ?>
                                        <i class="fas fa-exclamation-triangle text-warning ml-1 icono-diff" title="Difiere con retiros registrados" data-campo="retiros" data-caja="<?php echo $retiros; ?>" data-real="<?php echo $v_retiros; ?>" style="cursor:pointer"></i>
                                    <?php endif; ?>
                                </span>
                                <span><?php echo number_format((float)$retiros, 2, '.', ','); ?></span>
                            </div>
                            <div class="ticket-row ticket-total">
                                <span>EFECTIVO DISPONIBLE:</span>
                                <span><?php echo number_format((float)$efectivo_disponible, 2, '.', ','); ?></span>
                            </div>
                            <div class="ticket-line"></div>

                            <div class="ticket-row ticket-total">
                                <span>INGRESOS DEL DIA:</span>
                                <span><?php echo number_format((float)$final, 2, '.', ','); ?></span>
                            </div>
                        </div>

                        <?php if ($hay_diferencias): ?>
                            <div class="text-center mt-2" style="max-width:480px; margin:0 auto;">
                                <button type="button" id="btnActualizar" class="btn btn-warning btn-sm"
                                    data-fecha="<?php echo $fecha; ?>"
                                    <?php echo $puede_actualizar_ingresos ? '' : 'disabled'; ?>>
                                    <i class="fas fa-sync-alt mr-1"></i> Actualizar valores desde registros
                                </button>
                                <?php if (!$puede_actualizar_ingresos): ?>
                                    <div class="small text-muted mt-2">Solo el administrador o el usuario root pueden actualizar valores de una fecha distinta a hoy.</div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                    </form>

                </div>
            </div>
        </div>
    </section>

    <!-- Modal Abrir Caja -->
    <section>
        <div class="modal fade" id="modalDetallePagos" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background:#7262a1;">
                        <h5 class="modal-title text-white"><i class="fas fa-list mr-1"></i> Detalle de pagos — <span id="tituloDetalle"></span></h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-sm table-bordered table-striped" id="tablaDetallePagos">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Paciente</th>
                                    <th>Colaborador</th>
                                    <th>Método</th>
                                    <th class="text-right">Importe</th>
                                </tr>
                            </thead>
                            <tbody id="bodyDetallePagos"></tbody>
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td colspan="4" class="text-right">TOTAL:</td>
                                    <td class="text-right" id="totalDetallePagos"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Diferencias -->
    <section>
        <div class="modal fade" id="modalDiferencias" tabindex="-1" role="dialog" aria-labelledby="modalDiffLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title" id="modalDiffLabel"><i class="fas fa-exclamation-triangle mr-1"></i> Diferencias en caja</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small">Los valores en caja difieren de los registros calculados del sistema. Se muestran los importes actuales vs los calculados.</p>
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Concepto</th>
                                    <th>En caja</th>
                                    <th>Según sistema</th>
                                </tr>
                            </thead>
                            <tbody id="tablaDiff"></tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btnConfirmarActualizar" class="btn btn-warning"
                            data-fecha="<?php echo $fecha; ?>">
                            <i class="fas fa-sync-alt mr-1"></i> Actualizar
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Abrir Caja -->
    <section>
        <div class="modal fade" id="modalAbrirCaja" tabindex="-1" role="dialog" aria-labelledby="modalAbrirCajaLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form id="formAbrirCaja">
                    <div class="modal-content">
                        <div class="modal-header bg-green text-white">
                            <h5 class="modal-title" id="modalAbrirCajaLabel">Abrir Caja</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="accion_caja" name="accion_caja" value="abrir_caja">
                            <div class="form-group form-group-sm">
                                <label for="fecha_apertura">Fecha de Apertura:</label>
                                <input type="date" class="form-control form-control-sm" id="fecha_apertura" name="fecha_apertura" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="form-group form-group-sm">
                                <label for="monto_inicial">Monto Inicial:</label>
                                <input type="number" step="0.01" min="0.01" class="form-control form-control-sm" id="monto_inicial" name="monto_inicial" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Guardar</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- Modal Abrir Caja -->

    <section>

    </section>
    <section>

    </section>
    <!-- Modal Registrar Pago -->

    <section>

    </section>


</div>

<?php include_once 'templates/footer.php'; ?>
<script src="fjs/rptcaja.js?v=<?php echo (rand()); ?>"></script>
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