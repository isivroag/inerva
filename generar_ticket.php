<?php
require 'vendor/autoload.php';
include_once 'bd/conexion.php';
include_once 'bd/CifrasEnLetras.php';

 $enpesos = new CifrasEnLetras();
 


use Mpdf\Mpdf;

// Verificar si se recibió el id_pago
if (!isset($_GET['id_pago'])) {
    die('ERROR: NO SE ESPECIFICÓ EL ID DE PAGO');
}

$id_pago = $_GET['id_pago'];
$personalizado = isset($_GET['per']) ? (bool)$_GET['per'] : false;

$objeto = new conn();
$conexion = $objeto->connect();

// Consulta para obtener los datos del pago
$sql = "SELECT 
            id_pago,
            folio_cxc,
            fecha_pago,
            id_cita,
            fecha_cita,
            hora_cita,
            paciente,
            servicio,
            importe,
            metodo
        FROM vpago
        WHERE id_pago = :id_pago";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(':id_pago', $id_pago);
$stmt->execute();
$pago = $stmt->fetch(PDO::FETCH_ASSOC);
$pesos=$enpesos->convertirEurosEnLetras(floatval($pago['importe']));

if (!$pago) {
    die('ERROR: NO SE ENCONTRÓ EL REGISTRO DE PAGO ESPECIFICADO');
}

// Formatear fechas y hora
$fecha_pago = date('d/m/Y', strtotime($pago['fecha_pago']));
$fecha_cita = date('d/m/Y', strtotime($pago['fecha_cita']));
$hora_cita = substr($pago['hora_cita'], 0, 5);
$nombre_cliente = $personalizado ? mb_strtoupper($pago['paciente'], 'UTF-8') : 'PÚBLICO EN GENERAL';

// Cargar logo en base64 (opcional)
$logoPath = __DIR__ . '/img/logoempresa.png';
$logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';

// Estilos para ticket 80mm
$styles = "
<style>
    body { font-family: Arial, sans-serif; font-size: 8pt; }
    .ticket { width: 80mm; max-width: 80mm; margin: 0 auto; padding: 0; }
    .logo-container { text-align: center; margin: 0 auto; width: 100%; }
    .logo { max-width: 150px; height: auto; margin: 0 auto; display: inline-block; }
    .empresa { text-align: center; font-size: 10pt; font-weight: bold; margin-bottom: 2px; }
    .rfc, .direccion, .regimen { text-align: center; font-size: 6pt; margin-bottom: 1px; }
    .sucursal { text-align: center; font-size: 10pt; font-weight: bold; margin-bottom: 5px; }
    .direccion-sucursal { text-align: center; font-size: 7pt; margin-bottom: 5px; line-height: 1.2; }
    .title { text-align: center; font-size: 11pt; font-weight: bold; margin-bottom: 5px; }
    .info { font-size: 9pt; }
    .line { border-top: 1px dashed #000; margin: 5px 0; }
    .totales { font-size: 9pt; font-weight: bold; }
    .letras { font-size: 8pt;  }
    .footer { text-align: center; font-size: 8pt; margin-top: 10px; line-height: 1.2; }
    .label { font-weight: bold; }
    .concepto { margin: 0 0 5px 0; font-size: 8pt; }
    .paciente { margin: 10px 0 0 0; font-size: 8pt; }
    table { width: 100%; border-collapse: collapse; }
    td { padding: 2px 0; vertical-align: top; }
    .td-left { text-align: left; }
    .td-right { text-align: right; }
    .td-center { text-align: center; }
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
    <div class='title'>RECIBO DE PAGO</div>
    <div class='line'></div>
    
    <table>
        <tr>
            <td class='td-left'><span class='label'>Folio Pago: {$pago['id_pago']}</span></td>
            <td class='td-right'><span class='label'>Fecha: $fecha_pago</span></td>
        </tr>
        <tr>
            <td class='td-left'><span class='label'>Folio CXC: {$pago['folio_cxc']}</span></td>
            <td class='td-right'><span class='label'>Cita: {$pago['id_cita']}</span></td>
        </tr>
    </table>

    <div class='paciente'><span class='label'>Paciente:</span> ".htmlspecialchars($nombre_cliente)."</div>
    <div class='concepto'>
        <div><span class='label'>Concepto:</span> ".htmlspecialchars($pago['servicio'])."</div>
        <table>
            <tr>
                <td class='td-left'><span class='label'>Fecha cita:</span> $fecha_cita</td>
                <td class='td-right'><span class='label'>Hora:</span> $hora_cita</td>
            </tr>
        </table>
    </div>
    
    <div class='line'></div>
    
    <table>
        <tr>
            <td class='td-left'><span class='label'>Importe:</span></td>
            <td class='td-right'><span class='totales'>$".number_format($pago['importe'],2)."</span></td>
        </tr>
        <tr>
            <td class='td-left'><span class='label'>Método de pago:</span></td>
            <td class='td-right'>".htmlspecialchars($pago['metodo'])."</td>
        </tr>
        <tr>
            <td class='td-left'><span class='label'>Total en letras:</span></td>
           
        </tr>
        <tr>
            <td colspan=2 class='td-center'><span class='letras'>".$pesos."</span></td>
        </tr>
    </table>
    
    <div class='line'></div>
    <div class='footer'>¡Gracias por su pago!<br>Este documento no es un comprobante fiscal.</div>
</div>
";

// Configurar y generar PDF en formato ticket 80mm
$mpdf = new Mpdf([
    'format' => [80, 150], // 80mm x 150mm (alto ajustable)
    'margin_left' => 5,
    'margin_right' => 5,
    'margin_top' => 5,
    'margin_bottom' => 5,
    'margin_header' => 0,
    'margin_footer' => 0,
    'default_font_size' => 10
]);

$mpdf->SetTitle("TICKET PAGO {$pago['id_pago']}");
$mpdf->WriteHTML($html);

// Nombre del archivo para descarga
$filename = "TICKET_PAGO_{$pago['id_pago']}.pdf";

// Salida del PDF
$mpdf->Output($filename, \Mpdf\Output\Destination::INLINE);
exit;