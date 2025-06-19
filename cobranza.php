<?php
$pagina = "cobranza";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";

include_once "bd/conexion.php";
$objeto = new conn();
$conexion = $objeto->connect();
$fecha = date("Y-m-d");


if (isset ($_GET['folio_cob'])) {
    $folio_cob = $_GET['folio_cob'];
    $consulta = "SELECT * FROM cobranza WHERE folio_cob=:folio_cob";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':folio_cob', $folio_cob, PDO::PARAM_STR);
    $resultado->execute();
    $cobranza = $resultado->fetch(PDO::FETCH_ASSOC);
} else {
    $cobranza = null;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $consulta = "SELECT * FROM vcitap2 WHERE id=:id";
    $resultado = $conexion->prepare($consulta);
    $resultado->bindParam(':id', $id, PDO::PARAM_INT);
    $resultado->execute();
    $cita = $resultado->fetch(PDO::FETCH_ASSOC);
    $folio_cob="";

} else {
    $cita = null;
}


// Obtener servicios activos
$consulta = "SELECT id_serv, nom_serv, costo_serv FROM servicio WHERE edo_serv=1";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$servicios = $resultado->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<style>
    .selectinerva .hover {

        background-color: #7362a2 !important;
    }

    .selectinerva .dropdown-item:hover {
        background-color: #7362a2 !important;
        color: white !important;
    }

    select.selectinerva {
        width: 100% !important;
    }

    select.selectinerva .dropdown-toggle::after {
        display: none !important;
    }

    select.selectinerva .dropdown-toggle {
        background-color: #7362a2 !important;
        color: white !important;
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="card">
            <div class="card-header bg-green text-light">
                <h1 class="card-title mx-auto">COBRANZA</h1>
            </div>
            <div class="card-body">


                <div class="container-fluid">
                    <form id="formDatos" action="" method="POST">
                         <div class="row justify-content-start">
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-sm-1">
                                <div class="form-group input-group-sm">
                                    <label for="folio_cob" class="col-form-label">*FOLIO :</label>
                                    <input type="text" class="form-control" name="folio_cob" id="folio_cob" value="<?php echo $folio_cob ? $folio_cob : ''; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="form-group input-group-sm">
                                    <label for="id_cita" class="col-form-label">*ID CITA :</label>
                                    <input type="text" class="form-control" name="id_cita" id="id_cita" value="<?php echo $cita ? $cita['id'] : ''; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group input-group-sm">
                                    <label for="colaborador" class="col-form-label">Psicólogo :</label>
                                    <input type="hidden" name="id_col" id="id_col" value="<?php echo $cita ? $cita['id_col'] : ''; ?>">
                                    <input type="text" class="form-control" name="colaborador" id="colaborador" value="<?php echo $cita ? $cita['nombre'] : ''; ?>" readonly>
                                </div>
                            </div>

                            <div class="col-sm-1">
                                <div class="form-group input-group-sm">
                                    <label for="fecha" class="col-form-label">*FECHA :</label>
                                    <input type="date" class="form-control" name="fecha" id="fecha" value="<?php echo $fecha; ?>" required>
                                </div>
                            </div>



                        </div>

                        <div class=" row justify-content-center">
                            <div class="col-sm-7">
                                <div class="form-group input-group-sm">

                                    <input type="hidden" name="id_paciente" id="id_paciente" value="<?php echo $cita ? $cita['id_px'] : ''; ?>">
                                    <label for="paciente" class="col-form-label">*PACIENTE :</label>
                                    <input type="text" class="form-control" name="paciente" id="paciente" value="<?php echo $cita ? $cita['title'] : ''; ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-7">
                                <div class="form-group input-group-sm">
                                    <label for="id_serv" class="col-form-label form-control-sm">SERVICIO:</label>
                                    <select class="form-control form-control-sm selectpicker selectinerva" name="id_serv" id="id_serv" data-live-search="true" title="SELECCIONA SERVICIO" required>
                                        <?php foreach ($servicios as $servicio): ?>
                                            <option value="<?php echo $servicio['id_serv'] ?>" data-costo="<?php echo $servicio['costo_serv'] ?>"><?php echo $servicio['nom_serv'] ?> - $<?php echo number_format($servicio['costo_serv'], 2) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-sm-2">
                                <div class="form-group  input-group-sm">
                                    <label for="costo" class="col-form-label">*IMPORTE :</label>
                                    <input type="number" step="0.01" class="form-control" name="costo" id="costo" value="<?php echo $cita ? $cita['costo'] : ''; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="form-group input-group-sm">
                                    <label for="descuento" class="col-form-label">DESCUENTO :</label>
                                    <input type="number" step="0.01" class="form-control" name="descuento" id="descuento" value="<?php echo $cita ? $cita['descuento'] : ''; ?>" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group input-group-sm">
                                    <label for="total" class="col-form-label">TOTAL :</label>
                                    <input type="number" step="0.01" class="form-control" name="total" id="total" value="<?php echo $cita ? $cita['total'] : ''; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group input-group-sm">
                                    <label for="metodo_pago" class="col-form-label">*MÉTODO PAGO :</label>
                                    <select class="form-control form-control-sm selectpicker selectinerva" name="metodo_pago" id="metodo_pago" data-live-search="true" title="SELECCIONA MÉTODO PAGO" required>
                                        <option value="Efectivo">Efectivo</option>
                                        <option value="Tarjeta Crédito">Tarjeta Crédito</option>
                                        <option value="Tarjeta Débito">Tarjeta Débito</option>
                                        <option value="Transferencia">Transferencia</option>
                                        <option value="Cortesía">Cortesía</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                       

                    </form>

                </div>
            </div>
        </div>
    </section>


</div>

<?php include_once 'templates/footer.php'; ?>
<script src="fjs/cobranza.js?v=<?php echo (rand()); ?>"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/sweetalert2/sweetalert2.all.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
<script src="http://cdn.datatables.net/plug-ins/1.10.21/sorting/formatted-numbers.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>