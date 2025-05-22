<div class="row">
    <div class="col-sm-6">
        <div class="form-group row">
            <label for="talonariovoucher" class="col-lg-3 col-form-label">Talonario Voucher</label>
            <select name="talonariovoucher_id" id="talonariovoucher_id" data-placeholder="Talonario Voucher" class="col-lg-4 form-control required" data-fouc>
                <option value="">-- Seleccionar --</option>
                @foreach($talonariovoucher_query as $key => $value)
                    @if( (int) $value->id == (int) old('talonariovoucher_id', $data->talonariovoucher_id ?? session('talonariovoucher_id')))
                        <option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
                    @else
                        <option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                    @endif
                @endforeach
            </select>
        </div>
        <div class="form-group row">
            <label for="reserva" class="col-lg-2 col-form-label">Reserva</label>
            <input type="text" class="col-lg-9 reserva" id="reserva" name="reserva" value="{{$data->nombrepasajero ?? ''}}" readonly>
            <button type="button" title="Consulta reservas" style="padding:1;" class="btn-accion-tabla consultareserva tooltipsC">
                <i class="fa fa-search text-primary"></i>
            </button>
            <input type="hidden" class="reserva_id" id="reserva_id" name="reserva_id" value="{{$data->reserva_id ?? ''}}" >
            <input type="hidden" name="pasajero_id" id="pasajero_id" class="form-control" value="{{old('pasajero_id', $data->pasajero_id ?? '')}}">
            <input type="hidden" name="nombrepasajero" id="nombrepasajero" class="form-control" value="{{old('nombrepasajero', $data->nombrepasajero ?? '')}}">
            <input type="hidden" name="fechaarribo" id="fechaarribo" class="form-control" value="">
            <input type="hidden" name="fechapartida" id="fechapartida" class="form-control" value="">
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group row">
            <label for="fecha" class="col-lg-3 col-form-label">Fecha</label>
            <div class="col-lg-3">
                <input type="date" name="fecha" id="fecha" class="form-control required" value="{{old('fecha', $data->fecha ?? '')}}">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <div class="form-group row">
            <label for="pax" class="col-lg-3 col-form-label">Pax</label>
            <div class="col-lg-3">
                <input type="number" name="pax" id="pax" class="form-control" value="{{old('pax', $data->pax ?? '0')}}">
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group row">
            <label for="paxfree" class="col-lg-3 col-form-label">Free</label>
            <div class="col-lg-3">
                <input type="number" name="paxfree" id="paxfree" class="form-control" value="{{old('paxfree', $data->paxfree ?? '0')}}">
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group row">
            <label for="incluido" class="col-lg-3 col-form-label">Incluido</label>
            <div class="col-lg-3">
                <input type="number" name="incluido" id="incluido" class="form-control" value="{{old('incluido', $data->incluido ?? '0')}}">
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group row">
            <label for="opcional" class="col-lg-3 col-form-label">Opcional</label>
            <div class="col-lg-3">
                <input type="number" name="opcional" id="opcional" class="form-control" value="{{old('opcional', $data->opcional ?? '0')}}">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="form-group row">
            <label for="servicioterrestre" class="col-lg-4 col-form-label">Servicio</label>
            <select name="servicioterrestre_id" id="servicioterrestre_id" data-placeholder="Servicio terrestre" class="col-lg-6 form-control required" data-fouc>
                <option value="">-- Seleccionar --</option>
                @foreach($servicioterrestre_query as $key => $value)
                    @if( (int) $value->id == (int) old('servicioterrestre_id', $data->servicioterrestre_id ?? ''))
                        <option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
                    @else
                        <option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                    @endif
                @endforeach
            </select>
        </div>        
        <div class="form-group row">
            <label for="proveedor" class="col-lg-4 col-form-label">Proveedor del servicio</label>
            <select name="proveedor_id" id="proveedor_id" data-placeholder="Proveedor" class="col-lg-6 form-control required" data-fouc>
                <option value="">-- Seleccionar --</option>
                @foreach($proveedor_query as $key => $value)
                    @if( (int) $value->id == (int) old('proveedor_id', $data->proveedor_id ?? ''))
                        <option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
                    @else
                        <option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                    @endif
                @endforeach
            </select>
        </div>      
    </div>
    <div class="col-sm-6">  
        <div class="form-group row">
            <label for="formapago" class="col-lg-4 col-form-label">Forma de pago</label>
            <select name="formapago_id" id="formapago_id" data-placeholder="Forma de pago" class="col-lg-6 form-control required" data-fouc>
                <option value="">-- Seleccionar --</option>
                @foreach($formapago_query as $key => $value)
                    @if( (int) $value->id == (int) old('formapago_id', $data->formapago_id ?? ''))
                        <option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
                    @else
                        <option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                    @endif
                @endforeach
            </select>
        </div>        
        <div class="form-group row">
            <label for="moneda" class="col-lg-4 col-form-label">Moneda</label>
            <select name="moneda_id" id="moneda_id" data-placeholder="Moneda del pago" class="col-lg-6 form-control required" data-fouc>
                <option value="">-- Seleccionar --</option>
                @foreach($moneda_query as $key => $value)
                    @if( (int) $value->id == (int) old('moneda_id', $data->moneda_id ?? ''))
                        <option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
                    @else
                        <option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                    @endif
                @endforeach
            </select>
        </div>   
    </div>    
    <div class="row">
        <div class="col-sm-3">
            <div class="form-group row">
                <label for="montovoucher" class="col-lg-6 col-form-label">Monto</label>
                <div class="col-lg-6">
                    <input type="number" name="montovoucher" id="montovoucher" class="form-control" value="{{old('montovoucher', $data->montovoucher ?? '0')}}">
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group row">
                <label for="cotizacion" class="col-lg-6 col-form-label">Cotización</label>
                <div class="col-lg-6">
                    <input type="number" name="cotizacion" id="cotizacion" class="form-control" value="{{old('cotizacion', $data->cotizacion ?? '0')}}">
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group row">
                <label for="montoempresa" class="col-lg-6 col-form-label">Monto empresa</label>
                <div class="col-lg-6">
                    <input type="number" name="montoempresa" id="montoempresa" class="form-control" value="{{old('montoempresa', $data->montoempresa ?? '0')}}">
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group row">
                <label for="montoproveedor" class="col-lg-6 col-form-label">Monto proveedor</label>
                <div class="col-lg-6">
                    <input type="number" name="montoproveedor" id="montoproveedor" class="form-control" value="{{old('montoproveedor', $data->montoproveedor ?? '0')}}">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-group row">
    <label for="observacion" class="col-lg-3 col-form-label">Observaciones</label>
    <div class="col-lg-8">
        <input type="text" name="observacion" id="observacion" class="form-control" value="{{old('observacion', $data->observacion ?? '')}}">
    </div>
</div>
<h3>Guias</h3>
<div class="card-body">
    <table class="table" id="guia-table">
        <thead>
            <tr>
                <th style="width: 5%;"></th>
                <th style="width: 40%;">Guia</th>
                <th style="width: 30%;">Tipo de comisión</th>
                <th style="width: 10%;">Porcentaje</th>
                <th style="width: 20%;">Monto Comisión</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="tbody-guia-table">
        @if ($data->voucher_guias ?? '') 
            @foreach (old('guia', $data->voucher_guias->count() ? $data->voucher_guias : ['']) as $guia)
                <tr class="item-guia">
                    <td>
                        <input type="text" name="guia[]" class="form-control iiguia" readonly value="{{ $loop->index+1 }}" />
                    </td>
                    <td>
                        <select name="guia_ids[]" data-placeholder="Guía" class="form-control guia_id" data-fouc>
                            <option value="">-- Elija guía --</option>
                            @foreach ($guia_query as $guiaq)
                                <option value="{{ $guiaq->id }}"
                                    @if (old('guia', $guia->guia_id ?? '') == $guiaq->id) selected @endif
                                    >{{ $guiaq->nombre }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="tipocomisiones[]" class="col-lg-6 form-control tipocomision" required>
                            <option value="">-- Elija tipo de comisión --</option>
                            @foreach ($tipocomision_enum as $tipocomision)
                                <option value="{{ $tipocomision['valor'] }}"
                                    @if (old('tipocomision', $guia->tipocomision ?? '') == $tipocomision['valor']) selected @endif
                                    >{{ $tipocomision['nombre'] }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="porcentajecomisiones[]" class="form-control porcentajecomision" value="{{old('montocomision', $guia->porcentajecomision ?? '')}}">
                    </td>
                    <td>
                        <input type="number" name="montocomisiones[]" class="form-control montocomision" value="{{old('montocomision', $guia->montocomision ?? '')}}">
                    </td>
                    <td>
                        <button style="width: 7%;" type="button" title="Elimina esta linea" class="btn-accion-tabla eliminar_guia tooltipsC">
                            <i class="fa fa-times-circle text-danger"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
    @include('caja.voucher.template')
    <div class="row">
        <div class="col-md-12">
            <button id="agrega_renglon_guia" class="pull-right btn btn-danger">+ Agrega rengl&oacute;n</button>
        </div>
    </div>
</div>
@include('includes.receptivo.modalconsultareserva')

