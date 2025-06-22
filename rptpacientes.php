<?php
$pagina = "rptpacientes";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";

include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();;

// Consulta para obtener prospectos activos (edo_pros = 1)
$consulta = "SELECT * from vtotalpxmes WHERE ejercicio = YEAR(CURDATE()) ";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);

$consultaorg = "SELECT * from vtotalpxmesorg WHERE ejercicio = YEAR(CURDATE()) AND mes_id = MONTH(CURDATE())";
$resultadoorg = $conexion->prepare($consultaorg);
$resultadoorg->execute();
$dataorg = $resultadoorg->fetchAll(PDO::FETCH_ASSOC);

$origenes = [];
$meses = [];

foreach ($dataorg as $row) {
    $origen = $row['nom_medio'];
    $mes = $row['mes_nombre'];
    $meses[$mes] = true; // para obtener todos los meses
    $origenes[$origen][$mes] = $row['npacientes'];
}


$labels = [];
$values = [];

foreach ($dataorg as $row) {
    $label = $row['mes_nombre'] . " - " . $row['nom_medio'];
    $labels[] = $label;
    $values[] = $row['npacientes'];
}
$meses = array_keys($meses);
sort($meses); // ordenar meses si es necesario

$message = "";
?>

<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<style>
    .starchecked {
        color: rgba(255, 195, 0, 100)
    }

    .multi-line {
        white-space: normal;
        width: 250px;
    }

    .badge-asignado {
        background-color: #28a745;
    }

    .badge-seguimiento {
        background-color: #17a2b8;
    }

    .badge-finalizado {
        background-color: #6c757d;
    }

    .badge-pendiente {
        background-color: #6c757d;
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="card">
            <div class="card-header  bg-green text-light">
                <h1 class="card-title mx-auto">ESTADISTICAS DE PROSPECTOS</h1>
            </div>

            <div class="card-body">

                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <!-- GRAFICA DE ML VENDIDOS-->
                        <div class="col-sm-12">
                            <div class="card ">
                                <div class="card-header bg-green color-palette border-0">
                                    <h3 class="card-title">
                                        <i class="fas fa-th mr-1"></i>
                                        Pospectos nuevos por mes Ejercicio <?php echo date('Y') ?>
                                    </h3>

                                    <div class="card-tools">
                                        <button type="button" class="btn bg-green btn-sm" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>

                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row justify-content-center">
                                        <div class="col-sm-10">
                                            <canvas class="chart " id="line-chart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row justify-content-center">
                                        <div class="col-sm-12 justify-content-center ">
                                            <div class="table-responsive d-flex justify-content-center">
                                                <table class="table table-responsive table-bordered table-hover table-sm w-auto">
                                                    <thead class="text-center bg-green">
                                                        <tr>
                                                            <th>Mes</th>
                                                            <?php foreach ($data as $rowml) : ?>
                                                                <th><?php echo $rowml['mes_nombre']; ?></th>
                                                            <?php endforeach; ?>
                                                            <th>Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td># Pacientes</td>
                                                            <?php
                                                            $totalml = 0;
                                                            foreach ($data as $rowml) {
                                                                $totalml += $rowml['npacientes'];
                                                            ?>
                                                                <td class="text-right"><?php echo $rowml['npacientes']; ?></td>
                                                            <?php } ?>
                                                            <td class="text-right text-bold"><?php echo $totalml; ?></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->

                                <!-- /.card-footer -->
                            </div>
                        </div>
                        <!-- GRAFICA DE VENTAS-->
                        <div class="col-sm-6">
                            <div class="card ">
                                <div class="card-header bg-green color-palette border-0">
                                    <h3 class="card-title">
                                        <i class="fas fa-th mr-1"></i>
                                        Prospectos por origen Por Mes Ejercicio <?php echo date('Y') ?>
                                    </h3>

                                    <div class="card-tools">
                                        <button type="button" class="btn bg-green btn-sm" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>

                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row justify-content">
                                        <div class="col-sm-7">
                                            <canvas class="chart " id="line-chart2" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                        </div>
                                        <div class="col-sm-5 my-auto">
                                            <div class="table-responsive">
                                                <table class="table table-responsive table-bordered table-hover table-sm">
                                                    <thead class="text-center">
                                                        <tr>
                                                            <th>Origen</th>
                                                            <th>Pacientes</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $totalvtasml = 0;
                                                        foreach ($dataorg as $rowml) {
                                                            $totalvtasml += $rowml['npacientes'];
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $rowml['nom_medio'] ?></td>
                                                                <td class="text-right"><?php echo $rowml['npacientes'] ?></td>
                                                            </tr>
                                                        <?php } ?>

                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td>Total Pacientes <?php echo $mes ?></td>
                                                            <td class="text-right text-bold"><?php echo $totalvtasml ?></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->

                                <!-- /.card-footer -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>
    $(function() {
         var barChartCanvas = $('#line-chart').get(0).getContext('2d');

        // Paleta de colores (12 colores)
        var coloresMeses = [
            'rgba(255, 99, 132, 0.6)',   // Enero
            'rgba(54, 162, 235, 0.6)',   // Febrero
            'rgba(255, 206, 86, 0.6)',   // Marzo
            'rgba(75, 192, 192, 0.6)',   // Abril
            'rgba(153, 102, 255, 0.6)',  // Mayo
            'rgba(255, 159, 64, 0.6)',   // Junio
            'rgba(199, 199, 199, 0.6)',  // Julio
            'rgba(255, 205, 86, 0.6)',   // Agosto
            'rgba(54, 162, 100, 0.6)',   // Septiembre
            'rgba(201, 203, 207, 0.6)',  // Octubre
            'rgba(100, 149, 237, 0.6)',  // Noviembre
            'rgba(255, 87, 34, 0.6)'     // Diciembre
        ];

        var barChartData = {
            labels: [
                <?php foreach ($data as $d) : ?> "<?php echo $d['mes_nombre'] ?>",
                <?php endforeach; ?>
            ],
            datasets: [{
                label: 'Prospectos Nuevos por Mes',
                data: [
                    <?php foreach ($data as $d) : ?>
                        <?php echo $d['npacientes']; ?>,
                    <?php endforeach; ?>
                ],
                backgroundColor: coloresMeses,
                borderColor: coloresMeses.map(color => color.replace('0.6', '1')),
                borderWidth: 1
            }]
        };

        var barChartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0,
                        callback: function(value) {
                            return Number.isInteger(value) ? value : '';
                        }
                    }
                }
            }
        };

        new Chart(barChartCanvas, {
            type: 'bar',
            data: barChartData,
            options: barChartOptions
        });



        var ctx2 = $('#line-chart2').get(0).getContext('2d');

        var labels = <?php echo json_encode($labels); ?>;
        var data = <?php echo json_encode($values); ?>;
         var colores = [
            'rgba(255, 99, 132, 0.6)',
            'rgba(54, 162, 235, 0.6)',
            'rgba(255, 206, 86, 0.6)',
            'rgba(75, 192, 192, 0.6)',
            'rgba(153, 102, 255, 0.6)',
            'rgba(255, 159, 64, 0.6)',
            'rgba(199, 199, 199, 0.6)',
            'rgba(255, 205, 86, 0.6)',
            'rgba(54, 162, 100, 0.6)',
            'rgba(201, 203, 207, 0.6)',
            'rgba(100, 149, 237, 0.6)',
            'rgba(255, 87, 34, 0.6)'
        ];

        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pacientes por Mes y Origen',
                    data: data,
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        ticks: {
                            autoSkip: false,
                            maxRotation: 90,
                            minRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        }
                    }
                }
            }
        });
    });
   
</script>

<?php include_once 'templates/footer.php'; ?>
<script src="fjs/rptprospectos.js?v=<?php echo (rand()); ?>"></script>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-es_ES.min.js"></script>