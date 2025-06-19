<?php
$pagina = "calendario";
include_once "templates/header.php";
include_once "templates/barra.php";
include_once "templates/navegacion.php";

include_once 'bd/conexion.php';
$objeto = new conn();
$conexion = $objeto->connect();
$fecha = date('Y-m-d');

$consulta = "SELECT * FROM vcitap2 where estado<>3 and estado<>4 order by folio_citap";
$resultado = $conexion->prepare($consulta);
$resultado->execute();
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);

$consultacx = "SELECT * FROM paciente where edo_px='1' order by id_px";
$resultadocx = $conexion->prepare($consultacx);
$resultadocx->execute();
$datacx = $resultadocx->fetchAll(PDO::FETCH_ASSOC);

$consultai = "SELECT * FROM colaborador WHERE edo_col ='1' ORDER BY id_col";
$resultadoi = $conexion->prepare($consultai);
$resultadoi->execute();
$datai = $resultadoi->fetchAll(PDO::FETCH_ASSOC);

$consultacab = "SELECT * FROM consultorio WHERE edo_con ='1' ORDER BY id_con";
$resultadocab = $conexion->prepare($consultacab);
$resultadocab->execute();
$datacab = $resultadocab->fetchAll(PDO::FETCH_ASSOC);

$message = "";
?>

<!-- CSS Personalizado para el calendario -->
<style>
  /* Estilos para el calendario */
  #calendar {
    min-height: 70vh;
    background-color: white;
    border-radius: 0.375rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
  }

  .fc-event {
    cursor: pointer;
    border: none;
    font-size: 12px;
    padding: 2px;
    margin: 1px;
  }

  .fc-event .fc-event-main {
    padding: 2px;
  }

  .fc-event .fc-event-title {
    white-space: normal !important;
    overflow: visible !important;
    text-overflow: clip !important;
  }

  .fc-event .fc-event-time {
    font-weight: bold;
  }

  .fc-toolbar-title {
    font-size: 1.25rem;
  }

  /* Modal de detalles mejorado */
  .event-detail-row {
    margin-bottom: 10px;
  }

  .event-detail-label {
    font-weight: bold;
    color: #555;
  }

  /* Cargador */
  #div_carga {
    position: absolute;
    width: 100%;
    height: 100%;
    background-color: rgba(60, 60, 60, 0.5);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 3;
  }

  #cargador {
    position: absolute;
    top: 50%;
    left: 50%;
    margin-top: -25px;
    margin-left: -25px;
  }

  #textoc {
    position: absolute;
    top: 50%;
    left: 50%;
    margin-top: 120px;
    margin-left: 20px;
  }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="card card-primary">
        <div class="card-header bg-green">
          <h3 class="card-title">Calendario de Citas</h3>
          <div class="card-tools">
            <button id="btnNuevox" type="button" class="btn btn-tool bg-white" data-toggle="modal">
              <i class="fas fa-plus-square text-green"></i> Nueva Cita
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-3">
              <div class="card card-info">
                <div class="card-header">
                  <h3 class="card-title">Colaboradores</h3>
                </div>
                <div class="card-body p-0">
                  <table class="table table-sm table-hover">
                    <thead class="bg-info">
                      <tr>
                        <th>Nombre</th>
                        <th>Color</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($datai as $dat) { ?>
                        <tr>
                          <td><?php echo $dat['nombre_col'] ?></td>
                          <td><span class="badge" style="background-color: <?php echo $dat['color_col'] ?>">&nbsp;&nbsp;&nbsp;</span></td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="col-md-9">
              <div id="div_carga">
                <img id="cargador" src="img/loader.gif" />
                <span class="text-white" id="textoc"><strong>Cargando...</strong></span>
              </div>
              <div id="calendar"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Modal para detalles de cita -->
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-green">
        <h5 class="modal-title" id="eventModalLabel">Detalles de la Cita</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row event-detail-row">
            <div class="col-md-6">
              <span class="event-detail-label">Paciente:</span>
              <span id="modalPaciente"></span>
            </div>
            <div class="col-md-6">
              <span class="event-detail-label">Profesional:</span>
              <span id="modalProfesional"></span>
            </div>
          </div>
          <div class="row event-detail-row">
            <div class="col-md-6">
              <span class="event-detail-label">Consultorio:</span>
              <span id="modalConsultorio"></span>
            </div>
            <div class="col-md-6">
              <span class="event-detail-label">Fecha y Hora:</span>
              <span id="modalFecha"></span>
            </div>
          </div>
          <div class="row event-detail-row">
            <div class="col-md-6">
              <span class="event-detail-label">Duración:</span>
              <span id="modalDuracion"></span> minutos
            </div>
            <div class="col-md-6">
              <span class="event-detail-label">Estado:</span>
              <span id="modalEstado"></span>
            </div>
          </div>
          <div class="row event-detail-row">
            <div class="col-12">
              <span class="event-detail-label">Concepto:</span>
              <div id="modalConcepto" class="bg-light p-3 rounded mt-2"></div>
            </div>
          </div>
          <div class="row event-detail-row">
            <div class="col-12">
              <span class="event-detail-label">Observaciones:</span>
              <div id="modalObservaciones" class="bg-light p-3 rounded mt-2"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" id="btnCancelarCitaModal" class="btn btn-danger">Cancelar Cita</button>
      </div>
    </div>
  </div>
</div>

<!-- Incluir los modales existentes para nueva cita y búsqueda de pacientes -->

<?php include_once 'templates/footer.php'; ?>
<!-- jQuery primero -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Moment.js (esencial para el manejo de fechas) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/es.min.js"></script>

<!-- FullCalendar y sus plugins -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/plugins/daygrid/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/plugins/timegrid/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/plugins/interaction/main.min.js'></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.25/datatables.min.css" />
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.25/datatables.min.js"></script>
<script>
  $(document).ready(function() {
    // Inicializar el calendario
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      themeSystem: 'bootstrap',
      initialView: 'timeGridWeek',
      locale: 'es',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      buttonText: {
        today: 'Hoy',
        month: 'Mes',
        week: 'Semana',
        day: 'Día'
      },
      allDaySlot: false,
      slotMinTime: '08:00:00',
      slotMaxTime: '20:00:00',
      slotDuration: '00:30:00',
      events: {
        url: 'obtener_citas.php',
        method: 'GET',
        failure: function(error) {
          console.error('Error al cargar eventos:', error);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron cargar las citas. Por favor recarga la página.'
          });
        }
      },
      eventClick: function(info) {
        // Llenar el modal con los datos del evento
        $('#modalPaciente').text(info.event.title);
        $('#modalProfesional').text(info.event.extendedProps.nombre);
        $('#modalConsultorio').text(info.event.extendedProps.nom_con || 'No asignado');
        $('#modalFecha').text(moment(info.event.start).format('DD/MM/YYYY HH:mm') + ' - ' + moment(info.event.end).format('HH:mm'));
        $('#modalDuracion').text(info.event.extendedProps.duracion);
        $('#modalEstado').text(info.event.extendedProps.estado_citap);
        $('#modalConcepto').text(info.event.extendedProps.descripcion);
        $('#modalObservaciones').text(info.event.extendedProps.obs || 'Ninguna');

        // Configurar el botón de cancelar
        $('#btnCancelarCitaModal').off('click').on('click', function() {
          $('#foliocan').val(info.event.id);
          $('#modalcan').modal('show');
          $('#eventModal').modal('hide');
        });

        // Mostrar el modal
        $('#eventModal').modal('show');
      },
      eventContent: function(arg) {
        // Usa el formateador de fechas de FullCalendar en lugar de moment
        var timeStr = arg.timeText; // Esto usa el formateador interno de FC

        return {
          html: `<div class="fc-event-main-frame" style="background-color: ${arg.event.backgroundColor}; color: ${arg.event.textColor};">
            <div class="fc-event-title-container">
                <div class="fc-event-title">${arg.event.title}</div>
            </div>
            <div class="fc-event-time">${timeStr}</div>
            <div class="fc-event-desc">${arg.event.extendedProps.nombre}</div>
        </div>`
        };
      },
      loading: function(isLoading) {
        if (isLoading) {
          $('#div_carga').show();
        } else {
          $('#div_carga').hide();
        }
      }
    });

    calendar.render();

    // Configurar el botón de nueva cita
    $('#btnNuevox').click(function() {
      // Limpiar el formulario
      $('#formDatospx')[0].reset();
      $('#opcionx').val('1'); // 1 para nueva cita
      $('#modalpx').modal('show');
    });

    // Configurar la tabla de pacientes
    $('#tablaCx').DataTable({
      "language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
      }
    });

    // Configurar el botón de búsqueda de paciente
    $('#bclientex').click(function() {
      $('#modalProspectox').modal('show');
    });

    // Seleccionar paciente de la tabla
    $('#tablaCx tbody').on('click', 'tr', function() {
      var data = $('#tablaCx').DataTable().row(this).data();
      $('#id_px').val(data[0]);
      $('#nom_prosx').val(data[1]);
      $('#modalProspectox').modal('hide');
    });
  });
</script>