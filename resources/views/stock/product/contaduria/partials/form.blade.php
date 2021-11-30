@isset($edit)
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group row">
    				<label for="sku" class="col-lg-4 col-form-label requerido">Sku</label>
    				<div class="col-lg-5">
    					<input type="text" name="sku" id="sku" class="form-control" value="{{old('sku', $producto->sku ?? '')}}" required readonly/>
                	</div>
                </div>
                <div class="form-group row">
    				<label for="sku" class="col-lg-4 col-form-label requerido">Descripci&oacute;n</label>
    				<div class="col-lg-8">
    					<input type="text" name="descripcion" id="descripcion" class="form-control" value="{{old('descripcion', $producto->descripcion ?? '')}}" required disabled/>
                	</div>
                </div>
				<div class="form-group row">
    				<label for="cuentacontableventa_id" class="col-lg-4 col-form-label requerido">Cuenta contable venta</label>
					<select id="cuentacontableventa_id" name="cuentacontableventa_id" class="col-lg-8 form-control">
                        <option value="">-- Seleccionar --</option>
                        @foreach($ctamae as $key => $value)
                            @if( isset($producto) && (int) $value->id == (int) old('cuentacontableventa_id', $producto->cuentacontableventa_id ?? ''))
                                <option value="{{ $value->id }}" selected="select">{{$value->nombre}}{{-$value->codigo}}</option>    
                            @else
                                <option value="{{ $value->id }}">{{$value->nombre}}{{-$value->codigo}}</option>    
                            @endif
                        @endforeach
                    </select>
              	</div>
            </div>
            <div class="col-sm-6">
				<div class="form-group row">
    				<label for="impuesto_id" class="col-lg-4 col-form-label requerido">Impuesto aplicado</label>
					<select id="impuesto_id" name="impuesto_id" class="col-lg-8 form-control">
                        <option value="">-- Seleccionar --</option>
                        @foreach($codimp as $key => $value)
                            @if( isset($producto) && (int) $value->id == (int) old('impuesto_id', $producto->impuesto_id ?? ''))
                                <option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
                            @else
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                            @endif
                        @endforeach
                    </select>
              	</div>
                <div class="form-group row">
    				<label for="nomenclador" class="col-lg-4 col-form-label requerido">Nomenclador</label>
    				<div class="col-lg-8">
    					<input type="text" name="nomenclador" id="nomenclador" class="form-control" value="{{old('nomenclador', $producto->nomenclador ?? '')}}" required/>
                	</div>
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
</div>
@endisset
