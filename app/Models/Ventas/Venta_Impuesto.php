<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta_Impuesto extends Model
{
    use SoftDeletes;

    protected $fillable = ['concepto', 'baseimponible', 'tasa', 'importe', 'provincia_id', 'impuesto_id'];
    protected $table = 'venta_impuesto';
    protected $dates = ['deleted_at'];
}

