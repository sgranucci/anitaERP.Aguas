<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Model;
use App\Models\Stock\Talle;

class Pedido_Combinacion_Talle extends Model
{
    protected $fillable = ['pedido_combinacion_id', 'talle_id', 'cantidad', 'precio'];
    protected $table = 'pedido_combinacion_talle';

    public function pedidos_combinacion()
    {
        return $this->belongsTo(Pedido_Combinacion::class, 'pedido_combinacion_id');
    }

    public function talles()
    {
        return $this->belongsTo(Talle::class, 'talle_id');
    }

}

