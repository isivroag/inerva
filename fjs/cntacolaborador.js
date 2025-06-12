$(document).ready(function () {
  var id, opcion;
  opcion = 4;
  var fila;

  // TOOLTIP DATATABLE
  $('[data-toggle="tooltip"]').tooltip();

  tablaVis = $("#tablaV").DataTable({
    columnDefs: [
      {
        targets: -1,
        data: null,
        defaultContent:
          "<div class='text-center'><button class='btn btn-sm btn-primary btnEditar' data-toggle='tooltip' data-placement='top' title='Editar'><i class='fas fa-edit'></i></button>\
              <button class='btn btn-sm btn-danger btnBorrar' data-toggle='tooltip' data-placement='top' title='Eliminar'><i class='fas fa-trash-alt'></i></button></div>",
      },
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

    $(".modal-title").text("NUEVO COLABORADOR");

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

    nombre = fila.find("td:eq(1)").text();
    tel = fila.find("td:eq(2)").text();
    correo = fila.find("td:eq(3)").text();

    $("#id").val(id);
    $("#nombre").val(nombre);
    $("#tel").val(tel);
    $("#correo").val(correo);

    opcion = 2; //editar

    $(".modal-title").text("EDITAR COLABORADOR");
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
            url: "bd/crudcolaborador.php",
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

    if (nombre.length == 0) {
      Swal.fire({
        title: "Datos Faltantes",
        text: "Debe ingresar todos los datos marcados con *",
        icon: "warning",
      });
      return false;
    } else {
      $.ajax({
        url: "bd/crudcolaborador.php",
        type: "POST",
        dataType: "json",
        data: {
          nombre: nombre,
          tel: tel,
          id: id,
          correo: correo,
          opcion: opcion,
        },
        success: function (data) {
            swal.fire({
              title: "Operación Exitosa",
              text: "Registro guardado correctamente",
              icon: "success",
            });
          id = data[0].id_col;
          nombre = data[0].nombre_col;
          tel = data[0].tel_col;
          correo = data[0].correo_col;

          if (opcion == 1) {
            tablaVis.row.add([id, nombre, tel, correo]).draw();
          } else {
            tablaVis.row(fila).data([id, nombre, tel, correo]).draw();
          }
        },
      });
      $("#modalCRUD").modal("hide");
    }
  });
});
