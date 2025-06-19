<?php
$folio = (isset($_GET['id'])) ? $_GET['id'] : '';

require_once __DIR__.'/../vendor/autoload.php';

if (!class_exists('\Mpdf\Mpdf')) {
    die('Error: La clase Mpdf no está disponible. ¿Instalaste la librería con Composer?');
}

// Cargar favicon en Base64
$faviconPath = $_SERVER['DOCUMENT_ROOT'] . '/img/favicon-32x32.png';
$faviconBase64 = '';
if (file_exists($faviconPath)) {
    $faviconBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($faviconPath));
}

$tituloVentana = "Presupuesto INBA - Folio $folio";

$css = file_get_contents('../css/estilocotizacion2.css');
require_once('pcotiza.php');
$plantilla = getPlantilla($folio);

// Inyectar favicon y título en el HTML
if ($faviconBase64) {
    $plantilla = str_replace(
        '<head>',
        '<head>
        <title>' . $tituloVentana . '</title>
        <link rel="icon" type="image/png" href="' . $faviconBase64 . '">
        <div style="position: absolute; top: 10px; right: 10px;">
            <img src="' . $faviconBase64 . '" width="32" height="32">
        </div>',
        $plantilla
    );
} else {
    $plantilla = str_replace('<head>', '<head><title>' . $tituloVentana . '</title>', $plantilla);
}

// Configurar mPDF
$mpdf = new \Mpdf\Mpdf(['format' => 'Letter']);
$mpdf->SetTitle($tituloVentana); // Título en metadatos del PDF
$mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
$mpdf->WriteHTML($plantilla, \Mpdf\HTMLParserMode::HTML_BODY);
$mpdf->Output("Presupuesto $folio.pdf", "I");
?>