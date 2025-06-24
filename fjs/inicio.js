$(document).ready(function () {
  var id, opcion;
  opcion = 4;
  var fila;
  var textcolumnas = permisos();

  function permisos() {
    var tipousuario = parseInt($("#tipousuario").val());
    var columnas = "";
    console.log("Tipo de usuario:", tipousuario);

    switch (tipousuario) {
      case 1: // usuario normal
        columnas =
          "<div class='btn-group'>\
          <button class='btn btn-sm bg-success btnAsistencia' data-toggle='tooltip' title='PX Asistio'>\
              <i class='fa-duotone fa-solid fa-check'></i>\
            </button>\
            <button class='btn btn-sm btn-danger text-light btnNoasistio' data-toggle='tooltip' title='PX No Asistio'>\
              <i class=' fa-duotone fa-solid fa-xmark'></i>\
            </button>\
          <button class='btn btn-sm bg-green btnAgenda' data-toggle='tooltip' title='Agenda'>\
              <i class='fa-duotone fa-solid fa-calendar'></i>\
            </button>\
            <button class='btn btn-sm bg-primary btnCobro' data-toggle='tooltip' title='Cobranza'>\
              <i class='fa-duotone fa-solid fa-dollar-sign'></i>\
            </button>\
          </div>";
        break;
      case 2: // usuario administrador
      case 3: // usuario supervisor
        columnas =
          "<div class='btn-group'>\
          <button class='btn btn-sm bg-success btnAsistencia' data-toggle='tooltip' title='PX Asistio'>\
              <i class='fa-duotone fa-solid fa-check'></i>\
            </button>\
            <button class='btn btn-sm btn-danger text-light btnNoasistio' data-toggle='tooltip' title='PX No Asistio'>\
              <i class=' fa-duotone fa-solid fa-xmark'></i>\
            </button>\
          <button class='btn btn-sm bg-green btnAgenda' data-toggle='tooltip' title='Agenda'>\
              <i class='fa-duotone fa-solid fa-calendar'></i>\
            </button>\
            <button class='btn btn-sm bg-primary btnCobro' data-toggle='tooltip' title='Cobranza'>\
              <i class='fa-duotone fa-solid fa-dollar-sign'></i>\
            </button>\
          </div>";

        break;

      default:
        columnas = "";

        break;
    }
    return columnas;
  }

  var tablaNuevo = $("#tablaNuevo").DataTable({
    dom: "<'row'<'col-sm-12'tr>>", // Solo la tabla (sin l, B, f, i, p)
    paging: false, // Sin paginación
    info: false, // Sin leyenda de "Mostrando registros del..."
    searching: false, // Sin buscador
    language: {
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "No se encontraron resultados",
      info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
      infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
      infoFiltered: "(filtrado de un total de _MAX_ registros)",
      sSearch: "Buscar:",
      oPaginate: {
        sFirst: "Primero",
        sLast: "Último",
        sNext: "Siguiente",
        sPrevious: "Anterior",
      },
      sProcessing: "Procesando...",
    },
    columnDefs: [
      {
        targets: -1,
        data: null,
        defaultContent: textcolumnas,
      },
      { className: "hide_column", targets: [2] },
      { className: "hide_column", targets: [7] },
    ],

    rowCallback: function (row, data) {
      $($(row).find("td")[3]).css("background-color", data[2]);
      $($(row).find("td")[3]).css("color", "white");
      $($(row).find("td")[3]).css("font-weight:", "bold");

      valor = parseInt(data[7]);
      var icono = "";

      switch (valor) {
        case 0:
          icono =
            '<i class="fa-solid fa-square-question text-secondary fa-2x text-center" title="Sin información"></i>';
          $($(row).find("td")[8]).html(icono);
          $($(row).find("td")[8]).find("i").tooltip();
          break;
        case 1:
          icono =
            '<i class="fa-solid fa-phone text-success fa-2x text-center" title="Cita confirmada"></i>';
          $($(row).find("td")[8]).html(icono);
          $($(row).find("td")[8]).find("i").tooltip();
          break;
        case 2:
          icono =
            '<i class="fa-solid fa-phone-slash text-warning fa-2x text-center" title="Cita no confirmada"></i>';
          $($(row).find("td")[8]).html(icono);
          $($(row).find("td")[8]).find("i").tooltip();
          break;
        case 4:
          icono =
            '<i class="fa-solid fa-square-xmark text-danger fa-2x text-center" title="Cita Cancelada"></i>';
          $($(row).find("td")[8]).html(icono);
          $($(row).find("td")[8]).find("i").tooltip();
          break;
        case 5:
          icono =
            '<i class="fa-solid fa-user-check text-success fa-2x text-center" title="Paciente Asistió"></i>';
          $($(row).find("td")[8]).html(icono);
          $($(row).find("td")[8]).find("i").tooltip();
          break;
        case 6:
          icono =
            '<i class="fa-solid fa-user-xmark text-danger fa-2x text-center" title="Paciente No Asistió"></i>';
          $($(row).find("td")[8]).html(icono);
          $($(row).find("td")[8]).find("i").tooltip();
          break;
        case 10: // PAGADO
          icono =
            '<a href="#" class="verRecibo" title="Ver recibo">' +
            '<i class="fa-solid fa-money-check-alt text-success fa-2x text-center"></i>' +
            "</a>";
          $($(row).find("td")[8]).html(icono);
          $($(row).find("td")[8]).find("i").tooltip();
          // Guardar el folio_cita en el enlace para usarlo después
          $;
          break;
        default:
          icono =
            '<i class="fa-solid fa-square-xmark text-danger fa-2x text-center"></i>';
          $($(row).find("td")[8]).html(icono);
          break;
      }
    },
  });

  $(document).on("click", ".btnAgenda", function () {
    window.location.href = "calendario.php";
  });
  $(document).on("click", ".btnAsistencia", function () {
    var fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());
    var estado = parseInt(fila.find("td:eq(7)").text());
    opcion = 8;

    if (estado === 10) {
      Swal.fire({
        title: "Operación no permitida",
        text: "La cita ya ha sido concluida. No es posible registrar asistencia.",
        icon: "warning",
        confirmButtonText: "Aceptar",
      });
      return false;
    }

    Swal.fire({
      title: "¿Confirmar asistencia?",
      text: "¿Está seguro que el paciente asistió a la cita?",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Sí, confirmar",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.value) {
        $.ajax({
          url: "bd/buscarcita.php",
          type: "POST",
          dataType: "json",
          async: "false",
          data: { id: id, opcion: opcion },
          success: function (data) {
            Swal.fire({
              title: "¡Éxito!",
              text: "Se registró la visita del paciente con éxito.",
              icon: "success",
              timer: 1500,
              showConfirmButton: false,
            });
            setTimeout(function () {
              window.location.reload();
            }, 1500);
          },
        });
      }
    });
    return false;
  });

  $(document).on("click", ".btnNoasistio", function () {
    var fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());
    var estado = parseInt(fila.find("td:eq(7)").text());
    opcion = 9;

    if (estado === 10) {
      Swal.fire({
        title: "Operación no permitida",
        text: "La cita ya ha sido concluida. No es posible registrar inasistencia.",
        icon: "warning",
        confirmButtonText: "Aceptar",
      });
      return false;
    }

    Swal.fire({
      title: "¿Confirmar inasistencia?",
      text: "¿Está seguro que el paciente NO asistió a la cita?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, confirmar",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.value) {
        $.ajax({
          url: "bd/buscarcita.php",
          type: "POST",
          dataType: "json",
          async: "false",
          data: { id: id, opcion: opcion },
          success: function (data) {
            Swal.fire({
              title: "Inasistencia",
              text: "Paciente no asistió a su cita.",
              icon: "error",
              timer: 1500,
              showConfirmButton: false,
            });
            setTimeout(function () {
              window.location.reload();
            }, 1500);
          },
        });
      }
    });
    return false;
  });

  $(document).on("click", ".btnCobro", function () {
    var fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());
    var estado = parseInt(fila.find("td:eq(7)").text());

    if (estado === 10) {
      Swal.fire({
        title: "Operación no permitida",
        text: "La cita ya ha sido concluida. No es posible registrar cobro.",
        icon: "warning",
        confirmButtonText: "Aceptar",
      });
      return false;
    }

    if (estado === 5) {
      Swal.fire({
        title: "¿Registrar cobro?",
        text: "¿Desea registrar el cobro de este paciente?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí, registrar",
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.value) {
          window.location.href = "cobranza.php?id=" + id;
        }
      });
    } else {
      Swal.fire({
        title: "Operación no permitida",
        text: "Solo es posible realizar el cobro si el paciente asistió a la cita.",
        icon: "warning",
        confirmButtonText: "Aceptar",
      });
    }
  });

  $(document).on("click", ".verRecibo", function () {
    fila = $(this).closest("tr");
    var folio_cob = 0;
    folio_cita = parseInt(fila.find("td:eq(0)").text());
    console.log("Folio de cita:", folio_cita);

    $.ajax({
      url: "bd/buscarrecibo.php",
      type: "POST",
      dataType: "json",
      data: { folio_cita: folio_cita },
      success: function (data) {
        folio_cob = data;
        window.location.href = "cobranza.php?folio_cob=" + folio_cob;
      },
      error: function () {
        Swal.fire("Error", "Error de comunicación con el servidor", "error");
      },
    });
  });
});
