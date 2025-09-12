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
            'nome' => 'sometimes|required|string|max:255',
            'descricao' => 'sometimes|required|string',
            'data_inicio_evento' => 'sometimes|required|date',
            'data_fim_evento' => 'sometimes|required|date|after_or_equal:data_inicio_evento',
            'data_inicio_inscricao' => 'sometimes|required|date',
            'data_fim_inscricao' => 'sometimes|required|date|after_or_equal:data_inicio_inscricao',
            'tipo_evento' => 'sometimes|required|string|max:50',
            'logomarca_url' => 'sometimes|nullable|string',
            'status' => 'sometimes|required|string|max:50',
        ];
    }
}
