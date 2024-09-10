<?php

namespace App\Models\Compras;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\ApiAnita;
use App\Models\Seguridad\Usuario;
use App\Models\Contable\Cuentacontable;
use App\Models\Contable\Centrocosto;
use App\Models\Configuracion\Impuesto;
use App\Models\Configuracion\Localidad;
use App\Models\Configuracion\Provincia;
use App\Models\Configuracion\Pais;
use App\Models\Compras\Condicioncompra;
use App\Models\Compras\Condicionentrega;
use App\Models\Compras\Condicionpago;
use App\Models\Compras\Retencionganancia;
use App\Models\Compras\Retencioniva;
use App\Models\Compras\Retencionsyss;
use App\Models\Compras\Tipoempresa;
use App\Models\Caja\Conceptogasto;
use App\Models\Configuracion\Condicioniva;
use App\Models\Configuracion\CondicionIIBB;
use App\Models\Compras\Tiposuspensionproveedor;
use App\Traits\Compras\ProveedorTrait;

class Proveedor extends Model
{
	use SoftDeletes;
	use ProveedorTrait;

    protected $fillable = [
                            'nombre', 'codigo', 'contacto', 'fantasia', 'email', 'telefono', 'urlweb', 'domicilio',
                            'localidad_id', 'provincia_id', 'pais_id', 'codigopostal', 'tipoempresa_id', 
                            'nroinscripcion', 'condicioniva_id', 'agentepercepcioniva', 'retieneiva', 'retencioniva_id',
                            'retieneganancia', 'condicionganancia', 'retencionganancia_id', 'retienesuss', 
                            'retencionsuss_id','condicionIIBB_id', 'agentepercepcionIIBB', 'nroIIBB', 
                            'condicionpago_id', 'condicionentrega_id', 'condicioncompra_id', 'cuentacontable_id', 
                            'cuentacontableme_id', 'cuentacontablecompra_id', 'centrocostocompra_id', 'conceptogasto_id',
                            'estado', 'leyenda', 'tiposuspension_id', 'tipoalta', 'usuario_id'
                        ];
                               
    protected $table = 'proveedor';
	protected $dates = ['deleted_at'];

    public function proveedor_exclusiones()
	{
    	return $this->hasMany(Proveedor_Exclusion::class, 'proveedor_id');
	}

	public function proveedor_formapagos()
	{
    	return $this->hasMany(Proveedor_Formapago::class, 'proveedor_id');
	}

	public function proveedor_archivos()
	{
    	return $this->hasMany(Proveedor_Archivo::class, 'proveedor_id');
	}

    public function tipossuspensionproveedores()
    {
        return $this->belongsTo(Tiposuspensionproveedor::class, 'tiposuspension_id');
    }

    public function tipoempresas()
    {
        return $this->belongsTo(Tipoempresa::class, 'tipoempresa_id');
    }

    public function localidades()
    {
        return $this->belongsTo(Localidad::class, 'localidad_id');
    }

    public function provincias()
    {
        return $this->belongsTo(Provincia::class, 'provincia_id');
    }

    public function paises()
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }

    public function condicionivas()
    {
        return $this->belongsTo(Condicioniva::class, 'condicioniva_id');
    }

    public function condicionpagos()
    {
        return $this->belongsTo(Condicionpago::class, 'condicionpago_id');
    }

    public function cuentascontables()
    {
        return $this->belongsTo(Cuentacontable::class, 'cuentacontable_id');
    }

    public function cuentascontablesme()
    {
        return $this->belongsTo(Cuentacontable::class, 'cuentacontableme_id');
    }

    public function cuentascontablescompra()
    {
        return $this->belongsTo(Cuentacontable::class, 'cuentacontablecompra_id');
    }

    public function centrocostoscompra()
    {
        return $this->belongsTo(Centrocosto::class, 'centrocostocompra_id');
    }

    public function conceptogastos()
    {
        return $this->belongsTo(Conceptogasto::class, 'conceptogasto_id');
    }

    public function condicionIIBBs()
    {
        return $this->belongsTo(CondicionIIBB::class, 'condicionIIBB_id');
    }

    public function retencionganancias()
    {
        return $this->belongsTo(Retencionganancia::class, 'retencionganancia_id');
    }

    public function retencionivas()
    {
        return $this->belongsTo(Retencioniva::class, 'retencioniva_id');
    }

    public function retencionsusss()
    {
        return $this->belongsTo(Retencionsuss::class, 'retencionsuss_id');
    }

    public function usuarios()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

	public function getDescProvinciaAttribute()
	{
		$data = Provincia::find($this->provincia_id);
		return ($data ? $data->nombre : '');
	}

	public function getDescLocalidadAttribute()
	{
		$data = Localidad::find($this->localidad_id);
		return ($data ? $data->nombre : '');
	}

	public function getDescripcionEstadoAttribute()
	{
		$desc = self::$enumEstado[$this->estado];

		return ($desc);
	}
}

