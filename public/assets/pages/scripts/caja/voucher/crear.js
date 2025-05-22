	var reservaxcodigo;   
 
 	$(function () {
        $('#agrega_renglon_guia').on('click', agregaRenglonGuia);
        $(document).on('click', '.eliminar_guia', borraRenglonGuia);

		activa_eventos(true);

		$( ".botonsubmit" ).click(function() {
			$( "#form-general" ).submit();
		});
    });

	function activa_eventos(flInicio)
	{
		// Si esta agregando items desactiva los eventos
		if (!flInicio)
			$('.consultareserva').off('click');
		
		$('.consultareserva').on('click', function (event) {
        	reservaxcodigo = $(this).parents("tr").find(".reserva_id");

        	// Abre modal de consulta
			$("#consultareservaModal").modal('show');
    	});

		$('#consultareservaModal').on('shown.bs.modal', function () {
			$(this).find('[autofocus]').focus();
		})

    	$('#aceptaconsultareservaModal').on('click', function () {
        	$('#consultareservaModal').modal('hide');
    	});

		$(document).on('click', '.eligeconsultareserva', function () {
			let seleccion = $(this).parents("tr").children().html();
			let pasajero = $(this).parents("tr").find(".nombrepasajero").html();
			let fechaarribo = $(this).parents("tr").find(".fechaarribo").html();
			let fechapartida = $(this).parents("tr").find(".fechapartida").html();
			let pasajero_id = $(this).parents("tr").find(".pasajero_id").html();
			let fechaIn = fechaarribo.substring(6,8)+"-"+fechaarribo.substring(4,6)+"-"+fechaarribo.substring(0,4);
			let fechaOut = fechapartida.substring(6,8)+"-"+fechapartida.substring(4,6)+"-"+fechapartida.substring(0,4);

			// Asigna a grilla los valores devueltos por consulta
			$(reservaxcodigo).val(seleccion);

			//* Asigna nueva reserva
			$("#reserva_id").val(seleccion);
			$("#pasajero_id").val(pasajero_id);
			$("#nombrepasajero").val(pasajero);
			$("#reserva").val(pasajero+" File "+seleccion+" IN "+fechaIn+" OUT "+fechaOut);
		
			$('#consultareservaModal').modal('hide');
		});
	}

    function agregaRenglonGuia(event){
    	event.preventDefault();

		agregaUnRenglon();
	}

	function agregaUnRenglon()
	{
    	let renglon = $('#template-renglon-guia').html();

    	$("#tbody-guia-table").append(renglon);
    	actualizaRenglonesGuia();

		activa_eventos(false);
    }

    function borraRenglonGuia(event) {
    	event.preventDefault();
    	$(this).parents('tr').remove();
    	actualizaRenglonesCuenta();
    }

    function actualizaRenglonesGuia() {
    	var item = 1;

    	$("#tbody-guia-table .iiguia").each(function() {
    		$(this).val(item++);
    	});
    }
