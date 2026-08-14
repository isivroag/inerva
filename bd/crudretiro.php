<?php
include_once 'conexion.php';
session_start();

header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('America/Mexico_City');

$objeto = new conn();
$conexion = $objeto->connect();

$accion = $_POST['accion'] ?? '';
$fecha_hoy = date('Y-m-d');
$fecha = $_POST['fecha'] ?? $fecha_hoy;
$monto = (float)($_POST['monto'] ?? 0);
$rol_usuario = (int)($_SESSION['s_rol'] ?? 0);
$es_admin_o_root = in_array($rol_usuario, [2, 3], true);

function obtenerEstadoCaja(PDO $conexion, string $fecha): array {
    $stmtCaja = $conexion->prepare(
        "SELECT inicial, efectivo, tcredito, tdebito, transferencias, cortesia, gastos, retiros
         FROM caja
         WHERE fecha=:fecha
         LIMIT 1"
    );
    $stmtCaja->execute([':fecha' => $fecha]);
    $caja = $stmtCaja->fetch(PDO::FETCH_ASSOC);

    if (!$caja) {
        return [
            'existe' => false,
            'hay_diferencias' => false,
            'efectivo_disponible' => 0.0,
            'mensaje' => 'No existe caja abierta para esta fecha',
        ];
    }

    $stmtVista = $conexion->prepare(
        "SELECT COALESCE(MAX(efectivo),0) AS efectivo,
                COALESCE(MAX(tcredito),0) AS tcredito,
                COALESCE(MAX(tdebito),0) AS tdebito,
                COALESCE(MAX(transferencias),0) AS transferencias,
                COALESCE(MAX(cortesia),0) AS cortesia
         FROM vcaja_ingresos
         WHERE fecha=:fecha"
    );
    $stmtVista->execute([':fecha' => $fecha]);
    $vista = $stmtVista->fetch(PDO::FETCH_ASSOC) ?: [
        'efectivo' => 0,
        'tcredito' => 0,
        'tdebito' => 0,
        'transferencias' => 0,
        'cortesia' => 0,
    ];

    $stmtGastos = $conexion->prepare(
        "SELECT COALESCE(SUM(monto),0) AS total
         FROM gasto
         WHERE DATE(fecha)=:fecha AND COALESCE(estado_gasto,0)=1"
    );
    $stmtGastos->execute([':fecha' => $fecha]);
    $gastos = $stmtGastos->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0];

    $stmtRetiros = $conexion->prepare(
        "SELECT COALESCE(SUM(monto),0) AS total
         FROM retiro
         WHERE DATE(fecha)=:fecha AND COALESCE(estado_retiro,0)=1"
    );
    $stmtRetiros->execute([':fecha' => $fecha]);
    $retiros = $stmtRetiros->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0];

    $diff_efectivo = abs((float)$caja['efectivo'] - (float)$vista['efectivo']) > 0.001;
    $diff_tcredito = abs((float)$caja['tcredito'] - (float)$vista['tcredito']) > 0.001;
    $diff_tdebito = abs((float)$caja['tdebito'] - (float)$vista['tdebito']) > 0.001;
    $diff_transferencias = abs((float)$caja['transferencias'] - (float)$vista['transferencias']) > 0.001;
    $diff_cortesia = abs((float)$caja['cortesia'] - (float)$vista['cortesia']) > 0.001;
    $diff_gastos = abs((float)$caja['gastos'] - (float)$gastos['total']) > 0.001;
    $diff_retiros = abs((float)$caja['retiros'] - (float)$retiros['total']) > 0.001;

    $hay_diferencias = $diff_efectivo || $diff_tcredito || $diff_tdebito || $diff_transferencias || $diff_cortesia || $diff_gastos || $diff_retiros;

    $efectivo_disponible = ((float)$caja['inicial'] + (float)$caja['efectivo']) - ((float)$caja['gastos'] + (float)$caja['retiros']);

    return [
        'existe' => true,
        'hay_diferencias' => $hay_diferencias,
        'efectivo_disponible' => $efectivo_disponible,
        'mensaje' => $hay_diferencias
            ? 'La caja tiene diferencias con los ingresos y/o gastos. Primero actualiza la caja para continuar.'
            : 'Caja conciliada.',
        'caja' => $caja,
        'vista' => $vista,
    ];
}

function recalcularRetirosCaja(PDO $conexion, string $fecha): float {
    $stmt = $conexion->prepare(
        "SELECT COALESCE(SUM(monto),0) AS total
         FROM retiro
         WHERE DATE(fecha)=:fecha AND COALESCE(estado_retiro,0)=1"
    );
    $stmt->execute([':fecha' => $fecha]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0];
    $total = (float)$row['total'];

    $stmtCaja = $conexion->prepare("UPDATE caja SET retiros=:retiros WHERE fecha=:fecha");
    $stmtCaja->execute([':retiros' => $total, ':fecha' => $fecha]);

    return $total;
}

try {
    if ($accion === 'listar') {
        if (!$es_admin_o_root) {
            $fecha = $fecha_hoy;
        }

        $stmt = $conexion->prepare(
            "SELECT id_retiro,
                    DATE_FORMAT(fecha, '%Y-%m-%d') AS fecha,
                    DATE_FORMAT(fechaop, '%Y-%m-%d %H:%i:%s') AS fecha_op,
                    COALESCE(montoant, 0) AS montoant,
                    COALESCE(montodesp, 0) AS montodesp,
                    COALESCE(monto, 0) AS monto,
                    COALESCE(estado_retiro, 0) AS estado_retiro,
                    COALESCE(motivo_can, '') AS motivo_can,
                    COALESCE(usuario_can, '') AS usuario_can,
                    fecha_can
             FROM retiro
             WHERE DATE(fecha)=:fecha
             ORDER BY id_retiro DESC"
        );
        $stmt->execute([':fecha' => $fecha]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'ok', 'data' => $data]);
    } elseif ($accion === 'estado_caja') {
        if (!$es_admin_o_root) {
            $fecha = $fecha_hoy;
        }

        $estadoCaja = obtenerEstadoCaja($conexion, $fecha);
        if (!$estadoCaja['existe']) {
            echo json_encode(['status' => 'error', 'mensaje' => $estadoCaja['mensaje']]);
            exit;
        }

        echo json_encode([
            'status' => 'ok',
            'efectivo_disponible' => (float)$estadoCaja['efectivo_disponible'],
            'hay_diferencias' => $estadoCaja['hay_diferencias'],
            'mensaje' => $estadoCaja['mensaje'],
        ]);
    } elseif ($accion === 'crear') {
        if (!$es_admin_o_root) {
            $fecha = $fecha_hoy;
        }

        if ($monto <= 0) {
            echo json_encode(['status' => 'error', 'mensaje' => 'Debe capturar un monto mayor a cero']);
            exit;
        }

        $estadoCaja = obtenerEstadoCaja($conexion, $fecha);
        if (!$estadoCaja['existe']) {
            echo json_encode(['status' => 'error', 'mensaje' => $estadoCaja['mensaje']]);
            exit;
        }

        if ($estadoCaja['hay_diferencias']) {
            echo json_encode([
                'status' => 'error',
                'mensaje' => 'La caja tiene diferencias con los ingresos y/o gastos. Primero actualiza la caja para continuar.',
            ]);
            exit;
        }

        $caja = $estadoCaja['caja'];
        $montoant = (float)$caja['inicial'] + (float)$caja['efectivo'] - (float)$caja['gastos'] - (float)$caja['retiros'];
        $montodesp = $montoant - $monto;

        if ($monto > $montoant) {
            echo json_encode(['status' => 'error', 'mensaje' => 'El monto del retiro no puede ser mayor al efectivo disponible']);
            exit;
        }

        $fecha_op = date('Y-m-d H:i:s');
        $stmt = $conexion->prepare(
            "INSERT INTO retiro (fecha, monto, estado_retiro, fechaop, montoant, montodesp, motivo_can, usuario_can, fecha_can)
             VALUES (:fecha, :monto, 1, :fechaop, :montoant, :montodesp, '', '', NULL)"
        );
        $stmt->execute([
            ':fecha' => $fecha,
            ':monto' => $monto,
            ':fechaop' => $fecha_op,
            ':montoant' => $montoant,
            ':montodesp' => $montodesp,
        ]);

        recalcularRetirosCaja($conexion, $fecha);
        echo json_encode(['status' => 'ok', 'mensaje' => 'Retiro registrado correctamente']);
    } elseif ($accion === 'cancelar') {
        if (!$es_admin_o_root) {
            echo json_encode(['status' => 'error', 'mensaje' => 'No tiene permisos para cancelar retiros']);
            exit;
        }

        $id_retiro = (int)($_POST['id_retiro'] ?? 0);
        $motivo = trim((string)($_POST['motivo'] ?? ''));

        if ($id_retiro <= 0 || $motivo === '') {
            echo json_encode(['status' => 'error', 'mensaje' => 'Debe capturar el motivo de cancelación']);
            exit;
        }

        $stmt = $conexion->prepare(
            "SELECT fecha, estado_retiro FROM retiro WHERE id_retiro=:id_retiro LIMIT 1"
        );
        $stmt->execute([':id_retiro' => $id_retiro]);
        $retiro = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$retiro) {
            echo json_encode(['status' => 'error', 'mensaje' => 'El retiro no existe']);
            exit;
        }
        if ((int)$retiro['estado_retiro'] !== 1) {
            echo json_encode(['status' => 'error', 'mensaje' => 'El retiro ya está cancelado']);
            exit;
        }

        $usuario_can = (string)($_SESSION['s_id_usuario'] ?? ($_SESSION['s_usuario'] ?? '0'));
        $fecha_can = date('Y-m-d H:i:s');

        $stmt = $conexion->prepare(
            "UPDATE retiro
             SET estado_retiro = 0,
                 usuario_can = :usuario_can,
                 fecha_can = :fecha_can,
                 motivo_can = :motivo_can
             WHERE id_retiro = :id_retiro"
        );
        $stmt->execute([
            ':usuario_can' => $usuario_can,
            ':fecha_can' => $fecha_can,
            ':motivo_can' => $motivo,
            ':id_retiro' => $id_retiro,
        ]);

        recalcularRetirosCaja($conexion, (string)$retiro['fecha']);
        echo json_encode(['status' => 'ok', 'mensaje' => 'Retiro cancelado correctamente']);
    } else {
        echo json_encode(['status' => 'error', 'mensaje' => 'Acción no válida']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'mensaje' => $e->getMessage()]);
}

$conexion = null;
