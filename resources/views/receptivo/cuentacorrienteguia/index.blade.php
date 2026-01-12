@extends("theme.$theme.layout")
@section('titulo')
    Cuenta Corriente de Guias
@endsection

@section("scripts")
<script src="{{asset("assets/pages/scripts/admin/index.js")}}" type="text/javascript"></script>
@endsection

<?php use App\Helpers\biblioteca ?>

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Cuenta Corriente de Guia {{$nombreguia}}</h3>
                <div class="card-tools">
                    <a href="{{route('guia')}}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado de guías
                    </a>
                    <a href="{{route('consulta_movimiento_caja')}}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Movimientos de cajas
                    </a>
                </div>                
                <div class="d-md-flex justify-content-md-end">
					<form action="{{ route('listar_cuentacorriente_guia', ['id' => $id]) }}" method="GET">
						<div class="btn-group">
							<input type="text" name="busqueda" class="form-control" placeholder="Busqueda ..."> 
							<button type="submit" class="btn btn-default">
								<span class="fa fa-search"></span>
							</button>
						</div>
					</form>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                @include('includes.exportar-tabla-id', ['ruta' => 'listar_cuentacorriente_guia', 'id' => $id, 'busqueda' => $busqueda])
                <table class="table table-striped table-bordered table-hover" id="tabla-paginada">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th style="width: 6%">O.Servicio</th>
                            <th>Rendición</th>
                            <th>Mov. de Caja</th>
                            <th style="width: 3%;">Mon</th>
                            <th style="width: 9%; text-align: right;">Debe</th>
                            <th style="width: 9%; text-align: right;">Haber</th>
                            <th style="width: 9%; text-align: right;">Saldo $</th>
                            <th style="width: 9%; text-align: right;">Saldo U$S</th>
                            <th style="width: 9%; text-align: right;">Saldo REA</th>
                            <th style="width: 9%; text-align: right;">Saldo EA</th>
                            <th style="width: 9%; text-align: right;">Saldo GUA</th>
                            <th class="width80" data-orderable="false"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 1; $i <= 5; $i++)
                            @php $saldo[$i] = 0; @endphp
                        @endfor
                        @foreach ($cuentacorriente as $data)
                            @php $saldo[$data->moneda_id] += $data->monto; @endphp
                        <tr>
                            <td>{{date("d/m/Y", strtotime($data->fecha ?? ''))}}</td>
                            <td>{{$data->rendicion_ordenservicio_id??$data->caja_movimiento_ordenservicio_id??''}}</td>
                            <td>{{$data->numerorendicion??''}}</td>
                            <td><small>{{$data->abreviaturatipotransaccion??''}} {{$data->numerotransaccion??''}}</small></td>
                            <td>{{$data->abreviaturamoneda}}</td>
                            <td style="text-align: right;">
                                @if ($data->monto >= 0)
                                    {{number_format($data->monto, 2)}}
                                @endif
                            </td>
                            <td style="text-align: right;">
                                @if ($data->monto < 0)
                                    {{number_format(abs($data->monto), 2)}}
                                @endif
                            </td>
                            @for ($i = 1; $i <= 5; $i++)
                                <td style="text-align: right;">
                                <strong>
                                    {{number_format($saldo[$i], 2)}}
                                </strong>
                                </td>
                            @endfor
                            <td>
                                @if (isset($data->rendicion_ordenservicio_id))
                                    @if (can('editar-rendicion-receptivo', false))
                                        <a href="{{route('editar_rendicionreceptivo', ['id' => $data->rendicionreceptivo_id??0, 'origen' => 'listacuentacorrienteguia'])}}" class="btn-accion-tabla tooltipsC" title="Editar este registro">
                                        <i class="fa fa-edit"></i>
                                        </a>
                                    @endif        
                                @endif   
                                @if (isset($data->caja_movimiento_ordenservicio_id))
                                    @if (can('editar-ingresos-egresos-caja', false))
                                        <a href="{{route('editar_ingresoegreso', ['id' => $data->caja_movimiento_id??0, 'origen' => 'listacuentacorrienteguia'])}}" class="btn-accion-tabla tooltipsC" title="Editar este registro">
                                        <i class="fa fa-edit"></i>
                                        </a>
                                    @endif   
                                @endif                           
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{ $cuentacorriente->appends(['busqueda' => $busqueda]) }}
@endsection