
    function completarLetra(condicioniva_id){
		var condiva = "{{ $condicioniva_query }}";
		const replace = '"';
		var data = condiva.replace(/&quot;/g, replace);
		var dataP = JSON.parse(data);

		$.each(dataP, (index, value) => {
			if (value['id'] == condicioniva_id)
				$("#letra").val(value['letra']);
  		});
	}

    $(function () {
        $("#condicioniva_id").change(function(){
            var  condicioniva_id = $(this).val();
            completarLetra(condicioniva_id);
        });

        $("#botonform1").click(function(){
            $(".form2").hide();
            $(".form1").show();
        });

        $("#botonform2").click(function(){
            $(".form1").hide();
            $(".form2").show();
        });

        completarLetra({{$data->condicioniva_id}});
    });

