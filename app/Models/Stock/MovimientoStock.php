<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Stock\Articulo_Movimiento;
use App\Traits\Stock\MovimientoStockTrait;

class MovimientoStock extends Model
{
	use SoftDeletes;
	use MovimientoStockTrait;

    protected $table = "movimientostock";
    protected $fillable = ['fecha', 'fechajornada', 'tipotransaccion_id', 'codigo', 'leyenda', 'estado', 'usuario_id'];

	public function estadoEnum()
	{
		return MovimientoStockTrait::$enumEstado;
	}

	public function articulo_movimientos()
	{
		return $this->hasMany(Articulo_Movimiento::class, 'movimientostock_id');
	}
}
