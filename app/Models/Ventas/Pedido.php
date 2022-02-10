<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Stock\Mventa;
use App\Models\Ventas\Cliente;
use App\Models\Ventas\Condicionventa;
use App\Models\Ventas\Vendedor;
use App\Models\Ventas\Pedido_Combinacion;
use App\Models\Ventas\Transporte;
use App\Models\Seguridad\Usuario;
use App\Traits\Ventas\PedidoTrait;

class Pedido extends Model
{
	use SoftDeletes;
	use PedidoTrait;

    protected $fillable = ['fecha', 'fechaentrega', 'cliente_id', 'condicionventa_id', 'vendedor_id', 'transporte_id', 
							'mventa_id', 'estado', 'usuario_id', 'leyenda', 'descuento', 'descuentointegrado', 
							'lugarentrega', 'codigo'];
    protected $table = 'pedido';
	protected $dates = [
						'fecha',
						'fechaentrega'
						];

	public function pedido_combinaciones()
	{
    	return $this->hasMany(Pedido_Combinacion::class, 'pedido_id')->with('articulos')->with('pedido_combinacion_talles');
	}

    public function clientes()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function condicionesdeventa()
    {
        return $this->belongsTo(Condicionventa::class, 'condicionventa_id');
    }

    public function mventas()
    {
        return $this->belongsTo(Mventa::class, 'mventa_id');
    }

    public function vendedores()
    {
        return $this->belongsTo(Vendedor::class, 'vendedor_id');
    }

    public function transportes()
    {
        return $this->belongsTo(Transporte::class, 'transporte_id');
    }

    public function usuarios()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}

