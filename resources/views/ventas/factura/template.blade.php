<template id="template-renglon">
	<tr class="item-pedido">
    	<td>
       		<input type="text" name="items[]" class="form-control item" value="1" readonly>
            <input type="hidden" name="medidas[]" class="form-control medidas" readonly value="" />
            <input type="hidden" name="listasprecios_id[]" class="form-control listaprecio_id" readonly value="" />
            <input type="hidden" name="monedas_id[]" class="form-control moneda_id" readonly value="" />
            <input type="hidden" name="incluyeimpuestos[]" class="form-control incluyeimpuesto" readonly value="" />
            <input type="hidden" name="descuentos[]" class="form-control descuento" readonly value="0" />
			<input type="hidden" name="ids[]" class="form-control ids" value="0" />
			<input type="hidden" name="loteids[]" class="form-control ids" value="0" />
        </td>
        <td>
			<div class="form-group row" id="articulo">
				<input type="text" style="WIDTH: 70px;HEIGHT: 38px" class="articulo_id" name="articulos_id[]" value="{{$pedidoitem->articulo_id ?? ''}}" >
				<button type="button" title="Consulta por SKU" style="padding:0;" class="btn-accion-tabla consultaSKU tooltipsC">
                	<i class="fa fa-search text-primary"></i>
				</button>
			</div>
        </td>
		<td>
			<input type="text" style="WIDTH: 130px;HEIGHT: 38px" class="sku form-control" name="skus[]" value="{{$pedidoitem->sku ?? ''}}" >
		</td>							
		<td>
			<input type="text" style="WIDTH: 500px; HEIGHT: 38px" class="descripcion form-control" name="descripciones[]" value="{{$pedidoitem->descripcion ?? ''}}" >
		</td>		
        <td>
        	<select name="combinaciones_id[]" data-placeholder="Combinaciones" class="form-control combinacion" data-fouc>
        	</select>
        	<input type="hidden" class="combinacion_id_previa" name="combinacion_id_previa[]" value="{{old('combinacion_id') ?? ''}}" >
        	<input type="hidden" class="desc_combinacion" name="desc_combinacion[]" value="{{old('desc_combinacion[]') ?? ''}}" >
        </td>
        <td>
        	<select name="modulos_id[]" data-placeholder="Modulos" class="form-control modulo" data-fouc>
        	</select>
        	<input type="hidden" class="modulo_id_previa" name="modulo_id_previa[]" value="{{old('modulo_id') ?? ''}}" >
        	<input type="hidden" class="desc_modulo" name="desc_modulo[]" class="desc_modulo" value="{{old('desc_modulo[]') ?? ''}}" >
        </td>
        <td>
        	<input type="text" id="icantidad" name="cantidades[]" class="form-control cantidad" readonly value="" />
        </td>
        <td>
        	<input type="text" style="text-align: right;" id="iprecio" name="precios[]" class="form-control precio" readonly value="" />
        </td>
        <td>
			<button type="button" title="Elimina esta linea" style="padding:0;" class="btn-accion-tabla eliminar tooltipsC">
        		<i class="fa fa-trash text-danger"></i>
			</button>
        </td>
	</tr>
</template>
