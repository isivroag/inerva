<?php
// filepath: c:\xampp\htdocs\inerva\rptingresos.php
$pagina = "rptingresos";
include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";
include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-d');
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-d');

// Consulta sobre la vista vcobro
$sql = "SELECT folio_cob,fecha_cob, id_cita, fecha_cita, hora_cita, paciente, colaborador, total, metodo 
        FROM vcobro 
        WHERE DATE(fecha_cob) BETWEEN :fecha_inicio AND :fecha_fin
        ORDER BY metodo, fecha_cita, hora_cita";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(':fecha_inicio', $fecha_inicio);
$stmt->bindParam(':fecha_fin', $fecha_fin);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar por método de pago
$ingresos = [];
foreach ($data as $row) {
    $metodo = $row['metodo'];
    if (!isset($ingresos[$metodo])) {
        $ingresos[$metodo] = [
            'registros' => [],
            'subtotal' => 0
        ];
    }
    $ingresos[$metodo]['registros'][] = $row;
    $ingresos[$metodo]['subtotal'] += $row['total'];
}
$total_general = 0;
foreach ($ingresos as $m) {
    $total_general += $m['subtotal'];
}
?>
<style>
@media print {
  /* Ocultar botones, formularios, navbar, sidebar, footer */
  #btnImprimir, #btnDescargar, #exportExcel, form, .main-header, .main-sidebar, footer {
    display: none !important;
  }

  /* Ajustar ancho y quitar sombras para impresión */
  .content-wrapper, .card {
    width: 100% !important;
    box-shadow: none !important;
  }

  /* Tablas limpias y sin cortes */
  table {
    border-collapse: collapse !important;
    width: 100% !important;
    page-break-inside: avoid;
  }

  th, td {
    border: 1px solid black !important;
    padding: 6px !important;
    font-size: 12pt !important;
  }

  th {
    font-weight: bold !important;
    text-align: center !important;
    background-color: #d4efdf !important;
  }

  tr {
    page-break-inside: avoid !important;
  }

  /* Márgenes personalizados para la hoja */
  @page {
    margin: 20mm 15mm 20mm 15mm;
  }
}
</style>

<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

<div class="content-wrapper">
    <section class="content">
        <div class="card">
            <div class="card-header bg-green text-light">
                <h1 class="card-title mx-auto">Ingresos por Rango de Fechas</h1>
            </div>
            <div class="card-body">
                <form method="get">
                    <div class="row justify-content-center">
                        <div class="col-sm-1 ">
                            <div class="form-group input-group-sm">

                                <label for="fecha_inicio">Fecha inicio:</label>
                                <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" value="<?php echo $fecha_inicio; ?>">
                            </div>
                        </div>
                        <div class="col-sm-1 ">
                            <div class="form-group input-group-sm">
                                <label for="fecha_fin">Fecha fin:</label>
                                <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" value="<?php echo $fecha_fin; ?>">
                            </div>
                        </div>
                        <div class="col-sm-1 align-self-end text-center ">
                            <div class="form-group input-group-sm">
                                <button type="submit" class="btn bg-green ">Consultar</button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row justify-content-center mt-3">

                    <div class="col-auto">
                        <button id="btnImprimir" class="btn bg-green" onclick="window.open('rptingresos_print.php?fecha_inicio=<?php echo $fecha_inicio; ?>&fecha_fin=<?php echo $fecha_fin; ?>', '_blank')"><i class="fas fa-print"></i> Imprimir</button>

                        
                        <button id="btnPDF" class="btn bg-green" onclick="window.location.href='generar_pdf_ingresos.php?fecha_inicio=<?php echo $fecha_inicio; ?>&fecha_fin=<?php echo $fecha_fin; ?>'">
                            <i class="fas fa-file-pdf"></i> Descargar PDF
                        </button>
                        <form action="generar_excel_ingresos.php" method="post" target="_blank" class="d-inline">
                            <input type="hidden" name="fecha_inicio" value="<?php echo $fecha_inicio; ?>">
                            <input type="hidden" name="fecha_fin" value="<?php echo $fecha_fin; ?>">
                            <button type="submit" class="btn bg-green">
                                <i class="fas fa-file-excel"></i> Exportar a Excel
                            </button>
                        </form>
                    </div>


                </div>
                <?php
                $coloresMetodo = [
                    'Efectivo' => 'D4EFDF',
                    'Tarjeta Crédito' => 'FADBD8',
                    'Tarjeta Débito' => 'FCF3CF',
                    'Transferencia' => 'D6EAF8',
                    'Cortesía' => 'E8DAEF'
                ];
                ?>
                <?php foreach ($ingresos as $metodo => $grupo): ?>
                    <?php
                    $color = isset($coloresMetodo[$metodo]) ? $coloresMetodo[$metodo] : 'FFFFFF';
                    ?>
                    <h5 class="mt-1 text-primary">Método de Pago: <?php echo htmlspecialchars($metodo); ?></h5>
                    <div class="table-responsive">
                        <table class="tablaredonda table table-sm table-bordered table-striped">
                            <thead style="background-color: #<?php echo $color; ?>;" class="text-center">
                                <tr>
                                    <th>Folio Cobro</th>
                                    <th>Fecha Cobro</th>
                                    <th>Folio Cita</th>
                                    <th>Fecha Cita</th>
                                    <th>Hora</th>
                                    <th>Paciente</th>
                                    <th>Colaborador</th>
                                    <th>Importe</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($grupo['registros'] as $row): ?>
                                    <tr>
                                        <td class="text-center"><?php echo $row['folio_cob']; ?></td>
                                        <td class="text-center"><?php echo $row['fecha_cob']; ?></td>
                                        <td class="text-center"><?php echo $row['id_cita']; ?></td>
                                        <td class="text-center"><?php echo $row['fecha_cita']; ?></td>
                                        <td class="text-center"><?php echo $row['hora_cita']; ?></td>
                                        <td><?php echo htmlspecialchars($row['paciente']); ?></td>
                                        <td><?php echo htmlspecialchars($row['colaborador']); ?></td>
                                        <td class="text-right">$<?php echo number_format($row['total'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="font-weight-bold bg-light">
                                    <td colspan="7" class="text-right">Subtotal <?php echo htmlspecialchars($metodo); ?>:</td>
                                    <td class="text-right">$<?php echo number_format($grupo['subtotal'], 2); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>

                <div class="mt-2 text-right">
                    <h4>Total General: $<?php echo number_format($total_general, 2); ?></h4>
                </div>
            </div>
        </div>
    </section>
</div>



<?php include_once 'templates/footer.php'; ?>



<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>