@extends("theme.$theme.layout")
@section('titulo')
    Editar combinac&oacute;n
@endsection

@section("styles")
<link href="{{asset("assets/js/bootstrap-fileinput/css/fileinput.min.css")}}" rel="stylesheet" type="text/css"/>
@endsection

@section("scriptsPlugins")
<script src="{{asset("assets/js/bootstrap-fileinput/js/fileinput.min.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/js/bootstrap-fileinput/js/locales/es.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/js/bootstrap-fileinput/themes/fas/theme.min.js")}}" type="text/javascript"></script>
@endsection

@section("scripts")

<script src="{{asset("assets/pages/scripts/admin/crear.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/pages/scripts/admin/imprimirHtml.js")}}" type="text/javascript"></script>

    <script>
      $(document).ready(function(){
        let row_number = {{ count(old('capelladas', $combinacion->capearts->count() ? $combinacion->capearts : [''])) }};
        let row_number_avio = {{ count(old('avios', $combinacion->avioarts->count() ? $combinacion->avioarts : [''])) }};

        $("#add_row").click(function(e){
          e.preventDefault();
          let new_row_number = row_number - 1;
          $('#capellada' + row_number).html($('#capellada' + new_row_number).html()).find('td:first-child');
          $('#capelladas_table').append('<tr id="capellada' + (row_number + 1) + '"></tr>');
          row_number++;
        });
        $("#delete_row").click(function(e){
          e.preventDefault();
          if(row_number > 1){
            $("#capellada" + (row_number - 1)).html('');
            row_number--;
          }
        });

        $("#add_row_avio").click(function(e){
          e.preventDefault();
          let new_row_number = row_number_avio - 1;
          $('#avio' + row_number_avio).html($('#avio' + new_row_number).html()).find('td:first-child');
          $('#avios_table').append('<tr id="avio' + (row_number_avio + 1) + '"></tr>');
          row_number_avio++;
        });
        $("#delete_row_avio").click(function(e){
          e.preventDefault();
          if(row_number_avio > 1){
            $("#avio" + (row_number_avio - 1)).html('');
            row_number_avio--;
          }
        });
      });

      $('#foto').fileinput({
        language: 'es',
        allowedFileExtensions: ['jpg', 'jpeg', 'png'],
        maxFileSize: 1500,
        showUpload: false,
        showClose: false,
        initialPreviewAsData: true,
        dropZoneEnabled: false,
        theme: "fa",
      });

    </script>

@endsection

@section('contenido')
<div class="container-fluid">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.mensaje')
        <div class="card card-danger">
            <div id="enc" class="card-header">
                <h3 class="card-title">Editar Combinaci&oacute;n - Datos T&eacute;cnica - Combinacion: {{ $combinacion->codigo }} - {{ $combinacion->nombre }} - Estado: {{ $combinacion->estado }} </h3>
                <div class="card-tools">
                    <a href="{{  URL::previous() }}" class="btn btn-outline-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                    <a href="#" class="btn btn-outline-info btn-sm" onclick="imprimirHtml('printableArea', 'enc')">
                        <i class="fa fa-fw fa-print"></i> Imprimir ficha t&eacute;cnica
                    </a>
                </div>
            </div>
            <br>
            <form action="{{route('combinacion.tecnica.update', ['id' => $combinacion->id])}}" enctype="multipart/form-data" id="form-general" class="form-horizontal form--label-right" method="POST" autocomplete="off">
                @csrf @method("put")
                <input type="hidden" name="id" class="form-control" value="{{ $id }}" />
                <input type="hidden" name="combinacion_id" class="form-control" value="{{ $combinacion->id }}" />
                <input type="hidden" name="codigo" class="form-control" value="{{ $combinacion->codigo }}" />
                <input type="hidden" name="sku" class="form-control" value="{{ $combinacion->articulos->sku }}" />
                @include('stock.combinacion.tecnica.partials.form', ['edit' => true])
            </form>
        </div>
    </div>
</div>
@endsection
