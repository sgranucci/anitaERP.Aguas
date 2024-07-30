<div class="form-group row">
    <label for="nombre" class="col-lg-3 col-form-label requerido">Nombre</label>
    <div class="col-lg-4">
       <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group row">
    <label for="codigo" class="col-lg-3 col-form-label requerido">Codigo AFIP</label>
    <div class="col-lg-1">
       <input type="text" name="codigo" id="codigo" class="form-control" value="{{old('codigo', $data->codigo ?? '')}}" required/>
    </div>
</div>
<div class="form-group row">
    <label for="domicilio" class="col-lg-3 col-form-label requerido">Direcci&oacuten</label>
    <div class="col-lg-4">
        <input type="text" name="domicilio" id="domicilio" class="form-control" value="{{old('domicilio', $data->domicilio ?? '')}}" required/>
    </div>
</div>
<div class="form-group row">
    <label for="codigo" class="col-lg-3 col-form-label requerido">Empresa</label>
    <div class="col-lg-2">
        <select name="empresa_id" id="empresa_id" data-placeholder="Empresa" class="form-control required" data-fouc>
            <option value="">-- Seleccionar --</option>
            @foreach($empresa_query as $key => $value)
                @if( (int) $value->id == (int) old('empresa_id', $data->empresa_id ?? ''))
                    <option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
                @else
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                @endif
            @endforeach
        </select>
    </div>
</div>
<h3>Domicilio</h3>
<div class='col-md-12'>
    <div class="row mt-0">
        <div class="col-md-3">
            <div class="form-group">
                <label class="requerido">País</label>
                <select name="pais_id" id="pais_id" data-placeholder="País" class="form-control required" data-fouc>
                    <option value="">-- Seleccionar --</option>
                    @foreach($pais_query as $key => $value)
                        @if( (int) $value->id == (int) old('pais_id', $data->pais_id ?? ''))
                            <option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
                        @else
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3" id='prov'>
            <div class="form-group">
                <label class="requerido">Provincia</label>
                <select name="provincia_id" id="provincia_id" data-placeholder="Provincia" class="form-control required" data-fouc>
                    <option value="">-- Seleccionar --</option>
                    @foreach($provincia_query as $key => $value)
                        @if( (int) $value->id == (int) old('provincia_id', $data->provincia_id ?? ''))
                            <option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
                        @else
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                        @endif
                    @endforeach
                </select>
                <input type="hidden" id="desc_provincia" name="desc_provincia" value="{{old('desc_provincia', $data->desc_provincia ?? '')}}" >
            </div>
        </div>
        <div class="col-md-3" id='loc'>
            <div class="form-group">
                <label>Localidad</label>
                <select name="localidad_id" id='localidad_id' data-placeholder="Localidad" class="form-control" data-fouc>
                    @if($data->localidad_id ?? '')
                        @if($data->localidad_id == "")
                            <option selected></option>
                        @else
                            <option value="{{old('localidad_id', $data['localidad_id'])}}" selected>{{$data['desc_localidad']}}</option>
                        @endif
                    @endif
                </select>
                <input type="hidden" id="localidad_id_previa" name="localidad_id_previa" value="{{old('localidad_id', $data->localidad_id ?? '')}}" >
                <input type="hidden" id="desc_localidad" name="desc_localidad" value="{{old('desc_localidad', $data->desc_localidad ?? '')}}" >
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Código Postal</label>
                <input type="text" name="codigopostal" id="codigopostal" value="{{old('codigopostal', $data['codigopostal'] ?? '')}}" class="form-control" placeholder="Codigo Postal">
            </div>
        </div>
    </div>
</div>
<div class="form-group row">
    <label for="telefono" class="col-lg-3 col-form-label requerido">Telefono</label>
    <span class="input-group-text"><i class="fas fa-phone"></i></span>
    <div class="col-lg-3">
    <input type="text" name="telefono" id="telefono" class="form-control" value="{{old('telefono', $data->telefono ?? '')}}" required/>
    </div>
</div>
<div class="form-group row">
    <label for="email" class="col-lg-3 col-form-label">Email</label>
    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
    <div class="col-lg-5">
        <input type="email" name="email" id="email" class="form-control" value="{{old('email', $data->email ?? '')}}" placeholder="Ingrese email">
    </div>
</div>
<div class="form-group row">
    <label for="modofacturacion" class="col-lg-3 col-form-label requerido">Modo Facturaci&oacute;n</label>
    <select name="modofacturacion" class="col-lg-3 form-control" required>
        <option value="">-- Elija modo de facturaci&oacute;n --</option>
        @foreach($modofacturacionEnum as $value => $modofacturacion)
            @if( $value == old('modofacturacion', $data->modofacturacion ?? ''))
                <option value="{{ $value }}" selected="select">{{ $modofacturacion }}</option>    
            @else
                <option value="{{ $value }}">{{ $modofacturacion }}</option>    
            @endif
        @endforeach
    </select>
</div>
<div class="form-group row">
    <label for="webservice" class="col-lg-3 col-form-label">Web Service</label>
    <div class="col-lg-2">
        <input type="text" name="webservice" id="webservice" class="form-control" value="{{old('webservice', $data->webservice ?? '')}}"/>
    </div>
</div>
<div class="form-group row">
    <label for="pathafip" class="col-lg-3 col-form-label">Path m&oacute;dulo AFIP</label>
    <div class="col-lg-3">
        <input type="text" name="pathafip" id="pathafip" class="form-control" value="{{old('pathafip', $data->pathafip ?? '')}}"/>
    </div>
</div>
<input type="hidden" id="estado" name="estado" value="{{$data->estado ?? 'A'}}" >
