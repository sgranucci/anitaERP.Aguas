<?php

namespace App\Models\Contable;

use Illuminate\Database\Eloquent\Model;

class Asiento_Archivo extends Model
{
    protected $fillable = ['asiento_id', 'nombrearchivo'];
    protected $table = 'asiento_archivo';

	public function asientos()
	{
    	return $this->belongsTo(Asiento::class, 'asiento_id', 'id');
	}

}
