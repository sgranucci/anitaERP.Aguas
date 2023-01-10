<?php

namespace App\Rules\Produccion;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;
use App\Services\Produccion\MovimientoOrdentrabajoService;

class RuleMovimientoOrdentrabajo implements Rule
{
 	private $campo, $operacion_id, $tarea_id;
    private $movimientoOrdentrabajoService;
	private $ordenesConProblemas;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($campo, $tarea_id, $operacion_id,
                                MovimientoOrdentrabajoService $movimientoordentrabajoservice)
    {
      $this->movimientoOrdentrabajoService = $movimientoordentrabajoservice;
      $this->campo = $campo;	  
      $this->operacion_id = $operacion_id;
      $this->tarea_id = $tarea_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
      switch($this->campo)
      {
        case 'ordenestrabajo':
			$cc = $this->movimientoOrdentrabajoService->controlSecuencia($value, $this->operacion_id, $this->tarea_id);
			if ($cc['resultado'] == 0)
				$this->ordenesConProblemas = $cc['ordenestrabajo'];
            $retorno = $cc['resultado'];
      }
      return($retorno);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
		$ots = implode(',', $this->ordenesConProblemas);

        return 'Error en campo :attribute. OT: '.$ots;
    }
}
