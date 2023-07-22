<?php

namespace App\Queries\Stock;

interface Articulo_MovimientoQueryInterface
{
    public function generaDatosRepStockOt($estado, $mventa_id,
                                            $desdearticulo, $hastaarticulo,
                                            $desdelinea_id, $hastalinea_id,
                                            $desdecategoria_id, $hastacategoria_id,
                                            $desdelote, $hastalote);
    public function leeStockPorLote($lote, $articulo_id, $combinacion_id);
}

