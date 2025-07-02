<?php
// filepath: c:\xampp\htdocs\inerva\bd\getpagos.php
include_once 'conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

$folio_cxc = $_POST['folio_cxc'] ?? '';

$stmt = $conexion->prepare("SELECT id_pago,fecha_pago, importe, metodo, saldoini,saldofin FROM pago WHERE folio_cxc=:folio and edo_pago=1 ORDER BY id_pago,fecha_pago");
$stmt->bindParam(':folio', $folio_cxc);
$stmt->execute();
$pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
$botones="<div class='text-center'><button class='btn btn-sm btn-primary btnImprimir' data-toggle='tooltip' data-placement='top' title='Imprimir'><i class='fas fa-print'></i></button>
              <button class='btn btn-sm btn-danger btnCancelarPago' data-toggle='tooltip' data-placement='top' title='Eliminar'><i class='fas fa-trash-alt'></i></button></div>";

if ($pagos) {
    echo '<table id="tablaPagos" class="table table-bordered table-sm table-striped mx-auto tabla-condensed tablaredonda" style="width:100% !important; font-size:14px">
        <thead class="bg-green text-white">
            <tr>
                <th>Folio Pago</th>
                <th>Fecha</th>
                <th>Método</th>
                <th>Saldo Inicial</th>
                <th>Importe</th>
                <th>Saldo Final</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>';
    foreach ($pagos as $p) {
        echo '<tr>';
        echo '<td class="text-center">' . htmlspecialchars($p['id_pago']) . '</td>';
        echo '<td class="text-center">' . htmlspecialchars($p['fecha_pago']) . '</td>';
        echo '<td class="text-center">' . htmlspecialchars($p['metodo']) . '</td>';
        echo '<td class="text-center">$' . number_format($p['saldoini'], 2) . '</td>';
        echo '<td class="text-center">$' . number_format($p['importe'], 2) . '</td>';
        echo '<td class="text-center">$' . number_format($p['saldofin'], 2) . '</td>';
        echo '<td class="text-center">' . $botones . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
} else {
    echo '<div class="alert alert-info">No hay pagos registrados para esta cuenta.</div>';
}
$conexion = null;
