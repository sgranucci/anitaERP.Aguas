<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Traits\Ventas\PuntoventaTrait;
use App\Models\Configuracion\Localidad;
use App\Models\Configuracion\Provincia;
use App\Models\Configuracion\Pais;
use App\Models\Configuracion\Empresa;
use Illuminate\Database\Eloquent\SoftDeletes;

class Puntoventa extends Model
{
    use SoftDeletes;
	use PuntoventaTrait;

    protected $fillable = ['nombre', 'codigo', 'empresa_id', 'domicilio', 'localidad_id', 
                            'provincia_id', 'pais_id', 'codigopostal', 'email', 'telefono', 
                            'leyenda', 'modofacturacion', 'estado', 'webservice', 'pathafip'];
    protected $table = 'puntoventa';

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

    public function empresas()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
 
}

