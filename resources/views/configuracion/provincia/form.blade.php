<div class="form-group row">
    <label for="nombre" class="col-lg-3 col-form-label requerido">Nombre</label>
    <div class="col-lg-8">
    <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group row">
    <label for="abreviatura" class="col-lg-3 col-form-label">Abreviatura</label>
    <div class="col-lg-1">
    <input type="text" name="abreviatura" id="abreviatura" class="form-control" value="{{old('abreviatura', $data->abreviatura ?? '')}}">
    </div>
</div>
<div class="form-group row">
    <label for="jurisdiccion" class="col-lg-3 col-form-label">Jurisdicci&oacute;n</label>
    <div class="col-lg-2">
    <input type="text" name="jurisdiccion" id="jurisdiccion" class="form-control" value="{{old('jurisdiccion', $data->jurisdiccion ?? '')}}">
    </div>
</div>
<div class="form-group row">
    <label for="codigo" class="col-lg-3 col-form-label">Codigo</label>
    <div class="col-lg-2">
    <input type="text" name="codigo" id="codigo" class="form-control" value="{{old('codigo', $data->codigo ?? '')}}">
    </div>
</div>
<div class="form-group row">
    <label for="pais_id" class="col-lg-3 col-form-label requerido">Pa&iacute;s</label>
	<select name="pais_id" class="col-lg-3 form-control" required>
		<option value="">-- Elija pa&iacute;s --</option>
		@foreach ($pais_query as $pais)
			<option value="{{ $pais->id }}"
				@if (old('pais_id', $data->pais_id ?? '') == $pais->id) selected @endif
				>{{ $pais->nombre }}
			</option>
		@endforeach
	</select>
</div>
