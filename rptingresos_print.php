<?php
// rptingresos_print.php
$pagina = "rptingresos_print";
include_once "bd/conexion.php";
$objeto = new conn();
$conexion = $objeto->connect();

$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

$sql = "SELECT folio_cob, fecha_cob, id_cita, fecha_cita, hora_cita, paciente, colaborador, total, metodo 
        FROM vcobro 
        WHERE DATE(fecha_cob) BETWEEN :fecha_inicio AND :fecha_fin
        ORDER BY metodo, fecha_cita, hora_cita";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(':fecha_inicio', $fecha_inicio);
$stmt->bindParam(':fecha_fin', $fecha_fin);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$ingresos = [];
foreach ($data as $row) {
    $m = $row['metodo'];
    if (!isset($ingresos[$m])) {
        $ingresos[$m] = [
            'registros' => [],
            'subtotal' => 0
        ];
    }
    $ingresos[$m]['registros'][] = $row;
    $ingresos[$m]['subtotal'] += $row['total'];
}
$total_general = array_sum(array_column($ingresos, 'subtotal'));

$coloresMetodo = [
    'Efectivo' => 'D4EFDF',
    'Tarjeta Crédito' => 'FADBD8',
    'Tarjeta Débito' => 'FCF3CF',
    'Transferencia' => 'D6EAF8',
    'Cortesía' => 'E8DAEF'
];

// Leer logo en base64 para imprimir sin problemas de ruta
$logoPath = __DIR__ . '/img/logoempresa.png';
$logoBase64 = '';
if (file_exists($logoPath)) {
    $logoBase64 = base64_encode(file_get_contents($logoPath));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Reporte Ingresos <?php echo $fecha_inicio; ?> a <?php echo $fecha_fin; ?></title>
<style>
  body { font-family: Arial, sans-serif; margin: 20px; }
  .logo { text-align: center; margin-bottom: 10px; }
  .logo img { height: 80px; }
  h2, h3, h4 { text-align: center; margin-bottom: 10px; }
  table { border-collapse: collapse; width: 100%; margin-bottom: 15px; }
  th, td { border: 1px solid #000; padding: 6px; font-size: 12pt; }
  th {  font-weight: bold; text-align: center; }
  td { vertical-align: middle; }
  .subtotal-row { font-weight: bold; }
  .total-general { font-weight: bold; font-size: 1.3em; text-align: right; margin-top: 20px; }
</style>
</head>
<body>

<?php if ($logoBase64): ?>
    <div class="logo">
        <img src="data:image/png;base64,<?php echo $logoBase64; ?>" alt="Logo Empresa" />
    </div>
<?php endif; ?>

<h2>INERVA</h2>
<h3>REPORTE DE INGRESOS DEL <?php echo $fecha_inicio; ?> AL <?php echo $fecha_fin; ?></h3>

<?php foreach ($ingresos as $metodo => $grupo): 
    $colorFondo = $coloresMetodo[$metodo] ?? 'FFFFFF';
?>
    <h4 style="background-color: #<?php echo $colorFondo; ?>; padding: 5px;"><?php echo htmlspecialchars($metodo); ?></h4>
    <table>
        <thead style="background-color: #<?php echo $colorFondo; ?>;">
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
                <td style="text-align:center;"><?php echo $row['folio_cob']; ?></td>
                <td style="text-align:center;"><?php echo $row['fecha_cob']; ?></td>
                <td style="text-align:center;"><?php echo $row['id_cita']; ?></td>
                <td style="text-align:center;"><?php echo $row['fecha_cita']; ?></td>
                <td style="text-align:center;"><?php echo $row['hora_cita']; ?></td>
                <td><?php echo htmlspecialchars($row['paciente']); ?></td>
                <td><?php echo htmlspecialchars($row['colaborador']); ?></td>
                <td style="text-align:right;">$<?php echo number_format($row['total'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="subtotal-row" style="background-color: #<?php echo $colorFondo; ?>;">
                <td colspan="7" style="text-align:right;">Subtotal <?php echo htmlspecialchars($metodo); ?>:</td>
                <td style="text-align:right;">$<?php echo number_format($grupo['subtotal'], 2); ?></td>
            </tr>
        </tbody>
    </table>
<?php endforeach; ?>

<div class="total-general">TOTAL GENERAL: $<?php echo number_format($total_general, 2); ?></div>

<script>
  window.onload = function() {
    window.print();
  };
</script>

</body>
</html>
