<div class="form-group row">
	<label for="empresa_id" class="col-lg-3 col-form-label requerido">Empresa</label>
	<div class="col-lg-8">
		<select name="empresa_id" id="empresa_id" class="form-control" required>
			<option value="">Seleccione la empresa</option>
			@foreach($empresa_query as $id => $empresa)
				@if( isset($data) && (int) $empresa->id == (int) $data->empresa_id )
            		<option value="{{$empresa->id}}" selected>{{$empresa->nombre}}</option>
            	@else
            		<option value="{{$empresa->id}}">{{$empresa->nombre}}</option>
				@endif
			@endforeach
		</select>
	</div>
</div>
<div class="form-group row">
    <label for="nombre" class="col-lg-3 col-form-label requerido">Nombre</label>
    <div class="col-lg-8">
    <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group row">
    <label for="codigo" class="col-lg-3 col-form-label requerido">C&oacute;digo</label>
    <div class="col-lg-3">
    <input type="text" name="codigo" id="codigo" class="form-control" value="{{old('codigo', $data->codigo ?? '')}}" required/>
    </div>
</div>
<div class="form-group row">
	<label for="rubrocontable_id" class="col-lg-3 col-form-label requerido">Rubro contable</label>
	<div class="col-lg-8">
		<select name="rubrocontable_id" id="rubrocontable_id" class="form-control" required>
			<option value="">Seleccione el rubro contable</option>
			@foreach($rubrocontable_query as $id => $rubrocontable)
				@if( isset($data) && (int) $rubrocontable->id == (int) $data->rubrocontable_id )
            		<option value="{{$rubrocontable->id}}" selected>{{$rubrocontable->nombre}}</option>
            	@else
            		<option value="{{$rubrocontable->id}}">{{$rubrocontable->nombre}}</option>
				@endif
			@endforeach
		</select>
	</div>
</div>
<div class="form-group row">
    <label for="tipocuenta" class="col-lg-3 col-form-label requerido">Tipo de cuenta</label>
	<select name="tipocuenta" class="col-lg-3 form-control">
		@if (empty($data['tipocuenta']))
			<option value="">-- Elija el tipo de cuenta --</option>
		@endif
		@for ($i = 1; $i <= 3; $i++)
			@if (!empty($data['tipocuenta']) && $data['tipocuenta'] == $i)
				<option selected value="{{$i}}">{{($i==1?'Imputable':($i==2?'No imputable':'Totalizadora'))}}</option>
			@else
				<option value="{{$i}}">{{($i==1?'Imputable':($i==2?'No imputable':'Totalizadora'))}}</option>
			@endif
		@endfor
	</select>
</div>
<div class="form-group row">
    <label for="monetaria" class="col-lg-3 col-form-label requerido">Cuenta monetaria</label>
	<select name="monetaria" class="col-lg-3 form-control">
		@if (empty($data['tipocuenta']))
			<option value="">-- Elija si la cuenta es monetaria --</option>
		@endif
		@for ($i = 1; $i <= 2; $i++)
			@if (!empty($data['monetaria']) && $data['monetaria'] == $i))
				<option selected value="{{$i}}">{{$i==1?'Monetaria':'No monetaria'}}</option>
			@else
				<option value="{{$i}}">{{$i==1?'Monetaria':'No monetaria'}}</option>
			@endif
		@endfor
	</select>
</div>
<div class="form-group row">
    <label for="manejaccosto" class="col-lg-3 col-form-label requerido">Maneja centros de costo</label>
	<select name="manejaccosto" class="col-lg-3 form-control">
		@if (empty($data['manejaccosto']))
			<option value="">-- Elija si maneja centros de costo --</option>
		@endif
		@for ($i = 1; $i <= 2; $i++)
			@if (!empty($data['manejaccosto']) && $data['manejaccosto'] == $i))
				<option selected value="{{$i}}">{{$i==1?'Maneja c.costo':'No maneja c.costo'}}</option>
			@else
				<option value="{{$i}}">{{$i==1?'Maneja c.costo':'No maneja c.costo'}}</option>
			@endif
		@endfor
	</select>
</div>
