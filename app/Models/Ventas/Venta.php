<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Ventas\Tipotransaccion;
use App\Models\Ventas\Puntoventa;
use App\Models\Ventas\Cliente;

class Venta extends Model
{
    use SoftDeletes;
    
    protected $dates = ['deleted_at'];
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

    public function tipotransacciones()
    {
        return $this->hasOne(TipoTransaccion::class, 'id', 'tipotransaccion_id');
    }

    public function puntoventas()
    {
        return $this->hasOne(Puntoventa::class, 'id', 'puntoventa_id');
    }

    public function clientes()
    {
        return $this->hasOne(Cliente::class, 'id', 'cliente_id')
                    ->with("condicionivas");
    }

}

