$(document).ready(function () {
  var id, opcion;
  opcion = 4;
   var textcolumnas = permisos();

  function permisos() {
    var tipousuario =parseInt( $("#tipousuario").val());
    var columnas = "";
    console.log("Tipo de usuario:", tipousuario);

    switch (tipousuario) {
      case 1: // usuario normal
        columnas =
          "";
        break;
      case 2: // usuario administrador
      case 3: // usuario supervisor
        columnas =
          "<div class='btn-group'>\
            <button class='btn btn-sm btn-success btnSeguimiento' data-toggle='tooltip' title='Seguimiento'>\
              <i class='fa-duotone fa-solid fa-phone'></i>\
            </button>\
          </div>";
           break;
      case 4: // usuario colaborador
        columnas =
          "<div class='btn-group'>\
            <button class='btn btn-sm btn-success btnSeguimiento' data-toggle='tooltip' title='Seguimiento'>\
              <i class='fa-duotone fa-solid fa-phone'></i>\
            </button>\
          </div>";

        break;
        case 5: // usuario capturista
        columnas ="";
        break;
      default:
        columnas ="";
       
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
    ],
  });

  var tablaRealizado = $("#tablaRealizado").DataTable({
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
        targets: [7] ,
       
        render: function(data, type, row, meta) {
          return '<div class="multi-line ">' + data + '</div>';
        }
      }
       ],
  });

  var tablaAgenda = $("#tablaAgenda").DataTable({
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
      { className: "hide_column", targets: [0] },

      {
        targets: [8],

        render: function (data, type, row, meta) {
          return '<div class="multi-line ">' + data + "</div>";
        },
      },
    ],
  });

  $(document).on("click", ".btnSeguimiento", function () {
    var fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());

    window.location.href = "seguimiento.php?id_pros=" + id;
  });

  $(document).on("click", ".btnSeguir", function () {
    var fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());

    window.location.href = "seguimiento.php?id_seg=" + id;
  });
});
