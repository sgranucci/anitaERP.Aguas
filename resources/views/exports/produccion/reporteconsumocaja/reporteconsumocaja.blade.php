<h2> Reporte de Consumos de Cajas </h2>
<h1><strong>Ordenes de trabajo: {{$ordenestrabajo ?? '- - -'}}</strong>&nbsp;
	<strong>Desde: {{date("d/m/Y", strtotime($desdefecha ?? ''))}} </strong>&nbsp;
	<strong>Hasta: {{date("d/m/Y", strtotime($hastafecha ?? ''))}} </strong>
</h1>
<table>
	<thead>
    <tr>
       	<th>Caja</th>
		<th>Medida</th>
		<th>Art&iacute;culo</th>
       	<th>Total</th>
    </tr>
  	</thead>
    <tbody>
	
	@foreach ($data as $caja)
		
		@include('exports.produccion.reporteconsumocaja.imprimeunrenglon')
			
	@endforeach

	</tbody>
</table>
