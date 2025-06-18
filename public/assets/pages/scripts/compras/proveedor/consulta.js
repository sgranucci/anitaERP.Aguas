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




