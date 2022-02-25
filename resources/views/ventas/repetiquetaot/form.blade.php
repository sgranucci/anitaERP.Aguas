<div> 

<label for="ot" class="col-lg-3 col-form-label">Ordenes de trabajo a imprimir</label>
<select class='mi-selector' name='ordenestrabajo[]' multiple='multiple'>
	<option value=''>-- Seleccionar una OT --</option>
    	@foreach($ordentrabajo_query as $key => $value)
    		<option value="{{ $value->ordtm_nro_orden }}">{{ $value->ordtm_nro_orden }}</option>    
    	@endforeach
</select>

</div>
