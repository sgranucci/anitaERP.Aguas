<div class="form-group row">
	<label class="col-lg-3 col-form-label requerido">Proveedor</label>
	<select name="proveedor_id" id="proveedor_id" data-placeholder="Proveedor" class="col-lg-3 form-control required" data-fouc>
		<option value="">-- Seleccionar --</option>
		@foreach($proveedor_query as $key => $value)
			@if( (int) $value->id == (int) old('proveedor_id', $proveedor_servicioterrestre->proveedor_id ?? ''))
				<option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
			@else
				<option value="{{ $value->id }}">{{ $value->nombre }}</option>    
			@endif
		@endforeach
	</select>
</div>
<div class="form-group row">
	<label class="col-lg-3 col-form-label requerido">Servicio Terrestre</label>
	<select name="servicioterrestre_id" id="servicioterrestre_id" data-placeholder="Servicio Terrestre" class="col-lg-3 form-control required" data-fouc>
		<option value="">-- Seleccionar --</option>
		@foreach($servicioterrestre_query as $key => $value)
			@if( (int) $value->id == (int) old('servicioterrestre_id', $proveedor_servicioterrestre->servicioterrestre_id ?? ''))
				<option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
			@else
				<option value="{{ $value->id }}">{{ $value->nombre }}</option>    
			@endif
		@endforeach
	</select>
</div>
