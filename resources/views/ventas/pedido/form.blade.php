<div class="row">
	<div class="col-sm-6">
		<div class="form-group row">
   			<label for="cliente" class="col-lg-3 col-form-label requerido">Cliente</label>
        	<select name="cliente_id" id="cliente_id" data-placeholder="Cliente" class="col-lg-8 form-control required" data-fouc>
        		<option value="">-- Seleccionar cliente --</option>
        		@foreach($cliente_query as $key => $value)
        			@if( (int) $value->id == (int) old('cliente_id', $pedido->cliente_id ?? ''))
        				<option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
        			@else
        				<option value="{{ $value->id }}">{{ $value->nombre }}</option>    
        			@endif
        		@endforeach
        	</select>
		</div>
		<div class="form-group row">
   			<label for="vendedor" class="col-lg-3 col-form-label requerido">Vendedor</label>
        	<select name="vendedor_id" id="vendedor_id" data-placeholder="Vendedor" class="col-lg-8 form-control required" data-fouc>
        		<option value="">-- Seleccionar vendedor --</option>
        		@foreach($vendedor_query as $key => $value)
        			@if( (int) $value->id == (int) old('vendedor_id', $pedido->vendedor_id ?? ''))
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
        			@if( (int) $value->id == (int) old('transporte_id', $pedido->transporte_id ?? ''))
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
    			<input type="text" name="lugarentrega" id="lugarentrega" class="form-control" value="{{old('lugarentrega', $pedido->lugarentrega ?? '')}}">
    		</div>
		</div>
		<div class="form-group row" id="divcodigoentrega">
        	<label class="col-lg-3 col-form-label">Entrega en</label>
        	<select name="cliente_entrega_id" id='cliente_entrega_id' data-placeholder="Entrega" class="col-lg-8 form-control" data-fouc>
        		@if($pedido->cliente_entrega_id ?? '')
					@if($pedido->cliente_entrega_id == "")
        				<option selected></option>
        			@else
        				<option value="{{old('cliente_entrega_id', $pedido->cliente_entrega_id)}}" selected>{{$pedido->entrega_nombre}}</option>
					@endif
        		@endif
        	</select>
        	<input type="hidden" id="cliente_entrega_id_previa" name="cliente_entrega_id_previa" value="{{old('cliente_entrega_id', $pedido->cliente_entrega_id ?? '')}}" >
        	<input type="hidden" id="entrega_nombre" name="entrega_nombre" value="{{old('entrega_nombre', $pedido->entrega_nombre ?? '')}}" >
        </div>
	</div>
	<div class="col-sm-6">
		<div class="form-group row">
    		<label for="fecha" class="col-lg-3 col-form-label requerido">Fecha</label>
    		<div class="col-lg-5">
    			<input type="date" name="fecha" id="fecha" class="form-control" value="{{substr(old('fecha', $pedido->fecha ?? ''),0,10)}}" required>
    		</div>
		</div>
		<div class="form-group row">
    		<label for="fechaentrega" class="col-lg-3 col-form-label">Entrega</label>
    		<div class="col-lg-5">
    			<input type="date" name="fechaentrega" id="fechaentrega" class="form-control" value="{{substr(old('fechaentrega', $pedido->fechaentrega ?? ''),0,10)}}">
    		</div>
		</div>
		<div class="form-group row">
   			<label for="condicionventa" class="col-lg-3 col-form-label requerido">Cond. de Vta.</label>
        	<select name="condicionventa_id" id="condicionventa_id" data-placeholder="Condicion de Venta" class="col-lg-8 form-control required" data-fouc>
        		<option value="">-- Seleccionar cond. de vta.  --</option>
        		@foreach($condicionventa_query as $key => $value)
        			@if( (int) $value->id == (int) old('condicionventa_id', $pedido->condicionventa_id ?? ''))
        				<option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
        			@else
        				<option value="{{ $value->id }}">{{ $value->nombre }}</option>    
        			@endif
        		@endforeach
        	</select>
		</div>
		<div class="form-group row" id="marca" data-articulo="{{$articulo_query}}">
   			<label for="mventa" class="col-lg-3 col-form-label requerido">Marca</label>
        	<select name="mventa_id" id="mventa_id" data-placeholder="Marca de Venta" class="col-lg-8 form-control required" data-fouc>
        		<option value="">-- Seleccionar marca --</option>
        		@foreach($mventa_query as $key => $value)
        			@if( (int) $value->id == (int) old('mventa_id', $pedido->mventa_id ?? ''))
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
    	<table class="table" id="itemspedido-table">
    		<thead>
    			<tr>
    				<th style="width: 7%;">Item</th>
    				<th>Art&iacute;culo</th>
    				<th>Comb.</th>
    				<th style="width: 15%;">M&oacute;dulo</th>
    				<th style="width: 10%;">Cantidad</th>
    				<th style="width: 15%; text-align: right;">Precio</th>
    				<th style="width: 11%; margin-right: 0;">O.T.</th>
    			</tr>
    		</thead>
    		<tbody id="tbody-tabla">
		 		@if ($pedido->pedido_combinaciones ?? '') 
					@foreach (old('items', $pedido->pedido_combinaciones->count() ? $pedido->pedido_combinaciones : ['']) as $pedidoitem)
            			<tr class="item-pedido">
                			<td>
                				<input type="text" name="items[]" class="form-control item" value="{{ $loop->index+1 }}" readonly>
                				<input type="hidden" name="medidas[]" class="form-control medidas" readonly value="{{old('medidas', $pedidoitem->pedido_combinacion_talles)}}" />
                				<input type="hidden" name="listasprecios_id[]" class="form-control listaprecio_id" readonly value="{{old('listaprecios_id', $pedidoitem->listaprecio_id)}}" />
                				<input type="hidden" name="monedas_id[]" class="form-control moneda_id" readonly value="{{old('monedas_id', $pedidoitem->moneda_id)}}" />
                				<input type="hidden" name="incluyeimpuestos[]" class="form-control incluyeimpuesto" readonly value="{{old('incluyeimpuestos', $pedidoitem->incluyeimpuesto)}}" />
                				<input type="hidden" name="descuentos[]" class="form-control descuento" readonly value="{{old('descuentos', $pedidoitem->descuento)}}" />
                				<input type="hidden" name="observaciones[]" class="form-control observacion" readonly value="{{old('observaciones', $pedidoitem->observacion)}}" />
                			</td>
                			<td>
                				<select name="articulos_id[]" class="form-control articulo">
									@php
									@endphp
                					<option value="">-- Elija art&iacute;culo --</option>
                					@foreach ($articulo_query as $articulo)
                						<option value="{{ $articulo['id'] }}"
                						@if (old('articulos_id.' . $loop->parent->index, optional($pedidoitem)->articulo_id) == $articulo['id']) selected @endif
                						>{{ $articulo['descripcion'] }}</option>
                					@endforeach
                				</select>
        						<input type="hidden" class="articulo_id_previo" name="articulo_id_previo[]" value="{{$pedidoitem->articulo_id ?? ''}}" >
                			</td>
                			<td>
        						<select name="combinaciones_id[]" data-placeholder="Combinaciones" class="form-control combinacion" data-fouc>
        						</select>
        						<input type="hidden" class="combinacion_id_previa" name="combinacion_id_previa[]" value="{{old('combinacion_id', $pedidoitem->combinacion_id ?? '')}}" >
        						<input type="hidden" class="desc_combinacion" name="desc_combinacion[]" value="{{old('desc_combinacion', $pedidoitem->desc_combinacion ?? '')}}" >
                			</td>
                			<td>
        						<select name="modulos_id[]" data-placeholder="Modulos" class="form-control modulo" data-fouc>
        						</select>
        						<input type="hidden" class="modulo_id_previa" name="modulo_id_previa[]" value="{{old('modulo_id', $pedidoitem->modulo_id ?? '')}}" >
        						<input type="hidden" class="desc_modulo" name="desc_modulo[]" class="desc_modulo" value="{{old('desc_modulo', $pedidoitem->desc_modulo ?? '')}}" >
                			</td>
                			<td>
                				<input type="text" id="icantidad" name="cantidades[]" class="form-control cantidad" readonly value="{{number_format(old('cantidades.'.$loop->index, optional($pedidoitem)->cantidad),0)}}" />
                			</td>
                			<td>
                				<input type="text" style="text-align: right;" id="iprecio" name="precios[]" class="form-control precio" readonly value="{{number_format(old('precios.'.$loop->index, optional($pedidoitem)->precio),2)}}" />
                			</td>
                			<td>
                				<input type="text" name="ot_ids[]" class="form-control ot" 
                					value="{{ (old('ot_ids.' . $loop->index) ?? optional($pedidoitem)->ot_id) ?? '-1' }}" readonly> 
                			</td>
                			<td>
								<button type="button" title="Genera OT" style="padding:0;" class="btn-accion-tabla eliminar tooltipsC">
                            		<i class="fa fa-industry text-success"></i>
								</button>
								<button type="button" title="Imprime OT" style="padding:0;" class="btn-accion-tabla eliminar tooltipsC">
                            		<i class="fa fa-print text-success"></i>
								</button>
								<button type="button" title="Elimina esta linea" style="padding:0;" class="btn-accion-tabla eliminar tooltipsC">
                            		<i class="fa fa-times-circle text-danger"></i>
								</button>
                			</td>
                		</tr>
           			@endforeach
				@endif
       		</tbody>
       	</table>
		@include('ventas.pedido.template')
        <div class="row">
        	<div class="col-md-3">
        		<button id="agrega_renglon" class="pull-right btn btn-danger">+ Agrega rengl&oacute;n</button>
        	</div>
        	<div class="col-md-3"></div>
				<div class="col-sm-6">
                	<!-- textarea -->
                	<div class="form-group">
                		<label>Leyendas</label>
                		<textarea name="leyenda" class="form-control" rows="3" placeholder="Leyendas ...">{{old('leyenda', $pedido->leyenda ?? '')}}</textarea>
                	</div>
				</div>
            </div>
        </div>
    </div>
</div>

@include('ventas.pedido.modal')
