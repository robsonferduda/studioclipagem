<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PerfilRequest extends FormRequest
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
            'name' => 'required',
            'display_name' => 'required',
            'display_color' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Campo <strong>Nome</strong> é obrigatório',
            'display_name.required' => 'Campo <strong>Chave</strong> é obrigatório',
            'display_color.required' => 'Campo <strong>Cor</strong> é obrigatório'
        ];
    }
}