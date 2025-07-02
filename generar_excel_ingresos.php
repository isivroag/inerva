<?php
require 'vendor/autoload.php';
include_once 'bd/conexion.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

$fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d');
$fecha_fin = $_POST['fecha_fin'] ?? date('Y-m-d');

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

// Agrupar por método
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

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Ingresos');

$drawing = new Drawing();
$drawing->setName('Logo Empresa');
$drawing->setDescription('Logo Empresa');
$drawing->setPath(__DIR__ . '/img/logoempresa.png');  // Ajusta la ruta si es necesario
$drawing->setHeight(70);  // Altura del logo en pixeles
$drawing->setCoordinates('D1'); // Ubicación aproximada al centro (col D, fila 1)
$drawing->setOffsetX(0);
$drawing->setOffsetY(5);
$drawing->setWorksheet($sheet);

// Ahora el título un poco a la derecha (por ejemplo, columna C hasta H)


// Título
$titulo = "REPORTE DE INGRESOS DEL $fecha_inicio AL $fecha_fin";
$sheet->setCellValue('A2', $titulo);
$sheet->mergeCells('A1:H1');
$sheet->mergeCells('A2:H2');
$sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

// Opcional: ajustar alto de fila para que no quede muy pegado
$sheet->getRowDimension(1)->setRowHeight(80);
$sheet->getRowDimension(2)->setRowHeight(25);

$rowIndex = 3;

// Estilos
$borderThin = ['allBorders' => ['borderStyle' => Border::BORDER_THIN]];
$centered = ['horizontal' => Alignment::HORIZONTAL_CENTER];

$coloresMetodo = [
    'Efectivo' => 'D4EFDF',
    'Tarjeta Crédito' => 'FADBD8',
    'Tarjeta Débito' => 'FCF3CF',
    'Transferencia' => 'D6EAF8',
    'Cortesía' => 'E8DAEF'
];


$orden_metodos = ['Efectivo', 'Tarjeta Crédito', 'Tarjeta Débito', 'Transferencia', 'Cortesía'];

foreach ($orden_metodos as $metodo) {
    if (isset($ingresos[$metodo])) {
        $grupo = $ingresos[$metodo];
        $color = $coloresMetodo[$metodo] ?? 'F2F3F4'; // Gris por defecto si no se encuentra

        // Encabezado del método
        $sheet->setCellValue("A$rowIndex", "Método de Pago: $metodo");
        $sheet->mergeCells("A$rowIndex:H$rowIndex");
        $sheet->getStyle("A$rowIndex:H$rowIndex")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
            'alignment' => $centered,
        ]);
        $rowIndex++;

        // Encabezados de columna
        $headers = ['Folio Cobro', 'Fecha Cobro', 'Folio Cita', 'Fecha Cita', 'Hora', 'Paciente', 'Colaborador', 'Importe'];
        $sheet->fromArray($headers, null, "A$rowIndex");
        $sheet->getStyle("A$rowIndex:H$rowIndex")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
            'alignment' => $centered,
            'borders' => $borderThin,
        ]);
        $rowIndex++;

        foreach ($grupo['registros'] as $reg) {
            $sheet->fromArray([
                $reg['id_pago'],
                $reg['fecha_pago'],
                $reg['id_cita'],
                $reg['fecha_cita'],
                $reg['hora_cita'],
                $reg['paciente'],
                $reg['colaborador'],
                $reg['importe']
            ], null, "A$rowIndex");

            $sheet->getStyle("A$rowIndex:H$rowIndex")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("H$rowIndex")
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $rowIndex++;
        }

        // Subtotal
        $sheet->setCellValue("G$rowIndex", "Subtotal $metodo:");
        $sheet->setCellValue("H$rowIndex", $grupo['subtotal']);
        $sheet->getStyle("G$rowIndex:H$rowIndex")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'borders' => $borderThin,
        ]);
        $sheet->getStyle("H$rowIndex")
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        $rowIndex += 2;
    }
}


// Total general
$sheet->setCellValue("G$rowIndex", "TOTAL GENERAL:");
$sheet->setCellValue("H$rowIndex", $total_general);
$sheet->getStyle("G$rowIndex:H$rowIndex")->applyFromArray([
    'font' => ['bold' => true],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'AED6F1']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
    'borders' => $borderThin,
]);
$sheet->getStyle("H$rowIndex")
    ->getNumberFormat()
    ->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

// Autoajustar columnas
foreach (range('A', 'H') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Descargar
$filename = "Ingresos_{$fecha_inicio}_a_{$fecha_fin}.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
