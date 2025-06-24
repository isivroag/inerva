$(document).ready(function () {
  $.ajaxSetup({
    cache: false,
  });

  jQuery.ajaxSetup({
    beforeSend: function () {
      $("#div_carga").show();
    },
    complete: function () {
      $("#div_carga").hide();
    },
    success: function () {},
  });

  $.ajax({
    url: "bd/dbeventosp.php",
    type: "POST",
    async: false,

    success: function (data) {
      obj = JSON.stringify(data);
    },
    error: function (xhr, err) {
      alert("readyState: " + xhr.readyState + "\nstatus: " + xhr.status);
      alert("responseText: " + xhr.responseText);
    },
  });

  $("#datetimepicker1x").datetimepicker({
    locale: "es",
  });

  var opcion;
  var calendar;
  var date = new Date();
  calendario();
  var d = date.getDate(),
    m = date.getMonth(),
    y = date.getFullYear();

  function calendario() {
    var Calendar = FullCalendar.Calendar;
    var calendarEl = document.getElementById("calendar");

    calendar = new Calendar(calendarEl, {
      //defaultView: "timeGridWeek",
      plugins: ["bootstrap", "interaction", "dayGrid", "timeGrid"],

      header: {
        left: "prev,next today,myCustomButton",
        center: "title",
        right: "dayGridMonth,timeGridWeek,timeGridDay",
      },
      customButtons: {
        myCustomButton: {
          text: "Nueva Cita",
          click: function () {
            $("#formDatospx").trigger("reset");
            $(".modal-header").css("background-color", "#007bff");
            $(".modal-header").css("color", "white");
            opcion = 1;
            $("#formDatospx :input").prop("disabled", false);
            $("#btnCancelarctax").hide();
            $("#btnreagendarx").hide();
            $("#btnGuardarx").show();
            cargarhorasx();
            $("#modalpx").modal("show");
            $(".form-control").attr("disabled", false);
          },
        },
      },
      
      views: {
        timeGrid: {
          allDaySlot: false,

          slotDuration: "00:20:00",
          slotLabelInterval: "01:00",
          slotLabelFormat: {
            hour: "2-digit",
            minute: "2-digit",
            hour12: false,
          },
          slotMinTime: "08:00:00", //Hora mínima
          slotMaxTime: "20:00:00", //Hora máxima
          slotEventOverlap: false,
          scrollTime: "08:00:00", //Hora de inicio del scroll

          headerLabelFormat: {
            weekday: "short",
          },
        },
          dayGridMonth: {
    dayMaxEventRows: 35, // Muestra hasta 35 eventos por día (ajustable)
    moreLinkClick: 'popover', // O 'week' para expandir
  },
      },

      height: "100%",

      timeZone: 'America/Mexico_City',
      themeSystem: "bootstrap",
      locale: "es",
      cache: false,
      lazyFetching: true,

      events: {
        url: "bd/dbeventosp.php",
        method: "GET",
        extraParams: {
          timezone: 'America/Mexico_City', // Enviar zona horaria al backend
          custom_param1: "something",
          custom_param2: "somethingelse",
        },
      },

      eventRender: function (info) {
        if (
          info.event.extendedProps.estado == 5 ||
          info.event.extendedProps.estado == 10
        ) {
          // Crear elemento con el candado
          const lockIcon = document.createElement("i");
          lockIcon.className = "fas fa-lock";
          lockIcon.style.marginRight = "5px";

          // Insertar el icono antes del título
          info.el.querySelector(".fc-title").prepend(lockIcon);
        }
      },

      eventClick: function (calEvent) {
        var id = calEvent.event.id;
        opcion = 2;

        $.ajax({
          url: "bd/citasp.php",
          type: "POST",
          dataType: "json",
          data: { id: id, opcion: 4 },
          success: function (data) {
            if (data[0].tipo_p == 0) {
            } else {
              if (
                data[0].estado == 5 ||
                data[0].estado == 7 ||
                data[0].estado == 10
              ) {
                Swal.fire({
                  title: "Cita Bloqueada",
                  text: "No es posible editar una cita bloqueada",
                  icon: "warning",
                });
                return false;
              }

              $("#formDatospx :input").prop("disabled", false);
              $("#foliox").val(data[0].id);
              $("#id_px").val(data[0].id_px);
              $("#nom_prosx").val(data[0].title);
              $("#conceptox").val(data[0].descripcion);
              $("#responsablex").val(data[0].id_col);
              $("#fechax").val(data[0].fecha);
              $("#opcionx").val("1");
              $("#obsx").val(data[0].obs);
              $("#duracionx").val(data[0].duracion);
              $("#cabinax").val(data[0].id_con);
              $("#btnCancelarctax").show();

              rol = $("#tipousuario").val();
              if (rol == 1) {
                $("#btnGuardarx").hide();
                $("#btnreagendarx").hide();
                $("#btnCancelarctax").hide();
                $(".form-control").attr("disabled", true);
              } else {
                $("#btnGuardarx").hide();
                $("#btnreagendarx").show();
                $("#btnreagendarx").prop("disabled", false);
                $("#btnCancelarctax").prop("disabled", false);
              }

              cargarhorasx();

              $("#horax").val(data[0].hora);

              $("#modalpx").modal("show");
            }
          },
        });
      },
      dateClick: function (info) {
        console.log(info.dateStr);
        var soloFecha = info.dateStr.split("T")[0];

        // window.location.href = 'vcalendario.php?fecha=' + soloFecha
        window.location.href = "vcalendario.php?fecha=" + soloFecha;
      },

      editable: false,
      droppable: false,
    });

    calendar.render();
  }

  tablaC = $("#tablaC").DataTable({
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

  tablaC = $("#tablaCx").DataTable({
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

  $(document).on("click", "#bcliente", function () {
    $(".modal-header").css("background-color", "#007bff");
    $(".modal-header").css("color", "white");

    $("#modalProspecto").modal("show");
  });
  $(document).on("click", "#bclientex", function () {
    $(".modal-header").css("background-color", "#007bff");
    $(".modal-header").css("color", "white");

    $("#modalProspectox").modal("show");
    $("#btnCancelarctax").hide();
  });

  $(document).on("click", "#btnNuevo", function () {
    $("#formDatos").trigger("reset");
    $(".modal-header").css("background-color", "#007bff");
    $(".modal-header").css("color", "white");
    opcion = 1;
    $("#formDatos :input").prop("disabled", false);
    $("#btnCancelarcta").hide();
    $("#btnreagendar").hide();
    $("#btnGuardar").show();
    cargarhoras();
    $("#modalCRUD").modal("show");
    $(".form-control").attr("disabled", false);
  });

  $(document).on("click", "#btnNuevox", function () {
    $("#formDatospx").trigger("reset");
    $(".modal-header").css("background-color", "#007bff");
    $(".modal-header").css("color", "white");
    opcion = 1;
    $("#formDatospx :input").prop("disabled", false);
    $("#btnCancelarctax").hide();
    $("#btnreagendarx").hide();
    $("#btnGuardarx").show();
    cargarhorasx();
    $("#modalpx").modal("show");
    $(".form-control").attr("disabled", false);
  });

  $(document).on("click", ".btnSelCliente", function () {
    fila = $(this).closest("tr");

    IdCliente = fila.find("td:eq(0)").text();
    NomCliente = fila.find("td:eq(1)").text();

    $("#id_pros").val(IdCliente);
    $("#nom_pros").val(NomCliente);
    $("#modalProspecto").modal("hide");
  });

  $(document).on("click", ".btnSelClientex", function () {
    fila = $(this).closest("tr");

    IdClientex = fila.find("td:eq(0)").text();
    NomClientex = fila.find("td:eq(1)").text();

    $("#id_px").val(IdClientex);
    $("#nom_prosx").val(NomClientex);
    $("#modalProspectox").modal("hide");
  });

  $(document).on("click", "#btnGuardar", function () {
    var id_pros = $.trim($("#id_pros").val());
    var nombre = $.trim($("#nom_pros").val());
    var concepto = $.trim($("#concepto").val());

    var fecha = $.trim($("#fechap").val());
    var hora = $("#hora").val();
    fecha = fecha + " " + hora;

    var obs = $.trim($("#obs").val());
    var id = $.trim($("#folio").val());
    var tipop = $.trim($("#tipop").val());
    var responsable = $.trim($("#responsable").val());
    var duracion = $.trim($("#duracion").val());
    var cabina = $.trim($("#cabina").val());
    colaborador = responsable;
    inicio = fecha;
    if (
      id_pros.length == 0 ||
      $.trim($("#fechap").val()).length == 0 ||
      fecha.length == 0 ||
      responsable.length == 0 ||
      cabina.length == 0
    ) {
      Swal.fire({
        title: "Datos Faltantes",
        text: "Debe ingresar todos los datos requeridos",
        icon: "warning",
      });
      return false;
    } else {
      $.ajax({
        type: "POST",
        url: "bd/validarcita.php",
        async: false,
        dataType: "json",
        data: {
          inicio: inicio,
          duracion: duracion,
          colaborador: colaborador,
          cabina: cabina,
        },
        success: function (data) {
          if (data == 0) {
            $.ajax({
              url: "bd/citasp.php",
              type: "POST",
              dataType: "json",
              async: "false",
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
                  console.log(data);
                  Swal.fire({
                    title: "Operación Exitosa",
                    text: "Cita Guardada",
                    icon: "success",
                    timer: 1000,
                  });
                  window.setTimeout(function () {
                    location.reload();
                  }, 1500);
                } else {
                  Swal.fire({
                    title: "No es posible Agendar la Cita",
                    icon: "warning",
                  });
                }
              },
            });
          } else {
            swal.fire({
              title: "No es posible Agendar Cita",
              text: "Verifique la fecha, la hora, la cabina o el responsable",
              icon: "error",
              focusConfirm: true,
              confirmButtonText: "Aceptar",
            });
          }
        },
      });
    }
    //$("#modalCRUD").modal("hide");
  });

  $(document).on("click", "#btnreagendar", function () {
    var id_pros = $("#id_pros").val();
    var nombre = $("#nom_pros").val();
    var concepto = $("#concepto").val();
    var fecha = $("#fechap").val();
    var hora = $("#hora").val();
    fecha = fecha + " " + hora;
    var obs = $("#obs").val();
    var id = $("#folio").val();
    var tipop = $("#tipop").val();
    var responsable = $("#responsable").val();
    var duracion = $("#duracion").val();
    var cabina = $("#cabina").val();
    colaborador = responsable;
    inicio = fecha;

    opchr = $("#opcion").val();
    if (opchr == "1") {
      cita = $("#folio").val();
    } else {
      cita = 0;
    }
    console.log(
      inicio +
        "/ " +
        duracion +
        "/ " +
        colaborador +
        "/ " +
        cabina +
        "/ " +
        cita
    );
    opcion = 2;
    if (
      id_pros.length == 0 ||
      $.trim($("#fechap").val()).length == 0 ||
      fecha.length == 0 ||
      responsable.length == 0 ||
      cabina.length == 0
    ) {
      Swal.fire({
        title: "Datos Faltantes",
        text: "Debe ingresar todos los datos requeridos",
        icon: "warning",
      });
      return false;
    } else {
      $.ajax({
        type: "POST",
        url: "bd/validarcita.php",
        async: false,
        dataType: "json",
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
              url: "bd/citasp.php",
              type: "POST",
              dataType: "json",
              async: "false",
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
                  console.log(data);
                  Swal.fire({
                    title: "Operación Exitosa",
                    text: "Cita Guardada",
                    icon: "success",
                    timer: 1000,
                  });
                  window.setTimeout(function () {
                    location.reload();
                  }, 1500);
                } else {
                  Swal.fire({
                    title: "No es posible Agendar la Cita",
                    icon: "warning",
                  });
                }
              },
            });
          } else {
            swal.fire({
              title: "No es posible Agendar Cita",
              text: "Verifique la fecha, la hora, la cabina y el responsable",
              icon: "error",
              focusConfirm: true,
              confirmButtonText: "Aceptar",
            });
          }
        },
      });
    }
    //$("#modalCRUD").modal("hide");
  });

  $(document).on("click", "#btnGuardarx", function () {
    var id_px = $.trim($("#id_px").val());
    var nombre = $.trim($("#nom_prosx").val());
    var concepto = $.trim($("#conceptox").val());

    var fecha = $.trim($("#fechax").val());
    var hora = $("#horax").val();
    fecha = fecha + " " + hora;

    var obs = $.trim($("#obsx").val());
    var id = $.trim($("#foliox").val());
    var tipop = $.trim($("#tipopx").val());
    var responsable = $.trim($("#responsablex").val());
    var duracion = $.trim($("#duracionx").val());
    var cabina = $.trim($("#cabinax").val());
    colaborador = responsable;
    inicio = fecha;
    console.log(
      inicio +
        "/ " +
        duracion +
        "/ " +
        colaborador +
        "/ " +
        cabina +
        "/ " +
        id_px +
        "/ " +
        id +
        "/ " +
        tipop +
        "/ " +
        concepto +
        "/ " +
        obs +
        "/ " +
        responsable
    );

    if (
      id_px.length == 0 ||
      $.trim($("#fechax").val()).length == 0 ||
      fecha.length == 0 ||
      responsable.length == 0 ||
      cabina.length == 0
    ) {
      Swal.fire({
        title: "Datos Faltantes",
        text: "Debe ingresar todos los datos requeridos",
        icon: "warning",
      });
      return false;
    } else {
      $.ajax({
        type: "POST",
        url: "bd/validarcita.php",
        async: false,
        dataType: "json",
        data: {
          inicio: inicio,
          duracion: duracion,
          colaborador: colaborador,
          cabina: cabina,
        },
        success: function (data) {
          if (data == 0) {
            $.ajax({
              url: "bd/citasp.php",
              type: "POST",
              dataType: "json",
              data: {
                nombre: nombre,
                id_px: id_px,
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
                  console.log(data);
                  Swal.fire({
                    title: "Operación Exitosa",
                    text: "Cita Guardada",
                    icon: "success",
                    timer: 1000,
                  });
                  window.setTimeout(function () {
                    location.reload();
                  }, 1500);
                } else {
                  Swal.fire({
                    title: "No es posible Agendar la Cita",
                    icon: "warning",
                  });
                }
              },
            });
          } else {
            swal.fire({
              title: "No es posible Agendar Cita",
              text: "Verifique la fecha, la hora, la cabina o el responsable",
              icon: "error",
              focusConfirm: true,
              confirmButtonText: "Aceptar",
            });
          }
        },
      });
    }
    //$("#modalCRUD").modal("hide");
  });

  $(document).on("click", "#btnreagendarx", function () {
    var id_px = $.trim($("#id_px").val());
    var nombre = $.trim($("#nom_prosx").val());
    var concepto = $.trim($("#conceptox").val());

    var fecha = $("#fechax").val();
    var hora = $("#horax").val();
    fecha = fecha + " " + hora;

    var obs = $.trim($("#obsx").val());
    var id = $.trim($("#foliox").val());
    var tipop = $.trim($("#tipopx").val());
    var responsable = $.trim($("#responsablex").val());
    var duracion = $.trim($("#duracionx").val());
    var cabina = $.trim($("#cabinax").val());
    colaborador = responsable;
    inicio = fecha;
    opcion = 2;
    if (
      id_px.length == 0 ||
      $.trim($("#fechax").val()).length == 0 ||
      fecha.length == 0 ||
      responsable.length == 0 ||
      cabina.length == 0
    ) {
      Swal.fire({
        title: "Datos Faltantes",
        text: "Debe ingresar todos los datos requeridos",
        icon: "warning",
      });
      return false;
    } else {
      $.ajax({
        type: "POST",
        url: "bd/validarcita.php",
        async: false,
        dataType: "json",
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
              url: "bd/citasp.php",
              type: "POST",
              dataType: "json",
              async: "false",
              data: {
                nombre: nombre,
                id_px: id_px,
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
                  console.log(data);
                  Swal.fire({
                    title: "Operación Exitosa",
                    text: "Cita Guardada",
                    icon: "success",
                    timer: 1000,
                  });
                  window.setTimeout(function () {
                    location.reload();
                  }, 1500);
                } else {
                  Swal.fire({
                    title: "No es posible Agendar la Cita",
                    icon: "warning",
                  });
                }
              },
            });
          } else {
            swal.fire({
              title: "No es posible Agendar Cita",
              text: "Verifique la fecha, la hora, la cabina y el responsable",
              icon: "error",
              focusConfirm: true,
              confirmButtonText: "Aceptar",
            });
          }
        },
      });
    }
    //$("#modalCRUD").modal("hide");
  });

  $(document).on("click", "#btnCancelarcta", function () {
    folio = $("#folio").val();

    $("#formcan").trigger("reset");
    /*$(".modal-header").css("background-color", "#28a745");*/
    $(".modal-header").css("color", "white");
    $("#modalcan").modal("show");
    $("#foliocan").val(folio);
  });

  $(document).on("click", "#btnCancelarctax", function () {
    folio = $("#foliox").val();

    $("#formcan").trigger("reset");
    /*$(".modal-header").css("background-color", "#28a745");*/
    $(".modal-header").css("color", "white");
    $("#modalcan").modal("show");
    $("#foliocan").val(folio);
  });

  $(document).on("click", "#btnGuardarc", function () {
    motivo = $("#motivo").val();
    id = $("#foliocan").val();
    fecha = $("#fechac").val();
    usuario = $("#nameuser").val();
    $("#modalcan").modal("hide");
    opcion = 4;

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
          id: id,
          opcion: opcion,
          motivo: motivo,
          fecha: fecha,
          usuario: usuario,
        },
        success: function (data) {
          if (data[0].id == id) {
            mensaje();
            window.setTimeout(function () {
              window.location.reload();
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
      timer: 2000,
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

  tablaVis = $("#tablaV").DataTable({
    info: false,
    searching: false,
    paging: false,
    ordering: false,

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

  $("#fechap").on("change", function () {
    cargarhoras();
  });

  $("#responsable").on("change", function () {
    cargarhoras();
  });

  $("#cabina").on("change", function () {
    cargarhoras();
  });

  function cargarhoras() {
    fecha = $("#fechap").val();
    colaborador = $("#responsable").val();
    cabina = $("#cabina").val();
    opchr = $("#opcion").val();
    if (opchr == "1") {
      cita = $("#folio").val();
    } else {
      cita = 0;
    }

    $("#hora").empty();
    $.ajax({
      type: "POST",
      url: "bd/cargarhoras.php",
      dataType: "json",
      async: false,
      data: {
        fecha: fecha,
        colaborador: colaborador,
        cabina: cabina,
        cita: cita,
      },
      success: function (res) {
        for (var i = 0; i < res.length; i++) {
          $("#hora").append(
            $("<option>", {
              value: res[i].nhora,
              text: res[i].nhora,
            })
          );
        }
      },
      error: function () {
        Swal.fire({
          title: "Error al cargar horarios disponibles",
          icon: "error",
        });
      },
    });
  }
  $("#fechax").on("change", function () {
    cargarhorasx();
  });

  $("#responsablex").on("change", function () {
    cargarhorasx();
  });

  $("#cabinax").on("change", function () {
    cargarhorasx();
  });

  function cargarhorasx() {
    fecha = $("#fechax").val();
    colaborador = $("#responsablex").val();
    cabina = $("#cabinax").val();
    opchr = $("#opcionx").val();
    if (opchr == "1") {
      cita = $("#foliox").val();
    } else {
      cita = 0;
    }

    $("#horax").empty();
    $.ajax({
      type: "POST",
      url: "bd/cargarhoras.php",
      dataType: "json",
      async: false,
      data: {
        fecha: fecha,
        colaborador: colaborador,
        cabina: cabina,
        cita: cita,
      },
      success: function (res) {
        for (var i = 0; i < res.length; i++) {
          $("#horax").append(
            $("<option>", {
              value: res[i].nhora,
              text: res[i].nhora,
            })
          );
        }
      },
      error: function () {
        Swal.fire({
          title: "Error al cargar horarios disponibles",
          icon: "error",
        });
      },
    });
  }
});
