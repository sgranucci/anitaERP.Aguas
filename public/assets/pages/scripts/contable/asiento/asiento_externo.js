var cuentacontablexcodigo;
var nombrecontablexcodigo;
var codigocontablexcodigo;
var totalDebeAsiento = 0;
var totalHaberAsiento = 0;

    $(function () {
        $('#agrega_renglon_asiento').on('click', agregaRenglonCuentaAsiento);
        $(document).on('click', '.eliminar_cuenta_asiento', borraRenglonCuentaAsiento);

		activa_eventosAsiento(true);

		// Completa centros de costo al abrir asiento
		$("#tbody-cuenta-asiento-table .codigoasiento").each(function(index) {
			var codigo = $(this);
			var cuentacontable_id = $(this).parents("tr").find(".cuentacontable_id").val();
			var centrocosto_id = $(this).parents("tr").find(".centrocostoasiento_id_previo").val();

			completarCentroCostoAsiento(codigo, cuentacontable_id, centrocosto_id);
		});

		// Muestra sumatoria de montos del asiento
		sumaMontoAsiento();
    });

	function activa_eventosAsiento(flInicio)
	{
		// Si esta agregando items desactiva los eventos
		if (!flInicio)
		{
			$('.consultacuenta').off('click');
			$('.codigoasiento').off('change');
			$('.debeasiento').off('change');
			$('.haberasiento').off('change');
			$('.monedaasiento').off('change');
		}
		
		$('.codigoasiento').on('change', function (event) {
			event.preventDefault();
			var codigo = $(this);
			var codigo_ant = $(this).parents("tr").find(".codigo_previo_cuentacontable").val();
			var codigo_nuevo = codigo.val();
			let empresa_id = $('#empresa_id').val();

			let url_cta = '/anitaERP/public/contable/cuentacontable/leercuentacontableporcodigo/'+empresa_id+'/'+codigo_nuevo;

			$.get(url_cta, function(data){
				if (data.id > 0)
				{
					$(codigo).parents("tr").find('.cuentacontable_id').val(data.id);
					$(codigo).parents("tr").find(".cuentacontable_id_previa").val(data.id);
					$(codigo).parents("tr").find(".nombrecuentacontable").val(data.nombre);
				}
				else
				{
					alert("No existe la cuenta");

					// Borra el renglon
					$(codigo).parents('tr').remove();
					return;
				}
			});

			if (codigo_nuevo != codigo_ant && empresa_id)
				leeCentroCostoAsiento(this);
		});

		$('.consultacuenta').on('click', function (event) {
        	cuentacontablexcodigo = $(this).parents("tr").find(".cuentacontable_id");
			nombrecontablexcodigo = $(this).parents("tr").find(".nombrecuentacontable");
			codigocontablexcodigo = $(this).parents("tr").find(".codigoasiento");
			let empresa_id = $('#empresa_id').val();

        	// Abre modal de consulta
			if (empresa_id)
				$("#consultacuentaModal").modal('show');
			else	
				alert('Debe ingresar empresa');
    	});

		$('#consultacuentaModal').on('shown.bs.modal', function () {
			$(this).find('[autofocus]').focus();
		})

    	$('#aceptaconsultacuentaModal').on('click', function () {
        	$('#consultacuentaModal').modal('hide');
    	});

		$(document).on('click', '.eligeconsultacuentacontable', function () {
			var seleccion = $(this).parents("tr").children().html();
			var nombre = $(this).parents("tr").find(".nombrecuentacontable").html();
			var codigo = $(this).parents("tr").find(".codigocuentacontable").html();

			// Asigna a grilla los valores devueltos por consulta
			$(cuentacontablexcodigo).val(seleccion);
			$(nombrecontablexcodigo).val(nombre);
			$(codigocontablexcodigo).val(codigo);

			//* Asigna nueva cuentacontable
			$(cuentacontablexcodigo).parents("tr").find(".cuentacontable_id_previa").val($(cuentacontablexcodigo).val());
		
			$('#consultacuentaModal').modal('hide');

			leeCentroCostoAsiento(codigocontablexcodigo);
		});

		$('.debeasiento').on('change', function (event) {
			event.preventDefault();
			sumaMontoAsiento();
		});

		$('.haberasiento').on('change', function (event) {
			event.preventDefault();
			sumaMontoAsiento();
		});

		$('.monedaasiento').on('change', function (event) {
			event.preventDefault();
			leeCotizacionAsiento(this);
		});
	}

    function agregaRenglonCuentaAsiento(){
    	event.preventDefault();
    	let renglon = $('#template-renglon-cuenta-asiento').html();
		let monedaDefault = $("#tbody-cuenta-asiento-table").children(':first').find('.monedaasiento').val();

    	$("#tbody-cuenta-asiento-table").append(renglon);
    	actualizaRenglonesCuentaAsiento();

		// Asigna default de moneda
	 	$("#tbody-cuenta-asiento-table .monedaasiento").each(function() {
			if ($(this).val() < 1 || $(this).val() > 999999)
				$(this).val(monedaDefault);
    	});

		let ptrUltimoRenglon = $("#tbody-cuenta-asiento-table").last().find('.monedaasiento');

		// Lee cotizacion de la moneda
		leeCotizacionAsiento(ptrUltimoRenglon);

		activa_eventosAsiento(false);
    }

    function borraRenglonCuentaAsiento(event) {
    	event.preventDefault();
    	$(this).parents('tr').remove();
    	actualizaRenglonesCuentaAsiento();
		sumaMontoAsiento();
    }

    function actualizaRenglonesCuentaAsiento() {
    	var item = 1;

    	$("#tbody-cuenta-asiento-table .iicuentacontable").each(function() {
    		$(this).val(item++);
    	});
    }

	function completarCentroCostoAsiento(ptrcodigo, cuentacontable_id, centrocosto_id){
		let url_cta = '/anitaERP/public/contable/cuentacontable/leercuentacontablecentrocosto/'+cuentacontable_id;

		$.get(url_cta, function(data){
			if (data === "No maneja centro de costo")
			{
				$(ptrcodigo).parents("tr").find('.centrocostoasiento').empty();
				$(ptrcodigo).parents("tr").find('.centrocostoasiento').append('<option value="0" selected>Sin CC</option>');
				$(ptrcodigo).parents("tr").find('.centrocostoasiento').attr("readonly", true);
			}
			else
			{
				var cta = $.map(data, function(value, index){
					return [value];
				});
				$(ptrcodigo).parents("tr").find('.centrocostoasiento').empty();
				$(ptrcodigo).parents("tr").find('.centrocostoasiento').append('<option value="">-- Seleccione CC --</option>');
				$.each(cta, function(index,value){
					if (value.id == centrocosto_id)
						$(ptrcodigo).parents("tr").find('.centrocostoasiento').append('<option value="'+value.id+'" selected>'+value.codigo+'-'+value.nombre+'</option>');
					else
						$(ptrcodigo).parents("tr").find('.centrocostoasiento').append('<option value="'+value.id+'">'+value.codigo+'-'+value.nombre+'</option>');
				});
			}
        });
        setTimeout(() => {
        }, 3000);
    }

	function leeCentroCostoAsiento(ptr) 
	{
		var codigo = $(ptr);
		var codigo_ant = $(ptr).parents("tr").find(".codigo_previo_cuentacontable").val();
		var codigo_nuevo = codigo.val();

		if (codigo_nuevo != codigo_ant)
		{
			let empresa_id = $("#empresa_id").val();

			if (!empresa_id)
				alert("Debe ingresar empresa");
			else
			{
				let url_cta = '/anitaERP/public/contable/cuentacontable/leercuentacontableporcodigo/'+empresa_id+'/'+codigo_nuevo;

				$.get(url_cta, function(data){
					$(codigo).parents("tr").find('.cuentacontable_id').val(data.id);
					$(codigo).parents("tr").find(".cuentacontable_id_previa").val(data.id);
					$(codigo).parents("tr").find(".nombrecuentacontable").val(data.nombre);
					if (data.manejaccosto === 'S')
					{
						$(codigo).parents("tr").find('.centrocostoasiento').attr("readonly", false);

						completarCentroCosto(codigo, data.id, 0);
					}
					else
					{
						$(codigo).parents("tr").find('.centrocostoasiento').empty();
						$(codigo).parents("tr").find('.centrocostoasiento').append('<option value="0" selected>Sin CC</option>');
						$(codigo).parents("tr").find('.centrocostoasiento').attr("readonly", true);
					}
				});

				//* Asigna nuevo codigo de cuenta
				$(this).parents("tr").find(".codigo_previo_cuentacontable").val(codigo_nuevo);
			}
		}
	}

	function completarCentroCosto(ptrcodigo, cuentacontable_id, centrocosto_id){
		let url_cta = '/anitaERP/public/contable/cuentacontable/leercuentacontablecentrocosto/'+cuentacontable_id;

		$.get(url_cta, function(data){
			if (data === "No maneja centro de costo")
			{
				$(ptrcodigo).parents("tr").find('.centrocostoasiento').empty();
				$(ptrcodigo).parents("tr").find('.centrocostoasiento').append('<option value="0" selected>Sin CC</option>');
				$(ptrcodigo).parents("tr").find('.centrocostoasiento').attr("readonly", true);
			}
			else
			{
				var cta = $.map(data, function(value, index){
					return [value];
				});
				$(ptrcodigo).parents("tr").find('.centrocostoasiento').empty();
				$(ptrcodigo).parents("tr").find('.centrocostoasiento').append('<option value="">-- Seleccione CC --</option>');
				$.each(cta, function(index,value){
					if (value.id == centrocosto_id)
						$(ptrcodigo).parents("tr").find('.centrocostoasiento').append('<option value="'+value.id+'" selected>'+value.codigo+'-'+value.nombre+'</option>');
					else
						$(ptrcodigo).parents("tr").find('.centrocostoasiento').append('<option value="'+value.id+'">'+value.codigo+'-'+value.nombre+'</option>');
				});
			}
        });
        setTimeout(() => {
        }, 3000);
    }

	function leeCotizacionAsiento(ptr)
	{
		let fecha = $('#fecha').val();
		let moneda_id = $(ptr).parents("tr").find('.monedaasiento').val();
		let url_cot = '/anitaERP/public/configuracion/leercotizacion/'+fecha+'/'+moneda_id;
	
		$.get(url_cot, function(data){
			$(ptr).parents("tr").find('.cotizacionasiento').val(data.cotizacionventa);
			sumaMontoAsiento();
		});
	}

	function sumaMontoAsiento()
	{
		let totalDebeAsiento = 0;
		let totalHaberAsiento = 0;
		let monedaDefault = $("#tbody-cuenta-asiento-table").children(':first').find('.monedaasiento').val();

		$("#tbody-cuenta-asiento-table .debeasiento").each(function() {
            let valor = parseFloat($(this).val());
			let moneda = $(this).parents("tr").find('.monedaasiento').val();
			let cotizacion = $(this).parents("tr").find('.cotizacionasiento').val();

            if (valor >= 0)
			{
				let coef = calculaCoeficienteMoneda(monedaDefault, moneda, cotizacion);

                totalDebeAsiento += valor * coef;
			}
        });

        $("#tbody-cuenta-asiento-table .haberasiento").each(function() {
            let valor = parseFloat($(this).val());
			let moneda = $(this).parents("tr").find('.monedaasiento').val();
			let cotizacion = $(this).parents("tr").find('.cotizacionasiento').val();

			if (valor >= 0)
			{
				let coef = calculaCoeficienteMoneda(monedaDefault, moneda, cotizacion);

				totalHaberAsiento += valor * coef;
			}
    	});
		$("#totaldebeasiento").val(totalDebeAsiento.toFixed(2));
		$("#totalhaberasiento").val(totalHaberAsiento.toFixed(2));
	}
