<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'email' => 'required',
            'password' => 'required',
            'confirm_password' => 'required|same:password'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Campo <strong>Nome</strong> é obrigatório',
            'email.required' => 'Campo <strong>Email</strong> é obrigatório',
            'password.required' => 'Campo <strong>Senha</strong> é obrigatório',
            'confirm_password.required' => 'Campo <strong>Confirmar Senha</strong> é obrigatório',
            'confirm_password.same' => 'Campos <strong>Senha</strong> e <strong>Confirmação de Senha</strong> devem ser iguais'
        ];
    }
}