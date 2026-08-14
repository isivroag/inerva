<?php
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$fecha  = $_POST['fecha']  ?? date('Y-m-d');
$campo  = $_POST['campo']  ?? '';

// Mapeo campo → valor del metodo en vpago
$mapa = [
    'efectivo'       => 'Efectivo',
    'tcredito'       => 'Tarjeta Crédito',
    'tdebito'        => 'Tarjeta Débito',
    'transferencias' => 'Transferencia',
    'cortesia'       => 'Cortesía',
    'gastos'         => 'Gastos',
    'retiros'        => 'Retiros',
];

try {
    if ($campo === '') {
        // Sin filtro: todos los pagos del día
        $stmt = $conexion->prepare(
            "SELECT id_pago, fecha_pago, paciente, colaborador, importe, metodo
             FROM vpago WHERE DATE(fecha_pago)=:fecha AND edo_pago=1
             ORDER BY metodo, id_pago"
        );
        $stmt->execute([':fecha' => $fecha]);
    } elseif ($campo === 'gastos') {
        $stmt = $conexion->prepare(
            "SELECT id_gasto,
                    DATE_FORMAT(fecha, '%Y-%m-%d') AS fecha,
                    COALESCE(referencia, '') AS referencia,
                    COALESCE(concepto, '') AS concepto,
                    COALESCE(facturado, 0) AS facturado,
                    COALESCE(monto, 0) AS monto
             FROM gasto
             WHERE DATE(fecha)=:fecha AND COALESCE(estado_gasto,0)=1
             ORDER BY id_gasto DESC"
        );
        $stmt->execute([':fecha' => $fecha]);

        $gastos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = array_sum(array_map(static function ($gasto) {
            return (float)$gasto['monto'];
        }, $gastos));

        echo json_encode(['status' => 'ok', 'tipo' => 'gastos', 'gastos' => $gastos, 'total' => $total]);
        $conexion = null;
        exit;
    } elseif ($campo === 'retiros') {
        $stmt = $conexion->prepare(
            "SELECT id_retiro,
                    DATE_FORMAT(fecha, '%Y-%m-%d') AS fecha,
                    DATE_FORMAT(fecha_op, '%Y-%m-%d %H:%i:%s') AS fecha_op,
                    COALESCE(montoant, 0) AS montoant,
                    COALESCE(montodesp, 0) AS montodesp,
                    COALESCE(monto, 0) AS monto,
                    COALESCE(estado_retiro, 0) AS estado_retiro,
                    COALESCE(motivo_can, '') AS motivo_can
             FROM retiro
             WHERE DATE(fecha)=:fecha AND COALESCE(estado_retiro,0)=1
             ORDER BY id_retiro DESC"
        );
        $stmt->execute([':fecha' => $fecha]);

        $retiros = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = array_sum(array_map(static function ($retiro) {
            return (float)$retiro['monto'];
        }, $retiros));

        echo json_encode(['status' => 'ok', 'tipo' => 'retiros', 'retiros' => $retiros, 'total' => $total]);
        $conexion = null;
        exit;
    } else {
        $metodo = $mapa[$campo] ?? $campo;
        $stmt = $conexion->prepare(
            "SELECT id_pago, fecha_pago, paciente, colaborador, importe, metodo
             FROM vpago WHERE DATE(fecha_pago)=:fecha AND metodo=:metodo AND edo_pago=1
             ORDER BY id_pago"
        );
        $stmt->execute([':fecha' => $fecha, ':metodo' => $metodo]);
    }

    $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total = array_sum(array_column($pagos, 'importe'));
    echo json_encode(['status' => 'ok', 'tipo' => 'pagos', 'pagos' => $pagos, 'total' => $total]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'mensaje' => $e->getMessage()]);
}
$conexion = null;
