<?php
require 'vendor/autoload.php';
include_once 'bd/conexion.php';

use Mpdf\Mpdf;

$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

$objeto = new conn();
$conexion = $objeto->connect();

$sql = "SELECT id_pago, fecha_pago, id_cita, fecha_cita, hora_cita, paciente, colaborador, importe, metodo 
        FROM vpago 
        WHERE DATE(fecha_pago) BETWEEN :fecha_inicio AND :fecha_fin
        ORDER BY metodo, fecha_cita, hora_cita";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(':fecha_inicio', $fecha_inicio);
$stmt->bindParam(':fecha_fin', $fecha_fin);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar
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
    $ingresos[$m]['subtotal'] += $row['importe'];
}
$total_general = array_sum(array_column($ingresos, 'subtotal'));

// Estilos por método
$colores = [
    'Efectivo' => '#D4EFDF',
    'Tarjeta Crédito' => '#FADBD8',
    'Tarjeta Débito' => '#FCF3CF',
    'Transferencia' => '#D6EAF8',
    'Cortesía' => '#E8DAEF',
    'default' => '#F2F3F4'
];
$logoPath = __DIR__ . '/img/logoempresa.png'; // ruta absoluta o relativa
$logoBase64 = base64_encode(file_get_contents($logoPath));
$imgHtml = '<div style="text-align:center; margin-bottom:10px;">
    <img src="data:image/png;base64,' . $logoBase64 . '" style="height:80px;">
</div>';


// Construir HTML
$html = "
<style>
    body { font-family: Arial; font-size: 10px; }
    h2, h3 { text-align: center; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #888; padding: 2px; }
    th { font-weight: bold; text-align: center; }
    .subtotal, .total { font-weight: bold; text-align: right; background-color: #f0f0f0; }
</style>

<h3>REPORTE DE INGRESOS DEL $fecha_inicio AL $fecha_fin</h3>";
$html = $imgHtml . $html;

$orden_metodos = ['Efectivo', 'Tarjeta Crédito', 'Tarjeta Débito', 'Transferencia', 'Cortesía'];

foreach ($orden_metodos as $metodo) {
    if (isset($ingresos[$metodo])) {
        $grupo = $ingresos[$metodo];
        $bgcolor = $colores[$metodo] ?? $colores['default'];
        $html .= "
    <h4 style='background-color:$bgcolor; padding:5px;'>Método de Pago: $metodo</h4>
    <table>
        <thead>
            <tr>";
        // Aplico color en cada th
        $headers = ['Folio Cobro', 'Fecha Cobro', 'Folio Cita', 'Fecha Cita', 'Hora', 'Paciente', 'Colaborador', 'Importe'];
        foreach ($headers as $header) {
            $html .= "<th style='background-color:$bgcolor; font-weight:bold; text-align:center;'>$header</th>";
        }
        $html .= "</tr>
        </thead>
        <tbody>";

        foreach ($grupo['registros'] as $reg) {
            $html .= "<tr>
            <td align='center'>{$reg['id_pago']}</td>
            <td align='center'>{$reg['fecha_pago']}</td>
            <td align='center'>{$reg['id_cita']}</td>
            <td align='center'>{$reg['fecha_cita']}</td>
            <td align='center'>{$reg['hora_cita']}</td>
            <td>{$reg['paciente']}</td>
            <td>{$reg['colaborador']}</td>
            <td align='right'>$" . number_format($reg['importe'], 2) . "</td>
        </tr>";
        }

        // Fila subtotal con color aplicado en línea en ambas celdas
        $html .= "<tr>
        <td colspan='7' style='background-color:$bgcolor; font-weight:bold; text-align:right;'>Subtotal $metodo:</td>
        <td align='right' style='background-color:$bgcolor; font-weight:bold;'>$" . number_format($grupo['subtotal'], 2) . "</td>
    </tr>";

        $html .= "</tbody></table><br>";
    }
}


$html .= "<h3 class='total'>TOTAL GENERAL: $" . number_format($total_general, 2) . "</h3>";

// Crear PDF con mPDF
$mpdf = new Mpdf(['format' => 'Letter-L']);
$mpdf->SetTitle("Reporte de Ingresos $fecha_inicio a $fecha_fin");
$mpdf->WriteHTML($html);
$mpdf->Output("Reporte_Ingresos_$fecha_inicio.pdf", \Mpdf\Output\Destination::DOWNLOAD);
exit;
