// Scripts para carga de pedidos

	var talles_txt;
	var medidas_txt;
	var precios_txt;
	var tallesid_txt;
	var cantidadmodal_txt;
	var nombre_modulo;
	var descripcion_articulo;
	var nombre_combinacion;;
	var tbl_medidas;
	var medidas=[];
	var cantidades=[];
	var precios=[];
	var dpr=[];
	var dlp=[];
	var dii=[];
	var dmo=[];
	var totPares;
	var cantidad;
	var precio;

    function completarCombinaciones(articulo, combinacion_id){
        var comb_id;
		var articulo_id = $(articulo).val();
        $.get('/anitaERP/public/stock/leercombinaciones/'+articulo_id, function(data){
            var comb = $.map(data, function(value, index){
                return [value];
            });
            $(articulo).parents("tr").find('.combinacion').empty();
            $(articulo).parents("tr").find('.combinacion').append('<option value=""></option>');
            $.each(comb, function(index,value){
				if (value.id == combinacion_id)
                	$(articulo).parents("tr").find('.combinacion').append('<option value="'+value.id+'" selected>'+value.codigo+'-'+value.nombre+'</option>');
				else
                	$(articulo).parents("tr").find('.combinacion').append('<option value="'+value.id+'">'+value.codigo+'-'+value.nombre+'</option>');
            });
        });
        setTimeout(() => {
                var comb_id = $("#combinacion_id").val();
                if (comb_id != undefined) {
                    completarModulo(comb_id, 0);
                }
        }, 3000);
    }

    function completarModulos(articulo, modulo_id){
        var comb_id;
		var eligioModulo = false;
		var articulo_id = $(articulo).val();
		var flTieneModuloAbierto = false;
        $.get('/anitaERP/public/stock/leermodulos/'+articulo_id+'/'+modulo_id, function(data){
            var mod = $.map(data, function(value, index){
                return [value];
            });
            $(articulo).parents("tr").find('.modulo').empty();
            $(articulo).parents("tr").find('.modulo').append('<option value=""></option>');
			flTieneModuloAbierto = false;
            $.each(mod, function(index,value){
			  	if (value.id == 30)
				  	flTieneModuloAbierto = true;

				if (value.id == modulo_id)
				{
                	$(articulo).parents("tr").find('.modulo').append('<option value="'+value.id+'" selected>'+value.nombre+'</option>');
					eligioModulo = true;
				}
				else
                	$(articulo).parents("tr").find('.modulo').append('<option value="'+value.id+'">'+value.nombre+'</option>');
            });

			// Agrega modulo abierto
			if (!flTieneModuloAbierto)
            	$(articulo).parents("tr").find('.modulo').append('<option value="'+'30'+'">'+'Abierto'+'</option>');
        });
    }

	function completarTalles(modulo_id)
	{
		talles_txt = "";
		medidas_txt = "";
		precios_txt = "";
		tallesid_txt = "";
		nombre_modulo = "";

		// Lee talles del modulo
        $.get('/anitaERP/public/stock/leertalles/'+modulo_id, function(data){
			var flEncontro, flHayMedidas;

           	var tall = $.map(data, function(value, index){
               	return [value];
           	});
			talles_txt = "<table class='table-bordered table-striped'><tr>";
			medidas_txt = "<tr>";
			precios_txt = "<tr>";
			tallesid_txt = "<tr>";

			// Arma variables model
			cantidadmodal_txt = " autofocus ";
           	$.each(tall, function(index,value){
				nombre_modulo = value.nombre;
				for (var t in value.talles) {
					flEncontro = false;
					flHayMedidas = false;
					for (let s in medidas) 
					{
						flHayMedidas = true;
						if (parseFloat(value.talles[t].id) === parseFloat(medidas[s]))
						{
							var cant = parseFloat(cantidades[s]);
							var prec = parseFloat(precios[s]);

							agregaMedida(value.talles[t].nombre, cant, prec, value.talles[t].id);
							flEncontro = true;
							break;
						}
					}
					if (!flEncontro)
					{
						if (flHayMedidas)
							agregaMedida(value.talles[t].nombre, '', 0, value.talles[t].id);
						else
							agregaMedida(value.talles[t].nombre, (value.talles[t].pivot.cantidad == 0 ? '' : value.talles[i].pivot.cantidad), 0, value.talles[t].id);
					}
				}
			});
			talles_txt = talles_txt + "</tr>";
			medidas_txt = medidas_txt + "</tr>";
			precios_txt = precios_txt + "</tr>";
			tallesid_txt = tallesid_txt + "</tr>";
    	});
	}

	function agregaMedida(Ptalle, Pcant, Pprec, Ptalle_id)
	{
    	talles_txt = talles_txt + "<th><input name='medidasportalles[]' class='medidasportalles' style='width:30px; text-align:center; background-color   : #D2D8DC;' type='text' readonly value='"+Ptalle+"'></input></th>";
    	medidas_txt = medidas_txt + "<th><input name='cantidadesportalles[]' "+cantidadmodal_txt+" class='cantidadesportalles' style='width:30px;' type='text' value='"+Pcant+"'></input></th>";
    	precios_txt = precios_txt + "<th><input name='preciosportalles[]' class='preciosportalles' type='hidden' value='"+Pprec+"'></input></th>";
    	tallesid_txt = tallesid_txt + "<th><input name='tallesid[]' class='tallesid' type='hidden' value='"+Ptalle_id+"'></input></th>";
		cantidadmodal_txt = "";
	}

	function asignaPrecio(Particulo_id, Ptalle_id)
	{
		// Lee talles del modulo
        $.get('/anitaERP/public/stock/asignaprecio/'+Particulo_id+'/'+Ptalle_id, function(data){
           	var prec = $.map(data, function(value, index){
               	return [value];
           	});
			dpr=[];
			dlp=[];
			dii=[];
			dmo=[];
           	$.each(prec, function(index,value){
				dpr.push(value.precio);
				dlp.push(value.listaprecio_id);
				dii.push(value.incluyeimpuesto);
				dmo.push(value.moneda_id);
			});
		});
        setTimeout(() => {
			return(precio);
        }, 300);
	}

    $(function () {
		var articulo_id;
		var combinacion_id;
		var modulo_id;

		// Completa combinaciones y modulos al abrir pedido
		$("#tbody-tabla .articulo").each(function(index) {
			var articulo = $(this);
			var combinacion = $(this).parents("tr").find(".combinacion").val();
			var combinacion_id = $(this).parents("tr").find(".combinacion_id_previa").val();
			var modulo_id = $(this).parents("tr").find(".modulo_id_previa").val();

        	completarCombinaciones(articulo, combinacion_id);
        	completarModulos(articulo, modulo_id);
		});

		activa_eventos(true);
	});

	function activa_eventos(flInicio)
	{
		// Si esta agregando items desactiva los eventos
		if (!flInicio)
		{
			$('.articulo').off('click');
			$('.articulo').off('change');
        	$(".modulo").off('change');
        	$(".cantidad").off('click keydown');
			$('#medidasModal').off('show.bs.modal');
			$('#cierraModal').off('click');
			$('#aceptaModal').off('click');
			$('#medidasModal').off('hidden.bs.modal');
			$(document).off('change', '.desc_combinacion');
			$(document).off('change', '.desc_modulo');
			$(document).off('change', '.cantidadesportalles');
		}

		$('.articulo').on('click', function (event) {

			var select = $(this);
      		var options = select.children();
			var articulo_id = $(this).val();
			var mventa_id = $('#mventa_id').val();
			var mventa_nombre = $("#mventa_id option:selected").text();
			let sel_articulos = JSON.parse(document.querySelector('#marca').dataset.articulo);

			select.empty();

			if (mventa_nombre === "-- Seleccionar marca --")
				select.append('<option value="">-- Articulos sin filrar --</option>');
			else
				select.append('<option value="">-- Articulos ' + mventa_nombre + ' --</option>');

			$.each(sel_articulos, function(obj, item) {
				if (articulo_id == item.id)
					op = 'selected="selected"';
				else
					op = '';
				if (mventa_id == undefined || mventa_id == '')
					select.append('<option value="' + item.id + '"'+op+'>' + item.descripcion + '</option>');
				else
				{
					if (item.mventa_id == mventa_id)
						select.append('<option value="' + item.id + '"'+op+'>' + item.descripcion + '</option>');
				}
			});

			if (articulo_id > 0)
			{
				select.value = articulo_id;

				select.children().filter(function(){
    				return this.text == articulo_id;
				}).prop('selected', true);
			}

		});

		$('.articulo').on('change', function (event) {
			event.preventDefault();
			var articulo = $(this);
			var articulo_ant = $(this).parents("tr").find(".articulo_id_previo").val();
			var articulo_nuevo = articulo.val();

			if (articulo_nuevo != articulo_ant)
			{
            	completarCombinaciones(articulo, 0);
            	completarModulos(articulo, 0);

				//* Asigna nuevo articulo
				$(this).parents("tr").find(".articulo_id_previo").val(articulo_nuevo);
			}
        });

        $(".modulo").on('change', function() {
			modulo_id = $(this).parents("tr").find(".modulo").val();
		});

		// Con click sobre cantidad abre modal de medidas
        $(".cantidad").on('click keydown', function() {
			cantidad = $(this);

			articulo_id = $(this).parents("tr").find(".articulo").val();
			descripcion_articulo = $(this).parents("tr").find(".articulo option:selected").text();
			modulo_id = $(this).parents("tr").find(".modulo").val();
			combinacion_id = $(this).parents("tr").find(".combinacion").val();
			nombre_combinacion = $(this).parents("tr").find(".combinacion option:selected").text();

			// Lee tabla de medidas
			var val_medida = $(this).parents("tr").find(".medidas").val();

			medidas=[];
			cantidades=[];
			precios=[];

			if (val_medida != '')
			{
				var tbl_medidas = JSON.parse(val_medida);

           		$.each(tbl_medidas, function(index,value){
					medidas.push(value.talle_id);
					cantidades.push(value.cantidad);
					precios.push(value.precio);
				});
			}

			completarTalles(modulo_id);

        	setTimeout(() => {
				$("#medidasModal").modal('show');
        	}, 300);
        });

		// Controla apertura modal de medidas
		$('#medidasModal').on('show.bs.modal', function (event) {
  			var modal = $(this);

  			modal.find('.modal-title').text('Medidas item '+descripcion_articulo+' Combinacion '+nombre_combinacion+' Modulo '+nombre_modulo);
  			modal.find('#medidasModal').empty();
  			modal.find('#medidasModal').append(talles_txt+medidas_txt+precios_txt+tallesid_txt);
			sumaPares();
		});

		// Autofocus en modal de medidas
		$(document).on('shown.bs.modal', '.modal', function() {
  			$(this).find('[autofocus]').focus();
		});

		$('#cierraModal').on('click', function () {
		});

		// Acepta modal
		$('#aceptaModal').on('click', function () {
		  	let jsonObject = new Array();

			med = [];
			$(".medidasportalles").each(function() {
            	med.push($(this).val());
			});
			talleid = [];
			$(".tallesid").each(function() {
            	talleid.push($(this).val());
			});
			cant = [];
			$(".cantidadesportalles").each(function() {
            	cant.push($(this).val());
			});
        	prec = []
        	$(".preciosportalles").each(function(){
            	prec.push($(this).val());
        	});

			let jsonTallesId = JSON.stringify(talleid); 

			asignaPrecio(articulo_id, jsonTallesId);

			off = 0;
		    var flError = false;
        	setTimeout(() => {
			for (let i in med) 
			{
				if (cant[i] == '')
					cant[i] = 0;
			  	jsonObject.push({
					medida: med[i],
				  	cantidad: cant[i],
				  	precio: dpr[i],
				  	listaprecio: dlp[i],
				  	incluyeimpuesto: dii[i],
				  	moneda: dmo[i],
				  	talle_id: talleid[i]
				});
			  	// Valida cantidades que tengan precio
			    if (cant[i] > 0 && dpr[i] == 0)
			  	{
					flError = true;	  	
				    alert('Medida '+med[i]+' Cantidad '+cant[i]+' No tiene precio asignado');
			  	}
				if (dpr[i] > 0)
					off = i;		
			}

			let jsonString = JSON.stringify(jsonObject); 

			// Asigna medidas, cantidades y precios
			$(cantidad).parents('tr').find('.medidas').val(jsonString);

			// Asigna variables de precio
			var pre = fNumero(dpr[off], 2);
			var lis = fNumero(dlp[off], 0);
			var inc = fNumero(dii[off], 0);
			var mon = fNumero(dmo[off], 0);
			if (pre === 'NaN' || pre < 0 || pre > 9999999999)
			  	pre = 0;
	
			$(cantidad).parents('tr').find('.precio').val(pre);
			$(cantidad).parents('tr').find('.listaprecio_id').val(lis);
			$(cantidad).parents('tr').find('.incluyeimpuesto').val(inc);
			$(cantidad).parents('tr').find('.moneda_id').val(mon);
	
        	}, 300);

			$('#medidasModal').modal('hide');

			// Asigna total de pares a la cantidad del item en el formulario
			sumaPares();
			$(cantidad).val(totPares);
		});

		$('#medidasModal').on('hidden.bs.modal', function () {

			// Inicializa variables modal
			talles_txt = "";
			medidas_txt = "";
			precios_txt = "";
			tallesid_txt = "";
		});

		// Llena variable desc_combinacion
		$(document).on('change', '.desc_combinacion', function(event) {
     		$(this).val($(".combinacion option:selected").text());
		});
		// Llena variable desc_modulo
		$(document).on('change', '.desc_modulo', function(event) {
     		$(this).val($(".modulo option:selected").text());
		});
		$(document).on('change', '.cantidadesportalles', function(event) {
			sumaPares();
		});
    }

	function sumaPares()
	{
		totPares = 0;

		$(".cantidadesportalles").each(function() {
			if (parseFloat($(this).val()) >= 1 && parseFloat($(this).val()) <= 999999)
				totPares += parseFloat($(this).val());
		});
		$("#totPares").val(totPares.toFixed(0));
	}

	// Manejo de grilla 

    $(function () {
        $('#agrega_renglon').on('click', agregaRenglon);
        $(document).on('click', '.eliminar', borraRenglon);

		// Si no tiene items agrega el primero
		if(!$('.item-pedido').length)
			agregaRenglon();
    });

    function agregaRenglon(){
		if (event != undefined)
        	event.preventDefault();
        var renglon = $('#template-renglon').html();

        $("#tbody-tabla").append(renglon);
        actualizaRenglones();

		activa_eventos(false);
    }

    function borraRenglon() {
        event.preventDefault();
        $(this).parents('tr').remove();
        actualizaRenglones();
    }

    function actualizaRenglones() {
        var item = 1;

        $("#tbody-tabla .item").each(function() {
            $(this).val(item++);
        });
    }

