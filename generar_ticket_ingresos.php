<?php
// filepath: c:\xampp\htdocs\inerva\generar_ticket_ingresos.php
require 'vendor/autoload.php';
include_once 'bd/conexion.php';

use Mpdf\Mpdf;

$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

$objeto = new conn();
$conexion = $objeto->connect();

// Consulta sobre la vista vpago
$sql = "SELECT id_pago, fecha_pago, id_cita, fecha_cita, hora_cita, paciente, colaborador, importe, metodo 
        FROM vpago 
        WHERE DATE(fecha_pago) BETWEEN :fecha_inicio AND :fecha_fin
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
    $ingresos[$metodo]['subtotal'] += $row['importe'];
}
$total_general = 0;
foreach ($ingresos as $m) {
    $total_general += $m['subtotal'];
}

// Formatear fechas para mostrar
$fecha_inicio_formatted = date('d/m/Y', strtotime($fecha_inicio));
$fecha_fin_formatted = date('d/m/Y', strtotime($fecha_fin));

// Cargar logo en base64 (opcional)
$logoPath = __DIR__ . '/img/logoempresamn.png';
$logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';

// Estilos para ticket 80mm
$styles = "
<style>
    body { font-family: Arial, sans-serif; font-size: 7pt; }
    .ticket { width: 80mm; max-width: 80mm; margin: 0 auto; padding: 0; }
    .logo-container { text-align: center; margin: 0 auto; width: 100%; }
    .logo { max-width: 120px; height: auto; margin: 0 auto; display: inline-block; }
    .empresa { text-align: center; font-size: 9pt; font-weight: bold; margin-bottom: 2px; }
    .rfc, .direccion, .regimen { text-align: center; font-size: 6pt; margin-bottom: 1px; }
    .sucursal { text-align: center; font-size: 9pt; font-weight: bold; margin-bottom: 3px; }
    .direccion-sucursal { text-align: center; font-size: 6pt; margin-bottom: 5px; line-height: 1.1; }
    .title { text-align: center; font-size: 10pt; font-weight: bold; margin-bottom: 3px; }
    .periodo { text-align: center; font-size: 8pt; margin-bottom: 5px; }
    .info { font-size: 7pt; }
    .line { border-top: 1px dashed #000; margin: 3px 0; }
    .metodo-title { font-size: 8pt; font-weight: bold; margin: 8px 0 3px 0; text-align: center; }
    .subtotal { font-size: 8pt; font-weight: bold; text-align: right; margin: 3px 0; }
    .total-general { font-size: 9pt; font-weight: bold; text-align: center; margin: 8px 0; }
    .footer { text-align: center; font-size: 6pt; margin-top: 8px; line-height: 1.2; }
    .registro { margin-bottom: 3px; font-size: 7pt; }
    .registro-header { font-weight: bold; }
</style>
";

// Construir HTML del ticket
$html = $styles . "
<div class='ticket'>
    <div class='logo-container'>
        ".($logoBase64 ? "<img src='data:image/png;base64,$logoBase64' class='logo'>" : "")."
    </div>
    <br>
    
    <div class='empresa'>CASTASA S.A. DE C.V.</div>
    <div class='rfc'>CAS140213GL3</div>
    <div class='direccion'>AV. TEZIUTLAN NORTE #85 INT. 2</div>
    <div class='direccion'>COL. LA PAZ</div>
    <div class='direccion'>C.P. 72160 PUEBLA, PUEBLA</div>
    <div class='regimen'>REGIMEN GENERAL DE LEY PERSONAS MORALES</div>
    <br>
    <div class='sucursal'>INERVA TERAPIA COGNITIVO CONDUCTUAL</div>
    <div class='direccion-sucursal'>Av. Araucarias 209 Cp. 91190<br>Col. Indeco Animas, Xalapa, Veracruz</div>
    <br>
    <div class='title'>REPORTE DE INGRESOS</div>
    <div class='periodo'>Del $fecha_inicio_formatted al $fecha_fin_formatted</div>
    <div class='line'></div>
";

// Agregar cada método de pago y sus registros
foreach ($ingresos as $metodo => $grupo) {
    $html .= "<div class='metodo-title'>$metodo</div>";
    $html .= "<div class='line'></div>";
    
    foreach ($grupo['registros'] as $registro) {
        $fecha_pago_formatted = date('d/m/Y', strtotime($registro['fecha_pago']));
        $fecha_cita_formatted = date('d/m/Y', strtotime($registro['fecha_cita']));
        $hora_formatted = substr($registro['hora_cita'], 0, 5);
        
        $html .= "
        <div class='registro'>
            <div class='registro-header'>Folio: {$registro['id_pago']} - Cita: {$registro['id_cita']}</div>
            <div>Fecha: $fecha_pago_formatted</div>
            <div>Paciente: ".htmlspecialchars($registro['paciente'])."</div>
            <div>Colaborador: ".htmlspecialchars($registro['colaborador'])."</div>
            <div>Cita: $fecha_cita_formatted $hora_formatted</div>
            <div style='text-align: right; font-weight: bold;'>$".number_format($registro['importe'], 2)."</div>
        </div>";
    }
    
    $html .= "<div class='subtotal'>Subtotal $metodo: $".number_format($grupo['subtotal'], 2)."</div>";
    $html .= "<div class='line'></div>";
}

$html .= "
    <div class='total-general'>TOTAL GENERAL<br>$".number_format($total_general, 2)."</div>
    <div class='line'></div>
    <div class='footer'>Reporte generado el ".date('d/m/Y H:i')."<br>Este documento es solo informativo.</div>
</div>
";

// Configurar y generar PDF en formato ticket 80mm
$mpdf = new Mpdf([
    'format' => [80, 200], // 80mm x 200mm (alto mayor para el reporte)
    'margin_left' => 3,
    'margin_right' => 3,
    'margin_top' => 3,
    'margin_bottom' => 3,
    'margin_header' => 0,
    'margin_footer' => 0,
    'default_font_size' => 7,
    'dpi' => 100,
]);

$mpdf->SetTitle("TICKET INGRESOS {$fecha_inicio}_a_{$fecha_fin}");
$mpdf->WriteHTML($html);

// Nombre del archivo para descarga
$filename = "TICKET_INGRESOS_{$fecha_inicio}_a_{$fecha_fin}.pdf";

// Salida del PDF
$mpdf->Output($filename, \Mpdf\Output\Destination::INLINE);
exit;