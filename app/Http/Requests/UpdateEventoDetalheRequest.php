<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventoDetalheRequest extends FormRequest
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
            'evento_id' => 'sometimes|exists:eventos,id',
            'descricao' => 'sometimes|string',
            'data' => 'sometimes|date',
            'hora_inicio' => 'sometimes',
            'hora_fim' => 'sometimes|after:hora_inicio',
            'modalidade' => 'sometimes|string|max:100',
            'capacidade' => 'sometimes|nullable|integer',
            'localidade' => 'required|string|max:255'
        ];
    }
}
