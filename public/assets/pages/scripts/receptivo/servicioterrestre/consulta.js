function buscar_datos_servicioterrestre(consulta) {
    $.ajax({
        url: '/anitaERP/public/receptivo/servicioterrestre/consultaservicioterrestre',
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
        $("#datosservicioterrestre").html("");
        $("#datosservicioterrestre").html(resp);
    })
    .fail (function() {
        console.log("error");
    });
}

// Si pulsamos tecla enter en un Input no envia formulario
$("input").keydown(function (e){
    // Capturamos qué tecla ha sido
    var keyCode= e.which;
    // Si la tecla es el Intro/Enter
    if (keyCode == 13){
      // Evitamos que se ejecute eventos
      e.preventDefault();
      // Devolvemos falso
      return false;
    }
  });

$(document).on('keyup', '#consultaservicioterrestre', function () {
    var valor = $(this).val();
    if (valor != "") {
        buscar_datos_servicioterrestre(valor);
    } else {
        buscar_datos_servicioterrestre();
    }
});




