$(document).ready(function () {
  var id_usuario, opcion
  opcion = 4
  //cargarhoras()

  var date_input = document.getElementById('fecha')

  date_input.onchange = function () {
    window.location.href = 'vcalendario.php?fecha=' + this.value
  }
  /*
  tablacal = $('#tablacal').DataTable({
    stateSave: true,
    paging: false,
    ordering: false,
    info: false,
    searching: false,
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
  })
*/
  $(document).on('click', '.tarjetacita', function () {
    var id = $(this).attr('value')
    opcion = 2

    $.ajax({
      url: 'bd/citasp.php',
      type: 'POST',
      dataType: 'json',
      data: { id: id, opcion: 4 },
      success: function (data) {
        if (data[0].tipo_p == 0) {
          $('#formDatos :input').prop('disabled', false)
          $('#folio').val(data[0].id)
          $('#id_pros').val(data[0].id_pros)
          $('#nom_pros').val(data[0].title)
          $('#concepto').val(data[0].descripcion)
          $('#responsable').val(data[0].id_per)
          $('#fechap').val(data[0].fecha)

          $('#obs').val(data[0].obs)
          $('#cabina').val(data[0].id_cabina)
          $('#opcion').val('1')
          $('#duracion').val(data[0].duracion)
          $('#btnCancelarcta').show()
          $('#btnGuardar').hide()

          rol = $('#tipousuario').val()
          if (rol == 1) {
            $('#btnGuardar').hide()
            $('#btnreagendar').hide()
            $('#btnCancelarcta').hide()
            $('.form-control').attr('disabled', true);
          } else {
            $('#btnGuardar').hide()
            $('#btnreagendar').show()
            $('#btnreagendar').prop('disabled', false)
            $('#btnCancelarcta').prop('disabled', false)
            $('.form-control').attr('disabled', false);
          }

          cargarhoras()
          /*   $('#hora').append(
            $('<option>', {
              value: data[0].hora,
              text: data[0].hora,
              disabled: true,
            }),
          )*/
          $('#hora').val(data[0].hora)
          // $('#hora option[tmt').attr('disabled', true);

          $('#modalCRUD').modal('show')
        } else {
          $('#formDatospx :input').prop('disabled', false)
          $('#foliox').val(data[0].id)
          $('#id_prosx').val(data[0].id_pros)
          $('#nom_prosx').val(data[0].title)
          $('#conceptox').val(data[0].descripcion)
          $('#responsablex').val(data[0].id_per)
          $('#fechax').val(data[0].fecha)
          $('#opcionx').val('1')
          $('#obsx').val(data[0].obs)
          $('#duracionx').val(data[0].duracion)
          $('#cabinax').val(data[0].id_cabina)
          $('#btnCancelarctax').show()

          rol = $('#tipousuario').val()
          if (rol == 1) {
            $('#btnGuardarx').hide()
            $('#btnreagendarx').hide()
            $('#btnCancelarctax').hide()
            $('.form-control').attr('disabled', true);
          } else {
            $('#btnGuardarx').hide()
            $('#btnreagendarx').show()
            $('#btnreagendarx').prop('disabled', false)
            $('#btnCancelarctax').prop('disabled', false)
            $('.form-control').attr('disabled', false);
          }

          cargarhorasx()
          /* $('#horax').append(
            $('<option>', {
              value: data[0].hora,
              text: data[0].hora,
              disabled: true,
            }),
          )*/
          $('#horax').val(data[0].hora)

          $('#modalpx').modal('show')
        }
      },
    })
  })

  $('#datetimepicker1').datetimepicker({
    locale: 'es',
  })

  $('#datetimepicker1x').datetimepicker({
    locale: 'es',
  })

  tablaC = $('#tablaC').DataTable({
    columnDefs: [
      {
        targets: -1,
        data: null,
        defaultContent:
          "<div class='text-center'><div class='btn-group'><button class='btn btn-sm btn-success btnSelCliente'><i class='fas fa-hand-pointer'></i></button></div></div>",
      },
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
  })

  tablaC = $('#tablaCx').DataTable({
    columnDefs: [
      {
        targets: -1,
        data: null,
        defaultContent:
          "<div class='text-center'><div class='btn-group'><button class='btn btn-sm btn-success btnSelClientex'><i class='fas fa-hand-pointer'></i></button></div></div>",
      },
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
  })

  $(document).on('click', '#bcliente', function () {
    $('.modal-header').css('background-color', '#007bff')
    $('.modal-header').css('color', 'white')

    $('#modalProspecto').modal('show')
  })
  $(document).on('click', '#bclientex', function () {
    $('.modal-header').css('background-color', '#007bff')
    $('.modal-header').css('color', 'white')

    $('#modalProspectox').modal('show')
    $('#btnCancelarctax').hide()
  })

  $(document).on('click', '#btnNuevo', function () {
    $('#formDatos').trigger('reset')
    $('.modal-header').css('background-color', '#007bff')
    $('.modal-header').css('color', 'white')
    opcion = 1
    $('#formDatos :input').prop('disabled', false)
    $('#btnCancelarcta').hide()
    $('#btnreagendar').hide()
    $('#btnGuardar').show()
    $('#opcion').val(0)
    $('#fechap').val($('#fecha').val())
    cargarhoras()
    $('#modalCRUD').modal('show')
    $('.form-control').attr('disabled', false);
  })

  $(document).on('click', '#btnNuevox', function () {
    $('#formDatospx').trigger('reset')
    $('.modal-header').css('background-color', '#007bff')
    $('.modal-header').css('color', 'white')
    opcion = 1
    $('#formDatospx :input').prop('disabled', false)
    $('#btnCancelarctax').hide()
    $('#btnreagendarx').hide()
    $('#btnGuardarx').show()
    $('#opcionx').val(0)
    $('#fechax').val($('#fecha').val())
    cargarhorasx()
    $('#modalpx').modal('show')
    $('.form-control').attr('disabled', false);
  })

  $(document).on('click', '.btnSelCliente', function () {
    fila = $(this).closest('tr')

    IdCliente = fila.find('td:eq(0)').text()
    NomCliente = fila.find('td:eq(1)').text()

    $('#id_pros').val(IdCliente)
    $('#nom_pros').val(NomCliente)
    $('#modalProspecto').modal('hide')
  })

  $(document).on('click', '.btnSelClientex', function () {
    fila = $(this).closest('tr')

    IdClientex = fila.find('td:eq(0)').text()
    NomClientex = fila.find('td:eq(1)').text()

    $('#id_prosx').val(IdClientex)
    $('#nom_prosx').val(NomClientex)
    $('#modalProspectox').modal('hide')
  })

  $(document).on('click', '#btnGuardar', function () {
    var id_pros = $.trim($('#id_pros').val())
    var nombre = $.trim($('#nom_pros').val())
    var concepto = $.trim($('#concepto').val())
    var fecha = $.trim($('#fechap').val())
    var hora = $('#hora').val()
    fecha = fecha + ' ' + hora
    var obs = $.trim($('#obs').val())
    var id = $.trim($('#folio').val())
    var tipop = $.trim($('#tipop').val())
    var responsable = $.trim($('#responsable').val())
    var duracion = $.trim($('#duracion').val())
    var cabina = $.trim($('#cabina').val())
    colaborador = responsable
    inicio = fecha
    opchr = $('#opcion').val()
    if (opchr == '1') {
      cita = $('#folio').val()
    } else {
      cita = 0
    }
    console.log(
      inicio +
        '/ ' +
        duracion +
        '/ ' +
        colaborador +
        '/ ' +
        cabina +
        '/ ' +
        cita,
    )
    if (
      id_pros.length == 0 ||
      fecha.length == 0 ||
      hora.length == 0 ||
      responsable.length == 0 ||
      cabina.length == 0
    ) {
      Swal.fire({
        title: 'Datos Faltantes',
        text: 'Debe ingresar todos los datos requeridos',
        icon: 'warning',
      })
      return false
    } else {
      $.ajax({
        type: 'POST',
        url: 'bd/validarcita.php',
        async: false,
        dataType: 'json',
        data: {
          inicio: inicio,
          duracion: duracion,
          colaborador: colaborador,
          cabina: cabina,
          cita: cita,
        },
        success: function (data) {
          if (data == 0) {
            $.ajax({
              url: 'bd/citasp.php',
              type: 'POST',
              dataType: 'json',
              async: 'false',
              data: {
                nombre: nombre,
                id_pros: id_pros,
                fecha: fecha,
                obs: obs,
                tipop: tipop,
                concepto: concepto,
                id: id,
                opcion: opcion,
                responsable: responsable,
                duracion: duracion,
                cabina: cabina,
              },
              success: function (data) {
                if (data == 1) {
                  console.log(data)
                  Swal.fire({
                    title: 'Operación Exitosa',
                    text: 'Cita Guardada',
                    icon: 'success',
                    timer: 1000,
                  })
                  window.setTimeout(function () {
                    location.reload()
                  }, 1500)
                } else {
                  Swal.fire({
                    title: 'No es posible Agendar la Cita',
                    icon: 'warning',
                  })
                }
              },
            })
          } else {
            swal.fire({
              title: 'No es posible Agendar Cita',
              text: 'Verifique la fecha, la hora, la cabina o el responsable',
              icon: 'error',
              focusConfirm: true,
              confirmButtonText: 'Aceptar',
            })
          }
        },
      })
    }
    //$("#modalCRUD").modal("hide");
  })

  $(document).on('click', '#btnreagendar', function () {
    var id_pros = $('#id_pros').val()
    var nombre = $('#nom_pros').val()
    var concepto = $('#concepto').val()
    var fecha = $('#fechap').val()
    var hora = $('#hora').val()
    fecha = fecha + ' ' + hora
    var obs = $('#obs').val()
    var id = $('#folio').val()
    var tipop = $('#tipop').val()
    var responsable = $('#responsable').val()
    var duracion = $('#duracion').val()
    var cabina = $('#cabina').val()
    colaborador = responsable
    inicio = fecha

    opchr = $('#opcion').val()
    if (opchr == '1') {
      cita = $('#folio').val()
    } else {
      cita = 0
    }
    console.log(
      inicio +
        '/ ' +
        duracion +
        '/ ' +
        colaborador +
        '/ ' +
        cabina +
        '/ ' +
        cita,
    )
    opcion = 2
    if (
      id_pros.length == 0 ||
      fecha.length == 0 ||
      responsable.length == 0 ||
      cabina.length == 0
    ) {
      Swal.fire({
        title: 'Datos Faltantes',
        text: 'Debe ingresar todos los datos requeridos',
        icon: 'warning',
      })
      return false
    } else {
      $.ajax({
        type: 'POST',
        url: 'bd/validarcita.php',
        async: false,
        dataType: 'json',
        data: {
          inicio: inicio,
          duracion: duracion,
          colaborador: colaborador,
          cabina: cabina,
          cita: cita,
        },
        success: function (data) {
          if (data == 0) {
            $.ajax({
              url: 'bd/citasp.php',
              type: 'POST',
              dataType: 'json',
              async: 'false',
              data: {
                nombre: nombre,
                id_pros: id_pros,
                fecha: fecha,
                obs: obs,
                tipop: tipop,
                concepto: concepto,
                id: id,
                opcion: opcion,
                responsable: responsable,
                duracion: duracion,
                cabina: cabina,
              },
              success: function (data) {
                if (data == 1) {
                  console.log(data)
                  Swal.fire({
                    title: 'Operación Exitosa',
                    text: 'Cita Guardada',
                    icon: 'success',
                    timer: 1000,
                  })
                window.setTimeout(function () {
                    location.reload()
                  }, 1500)
                } else {
                  Swal.fire({
                    title: 'No es posible Agendar la Cita',
                    icon: 'warning',
                  })
                }
              },
            })
          } else {
            swal.fire({
              title: 'No es posible Agendar Cita',
              text: 'Verifique la fecha, la hora, la cabina y el responsable',
              icon: 'error',
              focusConfirm: true,
              confirmButtonText: 'Aceptar',
            })
          }
        },
      })
    }
    //$("#modalCRUD").modal("hide");
  })

  $(document).on('click', '#btnGuardarx', function () {
    var id_pros = $.trim($('#id_prosx').val())
    var nombre = $.trim($('#nom_prosx').val())
    var concepto = $.trim($('#conceptox').val())
    var fecha = $.trim($('#fechax').val())
    var hora = $('#horax').val()
    fecha = fecha + ' ' + hora
    var obs = $.trim($('#obsx').val())
    var id = $.trim($('#foliox').val())
    var tipop = $.trim($('#tipopx').val())
    var responsable = $.trim($('#responsablex').val())
    var duracion = $.trim($('#duracionx').val())
    var cabina = $.trim($('#cabinax').val())
    colaborador = responsable
    inicio = fecha
    opchr = $('#opcionx').val()
    if (opchr == '1') {
      cita = $('#foliox').val()
    } else {
      cita = 0
    }
    console.log(
      inicio +
        '/ ' +
        duracion +
        '/ ' +
        colaborador +
        '/ ' +
        cabina +
        '/ ' +
        cita,
    )
    if (
      id_pros.length == 0 ||
      fecha.length == 0 ||
      hora.length == 0 ||
      responsable.length == 0 ||
      cabina.length == 0
    ) {
      Swal.fire({
        title: 'Datos Faltantes',
        text: 'Debe ingresar todos los datos requeridos',
        icon: 'warning',
      })
      return false
    } else {
      $.ajax({
        type: 'POST',
        url: 'bd/validarcita.php',
        async: false,
        dataType: 'json',
        data: {
          inicio: inicio,
          duracion: duracion,
          colaborador: colaborador,
          cabina: cabina,
          cita: cita,
        },
        success: function (data) {
          if (data == 0) {
            $.ajax({
              url: 'bd/citasp.php',
              type: 'POST',
              dataType: 'json',
              data: {
                nombre: nombre,
                id_pros: id_pros,
                fecha: fecha,
                obs: obs,
                tipop: tipop,
                concepto: concepto,
                id: id,
                opcion: opcion,
                responsable: responsable,
                duracion: duracion,
                cabina: cabina,
              },
              success: function (data) {
                if (data == 1) {
                  console.log(data)
                  Swal.fire({
                    title: 'Operación Exitosa',
                    text: 'Cita Guardada',
                    icon: 'success',
                    timer: 1000,
                  })
                  window.setTimeout(function () {
                    location.reload()
                  }, 1500)
                } else {
                  Swal.fire({
                    title: 'No es posible Agendar la Cita',
                    icon: 'warning',
                  })
                }
              },
            })
          } else {
            swal.fire({
              title: 'No es posible Agendar Cita',
              text: 'Verifique la fecha, la hora, la cabina o el responsable',
              icon: 'error',
              focusConfirm: true,
              confirmButtonText: 'Aceptar',
            })
          }
        },
      })
    }
    //$("#modalCRUD").modal("hide");
  })

  $(document).on('click', '#btnreagendarx', function () {
    var id_pros = $('#id_prosx').val()
    var nombre = $('#nom_prosx').val()
    var concepto = $('#conceptox').val()

    var fecha = $('#fechax').val()
    var hora = $('#horax').val()
    fecha = fecha + ' ' + hora
    
    var obs = $('#obsx').val()
    var id = $('#foliox').val()
    var tipop = $('#tipopx').val()
    var responsable = $('#responsablex').val()
    var duracion = $('#duracionx').val()
    var cabina = $('#cabinax').val()
    colaborador = responsable
    inicio = fecha
    opcion = 2

    opchr = $('#opcionx').val()
    if (opchr == '1') {
      cita = $('#foliox').val()
    } else {
      cita = 0
    }

    console.log(
      inicio +
        '/ ' +
        duracion +
        '/ ' +
        colaborador +
        '/ ' +
        cabina +
        '/ ' +
        cita +
        '/ ' +
        id +
        '/ ' +
        id_pros,
    )

    if (
      id_pros.length == 0 ||
      fecha.length == 0 ||
      responsable.length == 0 ||
      cabina.length == 0
    ) {
      Swal.fire({
        title: 'Datos Faltantes',
        text: 'Debe ingresar todos los datos requeridos',
        icon: 'warning',
      })
      return false
    } else {
      $.ajax({
        type: 'POST',
        url: 'bd/validarcita.php',
        async: false,
        dataType: 'json',
        data: {
          inicio: inicio,
          duracion: duracion,
          colaborador: colaborador,
          cabina: cabina,
          cita: cita,
        },
        success: function (data) {
          if (data == 0) {
            $.ajax({
              url: 'bd/citasp.php',
              type: 'POST',
              dataType: 'json',
              async: 'false',
              data: {
                nombre: nombre,
                id_pros: id_pros,
                fecha: fecha,
                obs: obs,
                tipop: tipop,
                concepto: concepto,
                id: id,
                opcion: opcion,
                responsable: responsable,
                duracion: duracion,
                cabina: cabina,
              },
              success: function (data) {
                if (data == 1) {
                  console.log(data)
                  Swal.fire({
                    title: 'Operación Exitosa',
                    text: 'Cita Guardada',
                    icon: 'success',
                    timer: 1000,
                  })
                  window.setTimeout(function () {
                    location.reload()
                  }, 1500)
                } else {
                  Swal.fire({
                    title: 'No es posible Agendar la Cita',
                    icon: 'warning',
                  })
                }
              },
            })
          } else {
            swal.fire({
              title: 'No es posible Agendar Cita',
              text: 'Verifique la fecha, la hora, la cabina y el responsable',
              icon: 'error',
              focusConfirm: true,
              confirmButtonText: 'Aceptar',
            })
          }
        },
      })
    }
    //$("#modalCRUD").modal("hide");
  })

  $(document).on('click', '#btnCancelarcta', function () {
    folio = $('#folio').val()

    $('#formcan').trigger('reset')
    /*$(".modal-header").css("background-color", "#28a745");*/
    $('.modal-header').css('color', 'white')
    $('#modalcan').modal('show')
    $('#foliocan').val(folio)
  })

  $(document).on('click', '#btnCancelarctax', function () {
    folio = $('#foliox').val()

    $('#formcan').trigger('reset')
    /*$(".modal-header").css("background-color", "#28a745");*/
    $('.modal-header').css('color', 'white')
    $('#modalcan').modal('show')
    $('#foliocan').val(folio)
  })

  $(document).on('click', '#btnGuardarc', function () {
    motivo = $('#motivo').val()
    id = $('#foliocan').val()
    fecha = $('#fechac').val()
    usuario = $('#nameuser').val()
    $('#modalcan').modal('hide')
    opcion = 4

    if (motivo === '') {
      swal.fire({
        title: 'Datos Incompletos',
        text: 'Verifique sus datos',
        icon: 'warning',
        focusConfirm: true,
        confirmButtonText: 'Aceptar',
      })
    } else {
      $.ajax({
        type: 'POST',
        url: 'bd/buscarcita.php',
        async: false,
        dataType: 'json',
        data: {
          id: id,
          opcion: opcion,
          motivo: motivo,
          fecha: fecha,
          usuario: usuario,
        },
        success: function (data) {
          if (data[0].id == id) {
            mensaje()
            window.setTimeout(function () {
              window.location.reload()
            }, 1500)
          } else {
            mensajeerror()
          }
        },
      })
    }
  })

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

  tablaVis = $('#tablaV').DataTable({
    info: false,
    searching: false,
    paging: false,
    ordering: false,

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
      $($(row).find('td')[1]).css('background-color', data[1])

      //$($(row).find('td')[2]).addClass('bg-gradient-green')
    },
  })

  $('#fechap').on('change', function () {
    cargarhoras()
  })

  $('#responsable').on('change', function () {
    cargarhoras()
  })

  $('#cabina').on('change', function () {
    cargarhoras()
  })

  function cargarhoras() {
    fecha = $('#fechap').val()
    colaborador = $('#responsable').val()
    cabina = $('#cabina').val()
    opchr = $('#opcion').val()
    if (opchr == '1') {
      cita = $('#folio').val()
    } else {
      cita = 0
    }

    $('#hora').empty()
    $.ajax({
      type: 'POST',
      url: 'bd/cargarhoras.php',
      dataType: 'json',
      async: false,
      data: {
        fecha: fecha,
        colaborador: colaborador,
        cabina: cabina,
        cita: cita,
      },
      success: function (res) {
        for (var i = 0; i < res.length; i++) {
          $('#hora').append(
            $('<option>', {
              value: res[i].nhora,
              text: res[i].nhora,
            }),
          )
        }
      },
      error: function () {
        Swal.fire({
          title: 'Error al cargar horarios disponibles',
          icon: 'error',
        })
      },
    })
  }
  $('#fechax').on('change', function () {
    cargarhorasx()
  })

  $('#responsablex').on('change', function () {
    cargarhorasx()
  })

  $('#cabinax').on('change', function () {
    cargarhorasx()
  })

  function cargarhorasx() {
    fecha = $('#fechax').val()
    colaborador = $('#responsablex').val()
    cabina = $('#cabinax').val()
    opchr = $('#opcionx').val()
    if (opchr == '1') {
      cita = $('#foliox').val()
    } else {
      cita = 0
    }

    $('#horax').empty()
    $.ajax({
      type: 'POST',
      url: 'bd/cargarhoras.php',
      dataType: 'json',
      async: false,
      data: {
        fecha: fecha,
        colaborador: colaborador,
        cabina: cabina,
        cita: cita,
      },
      success: function (res) {
        for (var i = 0; i < res.length; i++) {
          $('#horax').append(
            $('<option>', {
              value: res[i].nhora,
              text: res[i].nhora,
            }),
          )
        }
      },
      error: function () {
        Swal.fire({
          title: 'Error al cargar horarios disponibles',
          icon: 'error',
        })
      },
    })
  }
})
