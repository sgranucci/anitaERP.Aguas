<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Ventas\RuleEmpleado;

class ValidacionMovimientoOrdentrabajo extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ordenestrabajo' => 'required',
            'tarea_id' => 'required',
            'operacion_id' => 'required',
            'fecha' => 'required',
        ];
    }
}
