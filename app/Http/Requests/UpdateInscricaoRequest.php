<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInscricaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // estes campos são opcionais numa edição
            'status'   => ['sometimes', 'nullable', 'in:pendente,confirmada,cancelada,ativa,inativa'],
            'presente' => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'status' => 'status da inscrição',
            'presente' => 'presença',
        ];
    }
}
