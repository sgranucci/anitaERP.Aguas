<template id="template-renglon-guia">
    <tr class="item-guia">
        <td>
            <input type="text" name="guia[]" class="form-control iiguia" readonly value="" />
        </td>
        <td>
            <select name="guia_ids[]" data-placeholder="Guía" class="form-control guia_id" data-fouc>
                <option value="">-- Elija guía --</option>
                @foreach ($guia_query as $guia)
                    <option value="{{ $guia->id }}">{{ $guia->nombre }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <select name="tipocomisiones[]" class="col-lg-8 form-control tipocomision" required>
                <option value="">-- Elija tipo de comisión --</option>
                @foreach ($tipocomision_enum as $tipocomision)
                    <option value="{{ $tipocomision['valor'] }}">{{ $tipocomision['nombre'] }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="porcentajecomisiones[]" class="form-control porcentajecomision" value="">
        </td>
        <td>
            <input type="number" name="montocomisiones[]" class="form-control montocomision" value="">
        </td>
        <td>
            <button style="width: 7%;" type="button" title="Elimina esta linea" class="btn-accion-tabla eliminar_guia tooltipsC">
                <i class="fa fa-times-circle text-danger"></i>
            </button>
        </td>
    </tr>
</template>