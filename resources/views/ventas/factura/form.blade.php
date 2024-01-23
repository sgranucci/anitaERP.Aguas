<div class="row">
	<div class="col-sm-6" id="datosfactura" data-puntoventa="{{$puntoventa_query}}" data-tipotransaccion="{{$tipotransaccion_query}}" data-incoterm="{{$incoterm_query}}" data-formapago="{{$formapago_query}}">
		<input type="hidden" id="codigofactura" class="form-control" value="{{old('codigofactura', $factura->codigo ?? '')}}" />
		<div class="form-group row" id="tipotransaccion">
			<label for="recipient-name" class="col-lg-3 col-form-label requerido">Tipo de transacci&oacute;n</label>
			<select name="tipotransaccion_id" id="tipotransaccion_id" data-placeholder="Tipo de transacci&oacute;n" class="col-lg-6 form-control" data-fouc required>
				<option value="">-- Seleccionar transacción  --</option>
				@foreach($tipotransaccion_query as $key => $value)
					@if( (int) $value->id == (int) old('tipotransaccion_id', $factura->tipotransaccion_id ?? ''))
						<option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
					@else
						<option value="{{ $value->id }}">{{ $value->nombre }}</option>    
					@endif
				@endforeach	
			</select>
		</div>
		<div class="form-group row" id="puntoventa">
			<label for="recipient-name" class="col-lg-3 col-form-label requerido">Punto de venta</label>
			<input type="hidden" id="puntoventaori_id" class="form-control" value="{{old('puntoventaori_id', $factura->puntoventa_id ?? '')}}" />
			<select name="puntoventa_id" id="puntoventa_id" data-placeholder="Punto de venta" class="col-lg-5 form-control required" data-fouc>
			</select>
		</div>
		<div class="form-group row">
   			<label for="cliente" class="col-lg-3 col-form-label requerido">Cliente</label>
        	<select name="cliente_id" id="cliente_id" data-placeholder="Cliente" class="col-lg-8 form-control required" data-fouc>
        		<option value="">-- Seleccionar cliente --</option>
        		@foreach($cliente_query as $key => $value)
        			@if( (int) $value->id == (int) old('cliente_id', $factura->cliente_id ?? ''))
        				<option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
        			@else
        				<option value="{{ $value->id }}">{{ $value->nombre }}</option>    
        			@endif
        		@endforeach
        	</select>
			@if ($datos['funcion'] == 'crear')
				<a href="{{route('crear_cliente', ['tipoalta' => 'P'])}}" id="clienteprovisorio" class="btn-accion-tabla tooltipsC" title="Crear cliente provisorio">
                	<i class="fa fa-user"></i>
            	</a>
			@endif
			<label for="Tiposuspension" id="nombretiposuspension" style="padding: 0px;" class="col-form-label text-danger"></label>
		</div>
		<div class="form-group row">
   			<label for="vendedor" class="col-lg-3 col-form-label requerido">Vendedor</label>
        	<select name="vendedor_id" id="vendedor_id" data-placeholder="Vendedor" class="col-lg-8 form-control required" data-fouc>
        		<option value="">-- Seleccionar vendedor --</option>
        		@foreach($vendedor_query as $key => $value)
        			@if( (int) $value->id == (int) old('vendedor_id', $factura->vendedor_id ?? ''))
        				<option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
        			@else
        				<option value="{{ $value->id }}">{{ $value->nombre }}</option>    
        			@endif
        		@endforeach
        	</select>
		</div>
		<div class="form-group row">
   			<label for="transporte" class="col-lg-3 col-form-label">Transporte</label>
        	<select name="transporte_id" id="transporte_id" data-placeholder="Transporte" class="col-lg-8 form-control" data-fouc>
        		<option value="">-- Seleccionar transporte --</option>
        		@foreach($transporte_query as $key => $value)
        			@if( (int) $value->id == (int) old('transporte_id', $factura->transporte_id ?? ''))
        				<option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
        			@else
        				<option value="{{ $value->id }}">{{ $value->nombre }}</option>    
        			@endif
        		@endforeach
        	</select>
		</div>
		<div class="form-group row" id="divlugar">
    		<label for="lugarentrega" class="col-lg-3 col-form-label">Lugar de Entrega</label>
    		<div class="col-lg-8">
    			<input type="text" name="lugarentrega" id="lugarentrega" class="form-control" value="{{old('lugarentrega', $factura->lugarentrega ?? '')}}">
    		</div>
		</div>
		<div class="form-group row" id="divcodigoentrega">
        	<label class="col-lg-3 col-form-label">Entrega en</label>
        	<select name="cliente_entrega_id" id='cliente_entrega_id' data-placeholder="Entrega" class="col-lg-8 form-control" data-fouc>
        		@if($factura->cliente_entrega_id ?? '')
					@if($factura->cliente_entrega_id == "")
        				<option selected></option>
        			@else
        				<option value="{{old('cliente_entrega_id', $factura->cliente_entrega_id)}}" selected>{{$factura->lugarentrega}}</option>
					@endif
        		@endif
        	</select>
        	<input type="hidden" id="cliente_entrega_id_previa" name="cliente_entrega_id_previa" value="{{old('cliente_entrega_id', $factura->cliente_entrega_id ?? '')}}" >
        	<input type="hidden" id="entrega_nombre" name="entrega_nombre" value="{{old('entrega_nombre', $factura->lugarentrega ?? '')}}" >
        </div>
	</div>
	<div class="col-sm-6">
		<div class="form-group row">
			<label for="fecha" class="col-lg-4 col-form-label requerido">Fecha</label>
			<div class="col-lg-3">
				<input type="date" name="fecha" id="fecha" class="form-control" value="{{substr(old('fecha', $factura->fecha ?? date('Y-m-d')),0,10)}}" required>
			</div>
		</div>
		<div class="form-group row">
			<label for="recipient-name" class="col-lg-4 col-form-label">Descuento de l&iacute;nea</label>
			<input type="number" id="descuentolinea" name="descuentolinea" value=""></input>
		</div>
		<div class="form-group row">
			<label for="recipient-name" class="col-lg-4 col-form-label">Descuento pie factura</label>
			<input type="number" id="descuentopie" name="descuentopie" value="$factura->descuento"></input>
		</div>
		<div class="form-group row" id="puntoventaremito">
			<label for="recipient-name" class="col-lg-4 col-form-label requerido">Pto.venta del remito</label>
			<input type="hidden" id="puntoventaremitoori_id" class="form-control" value="{{old('puntoventaremitoori_id', $factura->puntoventaremito_id ?? '')}}" />
			<select name="puntoventaremito_id" id="puntoventaremito_id" data-placeholder="Punto de venta del remito" class="col-lg-5 form-control required" data-fouc>
			</select>
		</div>
		<div class="form-group row">
			<label for="recipient-name" class="col-lg-4 col-form-label">Cantidad de bultos</label>
			<input type="number" id="cantidadbulto" name="cantidadbulto" value="0"></input>
		</div>
		<div class="form-group row">
			<label for="deposito" class="col-lg-3 col-form-label requerido">Depósito</label>
			<select name="deposito_id" id="deposito_id" data-placeholder="Depósito" class="col-lg-8 form-control required" data-fouc>
				<option value="">-- Seleccionar depósito  --</option>
				@foreach($deposito_query as $key => $value)
					@if( (int) $value->id == (int) old('deposito_id', $factura->deposito_id ?? '1'))
						<option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
					@else
						<option value="{{ $value->id }}">{{ $value->nombre }}</option>    
					@endif
				@endforeach
			</select>
		</div>
	</div>
</div>

<div class="card">
    <div class="card-body">
    	<table class="table table-hover" id="itemspedido-table">
    		<thead>
    			<tr>
    				<th style="width: 5%;">Item</th>
    				<th style="width: 10%;">Art&iacute;culo</th>
					<th style="width: 10%;">SKU</th>
					<th style="width: 20%;">Descripci&oacute;n</th>
    				<th style="width: 20%;">Combinaci&oacute;n</th>
    				<th style="width: 20%;">M&oacute;dulo</th>
    				<th style="width: 5%;">Cantidad</th>
    				<th style="width: 9%; text-align: right;">Precio</th>
    			</tr>
    		</thead>
    		<tbody id="tbody-tabla">
		 		@if ($factura->articulos_movimiento[0] ?? '') 
					@foreach ($factura->articulos_movimiento as $pedidoitem)
            			<tr class="item-pedido">
                			<td>
               					<input type="text" name="items[]" class="form-control item" value="{{ $loop->index+1 }}" readonly>
                				<input type="hidden" name="medidas[]" class="form-control medidas" readonly value="{{old('medidas', $pedidoitem->articulo_movimiento_talles??'')}}" />
                				<input type="hidden" name="listasprecios_id[]" class="form-control listaprecio_id" readonly value="{{old('listaprecios_id', $pedidoitem->listaprecio_id??'')}}" />
                				<input type="hidden" name="monedas_id[]" class="form-control moneda_id" readonly value="{{old('monedas_id', $pedidoitem->moneda_id??'')}}" />
                				<input type="hidden" name="incluyeimpuestos[]" class="form-control incluyeimpuesto" readonly value="{{old('incluyeimpuestos', $pedidoitem->incluyeimpuesto??'')}}" />
                				<input type="hidden" name="descuentos[]" class="form-control descuento" readonly value="{{old('descuentos', $pedidoitem->descuento??'')}}" />
                				<input type="hidden" name="ids[]" class="form-control ids" value="{{$pedidoitem->id??''}}" />
								<input type="hidden" name="loteids[]" class="form-control loteids" value="{{$pedidoitem->lote ?? 0}}" />
                			</td>
                			<td>
								<div class="form-group row" id="articulo">
									<input type="text" style="WIDTH: 70px;HEIGHT: 38px" class="articulo_id" name="articulos_id[]" value="{{$pedidoitem->articulo_id ?? ''}}" >
									<button type="button" title="Consulta artículos" style="padding:1;" class="btn-accion-tabla consultaSKU tooltipsC">
                            			<i class="fa fa-search text-primary"></i>
									</button>
								</div>
        						<input type="hidden" class="articulo_id_previo" name="articulo_id_previo[]" value="{{$pedidoitem->articulo_id ?? ''}}" >
                			</td>
							<td>
								<input type="text" style="WIDTH: 130px;HEIGHT: 38px" class="sku form-control" name="skus[]" value="{{$pedidoitem->sku ?? ''}}" >
							</td>							
							<td>
								<input type="text" style="WIDTH: 500px; HEIGHT: 38px" class="descripcion form-control" name="descripciones[]" value="{{$pedidoitem->descripcion ?? ''}}" >
							</td>
                			<td>
        						<select name="combinaciones_id[]" data-placeholder="Combinaciones" class="form-control combinacion" data-fouc>
        						</select>
        						<input type="hidden" class="combinacion_id_previa" name="combinacion_id_previa[]" value="{{old('combinaciones_id', $pedidoitem->combinacion_id ?? '')}}" >
        						<input type="hidden" class="desc_combinacion" name="desc_combinacion[]" value="{{old('desc_combinacion', $pedidoitem->combinaciones->nombre ?? '')}}" >
                			</td>
                			<td>
        						<select name="modulos_id[]" data-placeholder="Modulos" class="form-control modulo" data-fouc>
        						</select>
        						<input type="hidden" class="modulo_id_previa" name="modulo_id_previa[]" value="{{old('modulo_id', $pedidoitem->modulo_id ?? '')}}" >
        						<input type="hidden" class="desc_modulo" name="desc_modulo[]" class="desc_modulo" value="{{old('desc_modulo', $pedidoitem->desc_modulo ?? '')}}" >
                			</td>
                			<td>
                				<input type="text" id="icantidad" name="cantidades[]" class="form-control cantidad" readonly value="{{abs(number_format(old('cantidades.'.$loop->index, optional($pedidoitem)->cantidad),0))}}" />
                			</td>
                			<td>
                				<input type="text" style="text-align: right;" id="iprecio" name="precios[]" class="form-control precio" readonly value="{{number_format(old('precios.'.$loop->index, optional($pedidoitem)->precio),2)}}" />
                			</td>
                			<td>
								<button type="button" title="Elimina esta linea" style="padding:0;" class="btn-accion-tabla eliminar tooltipsC">
                            		<i class="fa fa-trash text-danger"></i>
								</button>
                			</td>
                		</tr>
           			@endforeach
				@endif
       		</tbody>
       	</table>
		@include('ventas.factura.template')
        <div class="row col-md-12">
        	<div class="col-md-2">
        		<button id="agrega_renglon" class="pull-right btn btn-danger">+ Agrega rengl&oacute;n</button>
        	</div>
			<div class="col-md-6">
               	<!-- textarea -->
			   <div class="form-group" id="div_leyendafacturacion">
	                <label>Leyendas</label>
    	            <textarea id="leyendafactura" class="form-control" cols="40" rows="6" placeholder="Leyendas de factura ..."></textarea>
            	</div>
			</div>
			<div class="col-md-4 row" id="div_totales">
				<table>
					<tbody>
						<tr>
							<td><label>Total pares</label></td>
							<td><input type="text" id="totalpares" name="totalpares" class="form-control" readonly value="" /></td>
						</tr>
						<tr>
							<td><label>Total comprobante</label></td>
							<td><input type="text" id="totalcomprobante" name="totalcomprobante" class="form-control" readonly value="" /></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<div class="row col-md-12">
			<div class="col-md-6">
				<div class="form-group" id="div_leyendaexportacion">
                	<label>Leyenda Exportaci&oacute;n</label>
                	<textarea id="leyendaexportacion" class="form-control" cols="90" rows="6" placeholder="Leyendas de exportación ..."></textarea>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group row" id="div_incoterm">
					<label for="recipient-name" class="col-lg-4 col-form-label requerido">Condiciones de venta (incoterms)</label>
					<select name="incoterm_id" id="incoterm_id" data-placeholder="Incoterms" class="col-lg-6 form-control required" data-fouc>
					</select>
				</div>
				<div class="form-group row" id="div_formapago">
					<label for="recipient-name" class="col-lg-4 col-form-label requerido">Forma de pago</label>
					<select name="formapago_id" id="formapago_id" data-placeholder="Forma de pago" class="col-lg-6 form-control required" data-fouc>
					</select>
				</div>
				<div class="form-group row" id="div_mercaderia">
					<label for="recipient-name" class="col-lg-4 col-form-label">Mercader&iacute;a</label>
					<input type="text" class="col-lg-6 form-control" id="mercaderia" name="marcaderia" value=""></input>
				</div>
			</div>
        </div>
    </div>
</div>
<input type="hidden" id="csrf_token" class="form-control" value="{{csrf_token()}}" />
<input type="hidden" id="tipotransacciondefault_id" class="form-control" value="{{$tipotransacciondefault_id}}" />

@include('ventas.factura.modal')
@include('includes.stock.modalconsultaarticulo')
