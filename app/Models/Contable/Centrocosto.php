<?php

namespace App\Models\Contable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Centrocosto extends Model
{
    protected $fillable = ['nombre', 'codigo', 'abreviatura'];
    protected $table = 'centrocosto';
}
