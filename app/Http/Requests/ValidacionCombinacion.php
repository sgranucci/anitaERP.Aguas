<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidacionCombinacion extends FormRequest
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
            'articulo_id' => ['required'],
            'codigo' => ['required','string','max:6'],
            'nombre' => ['string','max:40'],
            'observacion' => ['string', 'nullable'],
            'estado' => ['string','max:1'],
        ];
    }
}

