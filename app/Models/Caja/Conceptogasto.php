<?php

namespace App\Models\Caja;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Conceptogasto extends Model
{
    protected $fillable = ['nombre'];
    protected $table = 'conceptogasto';
}

