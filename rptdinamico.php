<?php
$pagina = "rptcitas";

include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";

include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();

// Obtener lista de colaboradores para el select
$consulta_colaboradores = "SELECT id_col, nombre FROM colaborador ORDER BY nombre";
$resultado_colaboradores = $conexion->prepare($consulta_colaboradores);
$resultado_colaboradores->execute();
$colaboradores = $resultado_colaboradores->fetchAll(PDO::FETCH_ASSOC);

// Definir todos los estados de cita
$estados_cita = [
    ['id' => 'todos', 'nombre' => 'Todos los estados'],
    ['id' => 0, 'nombre' => 'Pendiente'],
    ['id' => 1, 'nombre' => 'Confirmada'],
    ['id' => 2, 'nombre' => 'No Confirmada'],
    ['id' => 4, 'nombre' => 'Cancelada'],
    ['id' => 5, 'nombre' => 'Asistió'],
    ['id' => 6, 'nombre' => 'No Asistió'],
    ['id' => 10, 'nombre' => 'Pagada']
];

// Valores por defecto para los filtros
$fecha_inicio = date('Y-m-01'); // Primer día del mes actual
$fecha_fin = date('Y-m-d'); // Hoy
$estado_seleccionado = 'todos';
$colaborador_seleccionado = 'todos';
?>

<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">

<style>
    /* Estilos para las tarjetas de gráficas */
    .card-grafica {
        height: 100%;
        margin-bottom: 20px;
    }

    .card-grafica .card-body {
        padding: 10px !important;
        display: flex;
        flex-direction: column;
        height: calc(100% - 38px);
    }

    .card-grafica canvas {
        width: 100% !important;
        height: 250px !important;
        flex-grow: 1;
    }

    .card-grafica .card-title {
        font-size: 1rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .card-grafica .card-header {
        padding: 0.5rem 1rem;
    }

    @media (max-width: 768px) {
        .card-grafica {
            margin-bottom: 15px;
        }

        .card-grafica canvas {
            height: 200px !important;
        }
    }

    .filtros-container {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    #loading-indicator {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
        background: rgba(255, 255, 255, 0.8);
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .badge-estado {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }

    .badge-pendiente {
        background-color: #6c757d;
    }

    .badge-confirmada {
        background-color: #17a2b8;
    }

    .badge-noconfirmada {
        background-color: #ffc107;
        color: #212529;
    }

    .badge-asistio {
        background-color: #28a745;
    }

    .badge-noasistio {
        background-color: #dc3545;
    }

    .badge-cancelada {
        background-color: #da3545;
    }

    .badge-pagada {
        background-color: #007bff;
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <!-- Filtros -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="filtros-container">
                        <form id="filtros-form" method="POST">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Fecha Inicio:</label>
                                        <input type="date" class="form-control" name="fecha_inicio" value="<?php echo $fecha_inicio; ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Fecha Fin:</label>
                                        <input type="date" class="form-control" name="fecha_fin" value="<?php echo $fecha_fin; ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Estado:</label>
                                        <select class="form-control" name="estado">
                                            <?php foreach ($estados_cita as $estado): ?>
                                                <option value="<?php echo $estado['id']; ?>" <?php echo $estado_seleccionado == $estado['id'] ? 'selected' : ''; ?>>
                                                    <?php echo $estado['nombre']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Colaborador:</label>
                                        <select class="form-control" name="colaborador">
                                            <option value="todos">Todos los colaboradores</option>
                                            <?php foreach ($colaboradores as $colab): ?>
                                                <option value="<?php echo $colab['id_col']; ?>" <?php echo $colaborador_seleccionado == $colab['id_col'] ? 'selected' : ''; ?>>
                                                    <?php echo $colab['nombre']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficas y tablas -->
        <div class="container-fluid">
            <div class="row">
                <!-- Gráfica de citas por día -->
                <div class="col-md-4">
                    <div class="card card-grafica">
                        <div class="card-header bg-primary text-white py-2">
                            <h5 class="card-title mb-0">Citas por Día</h5>
                        </div>
                        <div class="card-body p-2">
                            <canvas id="grafica-citas-dia" height="250"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Gráfica de citas por colaborador -->
                <div class="col-md-4">
                    <div class="card card-grafica">
                        <div class="card-header bg-success text-white py-2">
                            <h5 class="card-title mb-0">Citas por Colaborador</h5>
                        </div>
                        <div class="card-body p-2">
                            <canvas id="grafica-citas-colab" height="250"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Gráfica de citas por estado -->
                <div class="col-md-4">
                    <div class="card card-grafica">
                        <div class="card-header bg-info text-white py-2">
                            <h5 class="card-title mb-0">Citas por Estado</h5>
                        </div>
                        <div class="card-body p-2">
                            <canvas id="grafica-citas-estado" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <!-- Tabla de resumen -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-secondary text-white py-2">
                            <h5 class="card-title mb-0">Resumen de Citas</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Total Citas</th>
                                            <th>Pendiente</th>
                                            <th>Confirmada</th>
                                            <th>No Confirmada</th>
                                            <th>Cancelada</th>
                                            <th>Asistió</th>
                                            <th>No Asistió</th>
                                            <th>Pagada</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td id="total-citas">0</td>
                                            <td id="pendiente">0</td>
                                            <td id="confirmada">0</td>
                                            <td id="noconfirmada">0</td>
                                            <td id="cancelada">0</td>
                                            <td id="asistio">0</td>
                                            <td id="noasistio">0</td>
                                            <td id="pagada">0</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Loading indicator -->
<div id="loading-indicator">
    <div class="text-center">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Cargando...</span>
        </div>
        <p class="mt-2">Cargando datos...</p>
    </div>
</div>

<?php include_once 'templates/footer.php'; ?>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>

<script>
    $(document).ready(function() {
        // Variables para almacenar las instancias de los gráficos
        var chartDias, chartColab, chartEstado;

        // Función para inicializar o actualizar los gráficos
        function actualizarGraficas(data) {
            // Destruir gráficos existentes si ya existen
            if (chartDias) chartDias.destroy();
            if (chartColab) chartColab.destroy();
            if (chartEstado) chartEstado.destroy();

            // Opciones comunes para todas las gráficas
            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 10,
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            };

            // Gráfica de citas por día
            var ctxDias = document.getElementById('grafica-citas-dia').getContext('2d');
            chartDias = new Chart(ctxDias, {
                type: 'line',
                data: {
                    labels: data.labels_dias,
                    datasets: [{
                        label: 'Citas por Día',
                        data: data.data_dias,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        tension: 0.1
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: {
                                    size: 9
                                }
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 9
                                },
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    }
                }
            });

            // Gráfica de citas por colaborador
            var ctxColab = document.getElementById('grafica-citas-colab').getContext('2d');
            chartColab = new Chart(ctxColab, {
                type: 'bar',
                data: {
                    labels: data.labels_colab,
                    datasets: [{
                        label: 'Citas por Colaborador',
                        data: data.data_colab,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    ...commonOptions,
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: {
                                    size: 9
                                }
                            }
                        },
                        y: {
                            ticks: {
                                font: {
                                    size: 9
                                }
                            }
                        }
                    }
                }
            });

            // Gráfica de citas por estado
            var ctxEstado = document.getElementById('grafica-citas-estado').getContext('2d');
            chartEstado = new Chart(ctxEstado, {
                type: 'pie',
                data: {
                    labels: data.labels_estado,
                    datasets: [{
                        data: data.data_estado,
                        backgroundColor: [
                            'rgba(108, 117, 125, 0.7)', // Pendiente
                            'rgba(23, 162, 184, 0.7)', // Confirmada
                            'rgba(255, 193, 7, 0.7)', // No Confirmada
                            'rgba(220, 53, 69, 0.7)', // Cancelada
                            'rgba(40, 167, 69, 0.7)', // Asistió
                            'rgba(220, 53, 69, 0.7)', // No Asistió
                            'rgba(0, 123, 255, 0.7)' // Pagada
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });

            // Actualizar la tabla de resumen
            $('#total-citas').text(data.total_citas);
            $('#pendiente').text(data.pendiente || 0);
            $('#confirmada').text(data.confirmada || 0);
            $('#noconfirmada').text(data.noconfirmada || 0);
            $('#cancelada').text(data.cancelada || 0);
            $('#asistio').text(data.asistio || 0);
            $('#noasistio').text(data.noasistio || 0);
            $('#pagada').text(data.pagada || 0);
        }

        // Función para cargar datos con los filtros actuales
        function cargarDatos() {
            var formData = $('#filtros-form').serialize();

            $.ajax({
                url: 'procesar_filtros.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: function() {
                    $('#loading-indicator').show();
                },
                success: function(data) {
                    actualizarGraficas(data);
                },
                complete: function() {
                    $('#loading-indicator').hide();
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar datos:', error);
                    alert('Ocurrió un error al cargar los datos. Por favor, inténtalo de nuevo.');
                }
            });
        }

        // Cargar datos iniciales
        cargarDatos();

        // Configurar eventos para los filtros
        $('#filtros-form').on('change', 'input, select', function() {
            cargarDatos();
        });

        // Configurar datepicker (opcional)
        $('input[type="date"]').on('change', function() {
            cargarDatos();
        });
    });
</script>