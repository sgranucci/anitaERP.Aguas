<!DOCTYPE html>
<html>
	<title>Comprobantes de Ventas</title>
	<head>
		<style>
			table {
				font-family: arial, sans-serif;
				border-collapse: collapse;
				width: 100%;
			}
			td, th {
				boder: 1px solid #dddddd;
				text-align: left;
				padding: 8px;
			}
			tr:nth-child(even) {
				background-color: #dddddd;
			}
		</style>
	</head>
	<body>
		<h2>Cuenta Corriente {{$nombreguia}}</h2>
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th class="width20">ID</th>
					<th>Fecha</th>
					<th>Orden de Servicio</th>
					<th>Rendición</th>
					<th>Mov. de Caja</th>
					<th>Moneda</th>					
					<th style="width: 12%; text-align: right;">Debe</th>
					<th style="width: 12%; text-align: right;">Haber</th>
					<th style="width: 12%; text-align: right;">Saldo</th>
				</tr>
			</thead>
			<tbody>
				@php $saldo = 0; @endphp
				@foreach ($cuentacorriente as $data)
					@php $saldo += $data->monto; @endphp
				<tr>
					<td>{{$data->id}}</td>
					<td>{{date("d/m/Y", strtotime($data->fecha ?? ''))}}</td>
					<td>{{$data->rendicionreceptivos->ordenservicio_id}}</td>
					<td>{{$data->rendicionreceptivos->numerotalonario}}</td>
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
					<td style="text-align: right;">
						{{number_format($saldo, 2)}}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</body>
</html>