<div class="form-group row">
    <label for="nombre" class="col-lg-3 col-form-label requerido">Nombre</label>
    <div class="col-lg-8">
    	<input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group row">
    <label for="sku" class="col-lg-3 col-form-label requerido">SKU</label>
    <div class="col-lg-8">
    	<input type="text" name="sku" id="sku" class="form-control" value="{{old('sku', $data->sku ?? '')}}" required/>
    </div>
</div>
<input type="hidden" name="articulo_id" class="form-control" value="{{old('articulo_id', $data->articulo_id ?? '')}}">

