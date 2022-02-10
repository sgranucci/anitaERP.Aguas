<div class="form-group row">
	<label for="desde_linea_id" class="col-lg-4 col-form-label requerido">Desde l&iacute;nea</label>
	<select id="desde_linea_id" name="desde_linea_id" class="col-lg-8 form-control">
       	<option value="">-- Seleccionar --</option>
       	@foreach($linea_query as $key => $value)
       		<option value="{{ $value->id }}">{{ $value->codigo }}-{{ $value->nombre }}</option>    
       	@endforeach
	</select>
</div>
<div class="form-group row">
	<label for="hasta_linea_id" class="col-lg-4 col-form-label requerido">Hasta l&iacute;nea</label>
	<select id="hasta_linea_id" name="hasta_linea_id" class="col-lg-8 form-control">
       	<option value="">-- Seleccionar --</option>
       	@foreach($linea_query as $key => $value)
       		<option value="{{ $value->id }}">{{ $value->codigo }}-{{ $value->nombre }}</option>    
        @endforeach
	</select>
</div>

