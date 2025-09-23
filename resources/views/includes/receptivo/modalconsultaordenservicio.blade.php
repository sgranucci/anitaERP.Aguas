<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="modal fade" id="consultaordenservicioModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Ordenes de Servicio</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="" method="post">
			      <div class="form-group row">
   				    <label for="consulta_ordenservicio" class="col-form-label">Buscar:</label>
              <input type="text" name="consultaordenservicio" id="consultaordenservicio" autofocus>
              <input type="hidden" name="consultaordenservicio_id" id="consultaordenservicio_id">
			      </div>
        </form>
        
        <table class="table table-striped table-bordered table-hover" id="tabla-data">
          <thead>
              <th>Orden Servicio</th>
              <th>Id Guía</th>
              <th>Nombre Guía</th>
              <th>Código Guía</th>
          </thead>
          <tbody id="datosordenservicio"></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" id="cierraconsultaordenservicioModal" class="btn btn-secondary" data-dismiss="modal">Cierra</button>
        <button type="button" id="aceptaconsultaordenservicioModal" class="btn btn-primary">Acepta</button>
      </div>
    </div>
  </div>
</div>
