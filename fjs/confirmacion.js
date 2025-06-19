$(document).ready(function () {
    var id_usuario, opcion, rol
    opcion = 4
    var textcolumnas = permisos()

  
    var date_input = document.getElementById('fecha')
    
  

    function permisos() {
        var tipousuario = $('#tipousuario').val()
        var columnas = ''
    
        if (tipousuario != 1) {
          columnas =
          "<div class='text-center'> <div class='btn-group'> <button class='btn btn-sm btn-success  btnAceptar data-toggle='tooltip' data-placement='top' title='Confirmar Cita''><i class='fas fa-phone'></i></button>\
          <button class='btn btn-sm btn-warning text-light btnNoConfirmar' data-toggle='tooltip' data-placement='top' title='No se localizo'><i class='fas fa-phone-slash'></i></button>\
          <button class='btn btn-sm bg-green text-light btnAgenda' data-toggle='tooltip' data-placement='top' title='Reagendar'><i class='fas fa-calendar'></i></button>\
          <button class='btn btn-sm btn-danger btnCancelar' data-toggle='tooltip' data-placement='top' title='Cancelar Cita'><i class='fas fa-ban'></i></button></div></div>"
        } else {
          columnas =
          "<div class='text-center'><button class='btn btn-sm btn-success  btnAceptar data-toggle='tooltip' data-placement='top' title='Confirmar Cita''><i class='fas fa-phone'></i></button>\
        <button class='btn btn-sm btn-warning text-light btnNoConfirmar' data-toggle='tooltip' data-placement='top' title='No se localizo'><i class='fas fa-phone-slash'></i></button>\
        </div>"
        }
        return columnas
      }
    
    date_input.onchange = function () {
      window.location.href="confirmacion.php?fecha="+this.value
    }

     $(document).on("click", ".btnAgenda", function () {
       window.location.href = "calendario.php";
    });

  
    tablacal = $('#tablacal').DataTable({
      stateSave: true,
      paging: false,
      ordering:false,
      info:false,
     



      columnDefs: [{
        targets: -1,
        data: null,
        defaultContent: textcolumnas,
    }, 
     { className: "hide_column", targets: [2] },
     {className: "text-center", targets: [7] },


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
      $($(row).find("td")[3]).css("background-color", data[2]);
      $($(row).find("td")[3]).css("color", "white");
      $($(row).find("td")[3]).css("font-weight:", "bold");

      valor = parseInt(data[7]);
      var icono = "";
    
        switch (valor) {
        case 0:
          icono =
            '<i class="fa-solid fa-square-question text-secondary fa-2x text-center" title="Sin información"></i>';
          $($(row).find("td")[7]).html(icono);
          $($(row).find("td")[7]).find("i").tooltip();
          break;
        case 1:
          icono =
            '<i class="fa-solid fa-phone text-success fa-2x text-center" title="Cita confirmada"></i>';
          $($(row).find("td")[7]).html(icono);
          $($(row).find("td")[7]).find("i").tooltip();
          break;
        case 2:
          icono =
            '<i class="fa-solid fa-phone-slash text-warning fa-2x text-center" title="Cita no confirmada"></i>';
          $($(row).find("td")[7]).html(icono);
          $($(row).find("td")[7]).find("i").tooltip();
          break;
        case 4:
          icono =
            '<i class="fa-solid fa-square-xmark text-danger fa-2x text-center" title="Cita Cancelada"></i>';
          $($(row).find("td")[7]).html(icono);
          $($(row).find("td")[7]).find("i").tooltip();
          break;
        case 5:
          icono =
            '<i class="fa-solid fa-user-check text-success fa-2x text-center" title="Paciente Asistió"></i>';
          $($(row).find("td")[7]).html(icono);
          $($(row).find("td")[7]).find("i").tooltip();
          break;
        case 6:
          icono =
            '<i class="fa-solid fa-user-xmark text-danger fa-2x text-center" title="Paciente No Asistió"></i>';
          $($(row).find("td")[7]).html(icono);
          $($(row).find("td")[7]).find("i").tooltip();
          break;
        default:
          icono =
            '<i class="fa-solid fa-square-xmark text-danger fa-2x text-center"></i>';
          $($(row).find("td")[7]).html(icono);
          break;
      }
    },



    })
  

    $(document).on("click", ".btnAceptar", function () {
        fila = $(this);
        id = parseInt($(this).closest("tr").find('td:eq(0)').text());
        opcion = 6;

        $.ajax({

            url: "bd/buscarcita.php",
            type: "POST",
            dataType: "json",
            async: "false",
            data: { id: id, opcion: opcion },

            success: function (data) {
                Swal.fire({
                    title: "Cita Confirmada",
                    text: "Paciente Confirmó su Cita",
                    icon: "success",
                    timer:1000,
                });

               

                buscar();
          
            }
        });
    });


    $(document).on("click", ".btnNoConfirmar", function () {
        fila = $(this);
        opcion=7;
        id = parseInt($(this).closest("tr").find('td:eq(0)').text());
        $.ajax({

            url: "bd/buscarcita.php",
            type: "POST",
            dataType: "json",
            async: "false",
            data: { id: id, opcion: opcion },

            success: function (data) {
                Swal.fire({
                    title: "Paciente No Confirmó la cita",
                    text: "la cita fue marcada como no confirmada",
                    icon: "warning",
                    timer:1000,
                });


                buscar();
            
            }
        });
    });


    function buscar(){
        fechad =   $("#fecha").val();
        tablacal.clear();
        tablacal.draw();

        $.ajax({

            url: "bd/buscarcalendario.php",
            type: "POST",
            dataType: "json",
            async: "false",
            data: { fechad: fechad },

            success: function (data) {
                for (var i = 0; i < data.length; i++) {
                    
                
                    tablacal.row
                        .add([
                            data[i].id,
                            data[i].hora,
                            data[i].color,
                            data[i].colaborador,
                            data[i].paciente,
                            data[i].consultorio,
                            data[i].descripcion,
                            data[i].estado,
                            
                        ])
                        .draw()
                }
            }

        });

    }

    $(document).on("click", ".btnCancelar", function () {
        fila = $(this);
        opcion=4;
        id = parseInt($(this).closest("tr").find('td:eq(0)').text());
        $("#formcan").trigger("reset");
        /*$(".modal-header").css("background-color", "#28a745");*/
        $(".modal-header").css("color", "white");
        $("#modalcan").modal("show");
        $("#foliocan").val(id);
    });
   

    $(document).on("click", "#btnGuardarc", function() {
        motivo = $("#motivo").val();
        id = $("#foliocan").val();
        fecha = $("#fechac").val();
        usuario = $("#nameuser").val();
        $("#modalcan").modal("hide");
        opcion=4;
    ;

        if (motivo === "") {
            swal.fire({
                title: "Datos Incompletos",
                text: "Verifique sus datos",
                icon: "warning",
                focusConfirm: true,
                confirmButtonText: "Aceptar",
            });
        } else {
            $.ajax({
                type: "POST",
                url: "bd/buscarcita.php",
                async: false,
                dataType: "json",
                data: {
                    id: id, opcion: opcion,
                    motivo: motivo,
                    fecha: fecha,
                    usuario: usuario,
                },
                success: function(data) {
                    if (data[0].id == id) {
                        mensaje();
                        window.setTimeout(function() {
                           buscar();
                        }, 1500);
                    } else {
                        mensajeerror();
                    }
                },
            });
        }
    });


    function mensaje() {
        swal.fire({
            title: "Registro Cancelado",
            icon: "success",
            focusConfirm: true,
            confirmButtonText: "Aceptar",
            timer: 2000
        });
    }

    function mensajeerror() {
        swal.fire({
            title: "Error al Cancelar el Registro",
            icon: "error",
            focusConfirm: true,
            confirmButtonText: "Aceptar",
        });
    }
  })
  