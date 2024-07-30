<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\Ventas\Ordentrabajo;

class Articulo_Movimiento extends Model
{
    //use SoftDeletes;
    protected $fillable = ['fecha','fechajornada', 'tipotransaccion_id', 'venta_id', 'movimientostock_id',
                        'pedido_combinacion_id', 'ordentrabajo_id', 'lote', 'articulo_id', 'combinacion_id', 
                        'concepto', 'modulo_id', 'cantidad', 'precio', 'costo', 'listaprecio_id', 'incluyeimpuesto', 
                        'moneda_id', 'descuento', 'descuentointegrado', 'deposito_id', 'loteimportacion_id'];
    protected $table = 'articulo_movimiento';

    public function articulo_movimiento_talles()
	{
    	return $this->hasMany(Articulo_Movimiento_Talle::class, 'articulo_movimiento_id');
	}

    public function ordenestrabajo()
	{
    	return $this->belongsTo(Ordentrabajo::class, 'ordentrabajo_id', 'id');
	}

    public function movimientosstock()
	{
    	return $this->belongsTo(MovimientoStock::class, 'movimientostock_id', 'id');
	}

    public function pedidos_combinacion()
	{
    	return $this->belongsTo(Pedido_Combinacion::class, 'pedido_combinacion_id', 'id')->with('articulos');
	}

    public function articulos()
    {
        return $this->belongsTo(Articulo::class, 'articulo_id', 'id');
    }

    public function combinaciones()
    {
        return $this->belongsTo(Combinacion::class, 'combinacion_id', 'id');
    }

    public function modulos()
    {
        return $this->belongsTo(Modulo::class, 'modulo_id');
    }

    public function listasprecio()
    {
        return $this->belongsTo(Listaprecio::class, 'listaprecio_id');
    }

    public function monedas()
    {
        return $this->belongsTo(Moneda::class, 'moneda_id');
    }

}
