$(document).ready(function() {
    var id_usuario, opcion;
    opcion = 4;

    tablaPersonas = $("#tablaUsuario").DataTable({



        "columnDefs": [{
                "targets": -1,
                "data": null,
                "defaultContent": "<div class='text-center'><div class='btn-group'><button class='btn btn-sm btn-primary  btnEditar'><i class='fas fa-edit'></i></button><button class='btn btn-sm btn-danger btnBorrar'><i class='fas fa-trash-alt'></i></button></div></div>"
            },
            { className: "hide_column", targets: [4] },
            { className: "hide_column", targets: [5] },
            { className: "hide_column", targets: [6] },
         

        ],

        //Para cambiar el lenguaje a español
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "sProcessing": "Procesando...",
        }
    });

    $("#btnNuevo").click(function() {


        $("#formDatos").trigger("reset");
        $(".modal-header").css("background-color", "#28a745");
        $(".modal-header").css("color", "white");
        $(".modal-title").text("Nuevo Usuario");
        $("#modalU").modal("show");
        id_usuario = null;
        opcion = 1; //alta
    });

    var fila; //capturar la fila para editar o borrar el registro

    //botón EDITAR    
    $(document).on("click", ".btnEditar", function() {
        fila = $(this).closest("tr");
        id_usuario = parseInt(fila.find('td:eq(0)').text());


        nombre = fila.find('td:eq(1)').text();
        email = fila.find('td:eq(2)').text();
        username = fila.find('td:eq(3)').text();
        password = "";
        rol_usuario = fila.find('td:eq(6)').text();
        rol = fila.find('td:eq(7)').text();
       
        $("#nombre").val(nombre);
        $("#username").val(username);
        $("#email").val(email);
        $("#rol").val(rol_usuario);

        opcion = 2; //editar

        $(".modal-header").css("background-color", "#007bff");
        $(".modal-header").css("color", "white");
        $(".modal-title").text("Editar Usuario");
        $("#modalU").modal("show");

    });

    //botón BORRAR
    $(document).on("click", ".btnBorrar", function() {
        fila = $(this);

        id_usuario = parseInt($(this).closest("tr").find('td:eq(0)').text());
        opcion = 3 //borrar
            //agregar codigo de sweatalert2
        var respuesta = confirm("¿Está seguro de eliminar el registro: " + id + "?");

        if (respuesta) {
            $.ajax({

                url: "bd/crudusu.php",
                type: "POST",
                dataType: "json",
                data: { id: id, opcion: opcion },

                success: function() {
                    console.log(data);

                    tablaPersonas.row(fila.parents('tr')).remove().draw();
                }
            });
        }
    });

    $("#formDatos").submit(function(e) {
        e.preventDefault();



        nombre = $.trim($("#nombre").val());
        username = $.trim($("#username").val());
        email = $.trim($("#email").val());
        rol_usuario = $.trim($("#rol").val());
        pass1 = $.trim($("#pass1").val());
        pass2 = $.trim($("#pass2").val());

        if (nombre.length == 0 || username.length == 0 || email.length == 0 ||
            rol_usuario.length == 0 || pass1.length == 0 || pass2.length == 0) {
            Swal.fire({
                title: 'Datos Faltantes',
                text: "Debe ingresar todos los datos del Prospecto",
                icon: 'warning',
            })
            return false;


        } else {
            if (pass1 == pass2) {
                $.ajax({
                    url: "bd/crudusuario.php",
                    type: "POST",
                    dataType: "json",
                    data: { nombre: nombre, username: username, password: pass1, email: email, id_usuario: id_usuario, rol_usuario: rol_usuario, opcion: opcion },
                    success: function(data) {
                        console.log(data);

                        //tablaPersonas.ajax.reload(null, false);
                        id_usuario = data[0].id_usuario;
                        nombre = data[0].nombre;
                        username = data[0].username;
                        email = data[0].email;
                        password = data[0].password;
                        edo_usuario = data[0].edo_usuario;
                        rol_usuario = data[0].rol_usuario;
                        id = data[0].id;
                        rol = data[0].rol;


                        if (opcion == 1) {
                            tablaPersonas.row.add([id_usuario, nombre, email, username, password, edo_usuario, rol_usuario, id, rol]).draw();
                        } else {
                            tablaPersonas.row(fila).data([id_usuario, nombre, email, username, password, edo_usuario, rol_usuario, id, rol]).draw();
                        }

                    }
                });

                $("#modalU").modal("hide");
            } else {
                Swal.fire({
                    title: 'Error en Contraseñas',
                    text: "Las Contraseñas deben Coincidir",
                    icon: 'warning',
                })
                return false;
            }



        }




    });

});