<div class="form-group row">
    <label for="nombre" class="col-lg-3 col-form-label requerido">Nombre</label>
    <div class="col-lg-8">
    	<input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group row">
    <label for="formula" class="col-lg-3 col-form-label requerido">F&oacute;rmula</label>
    <div class="col-lg-8">
    	<input type="text" name="formula" id="formula" class="form-control" value="{{old('formula', $data->formula ?? '')}}" required/>
    </div>
</div>
<div class="form-group row">
    <label for="incluyeimpuesto" class="col-lg-3 col-form-label requerido">Incluye impuestos</label>
	<select name="incluyeimpuesto" class="col-lg-3 form-control">
		@if (empty($data['incluyeimpuesto']))
			<option value="">-- Elija si incluye impuesto --</option>
		@endif
		@for ($i = 1; $i <= 2; $i++)
			@if (!empty($data['incluyeimpuesto']) && $data['incluyeimpuesto'] == $i))
				<option selected value="{{$i}}">{{$i==1?'S':'N'}}</option>
			@else
				<option value="{{$i}}">{{$i==1?'S':'N'}}</option>
			@endif
		@endfor
	</select>
</div>
<div class="form-group row">
    <label for="codigo" class="col-lg-3 col-form-label requerido">C&oacute;digo lista</label>
    <div class="col-lg-8">
    	<input type="number" name="codigo" id="codigo" class="form-control" value="{{old('codigo', $data->codigo ?? '')}}" required/>
    </div>
</div>
