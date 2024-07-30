<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="modal fade" id="consultaarticuloModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Art&iacute;culos</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="" method="post">
			      <div class="form-group row">
   				    <label for="consulta" class="col-form-label">Buscar:</label>
              <input type="text" name="consulta" id="consulta">
              <input type="hidden" name="consultaarticulo" id="consultaarticulo_id">
			      </div>
        </form>
        
        <table class="table table-striped table-bordered table-hover" id="tabla-data">
          <thead>
              <th>ID</th>
              <th>SKU</th>
              <th>Descripción</th>
              <th>Marca</th>
              <th>Línea</th>
              <th>Acci&oacute;n</th>
          </thead>
          <tbody id="datos"></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" id="cierraconsultaarticuloModal" class="btn btn-secondary" data-dismiss="modal">Cierra</button>
        <button type="button" id="aceptaconsultaarticuloModal" class="btn btn-primary">Acepta</button>
      </div>
    </div>
  </div>
</div>
