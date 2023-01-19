<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Venta extends Model
{
    protected $fillable = [
            'fecha', 'fechajornada', 'tipotransaccion_id',
            'puntoventa_id', 'numerocomprobante', 'cliente_id', 'condicionventa_id',
            'vendedor_id', 'transporte_id', 'total', 'moneda_id', 'estado',
            'usuario_id', 'leyenda', 'descuento', 'descuentointegrado', 'lugarentrega',
            'cliente_entrega_id', 'codigo', 'nombre', 'domicilio', 'localidad_id', 'provincia_id',
            'pais_id', 'codigopostal', 'email', 'telefono', 'nroinscripcion', 
            'condicioniva_id', 'cae', 'fechavencimientocae', 'puntoventaremito_id',
            'numeroremito', 'cantidadbulto'
    ];

    protected $table = 'venta';

}

