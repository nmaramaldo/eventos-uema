<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
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
            'coordenador_id' => 'required|exists:users,id',
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'data_inicio_evento' => 'required|date',
            'data_fim_evento' => 'required|date|after_or_equal:data_inicio_evento',
            'data_inicio_inscricao' => 'required|date',
            'data_fim_inscricao' => 'required|date|after_or_equal:data_inicio_inscricao',
            'tipo_evento' => 'required|string|max:50|in:presencial,online,hibrido',
            'logomarca_url' => 'nullable|string',
            'status' => 'required|string|max:50',
        ];
    }
}
