<?php

namespace App\Models\Caja;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Cuentacaja extends Model
{
    protected $fillable = ['nombre'];
    protected $table = 'cuentacaja';
}

