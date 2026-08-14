$(document).ready(function () {
	var esAdmin = String($("#ctxGasto").data("es-admin")) === "1";
	var fechaBase = $("#ctxGasto").data("fecha");
	var modoConsulta = false;
	var gastosMap = {};

	var tabla = $("#tablaGastos").DataTable({
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
			{ className: "text-center" },
			null,
			null,
			{ className: "text-right" },
			{ className: "text-center" },
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
		if (parseInt(estado, 10) === 0) {
			return "<span class='badge badge-danger'>CANCELADO</span>";
		}
		return "<span class='badge badge-success'>ACTIVO</span>";
	}

	function facturadoBadge(facturado) {
		if (parseInt(facturado, 10) === 1) {
			return "<span class='badge badge-primary'>SI</span>";
		}
		return "<span class='badge badge-secondary'>NO</span>";
	}

	function cargarGastos() {
		var fecha = fechaSeleccionada();

		$.ajax({
			url: "bd/crudgasto.php",
			type: "POST",
			dataType: "json",
			data: { accion: "listar", fecha: fecha },
			success: function (resp) {
				if (resp.status !== "ok") {
					Swal.fire("Error", resp.mensaje || "No se pudieron cargar los gastos", "error");
					return;
				}

				gastosMap = {};
				tabla.clear();

				$.each(resp.data, function (_, g) {
					gastosMap[g.id_gasto] = g;

					var acciones =
						"<button class='btn btn-sm btn-primary btnVer mr-1' data-id='" +
						g.id_gasto +
						"' title='Consultar'><i class='fas fa-eye'></i></button>";

					if (esAdmin && parseInt(g.estado_gasto, 10) === 1) {
						acciones +=
							"<button class='btn btn-sm btn-danger btnCancelar' data-id='" +
							g.id_gasto +
							"' title='Cancelar'><i class='fas fa-ban'></i></button>";
					}

					tabla.row.add([
						g.id_gasto,
						g.fecha,
						facturadoBadge(g.facturado),
						g.referencia,
						g.concepto,
						parseFloat(g.monto).toFixed(2),
						estadoBadge(g.estado_gasto),
						acciones,
					]);
				});

				tabla.draw();
			},
			error: function () {
				Swal.fire("Error", "Error de comunicacion con el servidor", "error");
			},
		});
	}

	function setReadOnlyForm(readOnly) {
		modoConsulta = readOnly;

		$("#fecha_gasto").prop("readonly", readOnly);
		$("#facturado").prop("disabled", readOnly);
		$("#referencia").prop("readonly", readOnly);
		$("#concepto").prop("readonly", readOnly);
		$("#monto").prop("readonly", readOnly);

		if (readOnly) {
			$("#btnGuardarGasto").hide();
		} else {
			$("#btnGuardarGasto").show();
		}
	}

	function limpiarModalGasto() {
		$("#formGasto").trigger("reset");
		$("#id_gasto").val("");
		$("#fecha_gasto").val(fechaSeleccionada());
		setReadOnlyForm(false);
		if (!esAdmin) {
			$("#fecha_gasto").prop("readonly", true);
		}
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

	$("#btnNuevo").click(function () {
		limpiarModalGasto();
		$("#modalGastoLabel").text("NUEVO GASTO");
		$("#modalGasto").modal("show");
	});

	$(document).on("click", ".btnVer", function () {
		var id = $(this).data("id");
		var g = gastosMap[id];

		if (!g) {
			Swal.fire("Error", "No se encontro el gasto seleccionado", "error");
			return;
		}

		$("#id_gasto").val(g.id_gasto);
		$("#fecha_gasto").val(g.fecha);
		$("#facturado").prop("checked", parseInt(g.facturado, 10) === 1);
		$("#referencia").val(g.referencia);
		$("#concepto").val(g.concepto);
		$("#monto").val(parseFloat(g.monto).toFixed(2));

		$("#modalGastoLabel").text("CONSULTAR GASTO");
		setReadOnlyForm(true);
		$("#modalGasto").modal("show");
	});

	$("#formGasto").submit(function (e) {
		e.preventDefault();
		if (modoConsulta) return;

		var fecha = $("#fecha_gasto").val();
		var referencia = $.trim($("#referencia").val());
		var concepto = $.trim($("#concepto").val());
		var monto = parseFloat($("#monto").val());
		var facturado = $("#facturado").is(":checked") ? 1 : 0;

		if (!fecha || referencia.length === 0 || concepto.length === 0 || isNaN(monto) || monto <= 0) {
			Swal.fire("Datos incompletos", "Complete todos los campos obligatorios.", "warning");
			return;
		}

		$.ajax({
			url: "bd/crudgasto.php",
			type: "POST",
			dataType: "json",
			data: {
				accion: "crear",
				fecha: fecha,
				facturado: facturado,
				referencia: referencia,
				concepto: concepto,
				monto: monto,
			},
			success: function (resp) {
				if (resp.status === "ok") {
					Swal.fire("Exito", resp.mensaje, "success");
					$("#modalGasto").modal("hide");
					cargarGastos();
				} else {
					Swal.fire("Error", resp.mensaje || "No se pudo guardar el gasto", "error");
				}
			},
			error: function () {
				Swal.fire("Error", "Error de comunicacion con el servidor", "error");
			},
		});
	});

	$(document).on("click", ".btnCancelar", function () {
		var id = $(this).data("id");
		$("#id_gasto_cancelar").val(id);
		$("#motivo_cancelacion").val("");
		$("#modalCancelarGasto").modal("show");
	});

	$("#formCancelarGasto").submit(function (e) {
		e.preventDefault();

		var id = $("#id_gasto_cancelar").val();
		var motivo = $.trim($("#motivo_cancelacion").val());

		if (motivo.length === 0) {
			Swal.fire("Datos incompletos", "Debe capturar el motivo de la cancelacion.", "warning");
			return;
		}

		$.ajax({
			url: "bd/crudgasto.php",
			type: "POST",
			dataType: "json",
			data: {
				accion: "cancelar",
				id_gasto: id,
				motivo: motivo,
			},
			success: function (resp) {
				if (resp.status === "ok") {
					Swal.fire("Exito", resp.mensaje, "success");
					$("#modalCancelarGasto").modal("hide");
					cargarGastos();
				} else {
					Swal.fire("Error", resp.mensaje || "No se pudo cancelar el gasto", "error");
				}
			},
			error: function () {
				Swal.fire("Error", "Error de comunicacion con el servidor", "error");
			},
		});
	});

	cargarGastos();
});
