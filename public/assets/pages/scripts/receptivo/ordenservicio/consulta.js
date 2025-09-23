function buscar_datos_ordenservicio(consulta) {

    $.ajax({
        url: '/anitaERP/public/receptivo/ordenservicio/consultaordenservicio',
        type: 'POST',
        dataType: 'HTML',
	    headers: {
        	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    	},
        data: {
            consulta: consulta,
        },
    })
    .done (function(respuesta) {
		const resp = respuesta.replace(/\\/g, '');
        $("#datosordenservicio").html("");
        $("#datosordenservicio").html(resp);
    })
    .fail (function() {
        console.log("error");
    });
}

// Si pulsamos tecla enter en un Input no envia formulario
$("input").keydown(function (e){
    // Capturamos qué telca ha sido
    var keyCode= e.which;
    // Si la tecla es el Intro/Enter
    if (keyCode == 13){
      // Evitamos que se ejecute eventos
      e.preventDefault();
      // Devolvemos falso
      return false;
    }
  });

$(document).on('keyup', '#consultaordenservicio', function () {
    var valor = $(this).val();
    if (valor != "") {
        buscar_datos_ordenservicio(valor);
    } else {
        buscar_datos_ordenservicio();
    }
});

function activa_eventos_consultaordenservicio()
{
    // Consulta de servicios
    $('.consultaordenservicio').on('click', function (event) {
        // Abre modal de consulta
        $("#consultaordenservicioModal").modal('show');
    });

    $('#consultaordenservicioModal').on('shown.bs.modal', function () {
        $(this).find('[autofocus]').focus();
    })

    $('#aceptaconsultaordenservicioModal').on('click', function () {
        $('#consultaordenservicioModal').modal('hide');
    });

    $(document).on('click', '.eligeconsultaordenservicio', function () {
        let seleccion = $(this).parents("tr").children().html();
        let nombre = $(this).parents("tr").find(".nombreguia").html();
        let guia_id = $(this).parents("tr").find(".guia_id").html();
        let codigoguia = $(this).parents("tr").find(".codigoguia").html();

        $("#ordenservicio_id").val(seleccion);
        $("#nombreguia").val(nombre);
        $("#guia_id").val(guia_id);
        $("#codigoguia").val(codigoguia);
        
        leeVoucher();
		leeOrdenServicio();

        $('#consultaordenservicioModal').modal('hide');
    });

    $('#ordenservicio_id').on('change', function (event) {
        event.preventDefault();

        // Lee servicio terrestre por codigo
        let ordenservicio_id = $("#ordenservicio_id").val();
        let url_res = '/anitaERP/public/receptivo/leeunaordenservicio/'+ordenservicio_id;

        $.get(url_res, function(data){
            if (data)
            {
                $("#ordenservicio_id").val(data.id);
                $("#nombreguia").val(data.nombregioa);
                $("#guia_id").val(data.guia_id);
                $("#codigoguia").val(data.codigoguia);

                leeVoucher();
		        leeOrdenServicio();
            }
        });
    });
}




