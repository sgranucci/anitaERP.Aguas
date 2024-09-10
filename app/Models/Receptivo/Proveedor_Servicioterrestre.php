<?php

namespace App\Models\Receptivo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Compras\Proveedor;

class Proveedor_Servicioterrestre extends Model
{
    protected $fillable = [
						 'proveedor_id', 'servicioterrestre_id'
						];
    protected $table = 'proveedor_servicioterrestre';

	public function proveedores()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

	public function servicioterrestres()
    {
        return $this->belongsTo(Servicioterrestre::class, 'servicioterrestre_id');
    }

}
