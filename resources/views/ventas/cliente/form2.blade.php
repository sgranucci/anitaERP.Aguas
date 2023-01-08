<div class="form2" style="display: none">
		<div class="row">
			<div class="col-sm-6">
				<div class="form-group row">
    				<label for="nroinscripcion" class="col-lg-4 col-form-label requerido">C.U.I.T.</label>
    				<div class="col-lg-8">
    					<input type="text" name="nroinscripcion" id="nroinscripcion" class="form-control" value="{{old('nroinscripcion', $data->nroinscripcion ?? '')}}" required/>
    				</div>
				</div>
				<div class="form-group row">
					@if ($tipoalta != 'P')
    					<label for="retieneiva" class="col-lg-4 col-form-label requerido">Retiene iva</label>
					@else
						<label for="retieneiva" class="col-lg-4 col-form-label">Retiene iva</label>
					@endif
					<select name="retieneiva" class="col-lg-3 form-control" @if ($tipoalta != 'P') required @endif>
    					<option value="">-- Elija retiene iva --</option>
        				@foreach ($retieneiva_enum as $value => $retieneiva)
        					<option value="{{ $value }}"
        						@if (old('retieneiva', $data->retieneiva ?? '') == $value) selected @endif
        						>{{ $retieneiva }}</option>
        				@endforeach
					</select>
				</div>
				<div class="form-group row">
					@if ($tipoalta != 'P')
    					<label for="modofacturacion" class="col-lg-4 col-form-label requerido">Modo facturaci&oacute;n</label>
					@else
						<label for="modofacturacion" class="col-lg-4 col-form-label">Modo facturaci&oacute;n</label>
					@endif
					<select name="modofacturacion" class="col-lg-3 form-control" @if ($tipoalta != 'P') required @endif>
    					<option value="">-- Elija modo de facturac&oacute;n --</option>
        				@foreach ($modofacturacion_enum as $value => $modofacturacion)
        					<option value="{{ $value }}"
        						@if (old('modofacturacion', $data->modofacturacion ?? '') == $value) selected @endif
        						>{{ $modofacturacion }}</option>
        				@endforeach
					</select>
				</div>
				<div class="form-group row">
					@if ($tipoalta != 'P')
    					<label for="nroiibb" class="col-lg-4 col-form-label requerido">Nro.IIBB</label>
					@else
						<label for="nroiibb" class="col-lg-4 col-form-label">Nro.IIBB</label>
					@endif
    				<div class="col-lg-8">
    					<input type="text" name="nroiibb" id="nroiibb" class="form-control" value="{{old('nroiibb', $data->nroiibb ?? '')}}" @if ($tipoalta != 'P') required @endif/>
    				</div>
				</div>
        		<div class="form-group row">
    				<label for="zonavta" class="col-lg-4 col-form-label">Zona de venta</label>
        			<select name="zonavta_id" id="zonavta_id" data-placeholder="Zona de venta" class="col-lg-5 form-control" data-fouc>
        				<option value="">-- Seleccionar Zona de Venta --</option>
        				@foreach($zonavta_query as $key => $value)
        					@if( (int) $value->id == (int) old('zonavta_id', $data->zonavta_id ?? ''))
        						<option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
        					@else
        						<option value="{{ $value->id }}">{{ $value->nombre }}</option>    
        					@endif
        				@endforeach
        			</select>
        		</div>
        		<div class="form-group row">
    				<label for="subzonavta" class="col-lg-4 col-form-label">Subzona de venta</label>
        			<select name="subzonavta_id" id="subzonavta_id" data-placeholder="Subzona de venta" class="col-lg-5 form-control" data-fouc>
        				<option value="">-- Seleccionar Subzona --</option>
        				@foreach($subzonavta_query as $key => $value)
        					@if( (int) $value->id == (int) old('subzonavta_id', $data->subzonavta_id ?? ''))
        						<option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
        					@else
        						<option value="{{ $value->id }}">{{ $value->nombre }}</option>    
        					@endif
        				@endforeach
        			</select>
        		</div>
        		<div class="form-group row">
    				<label for="condicionventa" class="col-lg-4 col-form-label">Condici&oacute;n de venta</label>
        			<select name="condicionventa_id" id="condicionventa_id" data-placeholder="Vendedor" class="col-lg-5 form-control" data-fouc>
        				<option value="">-- Seleccionar Cond. Venta --</option>
        				@foreach($condicionventa_query as $key => $value)
        					@if( (int) $value->id == (int) old('condicionventa_id', $data->condicionventa_id ?? ''))
        						<option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
        					@else
        						<option value="{{ $value->id }}">{{ $value->nombre }}</option>    
        					@endif
        				@endforeach
        			</select>
        		</div>
        		<div class="form-group row">
    				<label for="listaprecio" class="col-lg-4 col-form-label">Lista de precio</label>
        			<select name="listaprecio_id" id="listaprecio_id" data-placeholder="Lista de precio" class="col-lg-8 form-control" data-fouc>
        				<option value="">-- Seleccionar lista de precio --</option>
        				@foreach($listaprecio_query as $key => $value)
        					@if( (int) $value->id == (int) old('listaprecio_id', $data->listaprecio_id ?? ''))
        						<option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
        					@else
        						<option value="{{ $value->id }}">{{ $value->nombre }}</option>    
        					@endif
        				@endforeach
        			</select>
        		</div>
			</div>
			<div class="col-sm-6">
        		<div class="form-group row">
    				<label for="condicioniva_id" class="col-lg-4 col-form-label requerido">Condicion de iva.</label>
        			<select name="condicioniva_id" id="condicioniva_id" data-placeholder="Condicion de iva" class="col-lg-5 form-control" required data-fouc>
        				<option value="">-- Seleccionar --</option>
        				@foreach($condicioniva_query as $key => $value)
        					@if( (int) $value->id == (int) old('condicioniva_id', $data->condicioniva_id ?? ''))
        						<option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
        					@else
        						<option value="{{ $value->id }}">{{ $value->nombre }}</option>    
        					@endif
        				@endforeach
        			</select>
    				<input type="hidden" id="condicioniva_query" value="{{$condicioniva_query}}">
        		</div>
        		<div class="form-group row">
    				<label for="letra" class="col-lg-4 col-form-label">Letra </label>
    				<div class="col-lg-2">
    					<input type="text" name="letra" id="letra" class="form-control" value="" readonly>
        			</div>
        		</div>
				<div class="form-group row">
					@if ($tipoalta != 'P') 
    					<label for="condicioniibb" class="col-lg-4 col-form-label requerido">Condici&oacute;n IIBB</label>
					@else
						<label for="condicioniibb" class="col-lg-4 col-form-label">Condici&oacute;n IIBB</label>
					@endif
					<select name="condicioniibb" class="col-lg-5 form-control" @if ($tipoalta != 'P') required @endif>
    					<option value="">-- Elija condici&oacute;n IIBB --</option>
        				@foreach ($condicioniibb_enum as $value => $condicioniibb)
        					<option value="{{ $value }}"
        						@if (old('condicioniibb', $data->condicioniibb ?? '') == $value) selected @endif
        						>{{ $condicioniibb }}</option>
        				@endforeach
					</select>
				</div>
        		<div class="form-group row">
    				<label for="vendedor" class="col-lg-4 col-form-label">Vendedor</label>
        			<select name="vendedor_id" id="vendedor_id" data-placeholder="Vendedor" class="col-lg-8 form-control" data-fouc>
        				<option value="">-- Seleccionar Vendedor --</option>
        				@foreach($vendedor_query as $key => $value)
        					@if( (int) $value->id == (int) old('vendedor_id', $data->vendedor_id ?? ''))
        						<option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
        					@else
        						<option value="{{ $value->id }}">{{ $value->nombre }}</option>    
        					@endif
        				@endforeach
        			</select>
        		</div>
        		<div class="form-group row">
    				<label for="transporte" class="col-lg-4 col-form-label">Transporte</label>
        			<select name="transporte_id" id="transporte_id" data-placeholder="Transpore" class="col-lg-8 form-control" data-fouc>
        				<option value="">-- Seleccionar Transporte --</option>
        				@foreach($transporte_query as $key => $value)
        					@if( (int) $value->id == (int) old('transporte_id', $data->transporte_id ?? ''))
        						<option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
        					@else
        						<option value="{{ $value->id }}">{{ $value->nombre }}</option>    
        					@endif
        				@endforeach
        			</select>
        		</div>
        		<div class="form-group row">
					@if ($tipoalta != 'P') 
    					<label for="cuentacontable" class="col-lg-4 col-form-label requerido">Cuenta contable</label>
					@else
						<label for="cuentacontable" class="col-lg-4 col-form-label">Cuenta contable</label>
					@endif
        			<select name="cuentacontable_id" id="cuentacontable_id" data-placeholder="Cuenta contable para imputaciones" class="col-lg-8 form-control" data-fouc @if ($tipoalta != 'P') required @endif>
        				<option value="">-- Seleccionar Cta. Contable --</option>
        				@foreach($cuentacontable_query as $key => $value)
        					@if( (int) $value->id == (int) old('cuentacontable_id', $data->cuentacontable_id ?? ''))
        						<option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
        					@else
        						<option value="{{ $value->id }}">{{ $value->nombre }}</option>    
        					@endif
        				@endforeach
        			</select>
        		</div>
				<div class="form-group row">
    				<label for="descuento" class="col-lg-3 col-form-label">Descuento</label>
                    <span class="input-group-text"><i class="fas fa-percent"></i></span>
    				<div class="col-lg-3">
    					<input type="text" name="descuento" id="descuento" class="form-control" value="{{old('descuento', $data->descuento ?? '0')}}">
    				</div>
    			</div>
			</div>
		</div>
</div>
