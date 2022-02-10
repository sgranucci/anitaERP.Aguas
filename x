<!doctype html>
<html lang="es">
<head>
    <link rel="stylesheet" href="{{asset("assets/$theme/dist/css/adminlte.min.css")}}">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<style type="text/css">
	</style>
</head>
<body>
<div class="row">
<div class="card-body table-responsive p-0">
    <div class="mt-5">
		<strong>Pedido Nro.: {{$pedido->id ?? ''}}</strong><br>
		<strong>Fecha: {{date("d/m/Y", strtotime($pedido->fecha ?? ''))}} </strong><br>
		<strong>Cliente: {{ $pedido->clientes->nombre ?? ''}}</strong><br>
		<strong>Marca: {{ $pedido->mventas->nombre ?? ''}}</strong><br>
		<strong>Transporte: {{ $pedido->transportes->nombre ?? ''}}</strong><br>
		<strong>Lugar de entrega: {{ $pedido->lugarentrega ?? ''}}</strong><br>
	</div>
<table> 
	<thead>
    <tr>
       	<th>Sku</th>
       	<th>Descripcion</th>
       	<th>Combinacion</th>
       	<th>Modulo</th>
       	<th>Pares</th>
       	<th>Precio</th>
    </tr>
  	</thead>
    <tbody>
	@foreach ($pedido->pedido_combinaciones as $item)
        <tr>
			<td>{{ $item->articulos->sku }}</td>
			<td>{{ $item->articulos->descripcion }}</td>
			<td>{{ $item->combinaciones->codigo }}-{{ $item->combinaciones->nombre }}</td>
			<td>{{ $item->modulo_id }}</td>
			<td>{{ number_format($item->cantidad, 0) }}</td>
			<td>{{ number_format($item->precio, 2) }}</td>
        </tr>
    @endforeach
	</tbody>
</table>
</div>
</div>
</body>
</html>
