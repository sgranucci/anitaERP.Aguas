@extends("theme.$theme.layout")
@section('titulo')
	Crear Ordenes de trabajo
@endsection

@section("scripts")
<script src="{{asset("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
<script>
	var checksId=[];
	var leyendas=[];

    $(function () {
    	// Cierra modal 
    	$('#cierraOrdenTrabajoModal').on('click', function () {
    	});
	
    	// Acepta modal
    	$('#aceptaOrdenTrabajoModal').on('click', function () {
			var leyenda = JSON.stringify($("#leyendaot").val());
			var checkotstock = $("input:checkbox[class=checkboxotstock]:checked").val();
			var ids = JSON.stringify(checksId);
	
			$('#crearOrdenTrabajoModal').modal('hide');
	
			if (checkotstock == 'on')
				var listarUri = "/anitaERP/public/ventas/guardaordenestrabajo/ordentrabajo/"+ids+"/on/"+leyenda;
			else
				var listarUri = "/anitaERP/public/ventas/guardaordenestrabajo/ordentrabajo/"+ids+"/off/"+leyenda;
	
			window.location.href = listarUri;
    	});
	});

	function generaOt()
	{
		checksId=[];
		leyendas=[];
		$('input[type=checkbox]:checked').each(function() {
			var itemId = $(this).parents('tr').find('.ids').html();
			var leyenda = $(this).parents('tr').find('.observaciones').html();
			checksId.push(itemId);
			leyendas.push(leyenda);
		});
		
		$("#leyendaot").val(leyendas);
		$("#crearOrdenTrabajoModal").modal('show');
	}

</script>
@endsection

<?php use App\Helpers\biblioteca ?>

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Crear Ordenes de trabajo</h3>
				@if ($datas[0] ?? '')
					<h3 class="card-title">&nbsp; Art&iacute;culo: {{$datas[0]->pedido_combinaciones[0]->articulo_id}}-{{$datas[0]->pedido_combinaciones[0]->articulos->descripcion}}</h2>
					<h3 class="card-title">&nbsp; Combinaci&oacute;n: {{$datas[0]->pedido_combinaciones[0]->combinaciones->nombre}}</h2>
				@endif
                <div class="card-tools">
                    @if (can('crear-ordenes-de-trabajo', false))
						<button type="submit" onclick="generaOt()" class="btn btn-primary">
                    		<i class="fa fa-fw fa-industry"></i>Genera OT
						</button>
					@endif
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped table-bordered table-hover" id="tabla-data">
                    <thead>
                        <tr>
                            <th class="width20">ID</th>
                            <th>Fecha Pedido</th>
                            <th>Entrega</th>
                            <th>Pedido</th>
                            <th>Cliente</th>
                            <th>Pares</th>
                            <th>Observacion</th>
                            <th>OT</th>
                            <th class="width80" data-orderable="false">Genera OT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $pedido)
                        	@foreach ($pedido->pedido_combinaciones as $data)
                        	<tr>
                            	<td class="ids">{{$data->id}}</td>
            					<td>{{date("d/m/Y", strtotime($pedido->fecha ?? ''))}}</td>
            					<td>{{date("d/m/Y", strtotime($pedido->fechaentrega ?? ''))}}</td>
                            	<td>{{$pedido->codigo}}</td>
                            	<td>{{$pedido->clientes->nombre}}</td>
                            	<td>{{round($data->cantidad,0)}}</td>
                            	<td class="observaciones">{{$data->observacion}}</td>
                            	<td>{{$data->ot_id}}</td>
                            	<td>
									<input name="checks[]" class="checkImpresion" type="checkbox" autocomplete="off">
                            	</td>
                        	</tr>
                        	@endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('ventas.ordentrabajo.modalcrearordentrabajo')

@endsection
