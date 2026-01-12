<?php

namespace App\Models\Receptivo;

use Illuminate\Database\Eloquent\Model;
use App\Models\Configuracion\Moneda;
use App\Models\Caja\Rendicionreceptivo;
use App\Models\Caja\Caja_Movimiento;

class Guia_Cuentacorriente extends Model
{
    protected $fillable = ['fecha', 'guia_id', 'monto', 'moneda_id', 'cotizacion', 
							'rendicionreceptivo_id', 'caja_movimiento_id'];
	
    protected $table = 'guia_cuentacorriente';

	public function guias()
	{
    	return $this->belongsTo(Guia::class, 'guia_id', 'id');
	}

    public function monedas()
    {
        return $this->belongsTo(Moneda::class, 'moneda_id');
    }

    public function rendicionreceptivos()
	{
    	return $this->belongsTo(Rendicionreceptivo::class, 'rendicionreceptivo_id', 'id');
	}

	public function caja_movimientos()
	{
    	return $this->belongsTo(Caja_Movimiento::class, 'caja_movimiento_id', 'id')->with('tipotransaccioncajas');
	}

}
