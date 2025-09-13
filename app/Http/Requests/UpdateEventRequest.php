<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nome' => 'sometimes|string|max:255',
            'descricao' => 'sometimes|string',
            'data_inicio_evento' => 'sometimes|date',
            'data_fim_evento' => 'sometimes|date|after_or_equal:data_inicio_evento',
            'data_inicio_inscricao' => 'sometimes|date',
            'data_fim_inscricao' => 'sometimes|date|after_or_equal:data_inicio_inscricao',
            'tipo_evento' => 'sometimes|string|max:50',
            'logomarca_url' => 'sometimes|nullable|string',
            'status' => 'sometimes|string|max:50',
        ];
    }
}
