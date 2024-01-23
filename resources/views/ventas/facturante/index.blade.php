@extends("theme.$theme.layout")
@section('titulo')
    Facturas Tienda Nube
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
                <h3 class="card-title">Facturas Tienda Nube</h3>
                <div class="card-tools">
                    <a href="{{route('generar_facturas_tiendanube', ['desdefecha' => $desdefecha, 'hastafecha' => $hastafecha])}}" class="btn btn-outline-secondary btn-sm">
                       	<i class="fa fa-fw fa-plus-circle"></i> Genera facturas
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped table-bordered table-hover" id="tabla-data">
                    <thead>
                        <tr>
                            <th class="width20">ID</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Documento</th>
                            <th>CAE</th>
                            <th>Exento</th>
                            <th>Gravado</th>
                            <th>Percepciones IIBB</th>
                            <th>Iva</th>
                            <th>Total</th>
                            <th class="width80" data-orderable="false"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datas as $data)
                        <tr>
                            <td>{{$data->TipoComprobante}}&nbsp;{{$data->Prefijo}}-{{$data->Numero}}</td>
                            <td>{{date("d/m/Y", strtotime($data->FechaHora ?? ''))}}</td>
                            <td>{{$data->Cliente->RazonSocial}}</td>
                            <td>{{$data->Cliente->NroDocumento}}</td>
                            <td>{{$data->CAE}}</td>
                            <td>{{$data->SubTotalExcento}}</td>
                            <td>{{$data->TotalNeto}}</td>
                            <td>{{$data->PercepcionIIBB}}</td>
                            <td>{{$data->IVA1+$data->IVA2}}</td>
                            <td>{{$data->Total}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
