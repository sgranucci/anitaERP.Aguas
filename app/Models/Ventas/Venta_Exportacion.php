<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta_Exportacion extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = ['venta_id', 'incoterm_id', 'formapago_id', 'mercaderia', 'leyendaexportacion'];
    protected $table = 'venta_exportacion';

}

