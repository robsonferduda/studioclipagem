<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialSearchRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return ($this->isMethod('POST') ? $this->store() : $this->index());       
    }

    public function store()
    {
        return [
            'dt_inicial' => 'required',
            'dt_final' => 'required'
        ];
    }

    public function index()
    {
        return [

        ];
    }

    public function messages()
    {
        return [
            'dt_inicial.required' => 'Campo <strong>Data Inicial</strong> é obrigatório',
            'dt_final.required' => 'Campo <strong>Data Final</strong> é obrigatório'
        ];
    }
}