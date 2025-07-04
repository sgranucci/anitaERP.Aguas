<div class="modal fade" id="revertiringresoegresoModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Revierte Movimiento de Ingreso y Egreso</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form>
    		<div class="form-group row">
   				<label for="fechacopia" class="col-form-label col-lg-3">Fecha de movimiento a revertir</label>
          <input type="date" name="fechacopia" id="fechacopia" class="form-control col-lg-3" value="{{date('Y-m-d')}}">
			</div>
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" id="cierrarevertiringresoegresoModal" class="btn btn-secondary" data-dismiss="modal">Cierra</button>
        <button type="button" id="aceptarevertiringresoegresoModal" class="btn btn-primary">Revierte Movimiento</button>
      </div>
    </div>
  </div>
</div>
