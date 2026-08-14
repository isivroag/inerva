$(document).ready(function () {
  var esAdmin = String($("#ctxRetiro").data("es-admin")) === "1";
  var fechaBase = $("#ctxRetiro").data("fecha");
  var modoConsulta = false;
  var retirosMap = {};

  var tabla = $("#tablaRetiros").DataTable({
    language: {
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "No se encontraron resultados",
      info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
      infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
      infoFiltered: "(filtrado de un total de _MAX_ registros)",
      sSearch: "Buscar:",
      oPaginate: {
        sFirst: "Primero",
        sLast: "Ultimo",
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
      { className: "text-right" },
      { className: "text-center" },
      null,
      { className: "text-right" },
      { className: "text-right" },
      { orderable: false, className: "text-center" },
    ],
  });

  function fechaSeleccionada() {
    if (esAdmin && $("#ctrlfecha").length) {
      return $("#ctrlfecha").val();
    }
    return fechaBase;
  }

  function estadoBadge(estado) {
    return parseInt(estado, 10) === 1
      ? "<span class='badge badge-success'>ACTIVO</span>"
      : "<span class='badge badge-danger'>CANCELADO</span>";
  }

  function limpiarModal() {
    $("#formRetiro").trigger("reset");
    $("#id_retiro").val("");
    $("#fecha_retiro").val(fechaSeleccionada());
    $("#efectivo_caja, #montoant, #montodesp").val("");
    $("#fecha_retiro, #monto_retiro").prop("readonly", false).prop("disabled", false);
    $("#btnGuardarRetiro").show();
    modoConsulta = false;
    if (!esAdmin) {
      $("#fecha_retiro").prop("readonly", true);
    }
  }

  function actualizarMontosControl() {
    var efectivo = parseFloat($("#efectivo_caja").val()) || 0;
    var monto = parseFloat($("#monto_retiro").val()) || 0;

    $("#montoant").val(parseFloat(efectivo).toFixed(2));
    $("#montodesp").val(parseFloat(efectivo - monto).toFixed(2));
  }

  function cargarEstadoCaja(callback) {
    var fecha = $("#fecha_retiro").val() || fechaSeleccionada();

    $.ajax({
      url: "bd/crudretiro.php",
      type: "POST",
      dataType: "json",
      data: { accion: "estado_caja", fecha: fecha },
      success: function (resp) {
        if (resp.status !== "ok") {
          $("#efectivo_caja, #montoant, #montodesp").val("");
          Swal.fire("Error", resp.mensaje || "No se pudo consultar la caja", "error");
          return;
        }

        $("#efectivo_caja").val(parseFloat(resp.efectivo_disponible || 0).toFixed(2));
        actualizarMontosControl();
        if (typeof callback === "function") {
          callback(resp);
        }
      },
      error: function () {
        $("#efectivo_caja, #montoant, #montodesp").val("");
        Swal.fire("Error", "Error de comunicación con el servidor", "error");
      },
    });
  }

  function cargarRetiros() {
    $.ajax({
      url: "bd/crudretiro.php",
      type: "POST",
      dataType: "json",
      data: { accion: "listar", fecha: fechaSeleccionada() },
      success: function (resp) {
        if (resp.status !== "ok") {
          Swal.fire("Error", resp.mensaje || "No se pudieron cargar los retiros", "error");
          return;
        }

        retirosMap = {};
        tabla.clear();

        $.each(resp.data, function (_, r) {
          retirosMap[r.id_retiro] = r;
          var acciones = "<button class='btn btn-sm btn-primary btnVer mr-1' data-id='" + r.id_retiro + "' title='Consultar'><i class='fas fa-eye'></i></button>";
          if (esAdmin && parseInt(r.estado_retiro, 10) === 1) {
            acciones += "<button class='btn btn-sm btn-danger btnCancelar' data-id='" + r.id_retiro + "' title='Cancelar'><i class='fas fa-ban'></i></button>";
          }

          tabla.row.add([
            r.id_retiro,
            r.fecha,
            parseFloat(r.monto).toFixed(2),
            estadoBadge(r.estado_retiro),
            r.fecha_op,
            parseFloat(r.montoant).toFixed(2),
            parseFloat(r.montodesp).toFixed(2),
            acciones,
          ]);
        });

        tabla.draw();
      },
      error: function () {
        Swal.fire("Error", "Error de comunicación con el servidor", "error");
      },
    });
  }

  if (esAdmin && $("#ctrlfecha").length) {
    $("#ctrlfecha").on("change", function () {
      var fecha = $(this).val();
      if (!fecha) return;
      var url = new URL(window.location.href);
      url.searchParams.set("fecha", fecha);
      window.location.href = url.toString();
    });
  }

  $("#fecha_retiro").on("change", function () {
    var fecha = $(this).val();
    if (!fecha) {
      $("#efectivo_caja, #montoant, #montodesp").val("");
      return;
    }

    cargarEstadoCaja(function (resp) {
      if (resp.hay_diferencias) {
        Swal.fire(
          "Caja no conciliada",
          "La caja tiene diferencias con los ingresos y/o gastos. Primero actualiza la caja para continuar.",
          "warning"
        );
      }
    });
  });

  $("#monto_retiro").on("input", function () {
    actualizarMontosControl();
  });

  $("#btnNuevo").click(function () {
    limpiarModal();
    cargarEstadoCaja(function (resp) {
      if (resp.hay_diferencias) {
        Swal.fire(
          "Caja no conciliada",
          "La caja tiene diferencias con los ingresos y/o gastos. Primero actualiza la caja para continuar.",
          "warning"
        );
        return;
      }

      $("#modalRetiroLabel").text("NUEVO RETIRO");
      $("#modalRetiro").modal("show");
    });
  });

  $(document).on("click", ".btnVer", function () {
    var id = $(this).data("id");
    var r = retirosMap[id];
    if (!r) {
      Swal.fire("Error", "No se encontró el retiro seleccionado", "error");
      return;
    }

    $("#id_retiro").val(r.id_retiro);
    $("#fecha_retiro").val(r.fecha);
    $("#efectivo_caja").val(parseFloat(r.montoant).toFixed(2));
    $("#monto_retiro").val(parseFloat(r.monto).toFixed(2));
    $("#montoant").val(parseFloat(r.montoant).toFixed(2));
    $("#montodesp").val(parseFloat(r.montodesp).toFixed(2));
    $("#estado_retiro").val(parseInt(r.estado_retiro, 10) === 1 ? "ACTIVO" : "CANCELADO");
    $("#motivo_can").val(r.motivo_can || "");
    $("#usuario_can").val(r.usuario_can || "");
    $("#fecha_can").val(r.fecha_can || "");

    $("#modalRetiroLabel").text("CONSULTAR RETIRO");
    $("#fecha_retiro, #monto_retiro").prop("readonly", true);
    $("#btnGuardarRetiro").hide();
    modoConsulta = true;
    $("#modalRetiro").modal("show");
  });

  $("#formRetiro").submit(function (e) {
    e.preventDefault();
    if (modoConsulta) return;

    var fecha = $("#fecha_retiro").val();
    var monto = parseFloat($("#monto_retiro").val());

    if (!fecha || isNaN(monto) || monto <= 0) {
      Swal.fire("Datos incompletos", "Complete la fecha y el monto del retiro.", "warning");
      return;
    }

    $.ajax({
      url: "bd/crudretiro.php",
      type: "POST",
      dataType: "json",
      data: { accion: "crear", fecha: fecha, monto: monto },
      success: function (resp) {
        if (resp.status === "ok") {
          Swal.fire("Éxito", resp.mensaje, "success");
          $("#modalRetiro").modal("hide");
          cargarRetiros();
        } else {
          Swal.fire("Error", resp.mensaje || "No se pudo guardar el retiro", "error");
        }
      },
      error: function () {
        Swal.fire("Error", "Error de comunicación con el servidor", "error");
      },
    });
  });

  $(document).on("click", ".btnCancelar", function () {
    var id = $(this).data("id");
    $("#id_retiro_cancelar").val(id);
    $("#motivo_cancelacion").val("");
    $("#modalCancelarRetiro").modal("show");
  });

  $("#formCancelarRetiro").submit(function (e) {
    e.preventDefault();
    var id = $("#id_retiro_cancelar").val();
    var motivo = $.trim($("#motivo_cancelacion").val());

    if (!motivo) {
      Swal.fire("Datos incompletos", "Debe capturar el motivo de cancelación.", "warning");
      return;
    }

    $.ajax({
      url: "bd/crudretiro.php",
      type: "POST",
      dataType: "json",
      data: { accion: "cancelar", id_retiro: id, motivo: motivo },
      success: function (resp) {
        if (resp.status === "ok") {
          Swal.fire("Éxito", resp.mensaje, "success");
          $("#modalCancelarRetiro").modal("hide");
          cargarRetiros();
        } else {
          Swal.fire("Error", resp.mensaje || "No se pudo cancelar el retiro", "error");
        }
      },
      error: function () {
        Swal.fire("Error", "Error de comunicación con el servidor", "error");
      },
    });
  });

  cargarRetiros();
});
