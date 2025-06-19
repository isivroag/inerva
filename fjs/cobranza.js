$(document).ready(function() {
    // Escuchar cambios en el select de servicios
    $('#id_serv').change(function() {
        // Obtener el costo del option seleccionado
        var costo = $(this).find('option:selected').data('costo');
        
        // Actualizar el campo de costo
        $('#costo').val(costo);
        
        // Recalcular el total (por si hay descuento)
        calcularTotal();
    });
    
    // Escuchar cambios en el descuento para recalcular el total
    $('#descuento').on('input', function() {
        calcularTotal();
    });
    
    // Función para calcular el total
    function calcularTotal() {
        var costo = parseFloat($('#costo').val()) || 0;
        var descuento = parseFloat($('#descuento').val()) || 0;
        var total = costo - descuento;
        
        // Asegurarse que el total no sea negativo
        total = total < 0 ? 0 : total;
        
        $('#total').val(total.toFixed(2));
    }

    // Guardar cobranza
    $('#formDatos').submit(function(e) {
        e.preventDefault();

        var fecha = $('#fecha').val();
        var id_cita = $('#id_cita').val();
        var id_px = $('#id_paciente').val();
        var id_serv = $('#id_serv').val();
        var costo = $('#costo').val();
        var descuento = $('#descuento').val();
        var total = $('#total').val();
        var metodo = $('#metodo_pago').val();

        // Validar campos obligatorios
        if (!id_serv || !costo || !total || !id_cita || !id_px) {
            Swal.fire('Error', 'Por favor, completa todos los campos obligatorios antes de registrar la cobranza.', 'error');
            return;
        }

        $.ajax({
            url: 'bd/crudcobranza.php',
            type: 'POST',
            dataType: 'json',
            data: {
                opcion: 1,
                fecha: fecha,
                id_cita: id_cita,
                id_paciente: id_px,
                id_serv: id_serv,
                costo: costo,
                descuento: descuento,
                total: total,
                metodo_pago: metodo
            },
            success: function(respuesta) {
                if(respuesta.status === 'ok') {
                    Swal.fire('Éxito', 'Cobranza guardada correctamente', 'success').then(() => {
                        window.location.href="inicio.php";
                    });
                } else {
                    Swal.fire('Error', respuesta.mensaje || 'No se pudo guardar', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Error de comunicación con el servidor', 'error');
            }
        });
    });
});

