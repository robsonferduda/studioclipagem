<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificationRequest extends FormRequest
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
            'notification_id' => 'required',
            'dt_inicio' => 'required',
            'valor' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'notification_id.required' => 'Campo <strong>Tipo de Notificação</strong> é obrigatório',
            'dt_inicio.required' => 'Campo <strong>Data Inicial</strong> é obrigatório',
            'valor.required' => 'Campo <strong>Termo/Valor</strong> é obrigatório'
        ];
    }
}