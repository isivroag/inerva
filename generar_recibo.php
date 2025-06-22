<?php
require 'vendor/autoload.php';
include_once 'bd/conexion.php';

use Mpdf\Mpdf;

// Verificar si se recibió el folio de cobro
if (!isset($_GET['folio_cob'])) {
    die('ERROR: NO SE ESPECIFICÓ EL FOLIO DE COBRO');
}

$folio_cob = $_GET['folio_cob'];
$personalizado = isset($_GET['per']) ? (bool)$_GET['per'] : false;

$objeto = new conn();
$conexion = $objeto->connect();

// Consulta para obtener los datos del cobro
$sql = "SELECT 
            folio_cob, 
            fecha_cob, 
            id_cita, 
            fecha_cita, 
            hora_cita, 
            paciente, 
            servicio, 
            costo, 
            descuento, 
            total, 
            metodo
        FROM vcobro 
        WHERE folio_cob = :folio_cob";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(':folio_cob', $folio_cob);
$stmt->execute();
$cobro = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cobro) {
    die('ERROR: NO SE ENCONTRÓ EL REGISTRO DE COBRO ESPECIFICADO');
}

// Convertir todos los textos a mayúsculas
$nombre_cliente = $personalizado ? mb_strtoupper($cobro['paciente'], 'UTF-8') : 'PÚBLICO EN GENERAL';
$servicio = mb_strtoupper($cobro['servicio'], 'UTF-8');
$metodo = mb_strtoupper($cobro['metodo'], 'UTF-8');

// Formatear fechas
$fecha_cob = date('d/m/Y', strtotime($cobro['fecha_cob']));
$fecha_cita = date('d/m/Y', strtotime($cobro['fecha_cita']));
$hora_cita = substr($cobro['hora_cita'], 0, 5);

// Cargar logo en base64
$logoPath = __DIR__ . '/img/logoempresa.png';
$logoBase64 = base64_encode(file_get_contents($logoPath));
$faviconPath = $_SERVER['DOCUMENT_ROOT'] . '/img/icon/favicon-32x32.png';
$faviconImg = '';
if (file_exists($faviconPath)) {
    $faviconBase64 = base64_encode(file_get_contents($faviconPath));
    $faviconImg = '<img src="data:image/png;base64,'.$faviconBase64.'" style="height:20px;position:absolute;right:20px;top:15px;">';
}
// Estilos CSS
$styles = "
<style>
    body { font-family: Arial; font-size: 11px; line-height: 1.5; text-transform: uppercase; }
    .header { text-align: center; margin-bottom: 10px; }
    .logo { height: 35px; margin-bottom: 5px; }
    .title { font-size: 18px; font-weight: bold; margin: 10px 0; }
    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    .info-table td { padding: 3px 5px; vertical-align: top; }
    .info-table .label { font-weight: bold; white-space: nowrap; }
    .data-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
    .data-table th, .data-table td { border: 1px solid #000; padding: 8px; }
    .data-table th { background-color: #f2f2f2; text-align: left; font-weight: bold; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .total-row { font-weight: bold; background-color: #f2f2f2; }
    .footer { margin-top: 20px; font-size: 10px; color: #555; text-align: center; }
    
    .amount { font-weight: bold; }
</style>
";

// Construir HTML del recibo con diseño tabular
$html = $styles . "
<div class='bordered'>
     <div class='header' style='position:relative;'>
        ".$faviconImg."
        <img src='data:image/png;base64,".$logoBase64."' class='logo'>
        <div class='title'>recibo de pago</div>
    </div>

    <table class='info-table' style='width:100%;'>
        <tr>
            <td class='label' style='text-align:left; width:10%; white-space:nowrap;'>FOLIO COB.:</td>
            <td style='text-align:left; width:50%;'>" . $cobro['folio_cob'] . "</td>
            <td class='label' style=' width:5%; white-space:nowrap;'>FOLIO CITA:</td>
            <td style=' width:10%;'>" . $cobro['id_cita'] . "</td>
        </tr>
        <tr>
            <td class='label' style='text-align:left;'>FECHA:</td>
            <td style='text-align:left;'>" . $fecha_cob . "</td>
            <td class='label' style=''>FECHA SERVICIO:</td>
            <td style=''>" . $fecha_cita . " " . $hora_cita . " hrs</td>
        </tr>
        <tr>
            <td class='label' style='text-align:left;'>PACIENTE:</td>
            <td colspan='3' style='text-align:left;'>" . htmlspecialchars($nombre_cliente) . "</td>
        </tr>
    </table>

    <table class='data-table'>
        <thead>
            <tr>
                <th class='text-center' style='text-align:center;'>DESCRIPCIÓN</th>
                <th class='text-center' style='text-align:center;'>IMPORTE</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>" . htmlspecialchars($servicio) . "</td>
                <td class='text-right amount'>$" . number_format($cobro['costo'], 2) . "</td>
            </tr>
            <tr>
                <td>DESCUENTO</td>
                <td class='text-right amount'>$" . number_format($cobro['descuento'], 2) . "</td>
            </tr>
            <tr class='total-row'>
                <td>TOTAL</td>
                <td class='text-right amount'>$" . number_format($cobro['total'], 2) . "</td>
            </tr>
            <tr>
                <td colspan='2'><strong>MÉTODO DE PAGO:</strong> " . htmlspecialchars($metodo) . "</td>
            </tr>
        </tbody>
    </table>

    <div class='footer'>
        <p>este documento no es un comprobante fiscal</p>
    </div>
</div>";

// Configurar y generar PDF en formato media carta horizontal
$mpdf = new Mpdf([
    'format' => 'A5-L', // Formato media carta horizontal (A5 landscape)
    'margin_left' => 15,
    'margin_right' => 15,
    'margin_top' => 15,
    'margin_bottom' => 15,
    'margin_header' => 10,
    'margin_footer' => 10,
    'default_font_size' => 12
]);

$mpdf->SetTitle("RECIBO DE PAGO " . $cobro['folio_cob']);
$mpdf->SetAuthor('Inverva');
$mpdf->SetCreator('Sistema de Cobranza');
$mpdf->SetSubject('Recibo de Pago');
$mpdf->SetKeywords('recibo, pago, cobranza');
if (file_exists($faviconPath)) {
    $mpdf->SetWatermarkImage('data:image/png;base64,'.$faviconBase64);
    $mpdf->showWatermarkImage = true;
    $mpdf->watermarkImageAlpha = 0.1;
    $mpdf->watermarkImgBehind = true;
}

// Convertir favicon a formato adecuado para metadatos

$mpdf->WriteHTML($html);

// Nombre del archivo para descarga
$filename = "RECIBO_" . $cobro['folio_cob'] . ".pdf";

// Salida del PDF
$mpdf->Output($filename, \Mpdf\Output\Destination::INLINE);
exit;