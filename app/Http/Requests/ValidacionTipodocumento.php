<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidacionTipodocumento extends FormRequest
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
            'nombre' => 'required|max:255|unique:tipodocumento,nombre,' . $this->route('id'),
            'abreviatura' => 'required|max:5|unique:tipodocumento,abreviatura,' . $this->route('id'),
        ];
    }
}
