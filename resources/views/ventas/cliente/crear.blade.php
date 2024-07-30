@extends("theme.$theme.layout")
@section('titulo')
    Clientes
@endsection

@section("styles")

input:invalid {
  background-color: pink;
}

@endsection

@section("scripts")
<script src="{{asset("assets/pages/scripts/admin/crear.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/pages/scripts/admin/domicilio.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/pages/scripts/ventas/cliente/domicilioentrega.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/pages/scripts/ventas/cliente/crear.js")}}" type="text/javascript"></script>
<script>
$( "#botonform0" ).click(function() {
  $( "#form-general" ).submit();
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
                <h3 class="card-title">Crear Cliente @if ($tipoalta == 'P') Provisorio @endif</h3>
                <div class="card-tools">
                    <a href="{{route('cliente')}}" class="btn btn-outline-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            @if ($tipoalta == 'P')
                <form action="{{route('guardar_cliente_provisorio')}}" id="form-general" class="form-horizontal form--label-right" method="POST" autocomplete="off">
            @else
                <form action="{{route('guardar_cliente')}}" id="form-general" class="form-horizontal form--label-right" method="POST" autocomplete="off">
            @endif
                @csrf
                <div class="card-body" style="padding-bottom: 0; padding-top: 5px;">
                    @include('ventas.cliente.form1')
                    @include('ventas.cliente.form2')
                    @include('ventas.cliente.form3')
                    @include('ventas.cliente.form4')
                    @include('ventas.cliente.form5')
                </div>
                <div class="card-footer">
                	<div class="row">
                   		<div class="col-lg-4">
							<button type="button" id="botonform0" class="btn btn-success">
						   	<i class="fa fa-save"></i> Guardar
							</button>
                    	</div>
            			<div class="col-lg-8" align="right">
							<button type="button" id="botonform1" class="btn btn-primary btn-sm">
						   	<i class="fa fa-user"></i> Datos principales
							</button>
							<button type="button" id="botonform2" class="btn btn-info btn-sm">
         						<span class="fa fa-cash-register"></span> Datos facturac&oacute;n
      						</button>
							<button type="button" id="botonform3" class="btn btn-info btn-sm">
         						<span class="fa fa-truck"></span> Lugares de entrega
      						</button>
							<button type="button" id="botonform4" class="btn btn-info btn-sm">
         						<span class="fa fa-comment"></span> Leyendas
      						</button>
							<button type="button" id="botonform5" class="btn btn-info btn-sm">
         						<span class="fa fa-copy"></span> Archivos asociados
      						</button>
            			</div>
            		</div>
            	</div>
            </form>
        </div>
    </div>
</div>
@endsection
