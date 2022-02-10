<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use App\Traits\Configuracion\CondicionivaTrait;

class Condicioniva extends Model
{
	use CondicionivaTrait;

    protected $fillable = ['nombre', 'letra', 'coniva', 'coniibb'];
    protected $table = 'condicioniva';

	public function getDescConIvaAttribute()
	{
	  	return Arr::get(Condicioniva::$enumIva, $this->coniva);
	}
	public function getDescConIibbAttribute()
	{
	  	return Arr::get(Condicioniva::$enumIibb, $this->coniibb);
	}
}

