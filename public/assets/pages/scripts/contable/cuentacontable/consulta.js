function buscar_datos(consulta) {
    $.ajax({
        url: '/anitaERP/public/contable/cuentacontable/consultacuentacontable',
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
        $("#datos").html(resp);
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
      event.preventDefault();
      // Devolvemos falso
      return false;
    }
  });

$(document).on('keyup', '#consulta', function () {
    var valor = $(this).val();
    if (valor != "") {
        buscar_datos(valor);
    } else {
        buscar_datos();
    }
});




