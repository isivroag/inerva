$(document).ready(function () {
  var id, opcion;
  opcion = 4;
  var fila;

  // TOOLTIP DATATABLE
  $('[data-toggle="tooltip"]').tooltip();

  $("#medio").change(function () {
    // Obtener el valor de mas_medio del option seleccionado
    var masMedio = $(this).find("option:selected").data("mas-medio");

    // Mostrar u ocultar el div según el valor
    if (masMedio == 1) {
      $("#otro_medio_div").show();
    } else {
      $("#otro_medio_div").hide();
      $("#otro_medio").val(""); // Limpiar el campo si se oculta
    }
  });

  // Ejecutar al cargar la página por si hay un valor seleccionado por defecto
  $("#medio").trigger("change");

  tablaVis = $("#tablaV").DataTable({
    columnDefs: [
      {
        targets: -1,
        data: null,
        defaultContent:
          "<div class='text-center'><button class='btn btn-sm btn-primary btnEditar' data-toggle='tooltip' data-placement='top' title='Editar'><i class='fas fa-edit'></i></button>\
              <button class='btn btn-sm btn-danger btnBorrar' data-toggle='tooltip' data-placement='top' title='Eliminar'><i class='fas fa-trash-alt'></i></button></div>",
      },
      { targets: [5, 7], className: "hide_column" },
      { targets: [4], className: "text-center" }, // Ocultar columnas 5 y 7
    ],

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
  });

  //BONTON NUEVO
  $("#btnNuevo").click(function () {
    //window.location.href = "prospecto.php";
    $("#formDatos").trigger("reset");

    $(".modal-title").text("NUEVO PACIENTE");

    $("#modalCRUD").removeAttr("aria-hidden");
    $("#modalCRUD").removeAttr("inert");
    $("#modalCRUD").modal("show");
    id = null;
    opcion = 1;
  });

  //BOTON EDITAR
  $(document).on("click", ".btnEditar", function () {
    fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());

    // Obtener datos básicos
    nombre = fila.find("td:eq(1)").text();
    tel = fila.find("td:eq(2)").text();
    correo = fila.find("td:eq(3)").text();
    fechanac = fila.find("td:eq(4)").text();

    // Obtener datos del medio
    id_medio = fila.find("td:eq(5)").text();
    nom_medio = fila.find("td:eq(6)").text();
    otro_medio = fila.find("td:eq(7)").text();

    // Establecer valores en el formulario
    $("#id").val(id);
    $("#nombre").val(nombre);
    $("#tel").val(tel);
    $("#correo").val(correo);
    $("#fechanac").val(fechanac);

    // Establecer el medio seleccionado
    $("#medio").val(id_medio);

    // Establecer otro medio si existe
    $("#otro_medio").val(otro_medio);

    // Mostrar/ocultar el campo otro_medio según corresponda
    // Necesitamos verificar si el medio seleccionado tiene mas_medio=1
    // Para esto, buscamos el option seleccionado y verificamos su data-mas-medio
    console.log("Medio seleccionado:", nom_medio);
    var selectedOption = $("#medio option:selected");
    if (selectedOption.length && selectedOption.data("mas-medio") == 1) {
      $("#otro_medio_div").show();
    } else {
      $("#otro_medio_div").hide();
    }

    opcion = 2; //editar

    $(".modal-title").text("EDITAR PACIENTE");
    $("#modalCRUD").removeAttr("aria-hidden");
    $("#modalCRUD").removeAttr("inert");
    $("#modalCRUD").modal("show");
  });

  //BOTON BORRAR
  $(document).on("click", ".btnBorrar", function () {
    fila = $(this);

    id = parseInt($(this).closest("tr").find("td:eq(0)").text());
    opcion = 3;
    swal
      .fire({
        title: "ELIMINAR",
        text: "¿Desea eliminar el registro seleccionado?",
        showCancelButton: true,
        icon: "question",
        focusConfirm: true,
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#28B463",
        cancelButtonColor: "#d33",
      })
      .then(function (isConfirm) {
        if (isConfirm.value) {
          $.ajax({
            url: "bd/crudpaciente.php",
            type: "POST",
            dataType: "json",
            data: { id: id, opcion: opcion },
            success: function (data) {
              tablaVis.row(fila.parents("tr")).remove().draw();
            },
          });
        } else if (isConfirm.dismiss === swal.DismissReason.cancel) {
        }
      });
  });

  //GUARDAR COLABORADOR

  $("#formDatos").submit(function (e) {
    e.preventDefault();
    var id = $.trim($("#id").val());
    var nombre = $("#nombre").val();
    var tel = $("#tel").val();
    var correo = $("#correo").val();
    var fechanac = $("#fechanac").val();
    var medio = $("#medio").val();
    var otro_medio = $("#otro_medio").val();
    console.log("Medio seleccionado:", medio);

    if (nombre.length == 0 || tel.length == 0 || fechanac.length == 0) {
      Swal.fire({
        title: "Datos Faltantes",
        text: "Debe ingresar todos los datos marcados con *",
        icon: "warning",
      });
      return false;
    } else {
      $.ajax({
        url: "bd/crudpaciente.php",
        type: "POST",
        dataType: "json",
        data: {
          nombre: nombre,
          tel: tel,
          id: id,
          correo: correo,
          fechanac: fechanac,
          medio: medio,
          otro_medio: otro_medio,
          opcion: opcion,
        },
        success: function (data) {
          swal.fire({
            title: "Operación Exitosa",
            text: "Registro guardado correctamente",
            icon: "success",
          });
          id = data[0].id_px;
          nombre = data[0].nombre_px;
          tel = data[0].tel_px;
          correo = data[0].correo_px;
          fechanac = data[0].fechanac_px;
          id_medio = data[0].id_medio;
          nom_medio = data[0].nom_medio;
          otro_medio = data[0].otro_medio;

          if (opcion == 1) {
            tablaVis.row
              .add([
                id,
                nombre,
                tel,
                correo,
                fechanac,
                id_medio,
                nom_medio,
                otro_medio,
              ])
              .draw();
          } else {
            tablaVis
              .row(fila)
              .data([
                id,
                nombre,
                tel,
                correo,
                fechanac,
                id_medio,
                nom_medio,
                otro_medio,
              ])
              .draw();
          }
        },
      });
      $("#modalCRUD").modal("hide");
    }
  });
});
