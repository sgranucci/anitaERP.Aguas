<h2> Stock por OT </h2>
<h1><strong>Estado de combinaciones: {{$estado}}</strong>&nbsp;Marca: {{$nombremarca}}</h1>
<h1><strong>Desde articulo: {{$desdearticulo}} Hasta articulo: {{$hastaarticulo}} </strong></h1>
<h1><strong>Desde linea: {{$desdelinea}} Hasta linea: {{$hastalinea}} </strong></h1>
<table>
	<thead>
		<tr>
			<th>Foto</th>
			<th>L&iacute;nea</th>
			<th>Art&iacute;culo</th>
			<th>Descripci&oacute;n</th>
			@for ($ii = config('consprod.DESDE_MEDIDA'); $ii <= config('consprod.HASTA_MEDIDA'); $ii++)
				<th>{{$ii}}</th>
			@endfor
			<th>Mod</th>
			<th>M</th>
			<th>Total</th>
			<th>Precio</th>
			<th>Situaci&oacute;n</th>
			<th>Numero OT (lote)</th>
		</tr>
	</thead>
	<tbody>
	@foreach ($data as $lote)

		@include('exports.stock.reportestockot.imprimeunrenglon')
			
	@endforeach

	</tbody>
</table>
