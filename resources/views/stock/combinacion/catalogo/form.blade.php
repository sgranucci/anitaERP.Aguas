<div class="form-group row">
	<label for="mventa_id" class="col-lg-4 col-form-label requerido">Marca</label>
	<select id="mventa_id" name="mventa_id" class="col-lg-8 form-control">
   		<option value="">-- Seleccionar --</option>
        @foreach($mventa_query as $key => $value)
          	@if( (int) $value->id == (int) old('mventa_id'))
           		<option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>
           	@else
           		<option value="{{ $value->id }}">{{ $value->nombre }}</option>    
          	@endif
        @endforeach
	</select>
</div>
<div class="form-group row">
	<label for="categoria_id" class="col-lg-4 col-form-label requerido">Categor&iacute;a</label>
	<select id="categoria_id" name="categoria_id" class="col-lg-8 form-control">
   		<option value="">-- Seleccionar --</option>
        	<option value="T">Todas las categor&iacute;as</option>    
        @foreach($categoria_query as $key => $value)
        	<option value="{{ $value->id }}">{{ $value->nombre }}</option>    
    	@endforeach
   	</select>
</div>

@include('includes.stock.rangolinea')

