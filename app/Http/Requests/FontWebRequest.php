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
            'cd_pais' => 'required',
            'cd_estado' => 'required',
            'nome' => 'required',
            'url' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'cd_pais.required' => 'Campo <strong>País</strong> é obrigatório',
            'cd_estado.required' => 'Campo <strong>Estado</strong> é obrigatório',
            'nome.required' => 'Campo <strong>Nome</strong> é obrigatório',
            'url.required' => 'Campo <strong>URL</strong> é obrigatório'
        ];
    }
}