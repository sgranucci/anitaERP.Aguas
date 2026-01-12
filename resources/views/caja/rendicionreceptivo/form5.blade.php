<div class="card form5" style="display: none">
    <h3>Comisiones</h3>
    <div class="card-body">
        <table class="table" id="rendicionreceptivo-comision-table">
            <thead>
                <tr>
                    <th style="width: 12%;">Cuenta de caja</th>
                    <th style="width: 30%;">Descripción</th>
                    <th style="width: 10%;">Moneda</th>
                    <th style="width: 20%;">Monto</th>
                    <th style="width: 20%;">Cotización</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="tbody-rendicionreceptivo-comision-table" class="container-comision">
            @if ($data->rendicionreceptivo_comisiones ?? '') 
                @foreach (old('comision', $data->rendicionreceptivo_comisiones->count() ? $data->rendicionreceptivo_comisiones : ['']) as $comision)
                    <tr class="item-rendicionreceptivo-comision">
                        <td>
                            <input type="text" class="codigocuentacajacomision form-control" name="codigocuentacajacomisiones[]" value="{{$comision->cuentacajas->codigo ?? ''}}" readonly>
                            <input type="hidden" class="cuentacajacomision_id form-control" name="cuentacajacomision_ids[]" value="{{$comision->cuentacajas->id ?? ''}}" readonly>
                        </td>							
                        <td>
                            <input type="text" class="nombrecuentacajacomision form-control" name="nombrecuentacajacomisiones[]" value="{{$comision->cuentacajas->nombre ?? ''}}" readonly>
                        </td>
                        <td>
                            <input type="text" class="nombremonedacomision form-control" name="nombremonedacomisiones[]" value="{{$comision->monedas->abreviatura ?? ''}}" readonly>
                            <input type="hidden" class="monedacomision_id form-control" name="monedacomision_ids[]" value="{{$comision->moneda_id ?? ''}}">
                        </td>
                        <td>
                            <input type="number" name="montocomisiones[]" class="form-control montocomision" min="0" value="{{old('montos[]', $comision->monto ?? '')}}">
                        </td>				
                        <td>
                            <input type="number" name="cotizacioncomisiones[]" class="form-control cotizacioncomision" value="{{old('cotizaciones[]', $comision->cotizacion ?? '0')}}">
                        </td>		
                        <td>
                            <button type="button" title="Elimina esta linea" class="btn-accion-tabla eliminar_rendicionreceptivo_comision tooltipsC">
                                <i class="fa fa-times-circle text-danger"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
        @include('caja.rendicionreceptivo.templatecomision')
        <div class="form-group row totales-por-moneda-comision">
        </div>
        <div class="form-group row totales-comision-voucher">
        </div>        
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group row">
                    <button id="agrega_renglon_rendicionreceptivo_comision" class="pull-right btn btn-danger">+ Agrega rengl&oacute;n</button>
                </div>
            </div>
        </div>
    </div>
</div>
