$(document).ready(function () {
  var id, opcion, fila;
  let hoy = new Date().toISOString().slice(0, 10);
  $("#filtro_fecha").val(hoy);

  $("#filtro_fecha").on("change", function () {
    cargarCXC();
  });

  // Inicializar DataTable vacío
  var tabla = $("#tablaCXC").DataTable({
    columnDefs: [
      { targets: [2, 4, 7], className: "hide_column" },
      {
        targets: -1,
        data: null,
        defaultContent:
          "<div class='text-center'>\
          <button class='btn btn-sm btn-primary btnVer' data-toggle='tooltip' data-placement='top' title='Ver Detalle'><i class='fas fa-magnifying-glass'>  </i></button>\
          <button class='btn btn-sm btn-success btnPagar' data-toggle='tooltip' data-placement='top' title='Pagar'><i class='fas fa-dollar-sign'>  </i></button>\
          <button class='btn btn-sm bg-green btnVerPagos' data-toggle='tooltip' data-placement='top' title='Ver Pagos'><i class='fas fa-magnifying-glass-dollar'></i></button>\
              <button class='btn btn-sm btn-danger btnCancelar' data-toggle='tooltip' data-placement='top' title='Eliminar'><i class='fas fa-trash-alt'></i></button></div>",
      },
    ],
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
    ordering: false,
    responsive: true,
    columns: [
      null,
      null,
      null,
      null,
      null,
      null,
      null,
      null,
      null,
      null,
      { className: "text-right" }, // Total
      { className: "text-right" }, // Saldo
      { orderable: false },
    ],
  });

  // Buscar y cargar datos
  function cargarCXC() {
    var cliente = $("#filtro_cliente").val();
    var fecha = $("#filtro_fecha").val();
    var colaborador = $("#filtro_colaborador").val();

    $.ajax({
      url: "bd/getcxc.php",
      type: "POST",
      dataType: "json",
      data: {
        cliente: cliente,
        fecha: fecha,
        colaborador: colaborador,
      },
      success: function (data) {
        tabla.clear();
        data.forEach(function (row) {
          var acciones = `                   `;
          tabla.row.add([
            row.folio_cxc,
            row.fecha_cob,
            row.id_px,
            row.paciente,
            row.id_col,
            row.colaborador,
            row.id_cita,
            row.fecha_cita,
            row.hora_cita,
            row.servicio,
            parseFloat(row.total).toFixed(2),
            parseFloat(row.saldo).toFixed(2),
            acciones,
          ]);
        });
        tabla.draw();
      },
    });
  }

  $(document).on('click', '.btnImprimir', function() {
    var fila = $(this).closest("tr");
    var id_pago = fila.find("td:eq(0)").text(); // Asumiendo
    console.log("ID Pago:", id_pago);

    if (!id_pago) {
      Swal.fire("Error", "Por favor, selecciona un pago primero.", "error");
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
      if (result.value) {
        // Esto es equivalente a result.isConfirmed
        // Opción Personalizado (per=1)
        window.open(`generar_ticket.php?id_pago=${id_pago}&per=1`, "_blank");
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        // Opción Público General
        window.open(`generar_ticket.php?id_pago=${id_pago}`, "_blank");
      }
    });
  });

  // Cargar al inicio
  cargarCXC();

  // Buscar al hacer click
  $("#btnBuscar").click(function () {
    cargarCXC();
  });
  $(document).on("click", ".btnVer", function () {
    fila = $(this).closest("tr");
    var folio = $(fila).find("td:eq(0)").text(); // Obtener el folio de la fila
    window.location.href = "cobranza.php?folio_cob=" + folio;
  });
  $(document).on("click", ".btnPagar", function () {
    fila = $(this).closest("tr");
    var folio = $(fila).find("td:eq(0)").text(); // Obtener el folio de la fila
    console.log("Folio CXC:", folio);

    var saldo = $(fila).find("td:eq(10)").text(); // Obtener el saldo de la fila
    saldo = parseFloat(saldo.replace(/[$,]/g, "")); // Convertir
    console.log("Saldo CXC:", saldo);
    // Puedes hacer un AJAX para traer el saldo actual si lo deseas
    $("#folio_cxc_pago").val(folio);
    $("#saldo_ini").val(saldo);
    $("#saldo_fin").val(saldo); // Inicialmente el saldo final es igual al saldo inicial
    // Aquí podrías traer el saldo real desde backend si hay pagos previos

    $("#modalPago").modal("show");
  });

  $("#importe_pago").on("input", function () {
    var saldoIni = parseFloat($("#saldo_ini").val()) || 0;
    var importe = parseFloat($(this).val()) || 0;
    if (importe > saldoIni) {
      $(this).val(saldoIni);
      importe = saldoIni;
      Swal.fire(
        "Advertencia",
        "El importe no puede ser mayor al saldo pendiente.",
        "warning"
      );
    }
    var saldoFin = saldoIni - importe;
    saldoFin = saldoFin < 0 ? 0 : saldoFin;
    $("#saldo_fin").val(saldoFin.toFixed(2));
  });

  // Guardar pago
  $("#formPago").submit(function (e) {
    e.preventDefault();
    // Validar campos obligatorios
    $saldoini = parseFloat($("#saldo_ini").val());
    $importe = parseFloat($("#importe_pago").val());
    if ($importe > $saldoini) {
      Swal.fire(
        "Error",
        "El importe no puede ser mayor al saldo pendiente.",
        "error  "
      );
      return;
    }

    var datos = {
      folio_cxc: $("#folio_cxc_pago").val(),
      fecha_pago: $("#fecha_pago").val(),
      importe_pago: $("#importe_pago").val(),
      metodo_pago: $("#metodo_pago_real").val(),
    };
    $.ajax({
      url: "bd/crudpago.php",
      type: "POST",
      dataType: "json",
      data: datos,
      success: function (respuesta) {
        if (respuesta.status === "ok") {
          Swal.fire({
            title: "Éxito",
            text: "Pago registrado correctamente",
            icon: "success",
            timer: 1500,
            timerProgressBar: true,
            showConfirmButton: false,
          }).then(() => {
            window.location.reload();
          });
        } else {
          Swal.fire(
            "Error",
            respuesta.mensaje || "No se pudo registrar el pago",
            "error"
          );
        }
      },
      error: function () {
        Swal.fire("Error", "Error de comunicación con el servidor", "error");
      },
    });
  });

  // Ver pagos
  $(document).on("click", ".btnVerPagos", function () {
    fila = $(this).closest("tr");
    var folio_cxc = $(fila).find("td:eq(0)").text(); // Obtener el folio de la fila
    id = folio_cxc;
    console.log("Folio CXC:", folio_cxc);
    $.ajax({
      url: "bd/getpagos.php",
      type: "POST",
      data: { folio_cxc: folio_cxc },
      success: function (html) {
        $("#pagosBody").html(html);
        $("#modalVerPagos").modal("show");
      },
    });
  });

  // Cancelar pago (debes implementar la lógica backend)
  $(document).on("click", ".btnCancelarPago", function () {
    fila = $(this).closest("tr");
    var id_pago = fila.find("td:eq(0)").text(); // Obtener el folio
    var importe = fila.find("td:eq(4)").text(); // Obtener el importe de la fila
    importe = parseFloat(importe.replace(/[$,]/g, "")); // Convertir a número
    var folio_cxc = id;

    console.log("Folio CXC:", folio_cxc);
    console.log("Importe Pago:", importe);
    console.log("ID Pago:", id_pago);

    Swal.fire({
      title: "¿Desea cancelar este pago?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Sí, cancelar",
      cancelButtonText: "No",
    }).then((result) => {
      if (result.value) {
        $.ajax({
          url: "bd/cancelarpago.php",
          type: "POST",
          data: { folio_cxc: folio_cxc, importe: importe, id_pago: id_pago },
          success: function (resp) {
            console.log("Respuesta del servidor:", resp);
            if (resp == 1) {
              Swal.fire({
                title: "Pago cancelado",
                text: "El pago ha sido cancelado correctamente.",
                icon: "success",
                timer: 1500,
                timerProgressBar: true,
                showConfirmButton: false,
              });
              cargarCXC(); // Recargar la tabla de CXC
            } else {
              Swal.fire(
                "Error",
                resp.mensaje || "No se pudo cancelar el pago",
                "error"
              );
            }
          },
          error: function () {
            Swal.fire(
              "Error",
              "Error de comunicación con el servidor",
              "error"
            );
          },
        });
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        // Opción Público General
        return;
      }
    });
  });

  // Reimprimir pago (debes implementar la lógica backend)
  $(document).on("click", ".btnReimprimir", function () {
    var folio = $(this).data("folio");
    window.open("bd/reimprimirpago.php?folio_cxc=" + folio, "_blank");
  });

  // Cancelar CXC y regresar cita a estado 5
  $(document).on("click", ".btnCancelar", function () {
    fila = $(this).closest("tr");
    var folio_cxc = fila.find("td:eq(0)").text(); // Obtener el folio de la fila
    var id_cita = fila.find("td:eq(6)").text(); // Obtener el id_cita de la fila
    var importe = fila.find("td:eq(10)").text(); // Obtener el importe de la fila
    importe = parseFloat(importe.replace(/[$,]/g, "")); // Convertir a número
    var saldo = fila.find("td:eq(11)").text(); // Obtener el saldo de la fila
    saldo = parseFloat(saldo.replace(/[$,]/g, "")); // Convertir
    if (importe != saldo) {
      Swal.fire(
        "Error",
        "No se puede cancelar una cuenta con pagos realizados.",
        "error"
      );
      return;
    } else {
      Swal.fire({
        title: "¿Desea Cancelar esta cuenta?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, cancelar",
        cancelButtonText: "No",
      }).then((result) => {
        if (result.value) {
          $.ajax({
            url: "bd/cancelarcxc.php",
            type: "POST",
            data: { folio_cxc: folio_cxc, id_cita: id_cita },
            success: function (resp) {
              if (resp == 1) {
                Swal.fire("Cuenta cancelada", "", "success").then(() => {
                  cargarCXC();
                });
              } else {
                Swal.fire(
                  "Error",
                  resp.mensaje || "No se pudo cancelar la cuenta",
                  "error"
                );
              }
            },
            error: function () {
              Swal.fire(
                "Error",
                "Error de comunicación con el servidor",
                "error"
              );
            },
          });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
          // Opción Público General
          return;
        }
      });
    }
  });
});
