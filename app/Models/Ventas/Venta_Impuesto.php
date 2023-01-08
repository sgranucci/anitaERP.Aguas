<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Venta_Impuesto extends Model
{
    protected $fillable = ['concepto', 'baseimponible', 'tasa', 'importe', 'provincia_id', 'impuesto_id'];

    protected $table = 'venta_impuesto';

}

