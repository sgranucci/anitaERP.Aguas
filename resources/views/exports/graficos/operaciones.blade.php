<h2> OPERACIONES</h2>
<table> 
	<thead>
    <tr>
       	<th>ID Señal</th>
       	<th>ID Trade</th>
		<th>Nro.de Contratos</th>
       	<th>Evento</th>
       	<th>Tipo de Operación</th>
       	<th>Fecha</th>
       	<th>Desde Hora</th>
       	<th>Hasta Hora</th>
		<th>Zona (Open)</th>
		<th>Zona (High)</th>
		<th>Zona (Low)</th>
		<th>Zona (Close)</th>
		<th>EWO</th>
		<th>Banda Sup.</th>
		<th>Banda Inf.</th>
		<th>Precio Entrada</th>
		<th>Stop Loss</th>
		<th>Target</th>
		<th>Swing Bars</th>
		<th>Contra Swing Bars</th>
		<th>RV</th>
		<th>Retroceso</th>
		<th>Riesgo (en puntos)</th>
		<th>Riesgo (en ticks)</th>
		<th>Riesgo (en $)</th>
		<th>Retorno (en puntos)</th>
		<th>Retorno (en ticks)</th>
		<th>Retorno (en $)</th>
		<th>RRR</th>
		<th>Precio de Cierre</th>
		<th>P and L (en puntos)</th>
		<th>P and L (en ticks)</th>
		<th>P and L (en $)</th>
		<th>MPC</th>
		<th>MPF</th>
		<th>Eficiencia Entrada</th>
		<th>Eficiencia Salida</th>
	</tr>
  	</thead>
    <tbody>
	@php $anterIdSenial = 0; @endphp
    @foreach ($operaciones as $data)
		@if ($data['idSenial'] != $anterIdSenial && $anterIdSenial != 0)
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		@endif
		@php $anterIdSenial = $data['idSenial']; @endphp
        <tr>
			<td>{{$data['idSenial']}}</td>
			<td>{{$data['idTrade']}}</td>
			<td>{{$data['numeroContratos']}}</td>
			<td>{{$data['evento']}}</td>
			<td>{{$data['tipoOperacion']}}</td>
           	<td>{{$data['fechastr']}}</td>
			<td>{{$data['desdeHora']}}
           	<td>{{date('H:i:s', ceil($data['fecha']/1000))}}</td>
			<td>{{$data['zonaOpen']}}</td>
			<td>{{$data['zonaHigh']}}</td>
			<td>{{$data['zonaLow']}}</td>
			<td>{{$data['zonaClose']}}</td>
			<td align="right">{{number_format(floatval($data['ewo']), 4, ",", ".")}}</td>
			<td align="right">{{number_format(floatval($data['bandaSup']), 4, ",", ".")}}</td>
			<td align="right">{{number_format(floatval($data['bandaInf']), 4, ",", ".")}}</td>			
			<td align="right">{{number_format(floatval($data['precioEntrada']), 4, ",", ".")}}</td>
			<td align="right">{{number_format(floatval($data['stopLoss']), 2, ",", ".")}}</td>
			<td align="right">{{number_format(floatval($data['target']), 2, ",", ".")}}</td>
			<td align="right">{{number_format(floatval($data['swingBars']), 0, ",", ".")}}</td>
			<td align="right">{{number_format(floatval($data['contraSwingBars']), 0, ",", ".")}}</td>
			<td align="right">{{number_format(floatval($data['rv']), 8, ",", ".")}}</td>
			<td align="right">{{number_format(floatval($data['retroceso']), 5, ",", ".")}}</td>
			<td align="right">{{number_format(floatval($data['riesgoPuntos']), 2, ",", ".")}}</td>
			<td align="right">{{number_format(floatval($data['riesgoTicks']), 0, ",", ".")}}</td>
			<td align="right">{{number_format(floatval($data['riesgoPesos']), 2, ",", ".")}}</td>
			<td align="right">{{number_format(floatval($data['retornoPuntos']), 4, ",", ".")}}</td>
			<td align="right">{{number_format(floatval($data['retornoTicks']), 0, ",", ".")}}</td>
			<td align="right">{{number_format(floatval($data['retornoPesos']), 2, ",", ".")}}</td>
			<td align="right">{{number_format(floatval($data['rrr']), 8, ",", ".")}}</td>
			<td align="right">{{number_format(floatval($data['precioCierre']), 2, ",", ".")}}</td>
			<td align="right">{{number_format(floatval($data['plPuntos']), 2, ",", ".")}}</td>
			<td align="right">{{number_format(floatval($data['plTicks']), 0, ",", ".")}}</td>
			<td align="right">{{number_format(floatval($data['plPesos']), 2, ",", ".")}}</td>
			<td align="right">{{number_format(floatval($data['mpc']), 2, ",", ".")}}</td>
			<td align="right">{{number_format(floatval($data['mpf']), 2, ",", ".")}}</td>
			<td align="right">{{number_format(floatval($data['eficienciaEntrada']), 2, ",", ".")}}%</td>
			<td align="right">{{number_format(floatval($data['eficienciaSalida']), 2, ",", ".")}}%</td>
		</tr>
    @endforeach
	</tbody>
</table>
