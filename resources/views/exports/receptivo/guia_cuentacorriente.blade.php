<h2> Cuenta Corriente de Guías </h2>
<h2> Guía: {{$guia_id}}-{{$nombreguia}} </h2>
<table> 
	<thead>
	<tr>
		<th class="width20">ID</th>
		<th>Fecha</th>
		<th>Orden de Servicio</th>
		<th>Rendición</th>
		<th>Mov. de Caja</th>
		<th>Moneda</th>
		<th style="text-align: right;">Debe</th>
		<th style="text-align: right;">Haber</th>
		<th style="width: 9%; text-align: right;">Saldo $</th>
		<th style="width: 9%; text-align: right;">Saldo U$S</th>
		<th style="width: 9%; text-align: right;">Saldo REA</th>
		<th style="width: 9%; text-align: right;">Saldo EA</th>
		<th style="width: 9%; text-align: right;">Saldo GUA</th>
	</tr>
  	</thead>
    <tbody>
		@for ($i = 1; $i <= 5; $i++)
			@php $saldo[$i] = 0; @endphp
		@endfor		
		@foreach ($cuentacorriente as $data)
			@php $saldo[$data->moneda_id] += $data->monto; @endphp
		<tr>
			<td>{{$data->id}}</td>
			<td>{{date("d/m/Y", strtotime($data->fecha ?? ''))}}</td>
			<td>{{$data->rendicionreceptivos->ordenservicio_id??$data->caja_movimientos->ordenservicio_id}}</td>
			<td>{{$data->rendicionreceptivos->numerotalonario??''}}</td>
			<td>{{$data->caja_movimientos->tipotransaccioncajas->nombre??''}} {{$data->caja_movimientos->numerotransaccion??''}}</td>
			<td>{{$data->monedas->abreviatura}}</td>
			<td style="text-align: right;">
				@if ($data->monto >= 0)
					{{number_format($data->monto, 2)}}
				@endif
			</td>
			<td style="text-align: right;">
				@if ($data->monto < 0)
					{{number_format(abs($data->monto), 2)}}
				@endif
			</td>
			@for ($i = 1; $i <= 5; $i++)
				<td style="text-align: right;">
					{{number_format($saldo[$i], 2)}}
				</td>
			@endfor
		</tr>
		@endforeach
	</tbody>
</table>
