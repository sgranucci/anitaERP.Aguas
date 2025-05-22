<?php

namespace App\Models\Caja;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Configuracion\Empresa;
use App\Models\Ventas\Cliente;
use App\Models\Compras\Proveedor;
use App\Models\Contable\Asiento;
use Auth;

class Caja_Movimiento extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $fillable = ['empresa_id', 'tipotransaccion_caja_id', 'numerotransaccion', 'fecha', 
                            'caja_id', 'proveedor_id', 'cliente_id', 'detalle', 'usuario_id'];
    protected $table = 'caja_movimiento';

    public function caja_movimiento_cuentacajas()
	{
    	return $this->hasMany(Caja_Movimiento_Cuentacaja::class, 'caja_movimiento_id')
                        ->with('cuentacajas')
                        ->with('monedas');
	}

    public function caja_movimiento_estados()
	{
    	return $this->hasMany(Caja_Movimiento_Estado::class, 'caja_movimiento_id');
	}

	public function caja_movimiento_archivos()
	{
    	return $this->hasMany(Caja_Movimiento_Archivo::class, 'caja_movimiento_id');
	}

    public function asientos()
	{
    	return $this->belongsTo(Asiento::class, 'id', 'caja_movimiento_id')->with('asiento_movimientos');
	}

    public function empresas()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function tipotransaccioncajas()
    {
        return $this->belongsTo(Tipotransaccion_caja::class, 'tipotransaccion_caja_id');
    }

    public function proveedores()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function clientes()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function usuarios()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }


}
