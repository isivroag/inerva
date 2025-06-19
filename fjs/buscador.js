$(document).ready(function () {
  var id_usuario, opcion
  opcion = 4

  jQuery.ajaxSetup({
    beforeSend: function() {
        $("#div_carga").show();
    },
    complete: function() {
        $("#div_carga").hide();
    },
    success: function() {},
});

  tablacal = $('#tablacal').DataTable({
    stateSave: true,
    paging: false,
    ordering: false,
    info: false,

    columnDefs: [
     /* {
        targets: -1,
        data: null,
        defaultContent:
          "<div class='text-center'><button class='btn btn-sm btn-success  btnAceptar data-toggle='tooltip' data-placement='top' title='Confirmar Cita''><i class='fas fa-phone'></i></button>\
        <button class='btn btn-sm btn-warning text-light btnNoConfirmar' data-toggle='tooltip' data-placement='top' title='No se localizo'><i class='fas fa-phone-slash'></i></button>\
        <button class='btn btn-sm btn-danger btnCancelar' data-toggle='tooltip' data-placement='top' title='Cancelar Cita'><i class='fas fa-ban'></i></button>",
      },*/
      
      { className: 'hide_column', targets: [3] },
      { className: 'text-center', targets: [7] },

     
    ],
    //Para cambiar el lenguaje a español
    language: {
      lengthMenu: 'Mostrar _MENU_ registros',
      zeroRecords: 'No se encontraron resultados',
      info:
        'Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros',
      infoEmpty: 'Mostrando registros del 0 al 0 de un total de 0 registros',
      infoFiltered: '(filtrado de un total de _MAX_ registros)',
      sSearch: 'Buscar:',
      oPaginate: {
        sFirst: 'Primero',
        sLast: 'Último',
        sNext: 'Siguiente',
        sPrevious: 'Anterior',
      },
      sProcessing: 'Procesando...',
    },

    rowCallback: function (row, data) {
      
      $($(row).find('td')[4]).css('background-color', data[3])
      $($(row).find('td')[4]).css('color', 'white')
      $($(row).find('td')[4]).css('font-weight:', 'bold')

    
      valor = parseInt(data[7]);
      var icono = "";
    
      switch (valor) {
        case 0:
            icono =
            '<i class="fa-solid fa-square-question text-secondary fa-2x text-center" title="Sin información"></i>';
            $($(row).find("td")[7]).html(icono);
            $($(row).find("td")[7]).find('i').tooltip();
          break;
        case 1:
          icono =
            '<i class="fa-solid fa-phone text-success fa-2x text-center" title="Cita confirmada"></i>';
          $($(row).find("td")[7]).html(icono);
          $($(row).find("td")[7]).find('i').tooltip();
          break;
        case 2:
          icono =
            '<i class="fa-solid fa-phone-slash text-warning fa-2x text-center" title="Cita no confirmada"></i>';
            $($(row).find("td")[7]).html(icono);
            $($(row).find("td")[7]).find('i').tooltip();
            break;
        case 4:
          icono =
            '<i class="fa-solid fa-square-xmark text-danger fa-2x text-center" title="Cita Cancelada"></i>';
          $($(row).find("td")[7]).html(icono);
          $($(row).find("td")[7]).find('i').tooltip();
          break;
        default:
          icono =
            '<i class="fa-solid fa-square-xmark text-danger fa-2x text-center"></i>';
          $($(row).find("td")[7]).html(icono);
          break;
      }
    },


  
  })

  $(document).on('click', '#btnbuscar', function () {
    texto = $('#txtbuscar').val()

    if ($('#incluir').prop('checked')) {
      opcion = 2
    } else {
      opcion = 1
    }

    tablacal.clear()
    tablacal.draw()

if (texto.length>0){
    $.ajax({
        url: 'bd/buscador.php',
        type: 'POST',
        dataType: 'json',
        
        data: { texto: texto, opcion: opcion },
  
        success: function (data) {
          for (var i = 0; i < data.length; i++) {
            tablacal.row
              .add([
                data[i].id,
                data[i].fecha,
                data[i].hora,
                data[i].color,
                data[i].colaborador,
                data[i].consultorio,
                data[i].descripcion,
                data[i].estado,
                
             
              ])
              .draw()
          }
        },
        error: function () {
          swal.fire({
            title: 'Error al Buscar',
            icon: 'error',
            focusConfirm: true,
            confirmButtonText: 'Aceptar',
          })
        },
      })
}

    
  })

  function buscar() {
    tablacal.clear()
    tablacal.draw()

    $.ajax({
      url: 'bd/buscarcalendario.php',
      type: 'POST',
      dataType: 'json',
      async: 'false',
      data: { fechad: fechad },

      success: function (data) {
        for (var i = 0; i < data.length; i++) {
          tablacal.row
            .add([
              data[i].id,
              data[i].start,
              data[i].end,
              data[i].start,
              data[i].id_cabina,
              data[i].nom_cabina,
              data[i].tipo_p,
              data[i].title,
              data[i].descripcion,
              data[i].id_per,
              data[i].nombre,
              data[i].color,
              data[i].duracion,
              data[i].confirmar,
            ])
            .draw()
        }
      },
    })
  }

  function mensaje() {
    swal.fire({
      title: 'Registro Cancelado',
      icon: 'success',
      focusConfirm: true,
      confirmButtonText: 'Aceptar',
      timer: 2000,
    })
  }

  function mensajeerror() {
    swal.fire({
      title: 'Error al Cancelar el Registro',
      icon: 'error',
      focusConfirm: true,
      confirmButtonText: 'Aceptar',
    })
  }
})
