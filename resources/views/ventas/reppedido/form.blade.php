<div class="form-group row">
	<label for="desdefecha" class="col-lg-3 col-form-label requerido">Desde fecha</label>
	<div class="col-lg-4">
		<input type="text" name="desdefecha" id="desdefecha" class="form-control" value="{{old('desdefecha', date('d-m-Y'))}}" required/>
	</div>
</div>
<div class="form-group row">
	<label for="hastafecha" class="col-lg-3 col-form-label requerido">Hasta fecha</label>
	<div class="col-lg-4">
		<input type="text" name="hastafecha" id="hastafecha" class="form-control" value="{{old('desdefecha', date('d-m-Y'))}}" required/>
	</div>
</div>
<div class="form-group row">
	<label for="vendedor" class="col-lg-3 col-form-label requerido">Vendedor</label>
   	<select name="vendedor_id" id="vendedor_id" data-placeholder="Vendedor" class="col-lg-4 form-control required" data-fouc>
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
  	<label for="tipolistado" class="col-lg-3 col-form-label requerido">Tipo de listado</label>
	<select name="tipolistado" class="col-lg-3 form-control" required>
   		<option value="">-- Elija tipo de listado --</option>
    	@foreach($tipolistado_enum as $value => $tipolistado)
       		@if( (int) $value == old('tipoelistado', $data->tipolistado ?? ''))
       			<option value="{{ $value }}" selected="select">{{ $tipolistado }}</option>    
       		@else
       			<option value="{{ $value }}">{{ $tipolistado }}</option>    
       		@endif
       	@endforeach
	</select>
</div>
