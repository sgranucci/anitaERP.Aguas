<?php

namespace App\Models\Contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Configuracion\Empresa;
use App\Models\Ventas\Venta;
use App\Models\Stock\MovimientoStock;
use Auth;

class Asiento extends Model
{
    protected $fillable = ['empresa_id', 'tipoasiento_id', 'numeroasiento', 'fecha', 'venta_id', 'movimientostock_id',
                            'compra_id', 'ordencompra_id', 'recepcionproveedor_id', 'observacion', 'usuario_id'];
    protected $table = 'asiento';

    public function asiento_movimientos()
	{
    	return $this->hasMany(Asiento_Movimiento::class, 'asiento_id')
                        ->with('cuentacontables')
                        ->with('centrocostos')
                        ->with('monedas');
	}

	public function asiento_archivos()
	{
    	return $this->hasMany(Asiento_Archivo::class, 'asiento_id');
	}

    public function empresas()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function tipoasientos()
    {
        return $this->belongsTo(Tipoasiento::class, 'tipoasiento_id');
    }

    public function ventas()
    {
        return $this->belongsTo(Ventas::class, 'venta_id');
    }

    public function movimientostocks()
    {
        return $this->belongsTo(MovimientoStock::class, 'movimientostock_id');
    }

    public function usuarios()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }


}
