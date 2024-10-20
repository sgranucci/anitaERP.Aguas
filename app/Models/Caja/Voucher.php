<?php

namespace App\Models\Caja;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Receptivo\Servicioterrestre;
use App\Models\Compras\Proveedor;
use App\Models\Ventas\Formapago;
use App\Models\Configuracion\Moneda;

class Voucher extends Model
{
    use SoftDeletes;
    protected $fillable = ['talonariovoucher_id', 'numero', 'fecha', 'reserva_id', 'pasajero_id', 
							'nombrepasajero', 'pax', 'paxfree',
                            'incluido', 'opcional', 'servicioterrestre_id', 'proveedor_id', 'formapago_id',
                            'moneda_id', 'montovoucher', 'cotizacion', 'montoempresa', 'montoproveedor', 
							'observacion'];
    protected $table = 'voucher';

    public function voucher_guias()
	{
    	return $this->hasMany(Voucher_Guia::class, 'voucher_id');
	}

	public function talonariovouchers()
	{
    	return $this->belongsTo(Talonariovoucher::class, 'talonariovoucher_id');
	}

    public function servicioterrestres()
	{
    	return $this->belongsTo(Servicioterrestre::class, 'servicioterrestre_id');
	}

    public function proveedores()
	{
    	return $this->belongsTo(Proveedor::class, 'proveedor_id');
	}

    public function formapagos()
	{
    	return $this->belongsTo(Formapago::class, 'formapago_id');
	}

    public function monedas()
	{
    	return $this->belongsTo(Moneda::class, 'moneda_id');
	}
}



