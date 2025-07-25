<div class="card form4" style="display: none">
    <h3>Gastos a compensar</h3>
    <div class="card-body">
        <input type="hidden" name="empresa_id" id="empresa_id" class="form-control" value="1" />
        <table class="table" id="rendicionreceptivo-gasto-table">
            <thead>
                <tr>
                    <th>Concepto del gasto</th>
                    <th>Cuenta de caja</th>
                    <th>Descripción</th>
                    <th>Moneda</th>
                    <th>Monto</th>
                    <th>Cotización</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="tbody-rendicionreceptivo-gasto-table" class="container-gasto">
            @if ($data->rendicionreceptivo_gastos ?? '') 
                @foreach (old('formapago', $data->rendicionreceptivo_gastos->count() ? $data->rendicionreceptivo_gastos : ['']) as $formapago)
                    <tr class="item-rendicionreceptivo-gasto">
                        <td>
                            <div class="form-group row" id="conceptogasto">
                                <input type="hidden" name="conceptogasto[]" class="form-control iiconceptogasto" readonly value="{{ $loop->index+1 }}" />
                                <input type="text" style="WIDTH: 40px;HEIGHT: 38px" class="conceptogasto_id" name="conceptogasto_ids[]" value="{{$formapago->conceptogasto_id ?? ''}}" >
                                <input type="hidden" class="conceptogasto_id_previa" name="conceptogasto_id_previa[]" value="{{$formapago->conceptogasto_id ?? ''}}" >
                                <button type="button" title="Consulta conceptos" style="padding:1;" class="btn-accion-tabla consultaconceptogasto tooltipsC">
                                        <i class="fa fa-search text-primary"></i>
                                </button>
                                <input type="text" style="WIDTH: 100px;HEIGHT: 38px" class="nombreconceptogasto form-control" name="nombreconceptogastos[]" value="" >
                            </div>
                        </td>	
                        <td>
                            <div class="form-group row" id="formapago">
                                <input type="hidden" class="cuentacaja_id" name="cuentacaja_ids[]" value="{{$formapago->cuentacaja_id ?? ''}}" >
                                <input type="hidden" class="cuentacaja_id_previa" name="cuentacaja_id_previa[]" value="{{$formapago->cuentacaja_id ?? ''}}" >
                                <button type="button" title="Consulta cuentas" style="padding:1;" class="btn-accion-tabla consultacuentacaja tooltipsC">
                                        <i class="fa fa-search text-primary"></i>
                                </button>
                                <input type="text" style="WIDTH: 100px;HEIGHT: 38px" class="codigo form-control" name="codigos[]" value="{{$formapago->cuentacajas->codigo ?? ''}}" >
                                <input type="hidden" class="codigo_previo" name="codigo_previos[]" value="{{$formapago->cuentacajas->codigo ?? ''}}" >
                            </div>
                        </td>							
                        <td>
                            <input type="text" style="WIDTH: 250px; HEIGHT: 38px" class="nombre form-control" name="nombres[]" value="{{$formapago->cuentacajas->nombre ?? ''}}" readonly>
                        </td>
                        <td>
                            <select name="moneda_ids[]" data-placeholder="Moneda" class="moneda form-control required" required readonly data-fouc>
                                <option value="">-- Seleccionar --</option>
                                @foreach($moneda_query as $key => $value)
                                    @if( (int) $value->id == (int) old('moneda_ids[]', $formapago->moneda_id ?? ''))
                                        <option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
                                    @else
                                        <option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                                    @endif
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" style="WIDTH: 150px; HEIGHT: 38px" name="montos[]" class="form-control monto" min="0" value="{{old('montos[]', $formapago->monto ?? '')}}">
                        </td>				
                        <td>
                            <input type="number" style="WIDTH: 120px; HEIGHT: 38px" name="cotizaciones[]" class="form-control cotizacion" value="{{old('cotizaciones[]', $formapago->cotizacion ?? '0')}}">
                        </td>		
                        <td>
                            <button type="button" title="Elimina esta linea" class="btn-accion-tabla eliminar_rendicionreceptivo_gasto tooltipsC">
                                <i class="fa fa-times-circle text-danger"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
        @include('caja.rendicionreceptivo.templategasto')
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group row">
                    <button id="agrega_renglon_rendicionreceptivo_gasto" class="pull-right btn btn-danger">+ Agrega rengl&oacute;n</button>
                </div>
            </div>
        </div>
        <div class="form-group row totales-por-moneda-gasto">
        </div>
    </div>
</div>
<input type="hidden" id="csrf_token" class="form-control" value="{{csrf_token()}}" />
@include('includes.caja.modalconsultagasto')
