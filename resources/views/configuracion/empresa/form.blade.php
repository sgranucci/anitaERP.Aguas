<div class="form-group row">
    <label for="nombre" class="col-lg-3 col-form-label requerido">Nombre</label>
    <div class="col-lg-8">
    <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group row">
    <label for="domicilio" class="col-lg-3 col-form-label">Domicilio</label>
    <div class="col-lg-3">
    <input type="text" name="domicilio" id="domicilio" class="form-control" value="{{old('domicilio', $data->domicilio ?? '')}}"/>
    </div>
</div>
<div class="form-group row">
    <label for="nroinscripcion" class="col-lg-3 col-form-label requerido">Nro. Inscripci&oacute;n</label>
    <div class="col-lg-2">
    <input type="text" name="nroinscripcion" id="nroinscripcion" class="form-control" value="{{old('nroinscripcion', $data->nroinscripcion ?? '')}}"/>
    </div>
</div>
<div class="form-group row">
    <label for="codigo" class="col-lg-3 col-form-label requerido">C&oacute;digo</label>
    <div class="col-lg-2">
    <input type="number" name="codigo" id="codigo" class="form-control" value="{{old('codigo', $data->codigo ?? '')}}"/>
    </div>
</div>
