<div class="form-group row">
    <label for="nombre" class="col-lg-3 col-form-label requerido">Nombre</label>
    <div class="col-lg-8">
    <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group row">
    <label for="comisionventa" class="col-lg-3 col-form-label">Comisi&oacute;n Ventas</label>
    <div class="col-lg-8">
    	<input type="number" name="comisionventa" id="comisionventa" class="form-control" value="{{old('comisionventa', $data->comisionventa ?? '')}}" required/>
    </div>
</div>
<div class="form-group row">
    <label for="comisioncobranza" class="col-lg-3 col-form-label">Comisi&oacute;n Cobranzas</label>
    <div class="col-lg-8">
    	<input type="number" name="comisioncobranza" id="comisioncobranza" class="form-control" value="{{old('comisioncobranza', $data->comisioncobranza ?? '')}}" required/>
    </div>
</div>
