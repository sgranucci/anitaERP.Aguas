<div class="form-group row">
    <label for="nombre" class="col-lg-3 col-form-label requerido">Nombre</label>
    <div class="col-lg-8">
    <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group row">
    <label for="valor" class="col-lg-3 col-form-label requerido">Valor</label>
    <div class="col-lg-2">
    <input type="number" name="valor" id="valor" class="form-control" value="{{old('valor', $data->valor ?? '0')}}" required/>
    </div>
</div>
<div class="form-group row">
	<label for="fechavigencia" class="col-lg-3 col-form-label requerido">Fecha de vigencia</label>
	<div class="col-lg-8">
		<input type="text" name="fechavigencia" id="fechavigencia" class="form-control" value="{{old('fechavigencia', \Carbon\Carbon::parse(strtotime($data->fechavigencia ?? date('d-m-Y')))->formatLocalized('%d-%m-%Y') ?? date('d-m-Y'))}}" required/>
	</div>
</div>
