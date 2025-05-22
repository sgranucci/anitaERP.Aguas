function buscar_datos_cuentacaja(consulta) {
    let empresa_id = $("#empresa_id").val();

    $.ajax({
        url: '/anitaERP/public/caja/cuentacaja/consultacuentacaja',
        type: 'POST',
        dataType: 'HTML',
	    headers: {
        	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    	},
        data: {
            consulta: consulta,
            empresa_id: empresa_id
        },
    })
    .done (function(respuesta) {
		const resp = respuesta.replace(/\\/g, '');
        $("#datoscuentacaja").html("");
        $("#datoscuentacaja").html(resp);
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

$(document).on('keyup', '#consultacuentacaja', function () {
    var valor = $(this).val();
    if (valor != "") {
        buscar_datos_cuentacaja(valor);
    } else {
        buscar_datos_cuentacaja();
    }
});




