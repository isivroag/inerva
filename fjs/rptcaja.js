$(document).ready(function () {
    var $btnGuardar = $("#btnGuardar");
    var cajaNoExiste = String($btnGuardar.data("caja-no-existe")) === "1";

    function configurarEncabezadosDetalle(campo) {
        if (campo === "gastos") {
            $("#tablaDetallePagos thead th:eq(1)").text("Referencia");
            $("#tablaDetallePagos thead th:eq(2)").text("Concepto");
            $("#tablaDetallePagos thead th:eq(3)").text("Facturado");
            $("#tablaDetallePagos thead th:eq(4)").text("Monto");
        } else if (campo === "retiros") {
            $("#tablaDetallePagos thead th:eq(1)").text("Fecha Op");
            $("#tablaDetallePagos thead th:eq(2)").text("Monto Ant");
            $("#tablaDetallePagos thead th:eq(3)").text("Monto Desp");
            $("#tablaDetallePagos thead th:eq(4)").text("Monto");
        } else {
            $("#tablaDetallePagos thead th:eq(1)").text("Paciente");
            $("#tablaDetallePagos thead th:eq(2)").text("Colaborador");
            $("#tablaDetallePagos thead th:eq(3)").text("Método");
            $("#tablaDetallePagos thead th:eq(4)").text("Importe");
        }
    }

    if (cajaNoExiste) {
        Swal.fire({
            icon: "info",
            title: "Sin registro de caja",
            text: "No existe registro de caja para este día. ¿Desea agregarlo para poder modificar la caja?",
            showCancelButton: true,
            confirmButtonText: "Sí, agregar registro",
            cancelButtonText: "No",
            confirmButtonColor: "#7262a1"
        }).then(function (result) {
            if (result.value) {
                $("#accion_caja").val("abrir_caja");
                $("#fecha_apertura").val($btnGuardar.data("caja-fecha")).prop("readonly", true);
                $("#monto_inicial").val("");
                $("#modalAbrirCaja").modal("show");
                $("#monto_inicial").trigger("focus");
            }
        });
    }

    $(document).on("change", "#ctrlfecha", function () {
        var fecha = $(this).val();
        if (!fecha) {
            return;
        }

        var url = new URL(window.location.href);
        url.searchParams.set("fecha", fecha);
        url.searchParams.set("esconsulta", "1");
        url.searchParams.delete("consulta");
        window.location.href = url.toString();
    });

    var etiquetas = {
        efectivo: "Efectivo",
        tcredito: "T. Crédito",
        tdebito: "T. Débito",
        transferencias: "Transferencias",
        cortesia: "Cortesía",
        gastos: "Gastos"
        ,retiros: "Retiros"
    };

    $(document).on("click", ".icono-diff", function () {
        var campo = $(this).data("campo");
        var fecha = $("#ticketCaja").closest("form").find("#btnActualizar").data("fecha") || $("[data-caja-fecha]").data("caja-fecha");

        configurarEncabezadosDetalle(campo);
        $("#tituloDetalle").text(etiquetas[campo] || campo);
        $("#bodyDetallePagos").html('<tr><td colspan="5" class="text-center">Cargando...</td></tr>');
        $("#totalDetallePagos").text("");

        setTimeout(function () {
            $("#modalDetallePagos").modal("show");
        }, 150);

        $.ajax({
            url: "bd/getpagoscaja.php",
            type: "POST",
            dataType: "json",
            data: { fecha: fecha, campo: campo },
            success: function (r) {
                if (r.status !== "ok") {
                    $("#bodyDetallePagos").html('<tr><td colspan="5" class="text-danger text-center">' + r.mensaje + '</td></tr>');
                    return;
                }

                var html = "";
                if (r.tipo === "gastos") {
                    $.each(r.gastos || [], function (i, g) {
                        html += "<tr>" +
                            "<td>" + g.id_gasto + "</td>" +
                            "<td>" + g.referencia + "</td>" +
                            "<td>" + g.concepto + "</td>" +
                            "<td>" + (parseInt(g.facturado, 10) === 1 ? "Sí" : "No") + "</td>" +
                            "<td class='text-right'>" + parseFloat(g.monto).toFixed(2) + "</td>" +
                        "</tr>";
                    });
                } else if (r.tipo === "retiros") {
                    $.each(r.retiros || [], function (i, rto) {
                        html += "<tr>" +
                            "<td>" + rto.id_retiro + "</td>" +
                            "<td>" + rto.fecha_op + "</td>" +
                            "<td class='text-right'>" + parseFloat(rto.montoant).toFixed(2) + "</td>" +
                            "<td class='text-right'>" + parseFloat(rto.montodesp).toFixed(2) + "</td>" +
                            "<td class='text-right'>" + parseFloat(rto.monto).toFixed(2) + "</td>" +
                        "</tr>";
                    });
                } else {
                    $.each(r.pagos || [], function (i, p) {
                        html += "<tr>" +
                            "<td>" + p.id_pago + "</td>" +
                            "<td>" + p.paciente + "</td>" +
                            "<td>" + p.colaborador + "</td>" +
                            "<td>" + p.metodo + "</td>" +
                            "<td class='text-right'>" + parseFloat(p.importe).toFixed(2) + "</td>" +
                        "</tr>";
                    });
                }

                if (!html) {
                    html = '<tr><td colspan="5" class="text-center">Sin registros</td></tr>';
                }
                $("#bodyDetallePagos").html(html);
                $("#totalDetallePagos").text(parseFloat(r.total).toFixed(2));
            },
            error: function () {
                $("#bodyDetallePagos").html('<tr><td colspan="5" class="text-danger text-center">Error al cargar datos</td></tr>');
            }
        });
    });

    $("#btnActualizar").click(function () {
        var filas = "";
        $(".icono-diff").each(function () {
            var campo = $(this).data("campo");
            var caja = parseFloat($(this).data("caja")).toFixed(2);
            var real = parseFloat($(this).data("real")).toFixed(2);

            filas += "<tr><td>" + (etiquetas[campo] || campo) + "</td>" +
                     "<td class='text-right'>" + caja + "</td>" +
                     "<td class='text-right text-success font-weight-bold'>" + real + "</td></tr>";
        });
        if (!filas) {
            filas = "<tr><td colspan='3' class='text-center'>Sin diferencias</td></tr>";
        }
        $("#tablaDiff").html(filas);
        $("#btnConfirmarActualizar").data("fecha", $(this).data("fecha"));
        $("#modalDiferencias").modal("show");
    });

    $("#btnConfirmarActualizar").click(function () {
        var fecha = $(this).data("fecha");
        $.ajax({
            url: "bd/crudcaja.php",
            type: "POST",
            dataType: "json",
            data: { accion: "sincronizar_ingresos", fecha: fecha },
            success: function (response) {
                if (response.status === "ok") {
                    Swal.fire({ icon: "success", title: "Actualizado", text: response.mensaje, confirmButtonColor: "#7262a1" })
                        .then(function () { $("#modalDiferencias").modal("hide"); location.reload(); });
                } else {
                    Swal.fire({ icon: "error", title: "Error", text: response.mensaje, confirmButtonColor: "#7262a1" });
                }
            },
            error: function () {
                Swal.fire({ icon: "error", title: "Error", text: "Error en la petición", confirmButtonColor: "#7262a1" });
            }
        });
    });

    $("#btnGuardar").click(function (e) {
        e.preventDefault();
        var cajaExiste = $(this).data("caja-existe");
        var sRol = parseInt($(this).data("s-rol"), 10);

        if (cajaExiste == "1") {
            if (sRol !== 2 && sRol !== 3) {
                Swal.fire({ icon: "warning", title: "Sin permisos", text: "Ya existe una caja abierta para este día. Solo un administrador puede modificar el monto inicial.", confirmButtonColor: "#7262a1" });
                return;
            }
            Swal.fire({ icon: "question", title: "Caja ya abierta", text: "Ya existe una caja para esta fecha. ¿Desea modificar el monto inicial?", showCancelButton: true, confirmButtonText: "Sí, modificar", cancelButtonText: "Cancelar", confirmButtonColor: "#7262a1", cancelButtonColor: "#d33" }).then(function (result) {
                if (result.value) {
                    $("#accion_caja").val("modificar_inicial");
                    $("#fecha_apertura").val($("#btnGuardar").data("caja-fecha")).prop("readonly", true);
                    setTimeout(function () { $("#modalAbrirCaja").modal("show"); }, 300);
                }
            });
        } else {
            $("#accion_caja").val("abrir_caja");
            $("#fecha_apertura").prop("readonly", false);
            $("#modalAbrirCaja").modal("show");
        }
    });

    $("#formAbrirCaja").submit(function (e) {
        e.preventDefault();
        var fecha = $("#fecha_apertura").val();
        var inicial = $("#monto_inicial").val();
        var accion = $("#accion_caja").val();
        var inicialNum = parseFloat(inicial);

        if (!inicial || isNaN(inicialNum) || inicialNum <= 0) {
            Swal.fire({ icon: "warning", title: "Monto requerido", text: "Debe capturar un monto inicial mayor a 0 para abrir la caja.", confirmButtonColor: "#7262a1" });
            return;
        }

        $.ajax({
            url: "bd/crudcaja.php",
            type: "POST",
            dataType: "json",
            data: { accion: accion, fecha: fecha, inicial: inicial },
            success: function (response) {
                if (response.status == "ok") {
                    Swal.fire({ icon: "success", title: "Exito", text: response.mensaje, confirmButtonColor: "#7262a1" })
                        .then(function () { $("#modalAbrirCaja").modal("hide"); location.reload(); });
                } else {
                    Swal.fire({ icon: "error", title: "Error", text: response.mensaje, confirmButtonColor: "#7262a1" });
                }
            },
            error: function () {
                Swal.fire({ icon: "error", title: "Error", text: "Error en la petición", confirmButtonColor: "#7262a1" });
            }
        });
    });
});
