<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidacionArticuloTecnica extends FormRequest
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
            'sku' => 'required|max:20|unique:articulo,sku,' . $this->route('id'),
            'descripcion' => 'required|max:100|',
            'tipocorteforro_id' => ['integer', 'nullable'],
            'usoarticulo_id' => ['required', 'integer'],
            'tipocorte_id' => ['required', 'integer', 'nullable'],
            'puntera_id' => ['integer', 'nullable'],
            'contrafuerte_id' => ['integer', 'nullable'],
            'mventa_id' => ['required', 'integer', 'max:15'],
            'forro_id' => ['integer', 'nullable'],
            'compfondo_id' => ['integer'],
        ];
    }
}
