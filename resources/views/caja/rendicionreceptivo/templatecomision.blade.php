<template id="template-renglon-rendicionreceptivo-comision">
    <tr class="item-rendicionreceptivo-comision">
        <td>
            <div class="form-group row" id="cuenta">
                <input type="hidden" class="cuentacajacomision_id cuentacaja_id" name="cuentacajacomision_ids[]" value="" >
                <input type="hidden" class="cuentacajacomision_id_previa" name="cuentacajacomision_id_previa[]" value="" >
                <button type="button" title="Consulta cuentas" style="padding:1;" class="btn-accion-tabla consultacuentacaja tooltipsC">
                        <i class="fa fa-search text-primary"></i>
                </button>
                <input type="text" style="WIDTH: 100px;HEIGHT: 38px" class="codigocuentacajacomision codigo form-control" name="codigocuentacajacomisiones[]" value="" >
                <input type="hidden" class="codigocuentacajacomision_previo" name="codigocuentacajacomision_previos[]" value="" >
            </div>
        </td>							
        <td>
            <input type="text" style="WIDTH: 250px; HEIGHT: 38px" class="nombrecuentacajacomision nombre form-control" name="nombrecuentacajacomisiones[]" value="" readonly >
        </td>        
        <td>
            <select name="monedacomision_ids[]" data-placeholder="Moneda" class="monedacomision_id form-control" required data-fouc>
                <option value="">-- Seleccionar --</option>
                @foreach($moneda_query as $key => $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                @endforeach
            </select>
        </td>        
        <td>
            <input type="number" name="montocomisiones[]" class="form-control montocomision" min="0" value="">
        </td>				
        <td>
            <input type="number" name="cotizacioncomisiones[]" class="form-control cotizacioncomision" value="">
        </td>		
        <td>
            <button type="button" title="Elimina esta linea" class="btn-accion-tabla eliminar_rendicionreceptivo_comision tooltipsC">
                <i class="fa fa-times-circle text-danger"></i>
            </button>
        </td>
    </tr>
</template>