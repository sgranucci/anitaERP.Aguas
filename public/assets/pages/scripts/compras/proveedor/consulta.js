function buscar_datos_proveedor(consulta) {
    $.ajax({
        url: '/anitaERP/public/compras/proveedor/consultaproveedor',
        type: 'POST',
        dataType: 'HTML',
	    headers: {
        	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    	},
        data: {
            consulta: consulta
        },
    })
    .done (function(respuesta) {
		const resp = respuesta.replace(/\\/g, '');
        $("#datosproveedor").html("");
        $("#datosproveedor").html(resp);
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

$(document).on('keyup', '#consultaproveedor', function () {
    var valor = $(this).val();
    if (valor != "") {
        buscar_datos_proveedor(valor);
    } else {
        buscar_datos_proveedor();
    }
});

function activa_eventos_consultaproveedor()
{
    // Consulta de proveedores
    $('.consultaproveedor').on('click', function (event) {
        proveedorxcodigo = $(this).parents("tr").find(".proveedor_id");

        // Abre modal de consulta
        $("#consultaproveedorModal").modal('show');
    });

    $('#consultaproveedorModal').on('shown.bs.modal', function () {
        $(this).find('[autofocus]').focus();
    })

    $('#aceptaconsultaproveedorModal').on('click', function () {
        $('#consultaproveedorModal').modal('hide');
    });

    $(document).on('click', '.eligeconsultaproveedor', function () {
        let seleccion = $(this).parents("tr").children().html();
        let descripcion = $(this).parents("tr").find(".nombreproveedor").html();

        // Asigna a grilla los valores devueltos por consulta
        $(proveedorxcodigo).val(seleccion);

        // Asigna nueva reserva
        $("#proveedor_id").val(seleccion);
        $("#nombreproveedor").val(descripcion);
        $("#proveedor").val(descripcion);

        $('#consultaproveedorModal').modal('hide');
    });

    $(document).on('click', '.consultaproveedor', function () {
        let id = $(this).parents("tr").children().html();

        if (id > 0)
        {
            let url = urlConsultaProveedor;
            url = url.replace(':id', id);
            document.location.href=url;
        }
    });

    $('#proveedor_id').on('change', function (event) {
        event.preventDefault();

        // Lee servicio terrestre por codigo
        let proveedor_id = $("#proveedor_id").val();
        let url_res = '/anitaERP/public/compras/leerproveedor/'+proveedor_id;

        $.get(url_res, function(data){
            if (data)
            {
                $("#proveedor_id").val(data.id);
                $("#nombreproveedor").val(data.nombre);
                $("#proveedor").val(data.nombre);
            }
        });
    });
}


