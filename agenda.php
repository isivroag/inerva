<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario de Citas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <style>
        #calendar {
            background-color: white;
            border-radius: 0.375rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .fc-event {
            cursor: pointer;
        }

        .fc-toolbar-title {
            font-size: 1.25rem;
        }
    </style>
</head>

<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Calendario de Citas</h5>
                    </div>
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para detalles de cita -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Detalles de la Cita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Paciente:</strong> <span id="modalPaciente"></span></p>
                            <p><strong>Profesional:</strong> <span id="modalProfesional"></span></p>
                            <p><strong>Consultorio:</strong> <span id="modalConsultorio"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Fecha y Hora:</strong> <span id="modalFecha"></span></p>
                            <p><strong>Duración:</strong> <span id="modalDuracion"></span> minutos</p>
                            <p><strong>Estado:</strong> <span id="modalEstado"></span></p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <p><strong>Concepto:</strong></p>
                            <p id="modalConcepto" class="bg-light p-3 rounded"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <p><strong>Observaciones:</strong></p>
                            <p id="modalObservaciones" class="bg-light p-3 rounded"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery, Bootstrap JS, FullCalendar JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
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
                    success: function(response) {
                        console.log('Respuesta del servidor:', response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error en la solicitud:', {
                            status: status,
                            error: error,
                            responseText: xhr.responseText
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

                    // Mostrar el modal
                    var eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
                    eventModal.show();
                },
                eventContent: function(arg) {
                    // Personalizar el contenido del evento en el calendario
                    return {
                        html: `<div class="fc-event-main-frame" style="background-color: ${arg.event.backgroundColor}; color: ${arg.event.textColor}; font-size: 12px; padding: 5px; border-radius: 5px; max-height: 80px; overflow-y: auto;">
                            <div class="fc-event-title-container">
                                <div class="fc-event-title fc-sticky">${arg.event.title}</div>
                            </div>
                            <div class="fc-event-time">${moment(arg.event.start).format('HH:mm')}</div>
                            <div class="fc-event-description">${arg.event.extendedProps.descripcion || 'Sin descripción'}</div>
                            <div class="fc-event-professional">Profesional: ${arg.event.extendedProps.nombre}</div>
                        </div>`
                    };
                }
            });

            calendar.render();
        });
    </script>
</body>

</html>