<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Models\Stock\Mventa;
use App\Models\Stock\Lote;
use App\Models\Ventas\Cliente;
use App\Models\Ventas\Cliente_Entrega;
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
							'cliente_entrega_id', 'lugarentrega', 'codigo'];
    protected $table = 'pedido';
	protected $dates = [
						'fecha',
						'fechaentrega'
						];
	protected $casts = ['fecha' => 'datetime:d-m-Y'];

	public function pedido_combinaciones()
	{
    	return $this->hasMany(Pedido_Combinacion::class, 'pedido_id')
                    ->with('articulos')
                    ->with('combinaciones')
                    ->with('pedido_combinacion_talles')
                    ->with('pedido_combinacion_estados')
                    ->with('ordenestrabajo')
                    ->with('lotes');
	}

    public function clientes()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id')->with('tipossuspensioncliente');
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

    public function cliente_entregas()
    {
        return $this->hasOne(Cliente_Entrega::class, 'cliente_entrega_id');
    }

	public function getEntregaNombreAttribute()
	{
		$data = Cliente_entrega::find($this->cliente_entrega_id);
		return ($data ? $data->nombre : '');
	}

	public function scopeWithWhereHasOtArticuloCombinacion($query, $articulo_id, $combinacion_id)
	{
		return $query->with(['pedido_combinaciones' => function ($q) use($articulo_id, $combinacion_id) {
				$q->whereIn('ot_id',[-1,0])->where('articulo_id',$articulo_id)->where('combinacion_id',$combinacion_id);
				}, 'pedido_combinaciones.combinaciones'])->whereHas('pedido_combinaciones', function ($q) use ($articulo_id, $combinacion_id) {
					$q->whereIn('ot_id',[-1,0])->where('articulo_id',$articulo_id)->where('combinacion_id',$combinacion_id);
				});
	}
}

