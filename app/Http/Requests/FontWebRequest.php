<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FontWebRequest extends FormRequest
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
            'cd_estado' => 'required',
            'cd_cidade' => 'required',
            'nome' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'cd_estado.required' => 'Campo <strong>Estado</strong> é obrigatório',
            'cd_cidade.required' => 'Campo <strong>Cidade</strong> é obrigatório',
            'nome.required' => 'Campo <strong>Nome</strong> é obrigatório'
        ];
    }
}