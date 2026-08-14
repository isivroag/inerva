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
$rol_usuario = (int)($_SESSION['s_rol'] ?? 0);
$es_admin_o_root = in_array($rol_usuario, [2, 3], true);

try {
    if ($accion === 'listar') {
        if (!$es_admin_o_root) {
            $fecha = $fecha_hoy;
        }

        $stmt = $conexion->prepare(
            "SELECT id_gasto,
                    DATE_FORMAT(fecha, '%Y-%m-%d') AS fecha,
                    COALESCE(facturado, 0) AS facturado,
                    COALESCE(referencia, '') AS referencia,
                    COALESCE(concepto, '') AS concepto,
                    COALESCE(monto, 0) AS monto,
                    COALESCE(estado_gasto, 0) AS estado_gasto,
                    COALESCE(usuario_can, '') AS usuario_can,
                    fecha_can,
                    COALESCE(motivo_can, '') AS motivo_can
             FROM gasto
             WHERE DATE(fecha) = :fecha
             ORDER BY id_gasto DESC"
        );
        $stmt->execute([':fecha' => $fecha]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['status' => 'ok', 'data' => $data]);
    } elseif ($accion === 'crear') {
        $referencia = trim((string)($_POST['referencia'] ?? ''));
        $concepto = trim((string)($_POST['concepto'] ?? ''));
        $monto = (float)($_POST['monto'] ?? 0);
        $facturado = (int)($_POST['facturado'] ?? 0);
        $facturado = ($facturado === 1) ? 1 : 0;

        if (!$es_admin_o_root) {
            $fecha = $fecha_hoy;
        }

        if ($referencia === '' || $concepto === '' || $monto <= 0) {
            echo json_encode(['status' => 'error', 'mensaje' => 'Debe capturar todos los datos obligatorios']);
            exit;
        }

        $stmt = $conexion->prepare(
            "INSERT INTO gasto (fecha, facturado, referencia, concepto, monto, estado_gasto)
               VALUES (:fecha, :facturado, :referencia, :concepto, :monto, 1)"
        );
        $stmt->execute([
            ':fecha' => $fecha,
            ':facturado' => $facturado,
            ':referencia' => $referencia,
            ':concepto' => $concepto,
            ':monto' => $monto,
        ]);

        echo json_encode(['status' => 'ok', 'mensaje' => 'Gasto registrado correctamente']);
    } elseif ($accion === 'cancelar') {
        if (!$es_admin_o_root) {
            echo json_encode(['status' => 'error', 'mensaje' => 'No tiene permisos para cancelar gastos']);
            exit;
        }

        $id_gasto = (int)($_POST['id_gasto'] ?? 0);
        $motivo = trim((string)($_POST['motivo'] ?? ''));

        if ($id_gasto <= 0 || $motivo === '') {
            echo json_encode(['status' => 'error', 'mensaje' => 'Debe capturar el motivo de cancelacion']);
            exit;
        }

        $usuario_can = (string)($_SESSION['s_id_usuario'] ?? ($_SESSION['s_usuario'] ?? '0'));
        $fecha_can = date('Y-m-d H:i:s');

        $stmt = $conexion->prepare(
            "UPDATE gasto
             SET usuario_can = :usuario_can,
                 fecha_can = :fecha_can,
                 motivo_can = :motivo_can,
                 estado_gasto = 0
             WHERE id_gasto = :id_gasto
               AND COALESCE(estado_gasto, 0) = 1"
        );
        $stmt->execute([
            ':usuario_can' => $usuario_can,
            ':fecha_can' => $fecha_can,
            ':motivo_can' => $motivo,
            ':id_gasto' => $id_gasto,
        ]);

        if ($stmt->rowCount() <= 0) {
            echo json_encode(['status' => 'error', 'mensaje' => 'No se pudo cancelar el gasto (puede estar cancelado o no existir)']);
            exit;
        }

        echo json_encode(['status' => 'ok', 'mensaje' => 'Gasto cancelado correctamente']);
    } else {
        echo json_encode(['status' => 'error', 'mensaje' => 'Accion no valida']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'mensaje' => $e->getMessage()]);
}

$conexion = null;
