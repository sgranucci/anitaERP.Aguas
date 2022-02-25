@extends("theme.$theme.layout")
@section('titulo')
    Pedidos de clientes
@endsection

@section("scripts")
<script src="{{asset("assets/pages/scripts/admin/crear.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/pages/scripts/ventas/pedido/crear.js")}}" type="text/javascript"></script>
<script>
function sub()
{
	$('#form-general').submit();
}

   function completarCliente_Entrega(cliente_id){
        var loc_id, fl_tiene_entrega = false;
        $.get('/anitaERP/public/ventas/leercliente_entrega/'+cliente_id, function(data){
            var entr = $.map(data, function(value, index){
                return [value];
            });
            $("#cliente_entrega_id").empty();
            $("#cliente_entrega_id").append('<option value=""></option>');
            $.each(entr, function(index,value){
                $("#cliente_entrega_id").append('<option value="'+value.id+'">'+value.nombre+'</option>');
				fl_tiene_entrega = true;
            });
			if (fl_tiene_entrega)
			{
			  $("#divcodigoentrega").show();
			  $("#divlugar").hide();
			}
			else
			{
			  $("#divcodigoentrega").hide();
			  $("#divlugar").show();
			}
        });
        setTimeout(() => {
        }, 3000);
    }

    $(function () {
        $("#cliente_id").change(function(){
            var cliente_id = $(this).val();
            completarCliente_Entrega(cliente_id);
    	});

		$("#divlugar").show();
		$("#divcodigoentrega").hide();

        var cliente_id = $("#cliente_id").val();
        completarCliente_Entrega(cliente_id);
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
                <h3 class="card-title">Crear Pedidos de clientes</h3>
                <div class="card-tools">
                    <a href="{{route('pedido')}}" class="btn btn-outline-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            <form action="{{route('guardar_pedido')}}" id="form-general" class="form-horizontal form--label-right" method="POST" autocomplete="off">
                @csrf
                <div class="card-body">
                    @include('ventas.pedido.form')
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-lg-6">
							<button type="submit" onclick="sub()" class="btn btn-success">Guardar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
