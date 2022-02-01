@isset($edit)
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group row">
    				<label for="sku" class="col-lg-4 col-form-label requerido">Sku</label>
    				<div class="col-lg-5">
    					<input type="text" name="sku" id="sku" class="form-control" value="{{old('sku', $producto->sku ?? '')}}" required/>
                	</div>
                </div>
                <div class="form-group row">
    				<label for="descripcion" class="col-lg-4 col-form-label requerido">Descripci&oacute;n</label>
    				<div class="col-lg-8">
    					<input type="text" name="descripcion" id="descripcion" class="form-control" value="{{old('descripcion', $producto->descripcion ?? '')}}" required/>
                	</div>
                </div>
				<div class="form-group row">
    				<label for="tipcorteforro_id" class="col-lg-4 col-form-label">Tipo de corte forro</label>
					<select id="tipocorteforro_id" name="tipocorteforro_id" class="col-lg-8 form-control">
                        <option value="">-- Seleccionar --</option>
                        @foreach($tipoCorte as $key => $value)
                            @if( isset($producto) && (int) $value->id == (int) old('tipocorteforro_id', $producto->tipocorteforro_id ?? ''))
                                <option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
                            @else
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                            @endif
                        @endforeach
                    </select>
              	</div>
				<div class="form-group row">
    				<label for="usoarticulo_id" class="col-lg-4 col-form-label requerido">Uso de art&iacute;culo</label>
					<select id="usoarticulo_id" name="usoarticulo_id" class="col-lg-8 form-control" required>
                        <option value="">-- Seleccionar --</option>
                        @foreach($usosArticulos as $key => $value)
                            @if( isset($producto) && (int) $value->id == (int) old('usoarticulo_id', $producto->usoarticulo_id ?? ''))
                                <option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
                            @else
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                            @endif
                        @endforeach
                    </select>
              	</div>
				<div class="form-group row">
    				<label for="tipcorte_id" class="col-lg-4 col-form-label">Tipo de corte</label>
					<select id="tipocorte_id" name="tipocorte_id" class="col-lg-8 form-control">
                        <option value="">-- Seleccionar --</option>
                        @foreach($tipoCorte as $key => $value)
                            @if( isset($producto) && (int) $value->id == (int) old('tipocorte_id', $producto->tipocorte_id ?? ''))
                                <option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
                            @else
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                            @endif
                        @endforeach
                    </select>
              	</div>
				<div class="form-group row">
    				<label for="puntera_id" class="col-lg-4 col-form-label">Puntera</label>
					<select id="puntera_id" name="puntera_id" class="col-lg-8 form-control">
                        <option value="">-- Seleccionar --</option>
                        @foreach($punteras as $key => $value)
                            @if( isset($producto) && (int) $value->id == (int) old('puntera_id', $producto->puntera_id ?? ''))
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
    				<label for="contrafuerte_id" class="col-lg-4 col-form-label">Contrafuerte</label>
					<select id="contrafuerte_id" name="contrafuerte_id" class="col-lg-8 form-control">
                        <option value="">-- Seleccionar --</option>
                        @foreach($contrafuertes as $key => $value)
                            @if( isset($producto) && (int) $value->id == (int) old('contrafuerte_id', $producto->contrafuerte_id ?? ''))
                                <option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
                            @else
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                            @endif
                        @endforeach
                    </select>
              	</div>
				<div class="form-group row">
    				<label for="mventa_id" class="col-lg-4 col-form-label requerido">Marca</label>
					<select id="mventa_id" name="mventa_id" class="col-lg-8 form-control" required>
                        <option value="">-- Seleccionar --</option>
                        @foreach($marca as $key => $value)
                            @if( isset($producto) && (int) $value->id == (int) old('mventa_id', $producto->mventa_id ?? ''))
                                <option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>
                            @else
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                            @endif
                        @endforeach
                    </select>
                </div>
				<div class="form-group row">
    				<label for="forro_id" class="col-lg-4 col-form-label">Forro</label>
					<select id="forro_id" name="forro_id" class="col-lg-8 form-control">
                        <option value="">-- Seleccionar --</option>
                        @foreach($forro as $key => $value)
                            @if( isset($producto) && (int) $value->id == (int) old('forro_id', $producto->forro_id ?? ''))
                                <option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>
                            @else
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                            @endif
                        @endforeach
                    </select>
                </div>
				<div class="form-group row">
    				<label for="compfondo_id" class="col-lg-4 col-form-label">Componente del fondo</label>
					<select id="compfondo_id" name="fondo_id" class="col-lg-8 form-control">
                            <option value=""> -- Seleccionar -- </option>
                            @foreach( $compfondo as $key => $value)
                            	@if( isset($producto) && (int) $value->id == (int) old('compfondo_id', $producto->compfondo_id ?? ''))
                                    <option value="{{ $value->id }}" selected="select"> {{ $value->nombre }} </option>
                                @else
                                    <option value="{{ $value->id}}"> {{ $value->nombre }} </option>
                                @endif
                            @endforeach
                    </select>
                </div>
				<div class="form-group row">
    				<label for="unidadmedida_id" class="col-lg-4 col-form-label requerido">Unidad de medida</label>
					<select id="unidadmedida_id" name="unidadmedida_id" class="col-lg-8 form-control" required>
                        <option value="">-- Seleccionar --</option>
                        @foreach($unidadmedida as $key => $value)
                            @if( isset($producto) && (int) $value->id == (int) old('unidadmedida_id', $producto->unidadmedida_id ?? ''))
                                <option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
                            @else
                            	@if( !isset($producto) && (int) $value->abreviatura == "PAR" )
                                	<option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
								@else
                                	<option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                            	@endif
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
		<div class="card-footer">
        	<div class="row">
            	@isset($edit)
        			@include('includes.boton-form-editar')
            	@else
        			@include('includes.boton-form-crear')
            	@endisset
        	</div>
        </div>
    </div>
@endisset
