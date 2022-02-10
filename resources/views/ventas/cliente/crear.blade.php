@extends("theme.$theme.layout")
@section('titulo')
    Clientes
@endsection

@section("scripts")
<script src="{{asset("assets/pages/scripts/admin/crear.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/pages/scripts/admin/domicilio.js")}}" type="text/javascript"></script>
<script>
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

        $("#botonform0").click(function(){
            $(".form2").hide();
            $(".form1").show();

            $("#form-general").submit();
        });

        $("#botonform1").click(function(){
            $(".form2").hide();
            $(".form1").show();
        });

        $("#botonform2").click(function(){
            $(".form1").hide();
            $(".form2").show();
        });
    });
</script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.mensaje')
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">Crear Cliente</h3>
                <div class="card-tools">
                    <a href="{{route('cliente')}}" class="btn btn-outline-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            <form action="{{route('guardar_cliente')}}" id="form-general" class="form-horizontal form--label-right" method="POST" autocomplete="off">
                @csrf
                <div class="card-body" style="padding-bottom: 0; padding-top: 5px;">
                    @include('ventas.cliente.form1')
                    @include('ventas.cliente.form2')
                </div>
                <div class="card-footer">
                	<div class="row">
                   		<div class="col-lg-6">
							<button type="button" id="botonform0" class="btn btn-success">
						   	<i class="fa fa-save"></i> Guardar
							</button>
                    	</div>
            			<div class="col-lg-6" align="right">
							<button type="button" id="botonform1" class="btn btn-primary btn-sm">
						   	<i class="fa fa-user"></i> Datos principales
							</button>
							<button type="button" id="botonform2" class="btn btn-info btn-sm">
         						<span class="fa fa-cash-register"></span> Datos facturac&oacute;n
      						</button>
							<button type="button" id="botonform2" class="btn btn-info btn-sm">
         						<span class="fa fa-cash-register"></span> Lugares de entrega
      						</button>
							<button type="button" id="botonform2" class="btn btn-info btn-sm">
         						<span class="fa fa-cash-register"></span> Archivos asociados
      						</button>
            			</div>
            		</div>
            	</div>
            </form>
        </div>
    </div>
</div>
@endsection
