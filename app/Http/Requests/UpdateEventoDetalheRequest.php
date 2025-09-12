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
            'descricao' => 'sometimes|required|string',
            'data' => 'sometimes|required|date',
            'hora_inicio' => 'sometimes|required',
            'hora_fim' => 'sometimes|required|after:hora_inicio',
            'modalidade' => 'sometimes|required|string|max:100',
            'capacidade' => 'sometimes|nullable|integer',
        ];
    }
}
