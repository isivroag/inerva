$(document).ready(function () {
  const form = document.getElementById("formDatos");
  if (form.getAttribute("data-disabled") === "true") {
    const elements = form.elements;
    for (let i = 0; i < elements.length; i++) {
      elements[i].disabled = true;
    }
  }
  // Escuchar cambios en el select de servicios
  $("#id_serv").change(function () {
    // Obtener el costo del option seleccionado
    var costo = $(this).find("option:selected").data("costo");

    // Actualizar el campo de costo
    $("#costo").val(costo);

    // Recalcular el total (por si hay descuento)
    calcularTotal();
  });

  // Escuchar cambios en el descuento para recalcular el total
  $("#descuento").on("input", function () {
    calcularTotal();
  });

  // Función para calcular el total
  function calcularTotal() {
    var costo = parseFloat($("#costo").val()) || 0;
    var descuento = parseFloat($("#descuento").val()) || 0;
    var total = costo - descuento;

    // Asegurarse que el total no sea negativo
    total = total < 0 ? 0 : total;

    $("#total").val(total.toFixed(2));
  }

  // Guardar cobranza
  $("#btnGuardar").click(function (e) {
    e.preventDefault();

    var fecha = $("#fecha").val();
    var id_cita = $("#id_cita").val();
    var id_px = $("#id_paciente").val();
    var id_serv = $("#id_serv").val();
    var costo = $("#costo").val();
    var descuento = $("#descuento").val();
    var total = $("#total").val();
    var metodo = $("#metodo_pago").val();

    // Validar campos obligatorios
    if (!id_serv || !costo || !total || !id_cita || !id_px) {
      Swal.fire(
        "Error",
        "Por favor, completa todos los campos obligatorios antes de registrar la cobranza.",
        "error"
      );
      return;
    }

    $.ajax({
      url: "bd/crudcobranza.php",
      type: "POST",
      dataType: "json",
      data: {
        opcion: 1,
        fecha: fecha,
        id_cita: id_cita,
        id_paciente: id_px,
        id_serv: id_serv,
        costo: costo,
        descuento: descuento,
        total: total,
        metodo_pago: metodo,
      },
      success: function (respuesta) {
        if (respuesta.status === "ok") {
          Swal.fire("Éxito", "Cobranza guardada correctamente", "success").then(
            () => {
              $("#formDatos :input").prop("disabled", true);
              $("#btnRecibo, #btnHome").show();
              window.location.href = "inicio.php";
            }
          );
        } else {
          Swal.fire(
            "Error",
            respuesta.mensaje || "No se pudo guardar",
            "error"
          );
        }
      },
      error: function () {
        Swal.fire("Error", "Error de comunicación con el servidor", "error");
      },
    });
  });

  tablaCita = $("#tablaCita").DataTable({
    /* columnDefs: [
      {
        targets: -1,
        data: null,
        defaultContent:
          "<div class='text-center'><button class='btn btn-sm btn-primary btnSelecionar' data-toggle='tooltip' data-placement='top' title='Editar'><i class='fas fa-edit'></i></button>\
              </div>",
      },
    ],*/

    //Para cambiar el lenguaje a español
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
    rowCallback: function (row, data) {
      $($(row).find("td")[4]).css("background-color", data[4]);
    },
  });

  $("#bcita").click(function () {
    $("#modalCitas").modal("show");
  });

  // Manejar selección de cita
  $("#tablaCita tbody").on("click", "tr", function () {
    var data = $("#tablaCita").DataTable().row(this).data();
    id = data[0]; // Asignar el ID de la cita seleccionada
    // Llenar los campos del formulario con los datos de la cita seleccionada

    window.location.href = "cobranza.php?id=" + id;
    // Cerrar el modal de citas
  });
  $("#btnImprimir").click(function () {
    var folio_cob = $("#folio_cob").val();
    if (!folio_cob) {
      Swal.fire(
        "Error",
        "Por favor, selecciona una cobranza primero.",
        "error"
      );
      return;
    }
    Swal.fire({
      title: "<strong>Generar Recibo</strong>",
      html: `
        <div style="text-align:center; margin:15px 0;">
            <i class="fas fa-receipt" style="font-size:48px;color:#4e73df;"></i>
        </div>
        <p style="font-size:16px;">¿Cómo desea generar el recibo?</p>
        <p style="font-size:14px;color:#6c757d;">
            <i class="fas fa-info-circle"></i> Seleccione "Personalizado" para incluir el nombre del paciente
        </p>
    `,
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#4e73df",
      cancelButtonColor: "#6c757d",
      confirmButtonText: '<i class="fas fa-user-edit"></i> Personalizado',
      cancelButtonText: '<i class="fas fa-users"></i> Público General',
      reverseButtons: true,
      customClass: {
        popup: "animated bounceIn",
      },
    }).then((result) => {
      if (result.value) {  // Esto es equivalente a result.isConfirmed
        // Opción Personalizado (per=1)
        window.open(`generar_recibo.php?folio_cob=${folio_cob}&per=1`, '_blank');
    } else if (result.dismiss === Swal.DismissReason.cancel) {
        // Opción Público General
        window.open(`generar_recibo.php?folio_cob=${folio_cob}`, '_blank');
    }
    });
  });
  $("#btnHome").click(function () {
    window.location.href = "inicio.php";
  });
});
