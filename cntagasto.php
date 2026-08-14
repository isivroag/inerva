<?php
date_default_timezone_set('America/Mexico_City');
$pagina = "cntagasto";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";

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

<div class="content-wrapper" id="ctxGasto"
	data-rol="<?php echo $rol_usuario; ?>"
	data-es-admin="<?php echo $es_admin_o_root ? '1' : '0'; ?>"
	data-fecha="<?php echo $fecha; ?>">

	<section class="content">
		<div class="card">
			<div class="card-header bg-green text-light">
				<h1 class="card-title mx-auto">GASTOS</h1>
			</div>

			<div class="card-body">
				<div class="row mb-3">
					<div class="col-auto">
						<button id="btnNuevo" type="button" class="btn bg-green btn-ms">
							<i class="fas fa-plus-square text-light"></i><span class="text-light"> Nuevo</span>
						</button>
					</div>

					<?php if ($es_admin_o_root): ?>
						<div class="col-auto">
							<label for="ctrlfecha" class="mr-2 mb-0">Fecha:</label>
							<input type="date" id="ctrlfecha" name="ctrlfecha" value="<?php echo $fecha; ?>" class="form-control form-control-sm">
						</div>
					<?php endif; ?>
				</div>

				<div class="table-responsive">
					<table id="tablaGastos" class="tablaredonda table table-sm table-striped table-bordered table-condensed text-nowrap w-auto mx-auto" style="width:100%; font-size:14px">
						<thead class="text-center bg-green">
							<tr>
								<th>ID</th>
								<th>FECHA</th>
								<th>FACTURADO</th>
								<th>REFERENCIA</th>
								<th>CONCEPTO</th>
								<th>MONTO</th>
								<th>ESTADO</th>
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
		<div class="modal fade" id="modalGasto" tabindex="-1" role="dialog" aria-labelledby="modalGastoLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<form id="formGasto">
					<div class="modal-content">
						<div class="modal-header bg-green text-white">
							<h5 class="modal-title" id="modalGastoLabel">NUEVO GASTO</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body row">
							<input type="hidden" id="id_gasto" name="id_gasto">

							<div class="col-sm-4">
								<div class="form-group input-group-sm">
									<label for="fecha_gasto" class="col-form-label">*FECHA:</label>
									<input type="date" class="form-control" id="fecha_gasto" name="fecha_gasto" value="<?php echo $fecha; ?>" required>
								</div>
							</div>

							<div class="col-sm-4 d-flex align-items-end">
								<div class="form-group input-group-sm mb-2">
									<div class="form-check">
										<input type="checkbox" class="form-check-input" id="facturado" name="facturado" value="1">
										<label class="form-check-label" for="facturado">Facturado</label>
									</div>
								</div>
							</div>

							<div class="col-sm-4">
								<div class="form-group input-group-sm">
									<label for="monto" class="col-form-label">*MONTO:</label>
									<input type="number" step="0.01" min="0.01" class="form-control" id="monto" name="monto" placeholder="0.00" required>
								</div>
							</div>

							<div class="col-sm-12">
								<div class="form-group input-group-sm">
									<label for="referencia" class="col-form-label">*REFERENCIA:</label>
									<input type="text" class="form-control" id="referencia" name="referencia" placeholder="Ticket, nota, folio, etc." maxlength="150" required>
								</div>
							</div>

							<div class="col-sm-12">
								<div class="form-group input-group-sm">
									<label for="concepto" class="col-form-label">*CONCEPTO:</label>
									<textarea rows="3" class="form-control" id="concepto" name="concepto" placeholder="Descripcion del gasto" maxlength="400" required></textarea>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fas fa-ban"></i> Cerrar</button>
							<button type="submit" id="btnGuardarGasto" class="btn btn-success"><i class="far fa-save"></i> Guardar</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</section>

	<section>
		<div class="modal fade" id="modalCancelarGasto" tabindex="-1" role="dialog" aria-labelledby="modalCancelarGastoLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<form id="formCancelarGasto">
					<div class="modal-content">
						<div class="modal-header bg-danger text-white">
							<h5 class="modal-title" id="modalCancelarGastoLabel">CANCELAR GASTO</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<input type="hidden" id="id_gasto_cancelar" name="id_gasto_cancelar">
							<div class="form-group input-group-sm">
								<label for="motivo_cancelacion" class="col-form-label">*MOTIVO DE CANCELACION:</label>
								<textarea rows="3" class="form-control" id="motivo_cancelacion" name="motivo_cancelacion" placeholder="Describa el motivo" required></textarea>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-warning" data-dismiss="modal"><i class="fas fa-ban"></i> Cerrar</button>
							<button type="submit" class="btn btn-danger"><i class="fas fa-times-circle"></i> Cancelar Gasto</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</section>
</div>

<?php include_once 'templates/footer.php'; ?>
<script src="fjs/cntagasto.js?v=<?php echo (rand()); ?>"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>
