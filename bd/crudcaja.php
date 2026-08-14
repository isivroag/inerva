<?php
include_once 'conexion.php';
session_start();
$objeto = new conn();
$conexion = $objeto->connect();

$accion = $_POST['accion'] ?? '';
date_default_timezone_set('America/Mexico_City');
$fecha = $_POST['fecha'] ?? date('Y-m-d');
$inicial = $_POST['inicial'] ?? 0;
$s_rol = $_SESSION['s_rol'] ?? 0;
$fecha_hoy = date('Y-m-d');
$es_fecha_actual = ($fecha === $fecha_hoy);

try {
    if ($accion === 'abrir_caja') {
        $stmt = $conexion->prepare("SELECT id_caja FROM caja WHERE fecha=:fecha");
        $stmt->bindParam(':fecha', $fecha);
        $stmt->execute();
        $cajaExistente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cajaExistente) {
            echo json_encode(['status' => 'error', 'mensaje' => 'Ya existe una caja abierta para esta fecha']);
        } else {
            $stmt = $conexion->prepare("INSERT INTO caja (fecha, inicial, efectivo, tcredito, tdebito, transferencias, cortesia,  gastos, retiros, final)
                                       VALUES (:fecha, :inicial, 0, 0, 0, 0, 0, 0, 0, :inicial)");
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':inicial', $inicial);
            $stmt->execute();

            echo json_encode(['status' => 'ok', 'mensaje' => 'Caja abierta correctamente']);
        }
    } elseif ($accion === 'modificar_inicial') {
        // Solo admin (2) o root (3) pueden modificar
        if (!in_array($s_rol, [2, 3])) {
            echo json_encode(['status' => 'error', 'mensaje' => 'No tiene permisos para modificar el inicial de caja']);
        } else {
            $stmt = $conexion->prepare("UPDATE caja SET inicial=:inicial WHERE fecha=:fecha");
            $stmt->bindParam(':inicial', $inicial);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->execute();

            echo json_encode(['status' => 'ok', 'mensaje' => 'Inicial de caja modificado correctamente']);
        }
    } elseif ($accion === 'sincronizar_ingresos') {
        if (!$es_fecha_actual && !in_array($s_rol, [2, 3])) {
            echo json_encode(['status' => 'error', 'mensaje' => 'Solo el administrador o el usuario root pueden actualizar ingresos de una fecha distinta a hoy']);
        } else {
            // Trae los totales reales de ingresos y gastos y los guarda en caja aun cuando no existan registros para esa fecha
            $stmtV = $conexion->prepare(
                "SELECT COALESCE(MAX(efectivo),0) AS efectivo,
                        COALESCE(MAX(tcredito),0) AS tcredito,
                        COALESCE(MAX(tdebito),0) AS tdebito,
                        COALESCE(MAX(transferencias),0) AS transferencias,
                        COALESCE(MAX(cortesia),0) AS cortesia
                 FROM vcaja_ingresos
                 WHERE fecha=:fecha"
            );
            $stmtV->execute([':fecha' => $fecha]);
            $v = $stmtV->fetch(PDO::FETCH_ASSOC) ?: [
                'efectivo' => 0,
                'tcredito' => 0,
                'tdebito' => 0,
                'transferencias' => 0,
                'cortesia' => 0,
            ];

            $stmtG = $conexion->prepare(
                "SELECT COALESCE(SUM(monto),0) AS gastos
                 FROM gasto
                 WHERE DATE(fecha)=:fecha
                     AND COALESCE(estado_gasto,0)=1"
            );
            $stmtG->execute([':fecha' => $fecha]);
            $g = $stmtG->fetch(PDO::FETCH_ASSOC) ?: ['gastos' => 0];

            $stmtR = $conexion->prepare(
                "SELECT COALESCE(SUM(monto),0) AS retiros
                 FROM retiro
                 WHERE DATE(fecha)=:fecha
                   AND COALESCE(estado_retiro,0)=1"
            );
            $stmtR->execute([':fecha' => $fecha]);
            $r = $stmtR->fetch(PDO::FETCH_ASSOC) ?: ['retiros' => 0];

            $stmtCaja = $conexion->prepare("SELECT id_caja, inicial, gastos, retiros FROM caja WHERE fecha=:fecha LIMIT 1");
            $stmtCaja->execute([':fecha' => $fecha]);
            $caja = $stmtCaja->fetch(PDO::FETCH_ASSOC);

            if ($caja) {
                $stmt = $conexion->prepare(
                    "UPDATE caja SET efectivo=:ef, tcredito=:tc, tdebito=:td,
                                     transferencias=:tr, cortesia=:co, gastos=:ga, retiros=:re
                     WHERE fecha=:fecha"
                );
                $stmt->execute([
                    ':ef'    => $v['efectivo'],
                    ':tc'    => $v['tcredito'],
                    ':td'    => $v['tdebito'],
                    ':tr'    => $v['transferencias'],
                    ':co'    => $v['cortesia'],
                    ':ga'    => $g['gastos'],
                    ':re'    => $r['retiros'],
                    ':fecha' => $fecha,
                ]);
            } else {
                $inicial_caja = 0;
                $gastos_caja = (float)$g['gastos'];
                $retiros_caja = (float)$r['retiros'];
                $final_caja = (float)$v['efectivo'] + (float)$v['tcredito'] + (float)$v['tdebito'] + (float)$v['transferencias'];

                $stmt = $conexion->prepare(
                    "INSERT INTO caja (fecha, inicial, efectivo, tcredito, tdebito, transferencias, cortesia, gastos, retiros, final)
                     VALUES (:fecha, :inicial, :ef, :tc, :td, :tr, :co, :gastos, :retiros, :final)"
                );
                $stmt->execute([
                    ':fecha' => $fecha,
                    ':inicial' => $inicial_caja,
                    ':ef' => $v['efectivo'],
                    ':tc' => $v['tcredito'],
                    ':td' => $v['tdebito'],
                    ':tr' => $v['transferencias'],
                    ':co' => $v['cortesia'],
                    ':gastos' => $gastos_caja,
                    ':retiros' => $retiros_caja,
                    ':final' => $final_caja,
                ]);
            }

            echo json_encode(['status' => 'ok', 'mensaje' => 'Valores de caja actualizados correctamente']);
        }
    }
} catch(Exception $e) {
    echo json_encode(['status' => 'error', 'mensaje' => $e->getMessage()]);
}
$conexion = null;
